{{-- ============================================================
     TEMPLATE INHERITANCE
     Extends the main backend layout (master.blade.php).
     Sets the browser tab title dynamically from $data['title'].
     ============================================================ --}}
@extends('backend.master')
@section('title') {{ @$data['title'] }} @endsection

{{-- ============================================================
     PAGE-SPECIFIC CSS (pushed into the <head> via @stack('style'))
     Everything below is scoped to this page only.
     ============================================================ --}}
@push('style')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>

/* START: Accordion Collapse Fix */
.sect-body {
    overflow: hidden !important;
    transition: max-height 0.3s ease-in-out;
}
/* END: Accordion Collapse Fix */

.hw-portal{font-family:'Plus Jakarta Sans',sans-serif}

:root{
  --bp:#1d4ed8;--bpl:#dbeafe;--ba:#f59e0b;--bal:#fef3c7;
  --bg:#059669;--bgl:#d1fae5;--br:#dc2626;--brl:#fee2e2;
  --bs:#64748b;--bb:#e2e8f0;--bt:#0f172a;--rr:12px;
  --tb-outer: #c7d2e0;
  --tb-head:  #b8c5d6;
  --tb-row:   #e2e8f0;
  --tb-col:   #e8edf5;
}

/* Stat cards */
.sc{background:#fff;border-radius:var(--rr);border:1px solid var(--bb);padding:18px 20px;display:flex;align-items:center;gap:14px;transition:transform .15s,box-shadow .15s}
.sc:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.07)}
.si{width:46px;height:46px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:19px}
.sv{font-size:26px;font-weight:800;line-height:1;color:var(--bt)}
.sl{font-size:11px;color:var(--bs);font-weight:600;margin-top:2px;text-transform:uppercase;letter-spacing:.04em}

/* Chart cards */
.cc{background:#fff;border:1px solid var(--bb);border-radius:var(--rr);padding:20px}
.cc h6{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--bs);font-weight:700;margin-bottom:14px}

/* Filter bar */
.hw-filter-bar{background:#f8fafc;padding:15px;border-radius:10px;border:1px solid var(--bb);margin-bottom:20px}
.hw-filter-bar .nice-select{width:100%!important;min-width:0!important;box-sizing:border-box;font-size:12px!important}
.hw-filter-bar .nice-select .list{width:100%!important}

/* Dividers */
.section-divider{height:1px;background:linear-gradient(90deg,#e2e8f0 0%,#e2e8f0 100%);margin:24px 0;opacity:0.5}

/* Results container (hidden by default) */
#results-container{display:none}

/* Table wrapper */
.hw-wrap{
  border-radius:var(--rr);
  overflow:hidden;
  border:2px solid var(--tb-outer);
  background:#fff;
  margin-bottom:0;
  box-shadow:0 1px 4px rgba(0,0,0,.06);
}

/* Table base */
.ht{
  width:100%;
  border-collapse:collapse;
  font-size:13.5px;
  table-layout:auto;
}

/* Header row */
.ht thead th{
  background:#f0f4fa;
  padding:11px 13px;
  font-size:10.5px;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:.07em;
  color:var(--bs);
  border-bottom:2px solid var(--tb-head);
  border-right:1px solid var(--tb-col);
  white-space:nowrap;
}
.ht thead th:last-child{border-right:none}

/* Data cells */
.ht tbody td{
  padding:11px 13px;
  border-bottom:1px solid var(--tb-row);
  border-right:1px solid var(--tb-col);
  vertical-align:middle;
  background:#fff;
}
.ht tbody td:last-child{border-right:none}

/* Row alternating & hover */
.ht tbody tr:nth-child(even) td{background:#fafbff}
.ht tbody tr:last-child td{border-bottom:none}
.ht tbody tr:hover td{background:#eff4ff!important}

.ht tr.hh{display:none!important}

/* Type badges */
.tbg{display:inline-block;padding:2px 9px;border-radius:20px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
.tbg-quiz      {background:#dbeafe;color:#1e40af}
.tbg-hw        {background:#d1fae5;color:#065f46}
.tbg-project   {background:#fce7f3;color:#9d174d}
.tbg-activity  {background:#ede9fe;color:#5b21b6}
.tbg-game      {background:#fef3c7;color:#92400e}
.tbg-assignment{background:#e0f2fe;color:#075985}

.od{background:#fee2e2;color:#dc2626;border-radius:4px;padding:1px 6px;font-size:10.5px;font-weight:700}

/* Action button */
.ab{background:none;border:1.5px solid var(--bb);border-radius:6px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--bs);font-size:12px;transition:all .15s}
.ab:hover{border-color:var(--bp);color:var(--bp);background:var(--bpl)}

</style>
@endpush

{{-- ============================================================
     MAIN PAGE CONTENT
     ============================================================ --}}
@section('content')
<div class="page-content hw-portal">

<input type="hidden" id="url" value="{{ url('/') }}">

{{-- ── Page Header ──
     Left side: page title + breadcrumb trail.
--}}
<div class="page-header">
  <div class="row align-items-center">
    <div class="col-sm-6">
      <h4 class="bradecrumb-title mb-1">Homework &amp; Tasks</h4>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Homework &amp; Tasks</li>
      </ol>
    </div>
  </div>
</div>

{{-- ===== SECTION 1: GLOBAL STATS CARDS ===== --}}
<div class="row g-3 mb-4" id="stats-cards-container">
  <div class="col-md-6 col-lg-3">
    <div class="sc">
      <div class="si" style="background:#dbeafe"><i class="fa-solid fa-book" style="color:#1d4ed8"></i></div>
      <div style="flex:1">
        <div class="sv" id="stat-total-tasks">—</div>
        <div class="sl">Total Tasks Assigned</div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="sc">
      <div class="si" style="background:#d1fae5"><i class="fa-solid fa-check-circle" style="color:#059669"></i></div>
      <div style="flex:1">
        <div class="sv" id="stat-submitted">—</div>
        <div class="sl">Total Submitted</div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="sc">
      <div class="si" style="background:#fef3c7"><i class="fa-solid fa-hourglass-end" style="color:#f59e0b"></i></div>
      <div style="flex:1">
        <div class="sv" id="stat-pending">—</div>
        <div class="sl">Pending Evaluations</div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="sc">
      <div class="si" style="background:#fee2e2"><i class="fa-solid fa-star" style="color:#dc2626"></i></div>
      <div style="flex:1">
        <div class="sv" id="stat-score">—</div>
        <div class="sl">E6 Points (Total)</div>
      </div>
    </div>
  </div>
</div>

{{-- Section divider --}}
<div class="section-divider"></div>

{{-- ===== SECTION 2: FILTER BAR ===== --}}
<div class="hw-filter-bar">
  <div class="row g-2">
    <div class="col-md-3">
      <label class="form-label small fw-700 mb-2">Class</label>
      <select id="filter-class" class="nice-select niceSelect bordered_style" style="width:100%">
        <option value="">All Classes</option>
        @foreach($data['classes']??[] as $item)
          @if(!empty($item->class))
          <option value="{{$item->class->id}}">{{$item->class->name}}</option>
          @endif
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small fw-700 mb-2">Section</label>
      <select id="filter-section" class="nice-select niceSelect bordered_style" style="width:100%">
        <option value="">All Sections</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small fw-700 mb-2">Subject</label>
      <select id="filter-subject" class="nice-select niceSelect bordered_style" style="width:100%">
        <option value="">All Subjects</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small fw-700 mb-2">Task Type</label>
      <select id="filter-task-type" class="nice-select niceSelect bordered_style" style="width:100%">
        <option value="all">All Types</option>
        <option value="quiz">Quiz</option>
        <option value="homework">Homework</option>
        <option value="project">Project</option>
        <option value="activity">Activity</option>
        <option value="game">Game</option>
        <option value="assignment">Assignment</option>
      </select>
    </div>
  </div>
  <div class="row g-2 mt-2">
    <div class="col-12 d-flex gap-2">
      <button type="button" class="btn ot-btn-primary" id="proceed-btn">
        <i class="fa-solid fa-arrow-right me-1"></i>Proceed
      </button>
      <button type="button" class="btn btn-outline-secondary" id="reset-filters-btn">
        <i class="fa-solid fa-rotate-left me-1"></i>Reset
      </button>
    </div>
  </div>
</div>

{{-- Section divider --}}
<div class="section-divider"></div>

{{-- ===== SECTION 4: RESULTS AREA (Initially Hidden) ===== --}}
<div id="results-container" style="display:none">
  {{-- Charts Row --}}
  <div class="row g-3 mb-4">
    {{-- Donut Chart: Overall Task Status --}}
    <div class="col-lg-6">
      <div class="cc">
        <h6><i class="fa-solid fa-chart-pie me-1"></i>Overall Task Status</h6>
        <div style="font-size:10px;color:#94a3b8;margin-bottom:10px;margin-top:-8px">Submitted vs Pending vs Overdue</div>
        <canvas id="donut-chart-filtered" height="200"></canvas>
      </div>
    </div>

    {{-- Line Chart: Score Trend --}}
    <div class="col-lg-6">
      <div class="cc">
        <h6><i class="fa-solid fa-chart-line me-1"></i>Score Trend</h6>
        <div style="font-size:10px;color:#94a3b8;margin-bottom:10px;margin-top:-8px">Average score per task over time</div>
        <canvas id="line-chart-filtered" height="200"></canvas>
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="cc" style="padding:0;border:none">
    <div class="hw-wrap">
      <table class="ht">
        <thead>
          <tr>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap;width:32px">#</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0">Title</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap">Class / Section</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap">Subject</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap">Type</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap">Due Date</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap;width:55px">Marks</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;white-space:nowrap;width:44px">Act.</th>
          </tr>
        </thead>
        <tbody id="filtered-table-body">
          <tr><td colspan="8" style="padding:20px;text-align:center;color:#94a3b8">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

</div>{{-- /page-content hw-portal --}}

{{-- ============================================================
     MODALS
     ============================================================ --}}

{{-- ── Evaluation Modal (#mEv) ── --}}
<div class="modal fade" id="mEv" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-header modal-header-image"><h5 class="modal-title">Evaluation</h5><button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center" data-bs-dismiss="modal"><i class="fa fa-times text-white"></i></button></div>
    <form action="{{ route('homework.evaluation.submit') }}" method="post">@csrf<input type="hidden" name="homework_id" id="hw_id">
      <div class="modal-body p-4" id="ev-body"><div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fs-3 text-primary"></i></div></div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn ot-btn-primary"><i class="fa-solid fa-check me-1"></i>Save Marks</button></div>
    </form>
  </div></div>
</div>

{{-- ── Quiz Questions Modal (#mQ) ── --}}
<div class="modal fade" id="mQ" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl"></div></div>

@endsection

{{-- ============================================================
     PAGE-SPECIFIC JAVASCRIPT
     ============================================================ --}}
@push('script')

@include('backend.partials.delete-ajax')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>

{{-- Global chart instances to manage lifecycle --}}
let donutChartInstance = null;
let lineChartInstance = null;

$(document).ready(function(){

  {{-- Load global stats on page load --}}
  loadGlobalStats();

  {{-- Setup dependent dropdown change handlers --}}
  $('#filter-class').on('change', function(){
    const classId = $(this).val();
    if (!classId) {
      $('#filter-section').empty().append('<option value="">All Sections</option>').niceSelect('update');
      $('#filter-subject').empty().append('<option value="">All Subjects</option>').niceSelect('update');
      return;
    }
    loadSectionsByClass(classId);
  });

  $('#filter-section').on('change', function(){
    const classId = $('#filter-class').val();
    const sectionId = $(this).val();
    if (!classId || !sectionId) {
      $('#filter-subject').empty().append('<option value="">All Subjects</option>').niceSelect('update');
      return;
    }
    loadSubjectsBySection(classId, sectionId);
  });

  {{-- Proceed button --}}
  $('#proceed-btn').on('click', function(){
    getFilteredReport();
  });

  {{-- Reset button --}}
  $('#reset-filters-btn').on('click', function(){
    $('#filter-class').val('').niceSelect('update');
    $('#filter-section').empty().append('<option value="">All Sections</option>').niceSelect('update');
    $('#filter-subject').empty().append('<option value="">All Subjects</option>').niceSelect('update');
    $('#filter-task-type').val('all').niceSelect('update');
    $('#results-container').fadeOut(300);
  });

});

{{-- Load global stats --}}
function loadGlobalStats() {
  $.ajax({
    url: '{{ route("homework.ajax.global-stats") }}',
    method: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.success && response.data) {
        const stats = response.data;
        $('#stat-total-tasks').text(stats.total_tasks_assigned);
        $('#stat-submitted').text(stats.total_submitted);
        $('#stat-pending').text(stats.pending_evaluations);
        $('#stat-score').text(stats.cumulative_score_e6);
      }
    },
    error: function() {
      console.error('Failed to load global stats');
    }
  });
}

{{-- Load sections for selected class --}}
function loadSectionsByClass(classId) {
  $.ajax({
    url: '{{ route("homework.ajax.sections-by-class") }}',
    method: 'GET',
    data: { class_id: classId },
    dataType: 'json',
    success: function(response) {
      if (response.success && response.data) {
        $('#filter-section').empty().append('<option value="">All Sections</option>');
        response.data.forEach(function(section) {
          $('#filter-section').append('<option value="' + section.id + '">' + section.name + '</option>');
        });
        $('#filter-section').niceSelect('update');
      }
    },
    error: function() {
      console.error('Failed to load sections');
    }
  });
}

{{-- Load subjects for selected section --}}
function loadSubjectsBySection(classId, sectionId) {
  $.ajax({
    url: '{{ route("homework.ajax.subjects-by-section") }}',
    method: 'GET',
    data: { class_id: classId, section_id: sectionId },
    dataType: 'json',
    success: function(response) {
      if (response.success && response.data) {
        $('#filter-subject').empty().append('<option value="">All Subjects</option>');
        response.data.forEach(function(subject) {
          $('#filter-subject').append('<option value="' + subject.id + '">' + subject.name + '</option>');
        });
        $('#filter-subject').niceSelect('update');
      }
    },
    error: function() {
      console.error('Failed to load subjects');
    }
  });
}

{{-- Get filtered report data --}}
function getFilteredReport() {
  const filters = {
    class: $('#filter-class').val(),
    section: $('#filter-section').val(),
    subject: $('#filter-subject').val(),
    task_type: $('#filter-task-type').val(),
  };

  $.ajax({
    url: '{{ route("homework.ajax.filtered-report") }}',
    method: 'POST',
    data: Object.assign(filters, { _token: '{{ csrf_token() }}' }),
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        {{-- Update table --}}
        $('#filtered-table-body').html(response.table_html);

        {{-- Destroy old chart instances --}}
        if (donutChartInstance) donutChartInstance.destroy();
        if (lineChartInstance) lineChartInstance.destroy();

        {{-- Initialize donut chart --}}
        const donutCtx = document.getElementById('donut-chart-filtered');
        if (donutCtx && response.donut_data) {
          donutChartInstance = new Chart(donutCtx, {
            type: 'doughnut',
            data: {
              labels: response.donut_data.labels,
              datasets: [{
                data: response.donut_data.data,
                backgroundColor: response.donut_data.colors,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6
              }]
            },
            options: {
              cutout: '62%',
              responsive: true,
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: { font: { size: 10 }, padding: 10, usePointStyle: true }
                },
                tooltip: {
                  backgroundColor: '#0f172a',
                  padding: 10,
                  cornerRadius: 8
                }
              }
            }
          });
        }

        {{-- Initialize line chart --}}
        const lineCtx = document.getElementById('line-chart-filtered');
        if (lineCtx && response.trend_data) {
          lineChartInstance = new Chart(lineCtx, {
            type: 'line',
            data: {
              labels: response.trend_data.labels,
              datasets: response.trend_data.datasets.map(function(ds) {
                return {
                  label: ds.label,
                  data: ds.data,
                  borderColor: ds.borderColor,
                  backgroundColor: ds.backgroundColor,
                  borderWidth: 2,
                  tension: 0.4,
                  fill: false,
                  pointRadius: 4,
                  pointHoverRadius: 6
                };
              })
            },
            options: {
              responsive: true,
              interaction: { mode: 'index', intersect: false },
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: { font: { size: 10 }, padding: 8, usePointStyle: true }
                },
                tooltip: {
                  backgroundColor: '#0f172a',
                  padding: 10,
                  cornerRadius: 8
                }
              },
              scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } }
              }
            }
          });
        }

        {{-- Show results container --}}
        $('#results-container').fadeIn(300);
      }
    },
    error: function(xhr) {
      console.error('Failed to load filtered report:', xhr);
      alert('Error loading report. Please try again.');
    }
  });
}

{{-- Open evaluation modal --}}
function openEval(id) {
  $('#hw_id').val(id);
  $('#ev-body').html('<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fs-3 text-primary"></i></div>');
  $.post($('#url').val() + '/homework/students', {
    homework_id: id,
    _token: $('meta[name="csrf-token"]').attr('content')
  }, function(d) {
    $('#ev-body').html(d.view);
  }).fail(function() {
    $('#ev-body').html('<p class="text-danger p-3">Failed to load.</p>');
  });
}

{{-- View quiz questions --}}
function vQ(id) {
  $.ajax({
    type: 'GET',
    dataType: 'html',
    data: { id: id },
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
    url: $('#url').val() + '/homework/view-questions',
    success: function(d) {
      $('#mQ .modal-dialog').html(d);
    }
  });
}

</script>

@endpush
