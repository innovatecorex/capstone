@extends('layouts.admin')

@section('title', 'Subject Details: ' . $subject->subject_name)
@section('breadcrumb', 'Subjects / Details')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">{{ $subject->subject_name }}</h1>
      <p class="enc-page__subtitle">
        Subject Code: <code>{{ $subject->subject_code }}</code>
      </p>
    </div>
    <a href="{{ route('admin.subjects.index') }}" class="enc-btn enc-btn--secondary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
      Back to Subjects
    </a>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;">

  {{-- ── Left column ──────────────────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Subject Information --}}
    <div class="enc-card">
      <div class="enc-card__header"><div class="enc-card__title">Subject Information</div></div>
      <div class="enc-card__body" style="padding:24px;">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
          <div>
            <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Subject Code</div>
            <div style="font-size:.95rem;font-weight:600;color:#0f172a;">{{ $subject->subject_code }}</div>
          </div>
          <div>
            <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Credit Hours</div>
            <div style="font-size:.95rem;font-weight:600;color:#0f172a;">{{ $subject->credits ?? '—' }}</div>
          </div>
          <div>
            <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Year Level</div>
            <div style="font-size:.95rem;font-weight:600;color:#0f172a;">{{ $subject->year_level ?? '—' }}</div>
          </div>
          <div>
            <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Status</div>
            @php $sc = $subject->status === 'active' ? ['#166534','#86efac','#f0fdf4'] : ['#475569','#cbd5e1','#f8fafc']; @endphp
            <span style="display:inline-block;padding:.2rem .6rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $sc[0] }};background:{{ $sc[2] }};border:1px solid {{ $sc[1] }};">
              {{ ucfirst($subject->status) }}
            </span>
          </div>
        </div>

        <div style="border-top:1px solid #f1f5f9;padding-top:18px;">
          <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Description</div>
          <p style="font-size:.9rem;color:#334155;line-height:1.6;margin:0;">
            {{ $subject->description ?? 'No description provided.' }}
          </p>
        </div>

      </div>
    </div>

    {{-- Curriculum Usage --}}
    <div class="enc-card">
      <div class="enc-card__header"><div class="enc-card__title">Used in Curriculum</div></div>
      <div class="enc-card__body" style="padding:0;">
        @if($curriculumUsage->count() > 0)
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
              <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                  <th style="padding:11px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Academic Year</th>
                  <th style="padding:11px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Grade Level</th>
                  <th style="padding:11px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Type</th>
                  <th style="padding:11px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($curriculumUsage as $usage)
                <tr style="border-bottom:1px solid #f1f5f9;">
                  <td style="padding:11px 16px;">
                    <a href="{{ route('admin.academic-years.edit', $usage->academicYear) }}" style="color:#1d4ed8;font-weight:600;text-decoration:none;">
                      {{ $usage->academicYear->year_label }}
                    </a>
                  </td>
                  <td style="padding:11px 16px;font-weight:600;color:#0f172a;">{{ $usage->grade_level }}</td>
                  <td style="padding:11px 16px;">
                    @php $tc = $usage->is_required ? ['#1e40af','#bfdbfe','#eff6ff'] : ['#475569','#cbd5e1','#f8fafc']; @endphp
                    <span style="display:inline-block;padding:.18rem .5rem;border-radius:6px;font-size:.7rem;font-weight:700;color:{{ $tc[0] }};background:{{ $tc[2] }};border:1px solid {{ $tc[1] }};">
                      {{ $usage->is_required ? 'Required' : 'Elective' }}
                    </span>
                  </td>
                  <td style="padding:11px 16px;">
                    @php $uc = $usage->status === 'active' ? ['#166534','#86efac','#f0fdf4'] : ['#475569','#cbd5e1','#f8fafc']; @endphp
                    <span style="display:inline-block;padding:.18rem .5rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $uc[0] }};background:{{ $uc[2] }};border:1px solid {{ $uc[1] }};">
                      {{ ucfirst($usage->status) }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div style="padding:32px;text-align:center;color:#94a3b8;font-size:.9rem;">
            This subject is not currently used in any curriculum mappings.
          </div>
        @endif
      </div>
    </div>

  </div>

  {{-- ── Right column (sidebar) ───────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Subject ID --}}
    <div class="enc-card">
      <div class="enc-card__header"><div class="enc-card__title">Subject ID</div></div>
      <div class="enc-card__body" style="padding:20px;">
        <code style="display:block;background:#f8fafc;color:#475569;font-size:.82rem;padding:10px 12px;border-radius:8px;border:1px solid #e2e8f0;word-break:break-all;">
          {{ $subject->subject_id }}
        </code>
        <p style="font-size:.75rem;color:#94a3b8;margin:10px 0 0;line-height:1.5;">
          Immutable, unique identifier assigned at creation.
        </p>
      </div>
    </div>

    {{-- Metadata --}}
    <div class="enc-card">
      <div class="enc-card__header"><div class="enc-card__title">Metadata</div></div>
      <div class="enc-card__body" style="padding:20px;display:flex;flex-direction:column;gap:14px;">
        <div>
          <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Created</div>
          <div style="font-size:.85rem;font-weight:600;color:#0f172a;">{{ $subject->created_at->format('M d, Y g:i A') }}</div>
        </div>
        <div>
          <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Last Updated</div>
          <div style="font-size:.85rem;font-weight:600;color:#0f172a;">{{ $subject->updated_at->format('M d, Y g:i A') }}</div>
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="enc-card">
      <div class="enc-card__header"><div class="enc-card__title">Actions</div></div>
      <div class="enc-card__body" style="padding:20px;display:flex;flex-direction:column;gap:10px;">
        <a href="{{ route('admin.subjects.edit', $subject) }}"
           style="display:block;text-align:center;background:#1d4ed8;color:#fff;font-size:.85rem;font-weight:700;padding:.55rem 1rem;border-radius:8px;text-decoration:none;">
          Edit Subject
        </a>

        @if(!$subject->isUsedInCurriculum())
        <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
              data-confirm="Delete this subject? This action cannot be undone." data-confirm-type="danger" data-confirm-title="Delete Subject" data-confirm-ok="Delete">
          @csrf
          @method('DELETE')
          <button type="submit" style="width:100%;background:#fff;color:#dc2626;border:1px solid #fca5a5;font-size:.85rem;font-weight:700;padding:.55rem 1rem;border-radius:8px;cursor:pointer;">
            Delete Subject
          </button>
        </form>
        @else
        <div style="background:#fffbeb;border:1px solid #fde68a;padding:12px 14px;border-radius:8px;font-size:.8rem;color:#92400e;line-height:1.5;">
          Cannot delete — this subject is used in curriculum mappings.
        </div>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection
