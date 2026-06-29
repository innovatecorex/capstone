{{-- resources/views/admin/students/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Students Management')
@section('breadcrumb', 'Students')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Students Management</h1>
      <p class="enc-page__subtitle">View and manage all active students with detailed information</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      {{-- Export CSV — passes current filters --}}
      <a href="{{ route('admin.students.export', request()->only(['search','gender','grade_level','section_id'])) }}"
         class="enc-btn enc-btn--outline" style="font-size:.82rem;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        Export CSV
      </a>

      {{-- Print view — passes current filters --}}
      <a href="{{ route('admin.students.print', request()->only(['search','gender','grade_level','section_id'])) }}"
         target="_blank" class="enc-btn enc-btn--outline" style="font-size:.82rem;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
        </svg>
        Print
      </a>

      <a href="{{ route('admin.students.import') }}"
         style="display:inline-flex;align-items:center;gap:7px;padding:.55rem 1.1rem;background:#6366f1;color:#fff;border-radius:10px;font-size:.82rem;font-weight:700;text-decoration:none;transition:background .15s;"
         onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
        </svg>
        Import Students
      </a>
    </div>
  </div>
</div>

{{-- Stats Row --}}
<div class="enc-stats" style="margin-bottom:20px;">
  {{-- Total Students --}}
  <a href="{{ route('admin.students.index') }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--blue">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <path d="M50 10 L90 30 L50 50 L10 30 Z" fill="rgba(255,255,255,.9)" stroke="rgba(255,255,255,.5)" stroke-width="1.5"/>
        <path d="M25 38 L25 62 C25 72 36 80 50 80 C64 80 75 72 75 62 L75 38" stroke="rgba(255,255,255,.85)" stroke-width="5" fill="none" stroke-linecap="round"/>
        <circle cx="85" cy="30" r="5" fill="rgba(255,255,255,.7)"/>
        <line x1="85" y1="35" x2="85" y2="55" stroke="rgba(255,255,255,.7)" stroke-width="4" stroke-linecap="round"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['total_students'] }}</div>
      <div class="enc-stat-label">Total Students</div>
    </div>
  </a>

  {{-- Male Students --}}
  <a href="{{ route('admin.students.index', ['gender' => 'male']) }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--teal">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <circle cx="50" cy="38" r="18" fill="rgba(255,255,255,.85)" stroke="rgba(255,255,255,.5)" stroke-width="2"/>
        <path d="M26 82 C26 66 74 66 74 82" stroke="rgba(255,255,255,.85)" stroke-width="6" fill="none" stroke-linecap="round"/>
        <line x1="72" y1="22" x2="84" y2="10" stroke="rgba(255,255,255,.7)" stroke-width="4" stroke-linecap="round"/>
        <polyline points="75,10 84,10 84,19" stroke="rgba(255,255,255,.7)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['male_students'] }}</div>
      <div class="enc-stat-label">Male Students</div>
    </div>
  </a>

  {{-- Female Students --}}
  <a href="{{ route('admin.students.index', ['gender' => 'female']) }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--pink">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <circle cx="50" cy="36" r="18" fill="rgba(255,255,255,.85)" stroke="rgba(255,255,255,.5)" stroke-width="2"/>
        <line x1="50" y1="54" x2="50" y2="76" stroke="rgba(255,255,255,.8)" stroke-width="5" stroke-linecap="round"/>
        <line x1="36" y1="66" x2="64" y2="66" stroke="rgba(255,255,255,.8)" stroke-width="5" stroke-linecap="round"/>
        <path d="M28 80 C28 68 72 68 72 80" stroke="rgba(255,255,255,.6)" stroke-width="4" fill="none" stroke-linecap="round"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['female_students'] }}</div>
      <div class="enc-stat-label">Female Students</div>
    </div>
  </a>
</div>

{{-- Students Table --}}
<div class="enc-card">
  @php $hasFilters = request()->hasAny(['search','gender','grade_level','section_id']); @endphp
  <div class="enc-card__header" style="{{ $hasFilters ? 'flex-wrap:wrap;gap:8px;' : '' }}">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C6.228 6.253 2.092 7.582 2.092 9s4.136 2.747 9.908 2.747c5.772 0 9.908-1.254 9.908-2.747 0-1.418-4.136-2.747-9.908-2.747zm13 0c.586.3.951.893.951 1.602 0 1.813-4.176 3.358-9.322 3.358-5.146 0-9.322-1.545-9.322-3.358 0-.709.365-1.302.951-1.602m16.694 12.286l-3.582-3.582m0 0l-2.16-3.97m-2.5 12.005A11.955 11.955 0 112.487 2.1"/>
      </svg>
      All Students
    </div>
    @if($hasFilters)
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:5px;flex:1;">
      <span style="font-size:.71rem;color:#94a3b8;font-weight:600;">Filtered:</span>
      @if($search ?? null)
        <span style="font-size:.71rem;background:#eef2ff;color:#4338ca;padding:2px 8px;border-radius:99px;font-weight:600;">Search: {{ $search }}</span>
      @endif
      @if($gender ?? null)
        <span style="font-size:.71rem;background:#e0f2fe;color:#0369a1;padding:2px 8px;border-radius:99px;font-weight:600;">{{ ucfirst($gender) }}</span>
      @endif
      @if($gradeLevel ?? null)
        <span style="font-size:.71rem;background:#f0fdf4;color:#15803d;padding:2px 8px;border-radius:99px;font-weight:600;">{{ $gradeLevel === 'unassigned' ? 'Unassigned' : $gradeLevel }}</span>
      @endif
      @if(($sectionId ?? null) && ($activeSection = $sections->firstWhere('id', (int)$sectionId)))
        <span style="font-size:.71rem;background:#fdf4ff;color:#7e22ce;padding:2px 8px;border-radius:99px;font-weight:600;">{{ $activeSection->section_name }}</span>
      @endif
    </div>
    @endif
    <span class="enc-card__meta">
      @if($hasFilters)
        Showing {{ $students->total() }} of {{ $stats['total_students'] }} students
      @else
        {{ $students->total() }} students
      @endif
    </span>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.students.index') }}">
    <div class="enc-filter-bar">
      <div class="enc-filter-group">
        <span class="enc-filter-label">Search</span>
        <div class="enc-search-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
          </svg>
          <input type="text" name="search" value="{{ $search }}"
                 placeholder="Name, Username, LRN..."
                 class="enc-input enc-input--search" style="width:220px;">
        </div>
      </div>
      <div class="enc-filter-divider"></div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">Gender</span>
        <select name="gender" class="enc-select">
          <option value="">All Genders</option>
          <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Male</option>
          <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Female</option>
        </select>
      </div>
      <div class="enc-filter-divider"></div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">Grade Level</span>
        <select name="grade_level" class="enc-select">
          <option value="">All Grades</option>
          @foreach($gradeLevels as $gl)
            <option value="{{ $gl }}" {{ ($gradeLevel ?? '') === $gl ? 'selected' : '' }}>{{ $gl }}</option>
          @endforeach
          <option value="unassigned" {{ ($gradeLevel ?? '') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
        </select>
      </div>
      <div class="enc-filter-divider"></div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">Section</span>
        <select name="section_id" class="enc-select">
          <option value="">All Sections</option>
          @foreach($sections as $sec)
            <option value="{{ $sec->id }}" {{ ((int)($sectionId ?? 0)) === $sec->id ? 'selected' : '' }}>
              {{ $sec->section_name }}@if($sec->grade_level) ({{ $sec->grade_level }})@endif
            </option>
          @endforeach
        </select>
      </div>
      <div class="enc-filter-divider"></div>
      <button type="submit" class="enc-btn enc-btn--primary enc-btn--sm">Filter</button>
      @if(request()->hasAny(['search','gender','grade_level','section_id']))
        <a href="{{ route('admin.students.index') }}" class="enc-btn enc-btn--ghost enc-btn--sm">Clear</a>
      @endif
    </div>
  </form>

  {{-- Table --}}
  <div class="enc-card__body enc-card__body--no-pad">
    <div class="enc-table-wrap">
      <table class="enc-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>LRN</th>
            <th>Username</th>
            <th>Gender</th>
            <th>Grade &amp; Section</th>
            <th>Email</th>
            <th>Registered</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $student)
            <tr>
              {{-- Name --}}
              <td>
                <div style="font-weight:600;color:var(--navy);font-size:.82rem;">
                  {{ $student->first_name }} {{ $student->last_name }}
                </div>
              </td>

              {{-- LRN --}}
              <td class="mono" style="font-size:.75rem;">
                @if($student->lrn)
                  <span style="color:var(--info);">{{ $student->lrn }}</span>
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Username --}}
              <td class="mono">{{ $student->username }}</td>

              {{-- Gender --}}
              <td>
                @if($student->gender)
                  @if($student->gender === 'male')
                    <span class="enc-badge enc-badge--info">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;margin-right:4px;display:inline;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                      </svg>
                      Male
                    </span>
                  @else
                    <span class="enc-badge enc-badge--pink">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;margin-right:4px;display:inline;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                      </svg>
                      Female
                    </span>
                  @endif
                @else
                  <span style="color:var(--gray-200); font-size:.75rem;">Not specified</span>
                @endif
              </td>

              {{-- Grade & Section --}}
              <td style="font-size:.75rem;">
                @if($student->grade_level || $student->section)
                  <div style="font-weight:600;color:var(--gray-600);">{{ $student->grade_level ?? '—' }}</div>
                  @if($student->section)
                    <div style="color:var(--gray-400);font-size:.72rem;">{{ $student->section->section_name }}</div>
                  @endif
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Email --}}
              <td style="font-size:.75rem;">
                <span style="color:var(--gray-600);">{{ substr($student->email, 0, 20) }}...</span>
              </td>

              {{-- Registered Date --}}
              <td class="mono" style="font-size:.72rem;">
                {{ $student->created_at->format('m/d/Y') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:30px;color:var(--gray-200);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;margin:0 auto 10px;opacity:0.3;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>No students found.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Pagination --}}
  @if($students->hasPages())
    <div class="enc-card__footer" style="text-align:center;">
      {{ $students->links() }}
    </div>
  @endif
</div>


@endsection
