{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'User Management')
@section('breadcrumb', 'User Management')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">User Management</h1>
      <p class="enc-page__subtitle">Create, manage, and monitor all platform accounts</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.users.create') }}" class="enc-btn enc-btn--primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
        </svg>
        Create Account
      </a>
    </div>
  </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
  <div class="enc-alert enc-alert--info" style="margin-bottom:20px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="enc-alert__body">
      <div>{!! session('success') !!}</div>
    </div>
  </div>
@endif

@if(session('warning'))
  <div style="background:#fffbeb;border:1px solid #fcd34d;border-left:4px solid #f59e0b;border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:#d97706;flex-shrink:0;margin-top:1px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
    </svg>
    <div style="font-size:.875rem;color:#92400e;line-height:1.6;">
      <div style="font-weight:700;margin-bottom:4px;">Email delivery failed — save these credentials now</div>
      <div>{!! session('warning') !!}</div>
    </div>
  </div>
@endif

{{-- Stats Row --}}
<div class="enc-stats" style="margin-bottom:20px;">
  <a href="{{ route('admin.users.index', ['role' => '01']) }}" class="enc-stat-card" data-label="Total Students">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('role_id','01')->count() }}</div>
      <div class="enc-stat-label">Students</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['role' => '02']) }}" class="enc-stat-card" data-label="Faculty Members">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('role_id','02')->count() }}</div>
      <div class="enc-stat-label">Faculty</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['role' => '03']) }}" class="enc-stat-card" data-label="Registrar Staff">
    <div class="enc-stat-icon enc-stat-icon--teal">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('role_id','03')->count() }}</div>
      <div class="enc-stat-label">Registrars</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['status' => 'locked']) }}" class="enc-stat-card" data-label="Locked Accounts">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('status','locked')->count() }}</div>
      <div class="enc-stat-label">Locked</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['status' => 'deactivated']) }}" class="enc-stat-card" data-label="Deactivated Accounts">
    <div class="enc-stat-icon enc-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('status','deactivated')->count() }}</div>
      <div class="enc-stat-label">Deactivated</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['gender' => 'male']) }}" class="enc-stat-card" data-label="Male Users">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('gender_hash', \App\Models\User::hashFor('gender','male'))->count() }}</div>
      <div class="enc-stat-label">Male</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['gender' => 'female']) }}" class="enc-stat-card" data-label="Female Users">
    <div class="enc-stat-icon enc-stat-icon--pink">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A9.75 9.75 0 106.25 20.25M12 9v10.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('gender_hash', \App\Models\User::hashFor('gender','female'))->count() }}</div>
      <div class="enc-stat-label">Female</div>
    </div>
  </a>
</div>

{{-- Users Table --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
      All Accounts
    </div>
    <span class="enc-card__meta">{{ $users->total() }} total records</span>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.users.index') }}">
    <div class="enc-filter-bar">
      <div class="enc-filter-group">
        <span class="enc-filter-label">Search</span>
        <div class="enc-search-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
          </svg>
          <input type="text" name="search" value="{{ request('search') }}"
                 placeholder="Name, Username, LRN, Employee No..."
                 class="enc-input enc-input--search" style="width:220px;">
        </div>
      </div>
      <div class="enc-filter-divider"></div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">Role</span>
        <select name="role" class="enc-select">
          <option value="">All Roles</option>
          <option value="01" {{ request('role')=='01' ? 'selected' : '' }}>Student (R01)</option>
          <option value="02" {{ request('role')=='02' ? 'selected' : '' }}>Faculty (R02)</option>
          <option value="03" {{ request('role')=='03' ? 'selected' : '' }}>Registrar (R03)</option>
          <option value="04" {{ request('role')=='04' ? 'selected' : '' }}>Admin (R04)</option>
        </select>
      </div>
      <div class="enc-filter-divider"></div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">Status</span>
        <select name="status" class="enc-select">
          <option value="">All Status</option>
          <option value="active"      {{ request('status')=='active'      ? 'selected' : '' }}>Active</option>
          <option value="deactivated" {{ request('status')=='deactivated' ? 'selected' : '' }}>Deactivated</option>
          <option value="locked"      {{ request('status')=='locked'      ? 'selected' : '' }}>Locked</option>
        </select>
      </div>
      <div class="enc-filter-divider"></div>
      <button type="submit" class="enc-btn enc-btn--primary enc-btn--sm">Filter</button>
      @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.users.index') }}" class="enc-btn enc-btn--ghost enc-btn--sm">Clear</a>
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
            <th>Username</th>
            <th>Identifier</th>
            <th>Gender</th>
            <th>Role</th>
            <th>Status</th>
            <th>Date Added</th>
            <th>Last Login</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              {{-- Name --}}
              <td>
                <div style="font-weight:600;color:var(--navy);font-size:.82rem;">
                  {{ $user->first_name }} {{ $user->last_name }}
                </div>
                <div style="font-size:.7rem;color:var(--gray-400);">
                  {{ $user->email ?: '—' }}
                </div>
              </td>

              {{-- Username --}}
              <td class="mono">{{ $user->username }}</td>

              {{-- LRN or Employee No --}}
              <td class="mono" style="font-size:.75rem;">
                @if($user->lrn)
                  <span style="color:var(--info);">LRN: {{ $user->lrn }}</span>
                @elseif($user->employee_number)
                  <span style="color:var(--accent-blue);">EMP: {{ $user->employee_number }}</span>
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Gender --}}
              <td>
                @if($user->gender)
                  <span class="enc-badge enc-badge--{{ $user->gender === 'male' ? 'info' : 'warning' }}">
                    {{ ucfirst($user->gender) }}
                  </span>
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Role --}}
              <td>
                @php
                  $roleClass = match($user->role_id) {
                    '04' => 'danger', '03' => 'warning',
                    '02' => 'info',   '01' => 'neutral',
                    default => 'neutral'
                  };
                @endphp
                <span class="enc-badge enc-badge--{{ $roleClass }}">
                  {{ $user->role_label }}
                </span>
              </td>

              {{-- Status --}}
              <td>
                @if($user->status === 'active')
                  <span class="enc-badge enc-badge--success">Active</span>
                @elseif($user->status === 'locked')
                  <span class="enc-badge enc-badge--danger">Locked (auto)</span>
                  @if($user->locked_until)
                    <div style="font-size:.67rem;color:#b91c1c;margin-top:2px;white-space:nowrap;">
                      Failed logins · until {{ $user->locked_until->format('H:i, M d') }}
                    </div>
                  @endif
                @else
                  <span class="enc-badge enc-badge--neutral">Deactivated</span>
                @endif
                @if($user->password_reset_required)
                  <div style="display:flex;align-items:center;gap:4px;margin-top:3px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px;flex-shrink:0;color:#b45309;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                    </svg>
                    <span style="font-size:.67rem;font-weight:600;color:#b45309;white-space:nowrap;">Temp. password pending</span>
                  </div>
                @endif
              </td>

              {{-- Date Added — when the account was created, for traceability.
                   There is no created_by column on users, so WHO added the
                   account is traced through the audit log (CREATE_USER, which
                   records the acting admin). Adding created_by would need a
                   migration — deliberately out of scope here. --}}
              <td class="mono" style="font-size:.72rem;white-space:nowrap;">
                @if($user->created_at)
                  {{ $user->created_at->format('m/d/Y g:i A') }}
                @else
                  <span style="color:var(--gray-300);">—</span>
                @endif
              </td>

              {{-- Last Login — links to login history --}}
              <td style="font-size:.72rem;">
                @if($user->last_login_at)
                  <a href="{{ route('admin.users.login-history', $user) }}"
                     title="{{ $user->last_login_at->format('M d, Y H:i:s') }}"
                     style="color:var(--primary);text-decoration:none;"
                     onmouseover="this.style.textDecoration='underline'"
                     onmouseout="this.style.textDecoration='none'">
                    {{ $user->last_login_at->diffForHumans() }}
                  </a>
                @else
                  <span style="color:var(--gray-300);">Never</span>
                @endif
              </td>

              {{-- Actions --}}
              <td>
                <div style="display:flex;align-items:center;gap:6px;">
                  <a href="{{ route('admin.users.edit', $user) }}"
                     class="enc-btn enc-btn--outline enc-btn--sm" title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                    </svg>
                    Edit
                  </a>

                  <form method="POST" action="{{ route('admin.users.reset-password', $user) }}"
                        onsubmit="return confirm('Reset password for {{ $user->username }}?')">
                    @csrf
                    <button type="submit" class="enc-btn enc-btn--ghost enc-btn--sm" title="Reset Password">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                      </svg>
                      Reset PW
                    </button>
                  </form>

                  @if($user->status === 'locked')
                  {{-- Auto-locked: dedicated Unlock action, no toggle --}}
                  <form method="POST" action="{{ route('admin.users.unlock', $user) }}"
                        onsubmit="return confirm('Unlock {{ $user->username }}? This clears the failed-login lockout and resets the counter.')">
                    @csrf
                    <button type="submit" class="enc-btn enc-btn--outline enc-btn--sm" title="Unlock account"
                            style="color:#166534;border-color:#86efac;">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                      </svg>
                      Unlock
                    </button>
                  </form>
                  @else
                  @if($user->isSuperAdmin() || $user->id === auth()->id())
                    {{-- Only the super-admin is protected (so admin access can
                         never be locked out), plus your own account (so you
                         cannot deactivate yourself). Regular admins CAN be
                         deactivated — e.g. when an administrator resigns. --}}
                    <span class="enc-btn enc-btn--outline enc-btn--sm" style="opacity:.55;cursor:not-allowed;"
                          title="{{ $user->isSuperAdmin() ? 'The super administrator account cannot be deactivated' : 'You cannot deactivate your own account' }}">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Protected
                    </span>
                  @else
                  <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                        onsubmit="return confirm('Change status for {{ $user->username }}?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="enc-btn enc-btn--sm {{ $user->status === 'active' ? 'enc-btn--danger' : 'enc-btn--outline' }}"
                            title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                      @if($user->status === 'active')
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Deactivate
                      @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activate
                      @endif
                    </button>
                  </form>
                  @endif
                  @endif

                  {{-- Permanent Delete removed: accounts are deactivated, never
                       hard-deleted, to preserve audit trail and referential integrity. --}}
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9">
                <div class="enc-empty" style="padding:40px;">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;opacity:.4;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                  </svg>
                  <div class="enc-empty__title">No accounts found</div>
                  <div class="enc-empty__sub">Try adjusting your filters or create a new account</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="enc-pagination">
      <div class="enc-pagination__info">
        Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} accounts
      </div>
      <div class="enc-pagination__pages">
        {{-- First --}}
        @if($users->onFirstPage())
          <button class="enc-page-btn" disabled>First</button>
        @else
          <a href="{{ $users->url(1) }}" class="enc-page-btn">First</a>
        @endif

        {{-- Prev --}}
        @if($users->onFirstPage())
          <button class="enc-page-btn" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
          </button>
        @else
          <a href="{{ $users->previousPageUrl() }}" class="enc-page-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
          </a>
        @endif

        {{-- Page numbers --}}
        @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
          <a href="{{ $url }}" class="enc-page-btn {{ $page == $users->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        {{-- Next --}}
        @if($users->hasMorePages())
          <a href="{{ $users->nextPageUrl() }}" class="enc-page-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
          </a>
        @else
          <button class="enc-page-btn" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
          </button>
        @endif

        {{-- Last --}}
        @if($users->currentPage() >= $users->lastPage())
          <button class="enc-page-btn" disabled>Last</button>
        @else
          <a href="{{ $users->url($users->lastPage()) }}" class="enc-page-btn">Last</a>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection
