{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Account')
@section('breadcrumb', 'Edit Account')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Edit User Account</h1>
      <p class="enc-page__subtitle">Editing: {{ $user->username }} — {{ $user->role_label }}</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.users.login-history', $user) }}" class="enc-btn enc-btn--outline">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Login History
      </a>
      <a href="{{ route('admin.users.index') }}" class="enc-btn enc-btn--outline">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Users
      </a>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="enc-alert enc-alert--info" style="margin-bottom:20px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="enc-alert__body">{!! session('success') !!}</div>
  </div>
@endif

<div style="max-width:680px;">

  {{-- Edit Form --}}
  <div class="enc-card" style="margin-bottom:16px;">
    <div class="enc-card__header">
      <div class="enc-card__title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
        </svg>
        Edit Account Details
      </div>
      <span class="mono" style="font-size:.72rem;color:var(--gray-300);">ID: {{ $user->id }}</span>
    </div>

    <div class="enc-card__body">
      <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="enc-form-row">
          <div class="enc-form-group">
            <label class="enc-label">First Name <span style="color:var(--danger)">*</span></label>
            <input type="text" name="first_name"
                   value="{{ old('first_name', $user->first_name) }}"
                   class="enc-input enc-input--full {{ $errors->has('first_name') ? 'is-error' : '' }}"
                   placeholder="First name">
            @error('first_name')
              <div style="font-size:.73rem;color:var(--danger);margin-top:3px;">{{ $message }}</div>
            @enderror
          </div>
          <div class="enc-form-group">
            <label class="enc-label">Last Name <span style="color:var(--danger)">*</span></label>
            <input type="text" name="last_name"
                   value="{{ old('last_name', $user->last_name) }}"
                   class="enc-input enc-input--full {{ $errors->has('last_name') ? 'is-error' : '' }}"
                   placeholder="Last name">
            @error('last_name')
              <div style="font-size:.73rem;color:var(--danger);margin-top:3px;">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="enc-form-group" style="margin-bottom:16px;">
          <label class="enc-label">Institutional Email <span style="color:var(--danger)">*</span></label>
          <input type="email" name="email"
                 value="{{ old('email', $user->email) }}"
                 class="enc-input enc-input--full {{ $errors->has('email') ? 'is-error' : '' }}"
                 placeholder="email@pas.edu.ph">
          @error('email')
            <div style="font-size:.73rem;color:var(--danger);margin-top:3px;">{{ $message }}</div>
          @enderror
        </div>

        <div class="enc-form-group" style="margin-bottom:16px;">
          <label class="enc-label">Gender <span style="color:var(--danger)">*</span></label>
          <select name="gender" class="enc-select enc-input--full" style="height:42px;">
            <option value="">-- Select Gender --</option>
            <option value="male" {{ old('gender', $user->gender)=='male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $user->gender)=='female' ? 'selected' : '' }}>Female</option>
          </select>
          @error('gender')
            <div style="font-size:.73rem;color:var(--danger);margin-top:3px;">{{ $message }}</div>
          @enderror
        </div>

        <div class="enc-form-row">
          <div class="enc-form-group">
            <label class="enc-label">Role <span style="color:var(--danger)">*</span></label>
            <select name="role_id" class="enc-select" style="height:42px;width:100%;">
              <option value="01" {{ old('role_id', $user->role_id)=='01' ? 'selected':'' }}>01 - Student</option>
              <option value="02" {{ old('role_id', $user->role_id)=='02' ? 'selected':'' }}>02 - Faculty</option>
              <option value="03" {{ old('role_id', $user->role_id)=='03' ? 'selected':'' }}>03 - Registrar</option>
              <option value="04" {{ old('role_id', $user->role_id)=='04' ? 'selected':'' }}>04 - Admin</option>
            </select>
            @error('role_id')
              <div style="font-size:.73rem;color:var(--danger);margin-top:3px;">{{ $message }}</div>
            @enderror
          </div>
          <div class="enc-form-group">
            <label class="enc-label">Account Status <span style="color:var(--danger)">*</span></label>
            @if($user->status === 'locked')
            {{-- Locked accounts are read-only here; use the Unlock panel below --}}
            <div style="height:42px;display:flex;align-items:center;gap:8px;padding:0 .75rem;border:1px solid #fca5a5;border-radius:8px;background:#fef2f2;font-size:.85rem;font-weight:600;color:#991b1b;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
              </svg>
              Locked (auto — failed logins)
            </div>
            {{-- Pass through a safe status so the form is valid --}}
            <input type="hidden" name="status" value="active">
            @else
            <select name="status" class="enc-select" style="height:42px;width:100%;">
              <option value="active"      {{ old('status', $user->status)=='active'      ? 'selected':'' }}>Active</option>
              <option value="deactivated" {{ old('status', $user->status)=='deactivated' ? 'selected':'' }}>Deactivated</option>
            </select>
            @endif
          </div>
        </div>

        {{-- Locked-account panel: shown only when status=locked --}}
        @if($user->status === 'locked')
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.85rem 1rem;margin-top:4px;margin-bottom:4px;">
          <div style="font-size:.82rem;font-weight:700;color:#991b1b;margin-bottom:.25rem;">
            This account is auto-locked due to too many failed login attempts.
          </div>
          @if($user->locked_until)
          <div style="font-size:.75rem;color:#b91c1c;margin-bottom:.6rem;">
            Locked until: {{ $user->locked_until->format('M d, Y \a\t H:i') }}
            @if($user->locked_until->isFuture())
              ({{ $user->locked_until->diffForHumans() }})
            @else
              — will auto-unlock on next login attempt
            @endif
          </div>
          @endif
          <form method="POST" action="{{ route('admin.users.unlock', $user) }}"
                onsubmit="return confirm('Unlock {{ $user->username }}? This clears the lockout immediately.')">
            @csrf
            <button type="submit" class="enc-btn enc-btn--danger" style="font-size:.78rem;padding:.38rem .9rem;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
              </svg>
              Unlock Now
            </button>
          </form>
        </div>
        @endif

        <div style="height:1px;background:var(--gray-100);margin:20px 0 16px;"></div>

        <div style="display:flex;gap:10px;justify-content:flex-end;">
          <a href="{{ route('admin.users.index') }}" class="enc-btn enc-btn--outline">Cancel</a>
          <button type="submit" class="enc-btn enc-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Save Changes
          </button>
        </div>

      </form>
    </div>
  </div>

  {{-- Danger Zone --}}
  <div class="enc-card" style="border-color:var(--danger-border);">
    <div class="enc-card__header" style="background:var(--danger-bg);">
      <div class="enc-card__title" style="color:var(--danger);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        Danger Zone
      </div>
    </div>
    <div class="enc-card__body">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
        <div>
          <div style="font-weight:600;font-size:.85rem;color:var(--gray-700);margin-bottom:2px;">Reset Password</div>
          <div style="font-size:.75rem;color:var(--gray-400);">
            Generates a new temporary password. User must reset on next login.
            This action is logged in the audit trail.
          </div>
        </div>
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}"
              onsubmit="return confirm('Reset password for {{ $user->username }}? This cannot be undone.')">
          @csrf
          <button type="submit" class="enc-btn enc-btn--danger" style="white-space:nowrap;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
            </svg>
            Reset Password
          </button>
        </form>
      </div>
    </div>
  </div>

</div>

@endsection
