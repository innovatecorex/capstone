@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:4px 0;">

  {{-- Result count --}}
  <p style="margin:0;font-size:.78rem;color:#64748b;">
    Showing
    <strong style="color:#0f172a;font-weight:700;">{{ $paginator->firstItem() }}</strong>
    &ndash;
    <strong style="color:#0f172a;font-weight:700;">{{ $paginator->lastItem() }}</strong>
    of
    <strong style="color:#0f172a;font-weight:700;">{{ $paginator->total() }}</strong>
    results
  </p>

  {{-- Page buttons --}}
  <div style="display:flex;align-items:center;gap:4px;">

    @php
      $btnBase    = 'display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:7px;border:1px solid #e2e8f0;text-decoration:none;background:#fff;font-size:.78rem;transition:background .14s,border-color .14s,color .14s;';
      $btnActive  = $btnBase . 'color:#475569;cursor:pointer;';
      $btnDisabled= 'display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:7px;border:1px solid #e2e8f0;color:#cbd5e1;cursor:not-allowed;background:#fafafa;font-size:.78rem;';
      $hover      = "this.style.background='#f1f5f9';this.style.borderColor='#cbd5e1';this.style.color='#0f172a'";
      $hout       = "this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.color='#475569'";
    @endphp

    {{-- First --}}
    @if ($paginator->onFirstPage())
      <span style="{{ $btnDisabled }}" title="First page">First</span>
    @else
      <a href="{{ $paginator->url(1) }}" style="{{ $btnActive }}" title="First page"
         onmouseover="{{ $hover }}" onmouseout="{{ $hout }}">First</a>
    @endif

    {{-- Prev --}}
    @if ($paginator->onFirstPage())
      <span style="{{ $btnDisabled }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
      </span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="{{ $btnActive }}"
         onmouseover="{{ $hover }}" onmouseout="{{ $hout }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
      </a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;color:#94a3b8;font-size:.8rem;">&hellip;</span>
      @endif
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span aria-current="page" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;background:#1d4ed8;color:#fff;font-weight:700;font-size:.82rem;">{{ $page }}</span>
          @else
            <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;border:1px solid #e2e8f0;color:#475569;text-decoration:none;font-size:.82rem;background:#fff;"
               onmouseover="{{ $hover }}" onmouseout="{{ $hout }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="{{ $btnActive }}"
         onmouseover="{{ $hover }}" onmouseout="{{ $hout }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      </a>
    @else
      <span style="{{ $btnDisabled }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      </span>
    @endif

    {{-- Last --}}
    @if ($paginator->currentPage() >= $paginator->lastPage())
      <span style="{{ $btnDisabled }}" title="Last page">Last</span>
    @else
      <a href="{{ $paginator->url($paginator->lastPage()) }}" style="{{ $btnActive }}" title="Last page"
         onmouseover="{{ $hover }}" onmouseout="{{ $hout }}">Last</a>
    @endif

  </div>
</nav>
@endif
