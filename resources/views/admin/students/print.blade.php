<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student List — Phil. Academy of Sakya</title>
  <style>
    /* ── Screen styles ── */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; color: #0f172a; padding: 24px; }

    .screen-actions {
      display: flex; gap: 10px; justify-content: flex-end; margin-bottom: 20px;
    }
    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: .5rem 1.1rem; border-radius: 8px; font-size: .82rem;
      font-weight: 600; cursor: pointer; text-decoration: none; border: none;
    }
    .btn-primary { background: #1d4ed8; color: #fff; }
    .btn-outline  { background: #fff; color: #374151; border: 1px solid #d1d5db; }

    .enc-card {
      background: #fff; border-radius: 14px;
      border: 1px solid #e2e8f0; overflow: hidden;
      box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }

    .school-header {
      background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
      color: #fff; padding: 24px 28px;
    }
    .school-name  { font-size: 1.15rem; font-weight: 800; letter-spacing: .02em; }
    .school-sub   { font-size: .75rem; color: #94a3b8; margin-top: 2px; }
    .report-title { font-size: 1rem; font-weight: 700; margin-top: 14px; }
    .report-meta  {
      display: flex; flex-wrap: wrap; gap: 20px;
      font-size: .73rem; color: #94a3b8; margin-top: 8px;
    }
    .report-meta span { color: #e2e8f0; }

    .filter-bar {
      padding: 10px 20px; background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      font-size: .73rem; color: #64748b;
    }

    table { width: 100%; border-collapse: collapse; font-size: .78rem; }
    thead th {
      background: #f1f5f9; color: #475569; font-weight: 700;
      font-size: .68rem; text-transform: uppercase; letter-spacing: .05em;
      padding: 8px 12px; text-align: left; border-bottom: 2px solid #e2e8f0;
    }
    tbody td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:nth-child(even) { background: #f8fafc; }

    .badge-m { background: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 99px; font-size: .68rem; font-weight: 700; }
    .badge-f { background: #fce7f3; color: #be185d; padding: 2px 8px; border-radius: 99px; font-size: .68rem; font-weight: 700; }

    .footer { padding: 12px 20px; font-size: .7rem; color: #94a3b8; border-top: 1px solid #e2e8f0; text-align: right; }

    /* ── Print overrides ── */
    @media print {
      body { background: #fff; padding: 0; }
      .screen-actions { display: none !important; }
      .enc-card { border: none; box-shadow: none; border-radius: 0; }
      table { font-size: .72rem; }
      thead th { background: #f1f5f9 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      tbody tr:nth-child(even) { background: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .school-header { background: #0f172a !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      @page { size: A4 landscape; margin: 15mm 12mm; }
    }
  </style>
</head>
<body>

  {{-- Screen-only action bar --}}
  <div class="screen-actions">
    <a href="{{ route('admin.students.index', request()->only(['search','gender','address'])) }}" class="btn btn-outline">
      ← Back to List
    </a>
    <button onclick="window.print()" class="btn btn-primary">
      🖨 Print / Save as PDF
    </button>
  </div>

  <div class="enc-card">

    {{-- School header --}}
    <div class="school-header">
      <div class="school-name">Phil. Academy of Sakya</div>
      <div class="school-sub">Student Affairs — Registrar's Office</div>
      <div class="report-title">Student Master List</div>
      <div class="report-meta">
        <div>Generated: <span>{{ now()->format('F d, Y g:i A') }}</span></div>
        <div>Total Records: <span>{{ $students->count() }}</span></div>
        @if($filters['gender'])
          <div>Gender: <span>{{ ucfirst($filters['gender']) }}</span></div>
        @endif
        @if($filters['search'])
          <div>Search: <span>{{ $filters['search'] }}</span></div>
        @endif
        @if($filters['address'])
          <div>Address: <span>{{ $filters['address'] }}</span></div>
        @endif
      </div>
    </div>

    {{-- Table --}}
    <table>
      <thead>
        <tr>
          <th style="width:36px;">#</th>
          <th>LRN</th>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Grade Level</th>
          <th>Section</th>
          <th>Gender</th>
          <th>Username</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $i => $s)
        <tr>
          <td style="color:#94a3b8;font-size:.7rem;">{{ $i + 1 }}</td>
          <td style="font-family:monospace;font-size:.72rem;color:#1d4ed8;">{{ $s->lrn ?? '—' }}</td>
          <td style="font-weight:600;">{{ $s->last_name }}</td>
          <td>{{ $s->first_name }}</td>
          <td>{{ $s->grade_level ?? '—' }}</td>
          <td>{{ $s->section?->section_name ?? '—' }}</td>
          <td>
            @if($s->gender === 'male')
              <span class="badge-m">Male</span>
            @elseif($s->gender === 'female')
              <span class="badge-f">Female</span>
            @else
              <span style="color:#94a3b8;">—</span>
            @endif
          </td>
          <td style="font-family:monospace;font-size:.72rem;color:#64748b;">{{ $s->username }}</td>
          <td style="font-size:.72rem;">{{ ucfirst($s->status) }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="9" style="text-align:center;padding:30px;color:#94a3b8;">No students match the current filters.</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="footer">
      Phil. Academy of Sakya &mdash; Confidential &mdash; Generated {{ now()->format('Y-m-d H:i:s') }} PHT
    </div>

  </div>

</body>
</html>
