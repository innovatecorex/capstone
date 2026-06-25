<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantDocument extends Model
{
    protected $fillable = [
        'applicant_id',
        'document_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public static array $labels = [
        'birth_certificate' => 'Birth Certificate',
        'report_card'       => 'Previous Report Card',
        'good_moral'        => 'Good Moral Certificate',
        'other'             => 'Other Document',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function getLabelAttribute(): string
    {
        return self::$labels[$this->document_type] ?? ucfirst(str_replace('_', ' ', $this->document_type));
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function isImage(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }
}
