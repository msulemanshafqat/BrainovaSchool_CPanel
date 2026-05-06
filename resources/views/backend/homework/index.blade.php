{{-- ============================================================
     TEMPLATE INHERITANCE
     Extends the main backend layout (master.blade.php).
     Sets the browser tab title dynamically from $data['title'].
     ============================================================ --}}
@extends('backend.master')
@section('title') {{ @$data['title'] }} @endsection

{{-- ============================================================
     PAGE-SPECIFIC CSS (pushed into the <head> via @stack('style'))
     Scoped to this page only via .hw-portal wrapper.
     ============================================================ --}}
@push('style')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>

/* ─── Reset & Base ──────────────────────────────────────────── */
.hw-portal {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background: #f8f9fa;         /* Very-light-grey page canvas */
  min-height: 100vh;
}

/* ─── CSS Custom Properties ─────────────────────────────────── */
:root {
  --bp:    #1d4ed8;   /* Brand Primary (blue) */
  --bpl:   #dbeafe;
  --bpd:   #1e40af;   /* Darker hover shade   */
  --ba:    #f59e0b;
  --bal:   #fef3c7;
  --bg:    #059669;
  --bgl:   #d1fae5;
  --br:    #dc2626;
  --brl:   #fee2e2;
  --bs:    #64748b;
  --bb:    #e2e8f0;
  --bt:    #0f172a;
  --rr:    20px;        /* Soft-card border radius */
  --shadow-soft: 0 10px 30px rgba(0,0,0,0.04);
  --shadow-hover: 0 16px 40px rgba(29,78,216,0.12);

  /* Table chrome */
  --tb-outer: #c7d2e0;
  --tb-head:  #b8c5d6;
  --tb-row:   #e2e8f0;
  --tb-col:   #e8edf5;
}

/* ─── Section Spacing (4 rem between each major section) ─────── */
.hw-section {
  margin-bottom: 4rem;   /* ≈ 3 cm — gives the UI room to breathe */
}

/* ─── Soft Cards ─────────────────────────────────────────────── */
.soft-card {
  background:    #fff;
  border:        none;
  border-radius: var(--rr);
  box-shadow:    var(--shadow-soft);
  padding:       24px;
  transition:    transform .2s, box-shadow .2s;
}
.soft-card:hover {
  transform:  translateY(-2px);
  box-shadow: 0 16px 40px rgba(0,0,0,0.07);
}

/* ─── Stat Cards ─────────────────────────────────────────────── */
.sc {
  background:    #fff;
  border:        none;
  border-radius: var(--rr);
  box-shadow:    var(--shadow-soft);
  padding:       22px 24px;
  display:       flex;
  align-items:   center;
  gap:           16px;
  transition:    transform .2s, box-shadow .2s;
}
.sc:hover {
  transform:  translateY(-3px);
  box-shadow: var(--shadow-hover);
}
.si {
  width:          52px;
  height:         52px;
  border-radius:  14px;
  flex-shrink:    0;
  display:        flex;
  align-items:    center;
  justify-content: center;
  font-size:      20px;
}
.sv {
  font-size:   28px;
  font-weight: 800;
  line-height: 1;
  color:       var(--bt);
  letter-spacing: -0.5px;
}
.sl {
  font-size:      11px;
  color:          var(--bs);
  font-weight:    600;
  margin-top:     4px;
  text-transform: uppercase;
  letter-spacing: .06em;
}
.sc-trend {
  font-size:   10px;
  color:       var(--bg);
  font-weight: 700;
  margin-top:  4px;
}

/* ─── Filter Bar ─────────────────────────────────────────────── */
.hw-filter-bar {
  background:    #fff;
  border:        none;
  border-radius: var(--rr);
  box-shadow:    var(--shadow-soft);
  padding:       24px 28px;
}
.hw-filter-bar .filter-label {
  font-size:      10.5px;
  font-weight:    700;
  text-transform: uppercase;
  letter-spacing: .07em;
  color:          var(--bs);
  margin-bottom:  8px;
  display:        block;
}
.hw-filter-bar .niceSelect {
  width:      100% !important;
  min-width:  0   !important;
  box-sizing: border-box;
  font-size:  12.5px !important;
  border-radius: 10px !important;
  border-color: var(--bb) !important;
  height: 40px !important;
  line-height: 40px !important;
}
.hw-filter-bar .niceSelect.open,
.hw-filter-bar .niceSelect:hover { border-color: var(--bp) !important; }
.hw-filter-bar .niceSelect .list { width: 100% !important; border-radius: 10px !important; }
.hw-filter-bar .niceSelect .list li.option { font-size: 12.5px !important; }

/* ─── Proceed / Reset Buttons ────────────────────────────────── */
.btn-proceed {
  background:    var(--bp);
  color:         #fff;
  border:        none;
  border-radius: 10px;
  padding:       0 24px;
  height:        40px;
  font-size:     13px;
  font-weight:   700;
  letter-spacing: .03em;
  display:       inline-flex;
  align-items:   center;
  gap:           8px;
  cursor:        pointer;
  position:      relative;
  overflow:      hidden;
  transition:    background .2s, box-shadow .2s, transform .15s;
  /* Glow ring */
  box-shadow: 0 0 0 0 rgba(29,78,216,0.4);
  animation: proceedPulse 2.4s infinite;
}
.btn-proceed:hover {
  background:  var(--bpd);
  transform:   translateY(-1px);
  box-shadow:  0 8px 24px rgba(29,78,216,0.35);
  animation:   none;
  color:       #fff;
}
.btn-proceed:active { transform: translateY(0); }
@keyframes proceedPulse {
  0%   { box-shadow: 0 0 0 0   rgba(29,78,216,0.45); }
  60%  { box-shadow: 0 0 0 10px rgba(29,78,216,0); }
  100% { box-shadow: 0 0 0 0   rgba(29,78,216,0); }
}

.btn-reset {
  background:    transparent;
  color:         var(--bs);
  border:        1.5px solid var(--bb);
  border-radius: 10px;
  padding:       0 20px;
  height:        40px;
  font-size:     12.5px;
  font-weight:   600;
  display:       inline-flex;
  align-items:   center;
  gap:           7px;
  cursor:        pointer;
  transition:    border-color .2s, color .2s, background .2s;
}
.btn-reset:hover {
  border-color: var(--br);
  color:        var(--br);
  background:   var(--brl);
}

/* ─── Results Container (hidden on page load) ─────────────────── */
#results-container { display: none; }

/* ─── Chart Cards ─────────────────────────────────────────────── */
.cc {
  background:    #fff;
  border:        none;
  border-radius: var(--rr);
  box-shadow:    var(--shadow-soft);
  padding:       24px;
}
.cc-title {
  font-size:      11px;
  text-transform: uppercase;
  letter-spacing: .09em;
  color:          var(--bs);
  font-weight:    700;
  margin-bottom:  4px;
}
.cc-sub {
  font-size:    10px;
  color:        #94a3b8;
  margin-bottom: 18px;
}

/* Fixed height so Chart.js can measure while parent is visibility-hidden during init */
.hw-chart-canvas-wrap {
  position: relative;
  width:    100%;
  height:   220px;
}

/* ─── Section Label (inside Results) ─────────────────────────── */
.results-section-label {
  font-size:      10.5px;
  font-weight:    700;
  text-transform: uppercase;
  letter-spacing: .09em;
  color:          var(--bs);
  margin-bottom:  14px;
  display:        flex;
  align-items:    center;
  gap:            8px;
}
.results-section-label::after {
  content:    '';
  flex:       1;
  height:     1px;
  background: var(--bb);
}

/* ─── Homework Table ─────────────────────────────────────────── */
.hw-table-card {
  background:    #fff;
  border:        none;
  border-radius: var(--rr);
  box-shadow:    var(--shadow-soft);
  overflow:      hidden;
  margin-top:    3rem;   /* 3 rem gap above table as specified */
}

.ht {
  width:           100%;
  border-collapse: collapse;
  font-size:       13px;
  table-layout:    auto;
}

.ht thead th {
  background:     #f8fafc;
  padding:        12px 14px;
  font-size:      10px;
  font-weight:    700;
  text-transform: uppercase;
  letter-spacing: .07em;
  color:          var(--bs);
  border-bottom:  2px solid var(--tb-head);
  border-right:   1px solid var(--tb-col);
  white-space:    nowrap;
}
.ht thead th:last-child { border-right: none; }

.ht tbody td {
  padding:        12px 14px;
  border-bottom:  1px solid var(--tb-row);
  border-right:   1px solid var(--tb-col);
  vertical-align: middle;   /* spec: vertical-align middle */
  background:     #fff;
}
.ht tbody td:last-child { border-right: none; }

/* Table borderless feel + alt rows */
.ht tbody tr:nth-child(even) td { background: #fafbff; }
.ht tbody tr:last-child td      { border-bottom: none; }
.ht tbody tr:hover td           { background: #eff4ff !important; }
.ht tr.hh                       { display: none !important; }

/* ─── Type Badges ─────────────────────────────────────────────── */
.tbg {
  display:        inline-block;
  padding:        3px 10px;
  border-radius:  20px;
  font-size:      10px;
  font-weight:    700;
  text-transform: uppercase;
  letter-spacing: .04em;
}
.tbg-quiz       { background: #dbeafe; color: #1e40af; }
.tbg-hw         { background: #d1fae5; color: #065f46; }
.tbg-project    { background: #fce7f3; color: #9d174d; }
.tbg-activity   { background: #ede9fe; color: #5b21b6; }
.tbg-game       { background: #fef3c7; color: #92400e; }
.tbg-assignment { background: #e0f2fe; color: #075985; }

.od {
  background:   #fee2e2;
  color:        #dc2626;
  border-radius: 4px;
  padding:      2px 7px;
  font-size:    10px;
  font-weight:  700;
}

/* ─── Action Button ───────────────────────────────────────────── */
.ab {
  background:   none;
  border:       1.5px solid var(--bb);
  border-radius: 8px;
  width:        30px;
  height:       30px;
  display:      flex;
  align-items:  center;
  justify-content: center;
  cursor:       pointer;
  color:        var(--bs);
  font-size:    12px;
  transition:   all .15s;
}
.ab:hover {
  border-color: var(--bp);
  color:        var(--bp);
  background:   var(--bpl);
}

/* ─── Accordion / Collapse fix ────────────────────────────────── */
.sect-body {
  overflow:   hidden !important;
  transition: max-height 0.3s ease-in-out;
}

/* ─── Page-header tweak ───────────────────────────────────────── */
.hw-portal .page-header {
  margin-bottom: 2rem;
}

/* ─── Loading shimmer for stats ───────────────────────────────── */
@keyframes shimmer {
  0%   { background-position: -400px 0; }
  100% { background-position:  400px 0; }
}
.stat-loading {
  display:             inline-block;
  width:               60px;
  height:              28px;
  border-radius:       6px;
  background:          linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);
  background-size:     400px 100%;
  animation:           shimmer 1.2s infinite;
}

</style>
@endpush

{{-- ============================================================
     MAIN PAGE CONTENT
     ============================================================ --}}
@section('content')
<div class="page-content hw-portal">

  {{-- Hidden base URL for JS --}}
  <input type="hidden" id="url" value="{{ url('/') }}">

  {{-- ── Page Header ──────────────────────────────────────────── --}}
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

  {{-- =================================================================
       SECTION 1 · GLOBAL STATS CARDS
       4 soft cards: Assigned Tasks · Submitted · Pending · E6 Score
       margin-bottom: 4rem — the "breathe" gap
  ================================================================= --}}
  <div class="hw-section" id="stats-cards-container">
    <div class="row g-3">

      {{-- Card 1 · Assigned Tasks --}}
      <div class="col-md-6 col-lg-3">
        <div class="sc">
          <div class="si" style="background: linear-gradient(135deg,#dbeafe,#bfdbfe);">
            <i class="fa-solid fa-book-open" style="color:#1d4ed8"></i>
          </div>
          <div style="flex:1">
            <div class="sv" id="stat-total-tasks"><span class="stat-loading"></span></div>
            <div class="sl">Assigned Tasks</div>
          </div>
        </div>
      </div>

      {{-- Card 2 · Submitted --}}
      <div class="col-md-6 col-lg-3">
        <div class="sc">
          <div class="si" style="background: linear-gradient(135deg,#d1fae5,#a7f3d0);">
            <i class="fa-solid fa-circle-check" style="color:#059669"></i>
          </div>
          <div style="flex:1">
            <div class="sv" id="stat-submitted"><span class="stat-loading"></span></div>
            <div class="sl">Submitted</div>
          </div>
        </div>
      </div>

      {{-- Card 3 · Pending Evaluation --}}
      <div class="col-md-6 col-lg-3">
        <div class="sc">
          <div class="si" style="background: linear-gradient(135deg,#fef3c7,#fde68a);">
            <i class="fa-solid fa-hourglass-half" style="color:#d97706"></i>
          </div>
          <div style="flex:1">
            <div class="sv" id="stat-pending"><span class="stat-loading"></span></div>
            <div class="sl">Pending Evaluation</div>
          </div>
        </div>
      </div>

      {{-- Card 4 · E6 Score  (uses config('brainova.e6_multiplier') server-side) --}}
      <div class="col-md-6 col-lg-3">
        <div class="sc">
          <div class="si" style="background: linear-gradient(135deg,#ede9fe,#ddd6fe);">
            <i class="fa-solid fa-star-half-stroke" style="color:#7c3aed"></i>
          </div>
          <div style="flex:1">
            <div class="sv" id="stat-score"><span class="stat-loading"></span></div>
            <div class="sl">Total E6 Score</div>
          </div>
        </div>
      </div>

    </div>
  </div>{{-- /SECTION 1 --}}


  {{-- =================================================================
       SECTION 2 · FILTER BAR
       Rigid col-md-2 columns + align-items-end row so every label
       and control sits on the same baseline.
       margin-bottom: 4rem
  ================================================================= --}}
  <div class="hw-section">
    <div class="hw-filter-bar">

      {{-- Filter heading --}}
      <div class="d-flex align-items-center gap-2 mb-3">
        <div style="width:32px;height:32px;border-radius:8px;background:var(--bpl);display:flex;align-items:center;justify-content:center">
          <i class="fa-solid fa-sliders" style="color:var(--bp);font-size:13px"></i>
        </div>
        <div>
          <div style="font-size:13px;font-weight:700;color:var(--bt)">Filter Report</div>
          <div style="font-size:10.5px;color:var(--bs)">Narrow down by class, section, subject and task type</div>
        </div>
      </div>

      {{-- Dropdowns row — align-items-end keeps labels + selects flush --}}
      <div class="row g-2 align-items-end">

        {{-- Class (col-md-2) --}}
        <div class="col-md-2 col-sm-6">
          <label class="filter-label" for="filter-class">
            <i class="fa-solid fa-school me-1" style="color:var(--bp)"></i>Class
          </label>
          
          <select id="filter-class" class="niceSelect bordered_style">
            <option value="">All Classes</option>
            @foreach($data['classes'] ?? [] as $item)
              @if(!empty($item->class))
                <option value="{{ $item->class->id }}">{{ $item->class->name }}</option>
              @endif
            @endforeach
          </select>
        </div>

        {{-- Section (col-md-2) --}}
        <div class="col-md-2 col-sm-6">
          <label class="filter-label" for="filter-section">
            <i class="fa-solid fa-layer-group me-1" style="color:var(--bp)"></i>Section
          </label>
          
          <select id="filter-section" class="niceSelect bordered_style">  
            <option value="">All Sections</option>
          </select>
        </div>

        {{-- Subject (col-md-2) --}}
        <div class="col-md-2 col-sm-6">
          <label class="filter-label" for="filter-subject">
            <i class="fa-solid fa-book me-1" style="color:var(--bp)"></i>Subject
          </label>
          
          <select id="filter-subject" class="niceSelect bordered_style">

            <option value="">All Subjects</option>
          </select>
        </div>

        {{-- Task Type (col-md-2) --}}
        <div class="col-md-2 col-sm-6">
          <label class="filter-label" for="filter-task-type">
            <i class="fa-solid fa-tags me-1" style="color:var(--bp)"></i>Task Type
          </label>
          
           <select id="filter-task-type" class="niceSelect bordered_style"> 
          <option value="all">All Types</option>
            <option value="quiz">Quiz</option>
            <option value="homework">Homework</option>
            <option value="project">Project</option>
            <option value="activity">Activity</option>
            <option value="game">Game</option>
            <option value="assignment">Assignment</option>
          </select>
        </div>

        {{-- Spacer col to push buttons right (col-md-2 + col-md-2 = 4 cols spare) --}}
        <div class="col-md-2 d-none d-md-block"></div>

        {{-- Proceed + Reset buttons (col-md-2, pinned to end baseline) --}}
        <div class="col-md-2 col-sm-12 d-flex gap-2 align-items-end">
          <button type="button" class="btn-proceed" id="proceed-btn">
            <i class="fa-solid fa-arrow-right"></i>
            Proceed
          </button>
          <button type="button" class="btn-reset" id="reset-filters-btn">
            <i class="fa-solid fa-rotate-left"></i>
          </button>
        </div>

      </div>{{-- /row --}}
    </div>{{-- /hw-filter-bar --}}
  </div>{{-- /SECTION 2 --}}


  {{-- =================================================================
       SECTION 3 · RESULTS AREA  (hidden on page load — revealed via
       jQuery .fadeIn() when Proceed is clicked)
       margin-bottom: 4rem
  ================================================================= --}}
  <div class="hw-section" id="results-container" style="display:none">

    {{-- ── Charts Row ──────────────────────────────────────────── --}}
    <div class="results-section-label">
      <i class="fa-solid fa-chart-column" style="color:var(--bp)"></i>
      Analytics Overview
    </div>

    <div class="row g-3 mb-0">

      {{-- Donut Chart · col-md-6 --}}
      <div class="col-md-6">
        <div class="cc">
          <div class="cc-title">
            <i class="fa-solid fa-chart-pie me-1" style="color:var(--bp)"></i>
            Overall Task Status
          </div>
          <div class="cc-sub text-muted">Turned in &middot; not yet due &middot; overdue (missing)</div>
          <div class="hw-chart-canvas-wrap"><canvas id="donut-chart-filtered"></canvas></div>
        </div>
      </div>

      {{-- Line Chart · col-md-6 --}}
      <div class="col-md-6">
        <div class="cc">
          <div class="cc-title">
            <i class="fa-solid fa-chart-line me-1" style="color:var(--bp)"></i>
            Score Trend
          </div>
          <div class="cc-sub text-muted">Blue = graded average score · Gray = % of class roster submitted</div>
          <div class="hw-chart-canvas-wrap"><canvas id="line-chart-filtered"></canvas></div>
        </div>
      </div>

    </div>{{-- /charts row --}}

    {{-- ── Homework Table (full-width, 3 rem top margin) ────────── --}}
    <div class="hw-table-card">{{-- margin-top: 3rem applied via class --}}

      {{-- Table card header --}}
      <div class="d-flex align-items-center justify-content-between px-4 py-3"
           style="border-bottom:1px solid var(--bb)">
        <div class="results-section-label mb-0 flex-grow-1 me-3">
          <i class="fa-solid fa-table-list" style="color:var(--bp)"></i>
          Homework &amp; Tasks List
        </div>
        <span id="table-count-badge"
              style="background:var(--bpl);color:var(--bp);border-radius:20px;padding:3px 12px;font-size:10.5px;font-weight:700;white-space:nowrap">
          — records
        </span>
      </div>

      <div style="overflow-x:auto">
        <table class="ht">
          <thead>
            <tr>
              <th style="width:36px">#</th>
              <th>Title</th>
              <th style="white-space:nowrap">Class / Section</th>
              <th style="white-space:nowrap">Subject</th>
              <th style="white-space:nowrap">Type</th>
              <th style="white-space:nowrap">Due Date</th>
              <th style="white-space:nowrap;width:60px">Marks</th>
              <th style="white-space:nowrap;width:48px">Act.</th>
            </tr>
          </thead>
          <tbody id="filtered-table-body">
            <tr>
              <td colspan="8" style="padding:28px;text-align:center;color:#94a3b8">
                <i class="fa-solid fa-magnifying-glass me-2"></i>
                Apply filters and click <strong>Proceed</strong> to load results.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>{{-- /hw-table-card --}}

  </div>{{-- /SECTION 3 · results-container --}}

</div>{{-- /page-content hw-portal --}}


{{-- ============================================================
     MODALS
     ============================================================ --}}

{{-- ── Evaluation Modal (#mEv) ── --}}
<div class="modal fade" id="mEv" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header modal-header-image">
        <h5 class="modal-title">Evaluation</h5>
        <button type="button"
                class="m-0 btn-close d-flex justify-content-center align-items-center"
                data-bs-dismiss="modal">
          <i class="fa fa-times text-white"></i>
        </button>
      </div>
      <form action="{{ route('homework.evaluation.submit') }}" method="post">
        @csrf
        <input type="hidden" name="homework_id" id="hw_id">
        <div class="modal-body p-4" id="ev-body">
          <div class="text-center py-5">
            <i class="fa-solid fa-spinner fa-spin fs-3 text-primary"></i>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="submit" class="btn ot-btn-primary">
            <i class="fa-solid fa-check me-1"></i>Save Marks
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ── Quiz Questions Modal (#mQ) ── --}}
<div class="modal fade" id="mQ" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"></div>
</div>

@endsection


{{-- ============================================================
     PAGE-SPECIFIC JAVASCRIPT
     All repository-pattern AJAX routes are preserved exactly.
     ============================================================ --}}
@push('script')

@include('backend.partials.delete-ajax')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
let donutChartInstance = null;
let lineChartInstance  = null;

$(document).ready(function () {

  loadGlobalStats();

  /* ── Bind events FIRST ── */
  $('#filter-class').on('change', function () {
    const classId = $(this).val();

    $('#filter-section').val('').empty()
      .append('<option value="">All Sections</option>').niceSelect('update');
    $('#filter-subject').val('').empty()
      .append('<option value="">All Subjects</option>').niceSelect('update');

    if (!classId) return;
    loadSectionsByClass(classId);
  });

  $('#filter-section').on('change', function () {
    const classId   = $('#filter-class').val();
    const sectionId = $(this).val();

    $('#filter-subject').val('').empty()
      .append('<option value="">All Subjects</option>').niceSelect('update');

    if (!classId || !sectionId) return;
    loadSubjectsBySection(classId, sectionId);
  });

  /* ── Init niceSelect AFTER events ── */
  $('#filter-class').niceSelect();
  $('#filter-section').niceSelect();
  $('#filter-subject').niceSelect();
  $('#filter-task-type').niceSelect();

  /* ── Proceed button ── */
  $('#proceed-btn').on('click', function () {
    getFilteredReport();
  });

  /* ── Reset button naz ── */
  $('#reset-filters-btn').on('click', function () {
    $('#filter-class').val('').niceSelect('update');
    $('#filter-section').empty().append('<option value="">All Sections</option>').niceSelect('update');
    $('#filter-subject').empty().append('<option value="">All Subjects</option>').niceSelect('update');
    $('#filter-task-type').val('all').niceSelect('update');
    if (donutChartInstance) { donutChartInstance.destroy(); donutChartInstance = null; }
    if (lineChartInstance)  { lineChartInstance.destroy();  lineChartInstance  = null; }
    $('#results-container').stop(true, true).fadeOut(300, function () {
      $(this).css({ visibility: '', opacity: '' });
    });
  });

  /* ── Functions ── */

  function loadGlobalStats() {
    $.ajax({
      url: '{{ route("homework.ajax.global-stats") }}',
      method: 'GET',
      dataType: 'json',
      success: function (response) {
        if (response.success && response.data) {
          const s = response.data;
          $('#stat-total-tasks').text(s.total_tasks_assigned ?? '—');
          $('#stat-submitted'  ).text(s.total_submitted      ?? '—');
          $('#stat-pending'    ).text(s.pending_evaluations  ?? '—');
          $('#stat-score'      ).text(s.cumulative_score_e6  ?? '—');
          if (s.total_tasks_assigned) {
            $('#table-count-badge').text(s.total_tasks_assigned + ' records');
          }
        }
      },
      error: function () {
        ['#stat-total-tasks','#stat-submitted','#stat-pending','#stat-score']
          .forEach(function (sel) { $(sel).text('—'); });
      }
    });
  }

  function loadSectionsByClass(classId) {
    $.ajax({
      url:      '{{ route("homework.ajax.sections-by-class") }}',
      method:   'GET',
      data:     { class_id: classId },
      dataType: 'json',
      success: function (response) {
        if (response.success && response.data) {
          const $sel = $('#filter-section').empty().append('<option value="">All Sections</option>');
          response.data.forEach(function (section) {
            $sel.append('<option value="' + section.id + '">' + section.name + '</option>');
          });
          $sel.niceSelect('update');
        }
      },
      error: function () { console.error('Failed to load sections'); }
    });
  }

  function loadSubjectsBySection(classId, sectionId) {
    $.ajax({
      url:      '{{ route("homework.ajax.subjects-by-section") }}',
      method:   'GET',
      data:     { class_id: classId, section_id: sectionId },
      dataType: 'json',
      success: function (response) {
        if (response.success && response.data) {
          const $sel = $('#filter-subject').empty().append('<option value="">All Subjects</option>');
          response.data.forEach(function (subject) {
            $sel.append('<option value="' + subject.id + '">' + subject.name + '</option>');
          });
          $sel.niceSelect('update');
        }
      },
      error: function () { console.error('Failed to load subjects'); }
    });
  }

  function getFilteredReport() {
    const $btn = $('#proceed-btn');
    $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Loading…').prop('disabled', true);

    const filters = {
      class:     $('#filter-class'    ).val(),
      section:   $('#filter-section'  ).val(),
      subject:   $('#filter-subject'  ).val(),
      task_type: $('#filter-task-type').val(),
    };

    $.ajax({
      url:      '{{ route("homework.ajax.filtered-report") }}',
      method:   'POST',
      data:     Object.assign(filters, { _token: '{{ csrf_token() }}' }),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#filtered-table-body').html(response.table_html);
          const rowCount = $('#filtered-table-body tr:not(.hh)').length;
          $('#table-count-badge').text(rowCount + ' record' + (rowCount !== 1 ? 's' : ''));

          if (donutChartInstance) { donutChartInstance.destroy(); donutChartInstance = null; }
          if (lineChartInstance)  { lineChartInstance.destroy();  lineChartInstance  = null; }

          /* Chart.js reads canvas parent size; display:none gives 0×0 and charts disappear */
          var $results = $('#results-container');
          $results.css({ display: 'block', visibility: 'hidden', opacity: 0 });

          const donutCtx = document.getElementById('donut-chart-filtered');
          if (donutCtx && response.donut_data && Array.isArray(response.donut_data.data)) {
            var donutVals = response.donut_data.data.map(function (v) { return Number(v) || 0; });
            var donutLabels = response.donut_data.labels ? response.donut_data.labels.slice() : [];
            var donutColors = response.donut_data.colors ? response.donut_data.colors.slice() : [];
            var donutSum = donutVals.reduce(function (a, b) { return a + b; }, 0);
            if (donutSum === 0) {
              donutLabels = ['No activity yet'];
              donutVals = [1];
              donutColors = ['#e2e8f0'];
            }
            donutChartInstance = new Chart(donutCtx, {
              type: 'doughnut',
              data: {
                labels: donutLabels,
                datasets: [{
                  data: donutVals,
                  backgroundColor: donutColors.length >= donutVals.length ? donutColors : ['#10b981', '#f59e0b', '#dc2626'],
                  borderWidth: 3,
                  borderColor: '#fff',
                  hoverOffset: 8
                }]
              },
              options: {
                cutout: '65%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                  legend: {
                    position: 'bottom',
                    labels: { font: { size: 11, family: 'Plus Jakarta Sans' }, padding: 14, usePointStyle: true }
                  },
                  tooltip: {
                    enabled: donutSum > 0,
                    callbacks: {
                      label: function (ctx) {
                        return ' ' + (ctx.label || '') + ': ' + ctx.raw;
                      }
                    }
                  }
                }
              }
            });
          }

          const lineCtx = document.getElementById('line-chart-filtered');
          if (lineCtx && response.trend_data && response.trend_data.datasets && response.trend_data.datasets.length) {
            var td = response.trend_data;
            var scales = {
              x: { grid: { display: false }, ticks: { font: { size: 10 } } },
              y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: { font: { size: 10 } },
                title: {
                  display: true,
                  font: { size: 10, weight: '600' },
                  text: td.y_left_title || 'Score',
                  color: '#64748b'
                }
              }
            };
            if (td.y_suggested_max) {
              scales.y.suggestedMax = td.y_suggested_max;
            }
            if (td.dual_axis) {
              scales.y1 = {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                max: 100,
                grid: { drawOnChartArea: false },
                ticks: { font: { size: 10 } },
                title: {
                  display: true,
                  font: { size: 10, weight: '600' },
                  text: td.y_right_title || '% submitted',
                  color: '#64748b'
                }
              };
            }

            lineChartInstance = new Chart(lineCtx, {
              type: 'line',
              data: {
                labels: td.labels,
                datasets: td.datasets.map(function (ds) {
                  var bc = ds.borderColor || '#1d4ed8';
                  return {
                    label: ds.label,
                    data: ds.data,
                    yAxisID: ds.y_axis_id || 'y',
                    borderColor: bc,
                    backgroundColor: ds.backgroundColor || 'rgba(29,78,216,0.08)',
                    borderWidth: typeof ds.borderWidth === 'number' ? ds.borderWidth : 2.5,
                    tension: typeof ds.tension === 'number' ? ds.tension : 0.35,
                    fill: ds.fill !== false,
                    spanGaps: ds.spanGaps === true,
                    pointRadius: 4,
                    pointBackgroundColor: bc,
                    pointHoverRadius: 6
                  };
                })
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: scales,
                plugins: {
                  tooltip: {
                    callbacks: {
                      label: function (ctx) {
                        var v = ctx.raw;
                        var lbl = ctx.dataset.label || '';
                        if (v === null || v === undefined) {
                          return ' ' + lbl + ': —';
                        }
                        var sfx = '';
                        if (ctx.dataset.yAxisID === 'y1') {
                          sfx = '%';
                        } else if (td.y_suggested_max && ctx.dataset.yAxisID === 'y') {
                          sfx = '%';
                        }
                        return ' ' + lbl + ': ' + v + sfx;
                      }
                    }
                  }
                }
              }
            });
          }

          $results.css({ visibility: 'visible' });
          window.requestAnimationFrame(function () {
            if (donutChartInstance && typeof donutChartInstance.resize === 'function') donutChartInstance.resize();
            if (lineChartInstance && typeof lineChartInstance.resize === 'function') lineChartInstance.resize();
            $results.animate({ opacity: 1 }, 400);
          });
        }
      },
      error: function (xhr) {
        console.error('Failed to load filtered report:', xhr);
        alert('Error loading report. Please check your filters and try again.');
      },
      complete: function () {
        $btn.html('<i class="fa-solid fa-arrow-right"></i> Proceed').prop('disabled', false);
      }
    });
  }

}); // ← closes $(document).ready — THIS WAS MISSING


/* ── These stay outside document.ready (called from HTML onclick) ── */
function openEval(id) {
  $('#hw_id').val(id);
  $('#ev-body').html('<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fs-3 text-primary"></i></div>');
  $.post(
    $('#url').val() + '/homework/students',
    { homework_id: id, _token: $('meta[name="csrf-token"]').attr('content') },
    function (d) { $('#ev-body').html(d.view); }
  ).fail(function () { $('#ev-body').html('<p class="text-danger p-3">Failed to load.</p>'); });
}

function vQ(id) {
  $.ajax({
    type: 'GET', dataType: 'html', data: { id: id },
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
    url: $('#url').val() + '/homework/view-questions',
    success: function (d) { $('#mQ .modal-dialog').html(d); }
  });
}

</script>

@endpush
