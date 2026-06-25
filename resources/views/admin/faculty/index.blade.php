{{-- resources/views/admin/faculty/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Faculty Management')
@section('breadcrumb', 'Faculty')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Faculty Management</h1>
      <p class="enc-page__subtitle">View and manage all active faculty members with detailed information</p>
    </div>
  </div>
</div>

{{-- Stats Row --}}
<div class="enc-stats" style="margin-bottom:20px;">
  <a href="{{ route('admin.faculty.index') }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--green">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <rect x="15" y="55" width="28" height="28" rx="4" fill="rgba(255,255,255,.25)" stroke="rgba(255,255,255,.7)" stroke-width="2"/>
        <circle cx="29" cy="34" r="13" fill="rgba(255,255,255,.85)"/>
        <path d="M46 70 L65 40 L84 70" stroke="rgba(255,255,255,.85)" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        <line x1="55" y1="60" x2="75" y2="60" stroke="rgba(255,255,255,.6)" stroke-width="4" stroke-linecap="round"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['total_faculty'] }}</div>
      <div class="enc-stat-label">Total Faculty</div>
    </div>
  </a>

  <a href="{{ route('admin.faculty.index', ['gender' => 'male']) }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--indigo">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <circle cx="50" cy="38" r="18" fill="rgba(255,255,255,.85)" stroke="rgba(255,255,255,.5)" stroke-width="2"/>
        <path d="M26 82 C26 66 74 66 74 82" stroke="rgba(255,255,255,.85)" stroke-width="6" fill="none" stroke-linecap="round"/>
        <line x1="72" y1="22" x2="84" y2="10" stroke="rgba(255,255,255,.7)" stroke-width="4" stroke-linecap="round"/>
        <polyline points="75,10 84,10 84,19" stroke="rgba(255,255,255,.7)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['male_faculty'] }}</div>
      <div class="enc-stat-label">Male Faculty</div>
    </div>
  </a>

  <a href="{{ route('admin.faculty.index', ['gender' => 'female']) }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--pink">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <circle cx="50" cy="36" r="18" fill="rgba(255,255,255,.85)" stroke="rgba(255,255,255,.5)" stroke-width="2"/>
        <line x1="50" y1="54" x2="50" y2="76" stroke="rgba(255,255,255,.8)" stroke-width="5" stroke-linecap="round"/>
        <line x1="36" y1="66" x2="64" y2="66" stroke="rgba(255,255,255,.8)" stroke-width="5" stroke-linecap="round"/>
        <path d="M28 80 C28 68 72 68 72 80" stroke="rgba(255,255,255,.6)" stroke-width="4" fill="none" stroke-linecap="round"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['female_faculty'] }}</div>
      <div class="enc-stat-label">Female Faculty</div>
    </div>
  </a>
</div>

{{-- Faculty Table --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C6.228 6.253 2.092 7.582 2.092 9s4.136 2.747 9.908 2.747c5.772 0 9.908-1.254 9.908-2.747 0-1.418-4.136-2.747-9.908-2.747zm13 0c.586.3.951.893.951 1.602 0 1.813-4.176 3.358-9.322 3.358-5.146 0-9.322-1.545-9.322-3.358 0-.709.365-1.302.951-1.602m16.694 12.286l-3.582-3.582m0 0l-2.16-3.97m-2.5 12.005A11.955 11.955 0 112.487 2.1"/>
      </svg>
      All Faculty
    </div>
    <span class="enc-card__meta">{{ $faculty->total() }} total records</span>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.faculty.index') }}">
    <div class="enc-filter-bar">
      <div class="enc-filter-group">
        <span class="enc-filter-label">Search</span>
        <div class="enc-search-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
          </svg>
          <input type="text" name="search" value="{{ $search }}"
                 placeholder="Name, Username, Employee No..."
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
      <button type="submit" class="enc-btn enc-btn--primary enc-btn--sm">Filter</button>
      @if(request()->hasAny(['search','gender']))
        <a href="{{ route('admin.faculty.index') }}" class="enc-btn enc-btn--ghost enc-btn--sm">Clear</a>
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
            <th>Employee No.</th>
            <th>Username</th>
            <th>Gender</th>
            <th>Email</th>
            <th>Registered</th>
          </tr>
        </thead>
        <tbody>
          @forelse($faculty as $member)
            <tr>
              {{-- Name --}}
              <td>
                <div style="font-weight:600;color:var(--navy);font-size:.82rem;">
                  {{ $member->first_name }} {{ $member->last_name }}
                </div>
              </td>

              {{-- Employee No. --}}
              <td class="mono" style="font-size:.75rem;">
                @if($member->employee_number)
                  <span style="color:var(--info);">{{ $member->employee_number }}</span>
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Username --}}
              <td class="mono">{{ $member->username }}</td>

              {{-- Gender --}}
              <td>
                @if($member->gender)
                  @if($member->gender === 'male')
                    <span class="enc-badge enc-badge--info">Male</span>
                  @else
                    <span class="enc-badge enc-badge--pink">Female</span>
                  @endif
                @else
                  <span style="color:var(--gray-200); font-size:.75rem;">Not specified</span>
                @endif
              </td>

              {{-- Email --}}
              <td style="font-size:.75rem;">
                <span style="color:var(--gray-600);">{{ substr($member->email, 0, 20) }}...</span>
              </td>

              {{-- Registered Date --}}
              <td class="mono" style="font-size:.72rem;">
                {{ $member->created_at->format('m/d/Y') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center;padding:30px;color:var(--gray-200);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;margin:0 auto 10px;opacity:0.3;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>No faculty members found.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Pagination --}}
  @if($faculty->hasPages())
    <div class="enc-card__footer" style="text-align:center;">
      {{ $faculty->links() }}
    </div>
  @endif
</div>


@endsection
