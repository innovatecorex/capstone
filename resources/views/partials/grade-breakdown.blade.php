{{--
  Grade component breakdown — the single visual for "how this final grade was computed".

  Usage:  @include('partials.grade-breakdown', ['grade' => $grade])
          @include('partials.grade-breakdown', ['grade' => $grade, 'compact' => true])

  The numbers come from Grade::componentBreakdown(), which reads the SAME config
  as Grade::computeFinalGrade() — so the arithmetic displayed here always
  reconciles with the stored final_grade.
--}}
@php
  $b       = $grade->componentBreakdown();
  $items   = $grade->componentItems();   // individual activities per component (may be empty)
  $compact = $compact ?? false;
@endphp

<div class="gbd" style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#fff;">
  <table style="width:100%;border-collapse:collapse;font-size:{{ $compact ? '.74rem' : '.8rem' }};">
    <thead>
      <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
        <th style="padding:7px 10px;text-align:left;font-size:.66rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Component</th>
        <th style="padding:7px 10px;text-align:center;font-size:.66rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Score</th>
        <th style="padding:7px 10px;text-align:center;font-size:.66rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Weight</th>
        <th style="padding:7px 10px;text-align:right;font-size:.66rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Contribution</th>
      </tr>
    </thead>
    <tbody>
      @foreach($b['rows'] as $r)
      <tr style="border-bottom:1px solid #f1f5f9;">
        <td style="padding:6px 10px;color:#0f172a;">
          <strong>{{ $r['label'] }}</strong>
          @if($r['name'] !== $r['label'])
            <span style="color:#94a3b8;">· {{ $r['name'] }}</span>
          @endif
        </td>
        <td style="padding:6px 10px;text-align:center;font-family:monospace;color:{{ $r['score'] === null ? '#94a3b8' : '#0f172a' }};">
          {{ $r['score'] === null ? '—' : rtrim(rtrim(number_format($r['score'], 2), '0'), '.') }}
          @if($r['score'] !== null)<span style="color:#94a3b8;font-size:.9em;">/100</span>@endif
        </td>
        <td style="padding:6px 10px;text-align:center;font-family:monospace;color:#475569;">
          &times; {{ rtrim(rtrim(number_format($r['weight_pct'], 2), '0'), '.') }}%
        </td>
        <td style="padding:6px 10px;text-align:right;font-family:monospace;font-weight:700;color:{{ $r['contribution'] === null ? '#94a3b8' : '#0f172a' }};">
          {{ $r['contribution'] === null ? '—' : number_format($r['contribution'], 2) }}
        </td>
      </tr>

      {{-- Activities that make up THIS component: several individual scores that
           average to the component score above. Shown only when the faculty
           recorded them in the Score Calculator worksheet — so a component can
           demonstrate multiple activities rather than a single number. --}}
      @php $compItems = $items[$r['key']] ?? []; @endphp
      @if(!empty($compItems))
      <tr style="background:#fbfdff;">
        <td colspan="4" style="padding:2px 10px 9px 24px;">
          <div style="display:flex;flex-wrap:wrap;align-items:center;gap:5px;font-size:.68rem;color:#64748b;">
            <span style="font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;font-size:.62rem;">
              {{ count($compItems) }} {{ \Illuminate\Support\Str::plural('activity', count($compItems)) }}:
            </span>
            @foreach($compItems as $it)
              <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;background:#eef2f7;border:1px solid #e2e8f0;border-radius:6px;">
                {{ $it['label'] ?: 'Item '.$loop->iteration }}
                <strong style="font-family:monospace;color:#0f172a;">{{ $it['score'] === null ? '—' : rtrim(rtrim(number_format($it['score'],2),'0'),'.') }}</strong>
              </span>
            @endforeach
            @if($r['score'] !== null)
              <span style="color:#94a3b8;">&rarr; average =
                <strong style="font-family:monospace;color:#0f172a;">{{ rtrim(rtrim(number_format($r['score'],2),'0'),'.') }}</strong>
              </span>
            @endif
          </div>
        </td>
      </tr>
      @endif
      @endforeach
    </tbody>
    <tfoot>
      <tr style="background:{{ $b['is_complete'] ? '#f0fdf4' : '#fffbeb' }};border-top:2px solid {{ $b['is_complete'] ? '#86efac' : '#fcd34d' }};">
        <td colspan="3" style="padding:8px 10px;font-weight:800;color:{{ $b['is_complete'] ? '#166534' : '#92400e' }};">
          @if($b['is_complete'])
            Final Grade <span style="font-weight:600;color:#475569;">(sum of contributions)</span>
          @else
            Incomplete <span style="font-weight:600;color:#92400e;">— one or more components not yet graded</span>
          @endif
        </td>
        <td style="padding:8px 10px;text-align:right;font-family:monospace;font-size:1rem;font-weight:800;color:{{ $b['is_complete'] ? '#166534' : '#92400e' }};">
          {{ $b['is_complete'] ? number_format($b['total'], 2) : '—' }}
        </td>
      </tr>
    </tfoot>
  </table>

  {{-- The computation, written out explicitly: score × weight for every
       component, summing to the final grade. This is the line that makes the
       derivation auditable at a glance. --}}
  @if($b['is_complete'])
  <div style="padding:8px 10px;background:#f0f7ff;border-top:1px solid #dbeafe;font-size:.72rem;color:#1e3a8a;line-height:1.7;">
    <strong style="font-weight:700;">Computation:</strong>
    <span style="font-family:monospace;">
      @foreach($b['rows'] as $r){{ $r['label'] }}
        {{ rtrim(rtrim(number_format($r['score'], 2), '0'), '.') }}&times;{{ rtrim(rtrim(number_format($r['weight_pct'], 2), '0'), '.') }}%@if(!$loop->last) + @endif
      @endforeach
      = <strong>{{ number_format($b['total'], 2) }}</strong>
    </span>
  </div>
  @endif

  {{-- Policy legend — the weights that actually produced THIS grade. Older
       grades were computed under the previous component model, so the legend is
       taken from the grade itself rather than from the current config. --}}
  <div style="padding:7px 10px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:.68rem;color:#64748b;line-height:1.55;">
    Final grade = {{ $b['legend'] }}
    <span style="color:#94a3b8;">· per DepEd Order No. 8, s. 2015</span>
  </div>
</div>
