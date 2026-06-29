<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeGender extends Command
{
    protected $signature = 'users:normalize-gender
                            {--dry-run      : Preview changes without writing to the database}
                            {--fill-by-name : Also infer gender for NULL rows using first-name dictionary}';

    protected $description = 'Canonicalize non-standard gender values and optionally infer NULL gender from first name';

    private array $variantMap = [
        'm'      => 'male',
        'male'   => 'male',
        'f'      => 'female',
        'female' => 'female',
    ];

    /** Curated Filipino/common first names — covers DevelopmentSeeder name pool + common variants. */
    private array $femaleNames = [
        // DevelopmentSeeder firstNames pool (female)
        'maria','ana','rosa','carmen','elena','luz','patricia','gloria',
        'teresa','dolores','nena','carina',
        // Common Filipino female names
        'lourdes','concepcion','corazon','nimfa','maricel','maribel',
        'rosario','erlinda','leonora','remedios','norma','evelyn',
        'sofia','isabella','camille','cherry','gabrielle','pia','rose',
        'liza','grace','marites','rowena','sheila','vanessa','angeline',
        'precious','rachel','rebecca','diana','melissa','sandra',
        'danielle','nicole','michelle','claire','christine','stephanie',
        'jennifer','jessica','mary','elizabeth','sarah','emily','emma',
        'carmela','anna','anne','andrea','karen','kathleen','helen',
        'angela','lisa','marianne','rosalie','jennilyn','jessa','rhea',
        'tricia','nica','alyssa','katrina','nina','roselyn','cristina',
        'divina','florencia','milagros','felicidad','resurreccion',
        'teresita','luzviminda','narcisa','natividad','purificacion',
        'asuncion','encarnacion','adoracion','inmaculada','presentacion',
        'soledad','trinidad','paz','esperanza','fe','caridad',
        'yolanda','wilhelmina','florentina','flordeliza','floraida',
        'eden','heaven','joy','lovely','princess','angel','heart',
        'precious','star','sunshine','daisy','violet','ruby','pearl',
        // Short/nickname forms
        'marie','mary','ann','anne','lily','cris','tina','jane',
        'jean','joan','june','may','faith','hope','charity',
    ];

    private array $maleNames = [
        // DevelopmentSeeder firstNames pool (male)
        'juan','maria','pedro','jose','antonio','ricardo','miguel',
        'fernando','eduardo','ramon','alfredo','mario','ernesto','rodrigo',
        // Common Filipino male names
        'roberto','danilo','lourdes','alberto','emmanuel','manuel',
        'carlos','marco','luis','angelo','romeo','rafael','alex',
        'gilbert','renato','arnel','rodel','noel','danilo','alfredo',
        'oscar','felix','ruben','rogelio','elmer','roel','jerome',
        'randy','ronnie','allan','dennis','bernard','joel','mark',
        'john','paul','mike','james','robert','david','william',
        'joseph','charles','thomas','daniel','christopher','matthew',
        'anthony','donald','richard','kenneth','steven','edward',
        'brian','ronald','george','timothy','larry','jeffrey','gary',
        'frank','eric','stephen','patrick','harold','raymond','walter',
        'kyle','aaron','ryan','kevin','jason','justin','brandon',
        'adam','nicholas','samuel','benjamin','nathan','andrew',
        'jonathan','christian','michael','oggie','noel','rodel',
        'aries','julius','julius','nestor','virgilio','rolando',
        'reynaldo','wilfredo','armando','gregorio','celestino',
        'victorino','florencio','maximiliano','ambrosio','crisanto',
        'eugenio','faustino','gaudencio','hermenegildo','ildefonso',
        'jacinto','leoncio','macario','nicanor','onesimo','panfilo',
        'quirino','restituto','saturnino','teofilo','urduja','venancio',
        'wenceslao','xenophon','ygnacio','zosimo',
        // Short/nickname forms
        'ben','bob','dan','dave','don','ed','fred','gene',
        'hal','ian','joe','ken','leo','ned','pete','ray',
        'ron','sam','ted','tim','tom','vic','will',
    ];

    public function handle(): int
    {
        $dryRun     = $this->option('dry-run');
        $fillByName = $this->option('fill-by-name');

        if ($dryRun) {
            $this->warn('DRY RUN — no database changes will be made.');
        }

        $users = User::select('id', 'username', 'first_name', 'last_name', 'gender', 'role_id')
            ->where(function ($q) {
                $q->whereNotIn('gender', ['male', 'female'])
                  ->orWhereNull('gender');
            })
            ->get();

        if ($users->isEmpty()) {
            $this->info('No non-canonical gender values found. Nothing to do.');
            return self::SUCCESS;
        }

        $mapped   = 0;
        $inferred = 0;
        $skipped  = [];

        foreach ($users as $user) {
            $raw        = $user->getAttributes()['gender'] ?? null;
            $normalized = $this->normalize($raw);

            if ($normalized !== null) {
                $this->line(sprintf('  [map]    user %d (%s) — "%s" → "%s"',
                    $user->id, $user->username, $raw ?? 'NULL', $normalized));
                if (!$dryRun) {
                    DB::table('users')->where('id', $user->id)->update(['gender' => $normalized]);
                }
                $mapped++;
                continue;
            }

            if ($fillByName) {
                $guessed = $this->inferFromName($user->first_name);
                if ($guessed !== null) {
                    $this->line(sprintf('  [name]   user %d (%s %s, %s) → "%s"',
                        $user->id, $user->first_name, $user->last_name, $user->username, $guessed));
                    if (!$dryRun) {
                        DB::table('users')->where('id', $user->id)->update(['gender' => $guessed]);
                    }
                    $inferred++;
                    continue;
                }
            }

            $skipped[] = sprintf('  user %d (%s %s, %s) — raw: "%s"',
                $user->id, $user->first_name, $user->last_name, $user->username, $raw ?? 'NULL');
        }

        $this->newLine();
        $this->info("Mapped (variant→canonical): {$mapped}" . ($dryRun ? ' (dry run)' : ''));
        if ($fillByName) {
            $this->info("Inferred (by first name):   {$inferred}" . ($dryRun ? ' (dry run)' : ''));
        }
        $this->info('Still blank:                ' . count($skipped) . ' — manual review needed');

        if ($skipped) {
            $this->newLine();
            $this->warn('Could not determine gender for:');
            foreach ($skipped as $line) {
                $this->line($line);
            }
        }

        return self::SUCCESS;
    }

    private function normalize(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }
        return $this->variantMap[strtolower(trim($raw))] ?? null;
    }

    private function inferFromName(string $firstName): ?string
    {
        $key = strtolower(trim($firstName));
        if (in_array($key, $this->femaleNames, true)) return 'female';
        if (in_array($key, $this->maleNames, true))   return 'male';
        return null;
    }
}
