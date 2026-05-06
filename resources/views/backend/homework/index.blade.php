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
  background: linear-gradient(180deg, #f1f5f9 0%, #f8fafc 48%, #f1f5f9 100%);
  min-height: 100vh;
  padding-bottom: 3rem;
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
  --rr:    16px;
  --rr-lg: 20px;
  --shadow-soft: 0 1px 3px rgba(15,23,42,0.06), 0 12px 40px rgba(15,23,42,0.04);
  --shadow-hover: 0 8px 28px rgba(29,78,216,0.12);
  --hw-border: 1px solid rgba(226, 232, 240, 0.9);

  /* Table chrome */
  --tb-outer: #c7d2e0;
  --tb-head:  #e2e8f0;
  --tb-row:   #f1f5f9;
  --tb-col:   transparent;
}

/* ─── Page hero (title zone — reads first, not cramped) ─────── */
.hw-page-hero {
  background: #fff;
  border: var(--hw-border);
  border-radius: var(--rr-lg);
  box-shadow: var(--shadow-soft);
  padding: 1.75rem 2rem;
  margin-bottom: 2rem;
}
.hw-eyebrow {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .14em;
  text-transform: uppercase;
  color: var(--bp);
  margin: 0 0 .5rem;
}
.hw-page-title {
  font-size: clamp(1.35rem, 2.5vw, 1.65rem);
  font-weight: 800;
  color: var(--bt);
  letter-spacing: -0.03em;
  margin: 0 0 .5rem;
  line-height: 1.2;
}
.hw-page-lead {
  font-size: .9375rem;
  color: var(--bs);
  line-height: 1.55;
  max-width: 42rem;
  margin: 0 0 .75rem;
}
.hw-page-hero .breadcrumb {
  margin-bottom: 0;
  padding: 0;
  font-size: 13px;
  background: transparent;
}

/* ─── Panel sections (group content so it isn’t one flat wall) ─ */
.hw-panel {
  background: #fff;
  border: var(--hw-border);
  border-radius: var(--rr-lg);
  box-shadow: var(--shadow-soft);
  padding: 1.75rem 2rem 2rem;
  margin-bottom: 2rem;
}
.hw-panel-head {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f1f5f9;
}
.hw-filter-intro {
  margin-bottom: 1.5rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #f1f5f9;
}
.hw-results-panel .hw-table-card {
  margin-top: 1.75rem;
}
.hw-panel-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--bt);
  letter-spacing: -0.02em;
  margin: .35rem 0 0;
  line-height: 1.3;
}
.hw-panel-desc {
  font-size: .8125rem;
  color: var(--bs);
  margin: .5rem 0 0;
  line-height: 1.5;
  max-width: 48rem;
}

/* ─── Section spacing between major stacks ─────── */
.hw-section {
  margin-bottom: 0;
}
.hw-section + .hw-section {
  margin-top: 0;
}

/* ─── Soft Cards ─────────────────────────────────────────────── */
.soft-card {
  background:    #fff;
  border:        var(--hw-border);
  border-radius: var(--rr);
  box-shadow:    none;
  padding:       24px;
  transition:    box-shadow .2s;
}
.soft-card:hover {
  box-shadow: var(--shadow-soft);
}

/* ─── Stat Cards (calmer — less “busy”, clearer labels) ──────── */
.sc {
  background:    #fafbfc;
  border:        1px solid #eef2f7;
  border-radius: var(--rr);
  box-shadow:    none;
  padding:       1.125rem 1.25rem;
  display:       flex;
  align-items:   center;
  gap:           14px;
  transition:    border-color .2s, box-shadow .2s;
  height:        100%;
}
.sc:hover {
  border-color: #dbeafe;
  box-shadow:   0 4px 16px rgba(29,78,216,0.06);
}
.si {
  width:          44px;
  height:         44px;
  border-radius:  12px;
  flex-shrink:    0;
  display:        flex;
  align-items:    center;
  justify-content: center;
  font-size:      18px;
}
.sv {
  font-size:   clamp(1.35rem, 3vw, 1.65rem);
  font-weight: 800;
  line-height: 1;
  color:       var(--bt);
  letter-spacing: -0.03em;
}
.sl {
  font-size:      12px;
  color:          var(--bs);
  font-weight:    600;
  margin-top:     6px;
  letter-spacing: .01em;
}
.sc-trend {
  font-size:   10px;
  color:       var(--bg);
  font-weight: 700;
  margin-top:  4px;
}

/* ─── Filter Bar ─────────────────────────────────────────────── */
.hw-filter-bar {
  background:    transparent;
  border:        none;
  border-radius: 0;
  box-shadow:    none;
  padding:       0;
}
.hw-panel .hw-filter-bar {
  padding: 0;
}
.hw-filter-bar .filter-label {
  font-size:      12px;
  font-weight:    600;
  text-transform: none;
  letter-spacing: 0;
  color:          var(--bt);
  margin-bottom:  10px;
  display:        block;
}
.hw-filter-bar .filter-label i {
  opacity: .85;
}
/* Target the visible widget (.nice-select), not only the hidden <select> class */
.hw-filter-bar .nice-select,
.hw-filter-bar .niceSelect {
  width:         100% !important;
  min-width:     0 !important;
  float:         none !important;
  clear:         both !important;
  display:       block !important;
  box-sizing:    border-box;
  font-size:     12.5px !important;
  border-radius: 10px !important;
  border-color:  var(--bb) !important;
  height:        40px !important;
  line-height:   40px !important;
}
.hw-filter-bar .nice-select .current,
.hw-filter-bar .niceSelect .current {
  display:       block;
  overflow:      hidden;
  text-overflow: ellipsis;
  white-space:   nowrap;
  max-width:     100%;
  padding-right: 4px;
}
.hw-filter-bar .nice-select.open,
.hw-filter-bar .nice-select:hover,
.hw-filter-bar .niceSelect.open,
.hw-filter-bar .niceSelect:hover { border-color: var(--bp) !important; }
.hw-filter-bar .nice-select .list,
.hw-filter-bar .niceSelect .list { width: 100% !important; border-radius: 10px !important; }
.hw-filter-bar .nice-select .list li.option,
.hw-filter-bar .niceSelect .list li.option { font-size: 12.5px !important; }

/* Keeps label row aligned; selects sit on one baseline via mt-auto wrappers */
.hw-filter-bar .hw-filter-field {
  min-height: 0;
}

/* ─── Proceed / Reset Buttons ────────────────────────────────── */
.btn-proceed {
  background:    var(--bp);
  color:         #fff;
  border:        none;
  border-radius: 10px;
  padding:       0 22px;
  height:        40px;
  font-size:     13px;
  font-weight:   700;
  letter-spacing: .02em;
  display:       inline-flex;
  align-items:   center;
  gap:           8px;
  cursor:        pointer;
  transition:    background .2s, box-shadow .2s, transform .15s;
  box-shadow:    0 2px 8px rgba(29,78,216,0.25);
}
.btn-proceed:hover {
  background:  var(--bpd);
  transform:   translateY(-1px);
  box-shadow:  0 6px 20px rgba(29,78,216,0.35);
  color:       #fff;
}
.btn-proceed:active { transform: translateY(0); }

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
  background:    #fafbfc;
  border:        1px solid #eef2f7;
  border-radius: var(--rr-lg);
  box-shadow:    none;
  padding:       1.35rem 1.5rem 1.5rem;
  height:        100%;
}
.cc-title {
  font-size:      1rem;
  font-weight:    700;
  color:          var(--bt);
  letter-spacing: -0.02em;
  margin-bottom:  6px;
  display:        flex;
  align-items:    center;
  gap:            8px;
}
.cc-sub {
  font-size:    12px;
  color:        var(--bs);
  line-height:  1.45;
  margin-bottom: 16px;
}

/* Fixed height so Chart.js can measure while parent is visibility-hidden during init */
.hw-chart-canvas-wrap {
  position: relative;
  width:    100%;
  height:   236px;
}

/* ─── Block headings inside results (no noisy full-width rules) ─ */
.hw-block-head {
  margin-bottom: 1.25rem;
}
.hw-block-head .hw-eyebrow {
  margin-bottom: .35rem;
}
.hw-block-title {
  font-size:   1.2rem;
  font-weight: 800;
  color:       var(--bt);
  letter-spacing: -0.03em;
  margin:      0;
  line-height: 1.25;
}
.hw-block-desc {
  font-size:   .875rem;
  color:       var(--bs);
  margin:      .4rem 0 0;
  max-width:   40rem;
  line-height: 1.5;
}

/* Legacy: table toolbar still uses results-section-label — restyle */
.results-section-label {
  font-size:      1rem;
  font-weight:    700;
  text-transform: none;
  letter-spacing: -0.02em;
  color:          var(--bt);
  margin-bottom:  0;
  display:        flex;
  align-items:    center;
  gap:            10px;
}
.results-section-label::after {
  display: none;
}

/* ─── Homework Table ─────────────────────────────────────────── */
.hw-table-card {
  background:    #fff;
  border:        var(--hw-border);
  border-radius: var(--rr-lg);
  box-shadow:    var(--shadow-soft);
  overflow:      hidden;
  margin-top:    2rem;
}

.ht {
  width:           100%;
  border-collapse: collapse;
  font-size:       13px;
  table-layout:    auto;
}

.ht thead th {
  background:     #f8fafc;
  padding:        14px 16px;
  font-size:      11px;
  font-weight:    700;
  text-transform: uppercase;
  letter-spacing: .06em;
  color:          #475569;
  border-bottom:  1px solid var(--tb-head);
  border-right:   none;
  white-space:    nowrap;
}
.ht thead th:last-child { border-right: none; }

.ht tbody td {
  padding:        14px 16px;
  border-bottom:  1px solid var(--tb-row);
  border-right:   none;
  vertical-align: middle;
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
a.ab {
  text-decoration: none;
  color: inherit;
}
.hw-act-cell {
  display: flex;
  gap: 6px;
  justify-content: center;
  align-items: center;
  flex-wrap: nowrap;
}

/* ─── Accordion / Collapse fix ────────────────────────────────── */
.sect-body {
  overflow:   hidden !important;
  transition: max-height 0.3s ease-in-out;
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

@media (max-width: 575.98px) {
  .hw-page-hero { padding: 1.35rem 1.25rem; }
  .hw-panel { padding: 1.35rem 1.25rem 1.5rem; }
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

  {{-- ── Hero: primary heading + short context (not buried in breadcrumb-only UI) ── --}}
  <header class="hw-page-hero">
    <div class="row align-items-start align-items-lg-center g-3">
      <div class="col-lg">
        <p class="hw-eyebrow">Teaching workspace</p>
        <h1 class="hw-page-title">Homework &amp; tasks</h1>
        <p class="hw-page-lead">
          See workload at a glance, filter by class and subject, then open analytics or grade submissions.
        </p>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Homework &amp; tasks</li>
          </ol>
        </nav>
      </div>
      @if(hasPermission('homework_create'))
      <div class="col-lg-auto">
        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
          <a href="{{ route('homework.download-sample') }}"
             class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa-solid fa-download me-1"></i>CSV template
          </a>
          <a href="{{ route('homework.create') }}" class="btn ot-btn-primary btn-sm px-3">
            <i class="fa-solid fa-plus me-1"></i>New task
          </a>
        </div>
      </div>
      @endif
    </div>
  </header>

  {{-- SECTION 1 · Overview metrics — grouped panel -------------------------------- --}}
  <section class="hw-panel hw-section" id="stats-cards-container">
    <header class="hw-panel-head">
      <p class="hw-eyebrow">Overview</p>
      <h2 class="hw-panel-title">School-wide snapshot</h2>
      <p class="hw-panel-desc">Current session totals. These update when the page loads.</p>
    </header>
    <div class="row g-3 g-lg-4">

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
  </section>


  {{-- SECTION 2 · Filters — same visual weight as overview -------------------------------- --}}
  <section class="hw-panel hw-section">
    <div class="hw-filter-bar">
      <header class="hw-filter-intro">
        <p class="hw-eyebrow">Report builder</p>
        <h2 class="hw-panel-title">Filter your data</h2>
        <p class="hw-panel-desc mb-0">Choose class, section, and subject, then run the report. Task type is optional.</p>
      </header>

      {{-- Dropdown row: labels share one baseline; controls share one baseline (mt-auto per column) --}}
      <div class="row g-2 align-items-stretch">

        {{-- Class (col-md-2) --}}
        <div class="col-md-2 col-sm-6 d-flex">
          <div class="hw-filter-field d-flex flex-column w-100">
            <label class="filter-label" for="filter-class">
              <i class="fa-solid fa-school me-1" style="color:var(--bp)"></i>Class
            </label>
            <div class="mt-auto pt-0">
              <select id="filter-class" class="niceSelect bordered_style">
                <option value="">All Classes</option>
                @foreach($data['classes'] ?? [] as $item)
                  @if(!empty($item->class))
                    <option value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- Section (col-md-2) --}}
        <div class="col-md-2 col-sm-6 d-flex">
          <div class="hw-filter-field d-flex flex-column w-100">
            <label class="filter-label" for="filter-section">
              <i class="fa-solid fa-layer-group me-1" style="color:var(--bp)"></i>Section
            </label>
            <div class="mt-auto pt-0">
              <select id="filter-section" class="niceSelect bordered_style">
                <option value="">All Sections</option>
              </select>
            </div>
          </div>
        </div>

        {{-- Subject (col-md-2) --}}
        <div class="col-md-2 col-sm-6 d-flex">
          <div class="hw-filter-field d-flex flex-column w-100">
            <label class="filter-label" for="filter-subject">
              <i class="fa-solid fa-book me-1" style="color:var(--bp)"></i>Subject
            </label>
            <div class="mt-auto pt-0">
              <select id="filter-subject" class="niceSelect bordered_style">
                <option value="">All Subjects</option>
              </select>
            </div>
          </div>
        </div>

        {{-- Task Type (col-md-2) --}}
        <div class="col-md-2 col-sm-6 d-flex">
          <div class="hw-filter-field d-flex flex-column w-100">
            <label class="filter-label" for="filter-task-type">
              <i class="fa-solid fa-tags me-1" style="color:var(--bp)"></i>Task Type
            </label>
            <div class="mt-auto pt-0">
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
          </div>
        </div>

        {{-- Spacer col to push buttons right (col-md-2 + col-md-2 = 4 cols spare) --}}
        <div class="col-md-2 d-none d-md-block"></div>

        {{-- Proceed + Reset — spacer matches label height so buttons align with selects --}}
        <div class="col-md-2 col-sm-12 d-flex">
          <div class="hw-filter-field d-flex flex-column w-100 align-items-stretch">
            <div class="filter-label invisible user-select-none" aria-hidden="true">
              <i class="fa-solid fa-tags me-1"></i>Actions
            </div>
            <div class="mt-auto d-flex gap-2 justify-content-end flex-wrap">
              <button type="button" class="btn-proceed" id="proceed-btn">
                <i class="fa-solid fa-arrow-right"></i>
                Proceed
              </button>
              <button type="button" class="btn-reset" id="reset-filters-btn">
                <i class="fa-solid fa-rotate-left"></i>
              </button>
            </div>
          </div>
        </div>

      </div>{{-- /row --}}
    </div>{{-- /hw-filter-bar --}}
  </section>


  {{-- =================================================================
       SECTION 3 · RESULTS AREA  (hidden on page load — revealed via
       jQuery .fadeIn() when Proceed is clicked)
       margin-bottom: 4rem
  ================================================================= --}}
  <div class="hw-section" id="results-container" style="display:none">

    <section class="hw-panel hw-results-panel">

    <div class="hw-block-head mb-4">
      <p class="hw-eyebrow">Results</p>
      <h2 class="hw-block-title">Analytics &amp; task list</h2>
      <p class="hw-block-desc">Charts reflect your filters. The table lists matching homework rows.</p>
    </div>

    <div class="row g-3 g-lg-4 mb-0">

      {{-- Donut Chart · col-md-6 --}}
      <div class="col-md-6">
        <div class="cc">
          <div class="cc-title">
            <i class="fa-solid fa-chart-pie me-1" style="color:var(--bp)"></i>
            Overall Task Status
          </div>
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

    {{-- ── Homework Table ────────── --}}
    <div class="hw-table-card">

      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-4 py-3"
           style="border-bottom:1px solid #f1f5f9;background:#fafbfc">
        <div class="results-section-label mb-0 flex-grow-1 me-2">
          <span class="d-inline-flex align-items-center justify-content-center rounded-2 me-2"
                style="width:36px;height:36px;background:var(--bpl);color:var(--bp)">
            <i class="fa-solid fa-table-list" style="font-size:14px"></i>
          </span>
          <span>Matching tasks</span>
        </div>
        <span id="table-count-badge"
              class="badge rounded-pill px-3 py-2"
              style="background:var(--bpl);color:var(--bp);font-size:11px;font-weight:700;white-space:nowrap;border:1px solid rgba(29,78,216,0.15)">
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
              <th style="white-space:nowrap;min-width:88px">Act.</th>
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

    </section>

  </div>{{-- /SECTION 3 · results-container --}}

</div>{{-- /page-content hw-portal --}}


{{-- ============================================================
     MODALS
     ============================================================ --}}

{{-- ── Evaluation Modal (#mEv): openEval() shows modal + POST homework/students loads evaluation.blade.php into #ev-body ── --}}
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

  var modalEl = document.getElementById('mEv');
  if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
  } else if (modalEl && typeof $ !== 'undefined' && $.fn.modal) {
    $(modalEl).modal('show');
  }

  $.post(
    $('#url').val() + '/homework/students',
    { homework_id: id, _token: $('meta[name="csrf-token"]').attr('content') },
    function (d) {
      if (d && d.view) {
        $('#ev-body').html(d.view);
      } else {
        $('#ev-body').html('<p class="text-danger p-3">Unexpected response.</p>');
      }
    }
  ).fail(function () {
    $('#ev-body').html('<p class="text-danger p-3">Failed to load evaluation.</p>');
  });
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
