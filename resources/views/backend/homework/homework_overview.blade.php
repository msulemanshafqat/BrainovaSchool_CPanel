@extends('backend.master')
@section('title') {{ @$data['title'] }} @endsection

@push('style')
<style>
:root{--bp:#1d4ed8;--br:#dc2626;--brl:#fee2e2;--bg:#059669;--bgl:#d1fae5;--bs:#64748b;--bb:#e2e8f0;--rr:12px}
.ov-wrap{border-radius:var(--rr);overflow:hidden;border:2px solid #b8c5d6;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.ov-tbl{width:100%;border-collapse:collapse;font-size:13px}
.ov-tbl thead th{background:#eef2f9;padding:10px 12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--bs);border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap}
.ov-tbl thead th:last-child{border-right:none}
.ov-tbl tbody td{padding:11px 12px;border-bottom:1px solid #e2e8f0;border-right:1px solid #e8edf5;vertical-align:middle;background:#fff}
.ov-tbl tbody td:last-child{border-right:none}
.ov-tbl tbody tr:nth-child(even) td{background:#fafbff}
.ov-tbl tbody tr:hover td{background:#eef4ff!important}
.tbg{display:inline-block;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase}
.tbg-quiz{background:#dbeafe;color:#1e40af}.tbg-hw{background:#d1fae5;color:#065f46}
.tbg-project{background:#fce7f3;color:#9d174d}.tbg-activity{background:#ede9fe;color:#5b21b6}
.tbg-game{background:#fef3c7;color:#92400e}.tbg-assignment{background:#e0f2fe;color:#075985}
.ab{background:none;border:1.5px solid var(--bb);border-radius:6px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--bs);font-size:12px;transition:all .15s}
.ab:hover{border-color:var(--br);color:var(--br)}
/* Filter bar */

/* Filter bar — one row, equal-width dropdowns */
.filter-bar {
  background: #fff;
  border: 1px solid var(--bb);
  border-radius: var(--rr);
  padding: 12px 16px;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: nowrap;
  overflow: visible;
  position: relative;
  z-index: 50;
}

/* Each niceSelect wrapper gets equal share of the row */
.filter-bar .nice-select {
  float: none !important;
  flex: 1 1 0 !important;
  min-width: 0 !important;
  width: auto !important;
  height: 38px;
  line-height: 36px;
  font-size: 12px;
}

/* The dropdown popup list must escape the container */
/* Filter bar */
.filter-bar{background:#fff;border:1px solid var(--bb);border-radius:var(--rr);padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:nowrap;position:relative;z-index:50;overflow:visible}
.fb-select{flex:1 1 0;min-width:0;height:38px;padding:0 10px;border:1.5px solid var(--bb);border-radius:8px;font-size:12px;font-family:inherit;color:#334155;background:#fff;cursor:pointer;outline:none;transition:border-color .15s}
.fb-select:focus,.fb-select:hover{border-color:var(--bp)}
.filter-reset{font-size:12px;color:var(--bs);text-decoration:none;padding:5px 12px;border:1.5px solid var(--bb);border-radius:6px;background:#fff;transition:all .15s;white-space:nowrap;flex-shrink:0}
.filter-reset:hover{border-color:var(--br);color:var(--br)}
</style>
@endpush

@section('content')

<div class="page-content">
<input type="hidden" id="url" value="{{ url('/') }}">

{{-- BREADCRUMB --}}
<div class="page-header">
  <div class="row align-items-center">
    <div class="col-sm-8">
      <h4 class="bradecrumb-title mb-1">All Homework — Admin Overview</h4>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('homework.index') }}">Homework</a></li>
        <li class="breadcrumb-item active">Admin Overview</li>
      </ol>
    </div>
    <div class="col-sm-4 text-end">
      @if(hasPermission('homework_create'))
      <a href="{{ route('homework.create') }}" class="btn ot-btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i>New Task
      </a>
      @endif
    </div>
  </div>
</div>

{{-- SUMMARY CARDS --}}
@php
  $total   = $data['homeworks']->total();
  $byType  = $data['homeworks']->getCollection()->groupBy('task_type');
  $quizCnt = $byType->get('quiz', collect())->count();
  // Active filter indicator
  $filtered = request()->hasAny(['class','section','subject','task_type']);

@endphp
  <button type="submit" style="height:38px;padding:0 16px;background:var(--bp);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0">
  <i class="fa-solid fa-magnifying-glass me-1"></i>Apply
</button>



<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div style="background:#fff;border-radius:var(--rr);border:1px solid var(--bb);padding:16px 18px;display:flex;align-items:center;gap:12px">
      <div style="width:42px;height:42px;border-radius:9px;background:#dbeafe;color:var(--bp);display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0">
        <i class="fa-solid fa-layer-group"></i>
      </div>
      <div>
        <div style="font-size:24px;font-weight:800;color:#0f172a">{{ $total }}</div>
        <div style="font-size:11px;color:var(--bs);font-weight:600;text-transform:uppercase">
          {{ $filtered ? 'Filtered Results' : 'Total Tasks' }}
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div style="background:#fff;border-radius:var(--rr);border:1px solid var(--bb);padding:16px 18px;display:flex;align-items:center;gap:12px">
      <div style="width:42px;height:42px;border-radius:9px;background:#fef3c7;color:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0">
        <i class="fa-solid fa-circle-question"></i>
      </div>
      <div>
        <div style="font-size:24px;font-weight:800;color:#0f172a">{{ $quizCnt }}</div>
        <div style="font-size:11px;color:var(--bs);font-weight:600;text-transform:uppercase">Quizzes</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div style="background:#fff;border-radius:var(--rr);border:1px solid var(--bb);padding:16px 18px;display:flex;align-items:center;gap:12px">
      <div style="width:42px;height:42px;border-radius:9px;background:#d1fae5;color:var(--bg);display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0">
        <i class="fa-solid fa-file-lines"></i>
      </div>
      <div>
        <div style="font-size:24px;font-weight:800;color:#0f172a">{{ $data['homeworks']->lastPage() }}</div>
        <div style="font-size:11px;color:var(--bs);font-weight:600;text-transform:uppercase">Total Pages</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div style="background:#fff;border-radius:var(--rr);border:1px solid var(--bb);padding:16px 18px;display:flex;align-items:center;gap:12px">
      <div style="width:42px;height:42px;border-radius:9px;background:#fee2e2;color:var(--br);display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0">
        <i class="fa-solid fa-trash-can"></i>
      </div>
      <div>
        <div style="font-size:24px;font-weight:800;color:#0f172a">20</div>
        <div style="font-size:11px;color:var(--bs);font-weight:600;text-transform:uppercase">Per Page</div>
      </div>
    </div>
  </div>
</div>

{{-- ─────────────────────────────────────────────────────────────────────────
     FILTER BAR
     GET form — filters persist through pagination via withQueryString().
     Class → Section AJAX chain uses the same LMS custom.js handlers.
     Subject is a dependent AJAX dropdown populated after section changes.
────────────────────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('homework.question-list') }}" id="ov-filter">
  <div class="filter-bar">

    {{-- Filter icon label --}}
    <span style="font-size:12px;font-weight:700;color:var(--bs);white-space:nowrap">
      <i class="fa-solid fa-filter me-1"></i> Filter by:
    </span>

    {{-- CLASS --}}
    
   {{-- <select name="class" id="getSections"
            class="nice-select niceSelect sections bordered_style"
            style="min-width:140px;font-size:13px"--}}
    <select name="class" id="getSections"
        class="fb-select"        
            
            
          onchange="ovFilterSubmit()"
      <option value="">All Classes</option>
      @foreach($data['classes'] ?? [] as $item)
        @if(!empty($item->class))
        <option value="{{ $item->class->id }}"
          {{ request('class') == $item->class->id ? 'selected' : '' }}>
          {{ $item->class->name }}
        </option>
        @endif
      @endforeach
    </select>

    {{-- SECTION (AJAX populated by LMS custom.js when class changes) --}}
    
    
    {{-- <select name="section" id="getSubjects"
            class="nice-select niceSelect sections bordered_style"
            style="min-width:130px;font-size:13px"--}}
            
<select name="section" id="getSubjects"
        class="fb-select sections">
      <option value="">All Sections</option>
    </select>

    {{-- SUBJECT (AJAX populated after section) --}}
    {{--  <select name="subject" id="subject"
            class="nice-select niceSelect subjects bordered_style"
            style="min-width:140px;font-size:13px"--}}
            
<select name="subject" id="subject"
        class="fb-select subjects"
        onchange="if(event.isTrusted) ovFilterSubmit()">
      <option value="">All Subjects</option>
    </select>

    {{-- TASK TYPE — static dropdown, no AJAX needed --}}
  {{--  <select name="task_type"
            class="nice-select niceSelect bordered_style"
            style="min-width:130px;font-size:13px"--}}
            
            
       <select name="task_type"
        class="fb-select"     
            
            
            onchange="document.getElementById('ov-filter').submit()">
      <option value="">All Types</option>
      <option value="homework"   {{ request('task_type') === 'homework'   ? 'selected' : '' }}>Standard Homework</option>
      <option value="quiz"       {{ request('task_type') === 'quiz'       ? 'selected' : '' }}>Quiz</option>
      <option value="project"    {{ request('task_type') === 'project'    ? 'selected' : '' }}>Project</option>
      <option value="activity"   {{ request('task_type') === 'activity'   ? 'selected' : '' }}>Activity</option>
      <option value="game"       {{ request('task_type') === 'game'       ? 'selected' : '' }}>Game</option>
      <option value="assignment" {{ request('task_type') === 'assignment' ? 'selected' : '' }}>Assignment</option>
    </select>

    {{-- Reset link — only shown when a filter is active --}}
    @if($filtered)
    <a href="{{ route('homework.question-list') }}" class="filter-reset ms-auto">
      <i class="fa-solid fa-xmark me-1"></i>Clear Filters
    </a>
    @endif

  </div>
</form>

{{-- ─────────────────────────────────────────────────────────────────────────
     MAIN TABLE
     Columns: # | Title | Due Date | Marks | Status | Delete
     Removed: Class/Section, Subject, Type (all encoded in the auto-generated title)
     Removed: Date (assignment date — redundant with submission date)
────────────────────────────────────────────────────────────────────────── --}}
<div class="ov-wrap">
  <div style="background:#fff;padding:12px 16px;border-bottom:1px solid var(--bb);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <h5 style="margin:0;font-size:14px;font-weight:700;color:#0f172a">
      <i class="fa-solid fa-list-check me-2 text-primary"></i>
      All Homework &amp; Tasks
      @if($filtered)
        <span style="font-size:11px;font-weight:600;color:var(--bp);margin-left:8px;background:#dbeafe;padding:2px 8px;border-radius:20px">
          Filtered
        </span>
      @endif
    </h5>
    <span style="font-size:12px;color:var(--bs)">
      @if($data['homeworks']->total() > 0)
        Showing {{ $data['homeworks']->firstItem() }}–{{ $data['homeworks']->lastItem() }}
        of {{ $data['homeworks']->total() }} records
      @else
        No records found
      @endif
    </span>
  </div>

  <div class="table-responsive">
    <table class="ov-tbl">
      <thead>
        <tr>
          <th style="width:36px">#</th>
          <th>Title</th>
          <th style="width:100px">Due Date</th>
          <th style="width:60px;text-align:center">Marks</th>
          <th style="width:70px">Status</th>
          @if(hasPermission('homework_delete'))
          <th style="width:44px;text-align:center">Del.</th>
          @endif
        </tr>
      </thead>
      <tbody>

      @forelse($data['homeworks'] as $row)
      @php
        $typeMap   = ['homework'=>'hw','quiz'=>'quiz','project'=>'project','activity'=>'activity','game'=>'game','assignment'=>'assignment'];
        $typeKey   = $typeMap[$row->task_type ?? 'homework'] ?? 'hw';
        $isOverdue = $row->submission_date && \Carbon\Carbon::parse($row->submission_date)->isPast();
        $bg        = $loop->even ? '#fafbff' : '#fff';
      @endphp
      <tr style="background:{{$bg}}"
          onmouseover="this.style.background='#eef4ff'"
          onmouseout="this.style.background='{{$bg}}'">

        {{-- Row number — page-aware --}}
        <td style="font-size:11px;color:#94a3b8;text-align:center;font-weight:600">
          {{ $data['homeworks']->firstItem() + $loop->index }}
        </td>

        {{-- Title + type badge + topic tag
             Title encodes: topic_class_subject_type_date — all info in one string.
             Tooltip shows full title on hover for long values. --}}
        <td>
  <div style="font-weight:600;font-size:13px;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:340px"
       title="{{ $row->title ?? '' }}">
    {{ $row->title ?? '—' }}
  </div>
</td>

        {{-- Due Date --}}
        <td style="white-space:nowrap;font-size:12.5px;color:#334155">
          {{ $row->submission_date ?? '—' }}
          @if($isOverdue)
          <br><span style="font-size:10px;background:#fee2e2;color:#dc2626;border-radius:4px;padding:1px 5px;font-weight:700">Late</span>
          @endif
        </td>

        {{-- Marks --}}
        <td style="font-weight:700;font-size:14px;color:#1d4ed8;text-align:center">
          {{ $row->marks ?? '—' }}
        </td>

        {{-- Status --}}
        <td>
          @if($row->status == \App\Enums\Status::ACTIVE)
            <span class="badge-basic-success-text">Active</span>
          @else
            <span class="badge-basic-danger-text">Inactive</span>
          @endif
        </td>

        {{-- Delete — admin only --}}
        @if(hasPermission('homework_delete'))
        <td style="text-align:center">
          <button class="ab" title="Delete this homework record"
                  onclick="delete_row('homework/delete', {{ $row->id }})">
            <i class="fa-solid fa-trash-can" style="color:#dc2626;font-size:11px"></i>
          </button>
        </td>
        @endif

      </tr>
      @empty
      <tr>
        <td colspan="6" style="text-align:center;padding:48px 20px;color:#94a3b8">
          <i class="fa-solid fa-magnifying-glass" style="font-size:2rem;opacity:.2;display:block;margin-bottom:10px"></i>
          @if($filtered)
            No homework found matching the selected filters.
            <a href="{{ route('homework.question-list') }}" style="display:block;margin-top:8px;font-size:12px;color:var(--bp)">
              Clear filters to see all records
            </a>
          @else
            No homework records found.
          @endif
        </td>
      </tr>
      @endforelse

      </tbody>
    </table>
  </div>

  {{-- Pagination — withQueryString() keeps filter params across pages --}}
  <div style="padding:12px 16px;border-top:1px solid var(--bb);display:flex;justify-content:flex-end">
    {!! $data['homeworks']->appends(request()->query())->links() !!}
  </div>

</div>

</div>
@endsection

@push('script')
  @include('backend.partials.delete-ajax')

<script>
$(document).ready(function () {
  var oldClass   = "{{ request('class') }}";
  var oldSection = "{{ request('section') }}";
  var oldSubject = "{{ request('subject') }}";

  // This flag tells the onchange handlers NOT to submit
  // while we are restoring the dropdowns programmatically
  window.ovRestoringFilters = true;

  if (oldClass) {
    setTimeout(function () {
      $('#getSections').val(oldClass).trigger('change');
      setTimeout(function () {
        if (oldSection) {
          $('#getSubjects').val(oldSection).trigger('change');
          setTimeout(function () {
            if (oldSubject) {
              $('#subject').val(oldSubject).trigger('change');
            }
            // All dropdowns restored — now allow normal submit on change
            window.ovRestoringFilters = false;
          }, 700);
        } else {
          window.ovRestoringFilters = false;
        }
      }, 700);
    }, 300);
  } else {
    // No class selected — nothing to restore, allow submit immediately
    window.ovRestoringFilters = false;
  }
});

// Shared submit function — all four selects call this
// It does nothing while the flag is true (during restore)
function ovFilterSubmit() {
  if (!window.ovRestoringFilters) {
    document.getElementById('ov-filter').submit();
  }
}
</script>
@endpush
