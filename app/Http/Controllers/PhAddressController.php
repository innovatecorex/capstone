<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PhAddressController extends Controller
{
    private const PSGC = 'https://psgc.gitlab.io/api';
    private const TTL  = 60 * 60 * 24 * 30; // cache 30 days

    private function psgc(string $path): array
    {
        return Cache::remember('psgc:' . $path, self::TTL, function () use ($path) {
            $r = Http::timeout(15)->get(self::PSGC . $path);
            return $r->successful() ? $r->json() : [];
        });
    }

    public function provinces()
    {
        $list = $this->psgc('/provinces/');
        usort($list, fn($a, $b) => strcmp($a['name'], $b['name']));

        $out = [['code' => '130000000', 'name' => 'Metro Manila (NCR)', 'type' => 'region']];
        foreach ($list as $p) {
            $out[] = ['code' => $p['code'], 'name' => $p['name'], 'type' => 'province'];
        }

        return response()->json($out);
    }

    public function cities(string $code)
    {
        $path = $code === '130000000'
            ? '/regions/130000000/cities-municipalities/'
            : '/provinces/' . $code . '/cities-municipalities/';

        $list = $this->psgc($path);
        usort($list, fn($a, $b) => strcmp($a['name'], $b['name']));

        return response()->json(array_map(
            fn($c) => ['code' => $c['code'], 'name' => $c['name']],
            $list
        ));
    }

    public function barangays(string $code)
    {
        $list = $this->psgc('/cities-municipalities/' . $code . '/barangays/');
        usort($list, fn($a, $b) => strcmp($a['name'], $b['name']));

        return response()->json(array_map(
            fn($b) => ['code' => $b['code'], 'name' => $b['name']],
            $list
        ));
    }
}
