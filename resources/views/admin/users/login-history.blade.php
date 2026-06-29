{{-- resources/views/admin/users/login-history.blade.php --}}
@extends('layouts.app')

@section('title', 'Login History — ' . $user->username)
@section('breadcrumb', 'Login History')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Login History</h1>
      <p class="enc-page__subtitle">
        {{ $user->full_name }} &middot; {{ $user->username }} &middot; {{ $user->role_label }}
      </p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.users.edit', $user) }}" class="enc-btn enc-btn--outline">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Edit
      </a>
      <a href="{{ route('admin.users.index') }}" class="enc-btn enc-btn--outline">
        All Users
      </a>
    </div>
  </div>
</div>

{{-- Quick-reference last login from users table --}}
<div class="enc-card" style="margin-bottom:16px;">
  <div class="enc-card__body" style="display:flex;gap:32px;align-items:center;flex-wrap:wrap;">
    <div>
      <div style="font-size:.7rem;color:var(--gray-400);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Last Login (quick ref)</div>
      <div style="font-size:.92rem;font-weight:600;color:var(--navy);">
        {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : '—' }}
      </div>
    </div>
    <div>
      <div style="font-size:.7rem;color:var(--gray-400);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Last Login IP</div>
      <div class="mono" style="font-size:.85rem;color:var(--navy);">
        {{ $user->last_login_ip ?? '—' }}
      </div>
    </div>
    <div>
      <div style="font-size:.7rem;color:var(--gray-400);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Account Status</div>
      <div>
        @if($user->status === 'active')
          <span class="enc-badge enc-badge--success">Active</span>
        @elseif($user->status === 'locked')
          <span class="enc-badge enc-badge--danger">Locked (auto)</span>
        @else
          <span class="enc-badge enc-badge--neutral">Deactivated</span>
        @endif
      </div>
    </div>
    <div style="margin-left:auto;font-size:.72rem;color:var(--gray-300);max-width:320px;line-height:1.5;">
      The account table shows last login for quick reference.
      The complete tamper-evident history below is hash-chained — every event
      is verifiable with <code style="font-size:.68rem;">php artisan audit:verify</code>.
    </div>
  </div>
</div>

{{-- History table --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Session Events
    </div>
    <span style="font-size:.72rem;color:var(--gray-300);">
      Newest first &middot; LOGIN_SUCCESS / LOGIN_FAILED / LOGOUT / ACCOUNT_LOCKED
    </span>
  </div>

  <div class="enc-card__body" style="padding:0;">
    @if($logs->isEmpty())
      <div class="enc-empty" style="padding:40px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;margin:0 auto 10px;display:block;opacity:.35;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="enc-empty__title">No session events yet</div>
        <div class="enc-empty__sub">Events will appear here once the account has login activity.</div>
      </div>
    @else
      <table class="enc-table" style="width:100%;">
        <thead>
          <tr>
            <th style="width:160px;">Timestamp</th>
            <th style="width:130px;">Event</th>
            <th style="width:130px;">IP Address</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
          @php
            $isSuccess = $log->action_type === 'LOGIN_SUCCESS';
            $isFailed  = $log->action_type === 'LOGIN_FAILED';
            $isLocked  = $log->action_type === 'ACCOUNT_LOCKED';
            $payload   = $log->data_payload ? json_decode($log->data_payload, true) : [];
          @endphp
          <tr>
            <td class="mono" style="font-size:.72rem;color:var(--gray-500);white-space:nowrap;">
              {{ $log->created_at->format('M d, Y') }}<br>
              <span style="color:var(--gray-400);">{{ $log->created_at->format('g:i:s A') }}</span>
            </td>
            <td>
              @if($isSuccess)
                <span class="enc-badge enc-badge--success" style="font-size:.65rem;">LOGIN_SUCCESS</span>
              @elseif($isFailed)
                <span class="enc-badge enc-badge--danger" style="font-size:.65rem;">LOGIN_FAILED</span>
              @elseif($isLocked)
                <span class="enc-badge enc-badge--danger" style="font-size:.65rem;background:#7f1d1d;">ACCOUNT_LOCKED</span>
              @else
                <span class="enc-badge enc-badge--neutral" style="font-size:.65rem;">{{ $log->action_type }}</span>
              @endif
            </td>
            <td class="mono" style="font-size:.72rem;color:var(--gray-500);">
              {{ $log->source_ip ?? '—' }}
            </td>
            <td style="font-size:.75rem;color:var(--gray-500);">
              @if($isFailed && !empty($payload['reason']))
                {{ $payload['reason'] }}
                @if(!empty($payload['failed_attempts']))
                  &middot; attempt {{ $payload['failed_attempts'] }} of 5
                @endif
              @elseif($isLocked)
                Account locked after 5 consecutive failures
              @elseif($isSuccess)
                <span style="color:#166534;">Authenticated successfully</span>
              @else
                {{-- LOGOUT or other --}}
                @if(!empty($payload))
                  <span style="color:var(--gray-400);">{{ implode(' · ', array_filter(array_values($payload))) }}</span>
                @else
                  —
                @endif
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      @if($logs->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--gray-100);">
          {{ $logs->links() }}
        </div>
      @endif
    @endif
  </div>
</div>

<div style="margin-top:10px;font-size:.7rem;color:var(--gray-300);text-align:right;">
  For the full system-wide audit trail, filterable by actor, go to
  <a href="{{ route('admin.audit.index', ['actor' => $user->id]) }}" style="color:var(--primary);">
    Audit Log → filter by this user
  </a>
</div>

@endsection
