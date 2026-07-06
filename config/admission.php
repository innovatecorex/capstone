<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Required-documents checklist
    |--------------------------------------------------------------------------
    | Keyed by a stable snake_case identifier that is stored in
    | applicant_requirement_checks.requirement_key.
    |
    | required = true  → the registrar CANNOT set status to "accepted" until
    |                    this item is checked.
    | required = false → shown on the checklist and tracked, but never blocks.
    |
    | Do NOT rename existing keys — that would orphan existing DB rows.
    */
    'requirements' => [
        'form_137'          => ['label' => 'Form 137 (Permanent Record)',         'required' => false],
        'form_138'          => ['label' => 'Form 138 (Report Card)',               'required' => true],
        'good_moral'        => ['label' => 'Certificate of Good Moral Character', 'required' => false],
        'birth_certificate' => ['label' => 'PSA Birth Certificate',               'required' => true],
        'report_card'       => ['label' => 'Previous Report Card',                'required' => false],
        'picture_2x2'       => ['label' => '2×2 ID Picture',                     'required' => false],
    ],
];
