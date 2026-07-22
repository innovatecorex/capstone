@extends('layouts.app')
@section('title', 'Bulk Student Import')
@section('breadcrumb', 'Import Students')

@section('content')
<div style="max-width:860px;">

  <div style="margin-bottom:28px;">
    <h1 style="font-size:1.3rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Bulk Student Import</h1>
    <p style="font-size:.875rem;color:#94a3b8;margin:0;">Upload a CSV file to enroll multiple students at once.</p>
  </div>

  {{-- ── Results panel ──────────────────────────────────────────────────────
       Colour follows the OUTCOME, not just "we finished":
         · all rows imported        → green  (success)
         · some imported, some not  → amber  (partial)
         · nothing imported         → red    (rejected / not accepted)
       Per panel feedback: a rejected import must not read as green. --}}
  @if(isset($imported))
  @php
    $allOk   = $skipped === 0 && $imported > 0;
    $allFail = $imported === 0;
    if ($allOk)        { $rBg = '#f0fdf4'; $rBorder = '#bbf7d0'; $rInk = '#166534'; $rTitle = "Import complete — {$imported} student(s) created."; }
    elseif ($allFail)  { $rBg = '#fef2f2'; $rBorder = '#fecaca'; $rInk = '#991b1b'; $rTitle = "Import failed — no students created" . ($skipped > 0 ? ", {$skipped} row(s) rejected." : "."); }
    else               { $rBg = '#fffbeb'; $rBorder = '#fcd34d'; $rInk = '#92400e'; $rTitle = "Import finished with warnings — {$imported} created, {$skipped} rejected."; }
  @endphp
  <div style="background:{{ $rBg }};border:1px solid {{ $rBorder }};border-radius:14px;padding:20px 24px;margin-bottom:24px;">
    <div style="font-size:.95rem;font-weight:700;color:{{ $rInk }};margin-bottom:6px;">
      {{ $rTitle }}
    </div>
    @if(!empty($rowErrors))
    <div style="margin-top:12px;">
      <div style="font-size:.8rem;font-weight:700;color:{{ $rInk }};margin-bottom:8px;">Rejected rows:</div>
      <ul style="margin:0;padding-left:18px;font-size:.8rem;color:#64748b;line-height:1.8;">
        @foreach($rowErrors as $err)
        <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
    @endif
  </div>
  @endif

  @error('csv_file')
  <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#991b1b;">
    {{ $message }}
  </div>
  @enderror

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">

    {{-- ── Upload form ─────────────────────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
      <div style="font-size:.88rem;font-weight:700;color:#374151;margin-bottom:16px;">Upload CSV</div>

      <form method="POST" action="{{ route('admin.students.import.submit') }}" enctype="multipart/form-data">
        @csrf

        <label style="display:block;font-size:.78rem;font-weight:600;color:#374151;margin-bottom:6px;">
          CSV File <span style="color:#dc2626;">*</span>
        </label>
        <input type="file" name="csv_file" accept=".csv,.txt" required
               style="display:block;width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px;font-size:.84rem;margin-bottom:16px;box-sizing:border-box;">

        <button type="submit"
                style="width:100%;padding:.6rem;background:#6366f1;color:#fff;border:none;border-radius:9px;font-size:.88rem;font-weight:700;cursor:pointer;">
          Import Students
        </button>
      </form>

      {{-- Excel turns a 12-digit LRN typed into a NUMBER cell into scientific
           notation (1.2348E+11) and the digits are lost for good. Warn up front. --}}
      <div style="margin-top:16px;background:#fffbeb;border:1px solid #fcd34d;border-left:4px solid #f59e0b;border-radius:10px;padding:12px 14px;display:flex;gap:10px;align-items:flex-start;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:17px;height:17px;color:#d97706;flex-shrink:0;margin-top:1px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <div style="font-size:.76rem;color:#92400e;line-height:1.65;">
          <strong>Format the LRN column as Text.</strong>
          In your spreadsheet, set the LRN column to <strong>Text</strong> (not Number) before saving.
          Excel converts a 12-digit number to scientific notation
          (<code style="background:#fef3c7;padding:0 3px;border-radius:3px;">1.2348E+11</code>),
          which <strong>permanently destroys the digits</strong> — such rows will be rejected.
        </div>
      </div>

      <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f1f5f9;">
        <a href="{{ route('admin.students.import.template') }}"
           style="font-size:.78rem;color:#6366f1;font-weight:600;text-decoration:none;">
          ↓ Download CSV Template
        </a>
      </div>
    </div>

    {{-- ── Format guide ─────────────────────────────────────────────────── --}}
    <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
      <div style="font-size:.88rem;font-weight:700;color:#374151;margin-bottom:12px;">CSV Format</div>

      <div style="font-size:.76rem;color:#374151;line-height:1.7;margin-bottom:14px;">
        <strong>Required columns</strong> (must be in header row):
      </div>
      <table style="width:100%;font-size:.75rem;border-collapse:collapse;margin-bottom:14px;">
        <thead>
          <tr style="background:#e2e8f0;">
            <th style="padding:5px 8px;text-align:left;font-weight:700;">Column</th>
            <th style="padding:5px 8px;text-align:left;font-weight:700;">Rules</th>
          </tr>
        </thead>
        <tbody>
          @foreach([
            ['first_name',   'Text, required'],
            ['last_name',    'Text, required'],
            ['email',        'Valid email, unique'],
            ['lrn',          'Exactly 12 digits, unique · format cell as TEXT'],
            ['grade_level',  'Optional · 7–12'],
            ['section_name', 'Optional · must match existing section'],
            ['gender',       'Optional · male or female'],
            ['phone',        'Optional'],
            ['address',      'Optional'],
          ] as [$col, $rule])
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:5px 8px;font-family:monospace;color:#6366f1;">{{ $col }}</td>
            <td style="padding:5px 8px;color:#64748b;">{{ $rule }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <div style="font-size:.76rem;color:#64748b;line-height:1.6;">
        <strong>Notes:</strong><br>
        · Temporary password = student's LRN<br>
        · Students must reset password on first login<br>
        · Duplicate LRN or email rows are skipped<br>
        · Max file size: 2 MB (~5,000 students)
      </div>
    </div>

  </div>

</div>
@endsection
