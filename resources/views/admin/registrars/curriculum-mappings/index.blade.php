@extends('layouts.admin')

@section('title', 'Curriculum Mapping')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Curriculum Mapping</h1>
        <p class="text-gray-600 mt-1">Define subjects required for each grade level</p>
    </div>
    <a href="{{ route('admin.curriculum-mappings.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
        + Add Mapping
    </a>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <form method="GET" action="{{ route('admin.curriculum-mappings.index') }}" class="flex gap-4 flex-wrap">
        <select name="academic_year_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
            <option value="">All Academic Years</option>
            @foreach($academicYears as $year)
            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                {{ $year->year_label }}
            </option>
            @endforeach
        </select>
        <select name="grade_level" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
            <option value="">All Grade Levels</option>
            @foreach($gradeLevels as $level)
            <option value="{{ $level }}" {{ request('grade_level') === $level ? 'selected' : '' }}>
                {{ $level }}
            </option>
            @endforeach
        </select>
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
            <option value="">All Statuses</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-lg transition">
            Filter
        </button>
    </form>
</div>

<!-- Bulk-action toolbar (hidden until ≥1 row is checked) -->
<div id="bulk-toolbar" class="hidden mb-4 flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
    <span id="bulk-count" class="text-sm font-semibold text-blue-800">0 selected</span>
    <form id="bulk-form" method="POST" action="{{ route('admin.curriculum-mappings.bulk-action') }}">
        @csrf
        <input type="hidden" name="action" id="bulk-action-input">
        <div id="bulk-ids-container"></div>
        <div class="flex items-center gap-2">
            <select id="bulk-action-select" class="px-3 py-1.5 border border-blue-300 rounded text-sm bg-white text-gray-700 focus:ring-blue-500">
                <option value="">-- Bulk Action --</option>
                <option value="activate">Activate Selected</option>
                <option value="deactivate">Deactivate Selected</option>
                <option value="delete">Delete Selected</option>
            </select>
            <button type="button" onclick="applyBulkAction()"
                    class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition">
                Apply
            </button>
        </div>
    </form>
</div>

<!-- Curriculum Mappings Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($mappings->count() > 0)
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 w-10">
                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 cursor-pointer"
                           title="Select all">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Academic Year</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Grade Level</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Prerequisite</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($mappings as $mapping)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-4">
                    <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 cursor-pointer"
                           value="{{ $mapping->id }}">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="font-semibold text-gray-900">{{ $mapping->academicYear->year_label }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $mapping->grade_level }}
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm">
                        <span class="font-semibold text-gray-900">{{ $mapping->subject->subject_code }}</span>
                        <p class="text-gray-600">{{ $mapping->subject->subject_name }}</p>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm">
                    @if($mapping->prerequisiteSubject)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">
                            {{ $mapping->prerequisiteSubject->subject_code }}
                        </span>
                        <div class="text-xs text-gray-500 mt-1">min {{ $mapping->prerequisite_min_grade }}</div>
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $mapping->is_required ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $mapping->is_required ? 'Required' : 'Elective' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $mapping->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($mapping->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.curriculum-mappings.edit', $mapping) }}"
                       class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form method="POST" action="{{ route('admin.curriculum-mappings.destroy', $mapping) }}"
                          class="inline-block ml-3"
                          data-confirm="Delete this curriculum mapping? This cannot be undone." data-confirm-type="danger" data-confirm-title="Delete Mapping" data-confirm-ok="Delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="p-6 text-center text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
        <p>No curriculum mappings found</p>
    </div>
    @endif
</div>

<script>
(function () {
    const selectAll = document.getElementById('select-all');
    const toolbar   = document.getElementById('bulk-toolbar');
    const countEl   = document.getElementById('bulk-count');

    function getChecked() {
        return Array.from(document.querySelectorAll('.row-checkbox:checked'));
    }

    function syncToolbar() {
        const checked = getChecked();
        const all     = document.querySelectorAll('.row-checkbox');
        toolbar.classList.toggle('hidden', checked.length === 0);
        if (checked.length > 0) {
            countEl.textContent = checked.length + ' selected';
        }
        if (selectAll) {
            selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
            selectAll.checked       = all.length > 0 && checked.length === all.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = selectAll.checked; });
            syncToolbar();
        });
    }

    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', syncToolbar);
    });

    window.applyBulkAction = function () {
        const action  = document.getElementById('bulk-action-select').value;
        const checked = getChecked();

        if (!action)          { alert('Please select a bulk action.'); return; }
        if (!checked.length)  { alert('No rows selected.');           return; }

        if (action === 'delete' &&
            !confirm('Delete ' + checked.length + ' mapping(s)? This cannot be undone.')) {
            return;
        }

        document.getElementById('bulk-action-input').value = action;

        const container = document.getElementById('bulk-ids-container');
        container.innerHTML = '';
        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        document.getElementById('bulk-form').submit();
    };
})();
</script>

<!-- Pagination -->
@if($mappings->hasPages())
<div class="mt-6">
    {{ $mappings->links('pagination::tailwind') }}
</div>
@endif
@endsection
