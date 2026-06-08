@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:4px 0;">
  @if ($paginator->onFirstPage())
    <span style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;border:1px solid #e2e8f0;color:#cbd5e1;font-size:.82rem;cursor:not-allowed;background:#fafafa;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
      Previous
    </span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;border:1px solid #e2e8f0;color:#475569;font-size:.82rem;font-weight:600;text-decoration:none;background:#fff;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
      Previous
    </a>
  @endif

  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;border:1px solid #e2e8f0;color:#475569;font-size:.82rem;font-weight:600;text-decoration:none;background:#fff;">
      Next
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>
  @else
    <span style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;border:1px solid #e2e8f0;color:#cbd5e1;font-size:.82rem;cursor:not-allowed;background:#fafafa;">
      Next
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </span>
  @endif
</nav>
@endif
