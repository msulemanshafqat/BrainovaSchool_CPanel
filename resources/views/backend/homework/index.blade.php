{{-- ============================================================
     TEMPLATE INHERITANCE
     Extends the main backend layout (master.blade.php).
     Sets the browser tab title dynamically from $data['title'].
     ============================================================ --}}
@extends('backend.master')
@section('title') {{ @$data['title'] }} @endsection

{{-- ============================================================
     PAGE-SPECIFIC CSS (pushed into the <head> via @stack('css'))
     Scoped to this page only via .hw-portal wrapper.
     ============================================================ --}}
@push('css')
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ global_asset('backend/assets/css/homework-gamified.css') }}">
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
        <p class="hw-eyebrow">Mission control</p>
        <h1 class="hw-page-title">Homework quest board</h1>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
          <span class="hw-badge-level"><i class="fa-solid fa-trophy"></i> Level up your class</span>
        </div>
        <p class="hw-page-lead">
          Track quests (tasks), loot (submissions), and boss fights (grading)—filter your arena, then chart the win.
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
      <p class="hw-eyebrow">Player stats</p>
      <h2 class="hw-panel-title">Power-ups &amp; progress</h2>
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
            <div class="sl">Active quests</div>
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
            <div class="sl">Turned in</div>
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
            <div class="sl">Awaiting score</div>
          </div>
        </div>
      </div>

      {{-- Card 4 · cumulative score (uses config('brainova.e6_multiplier') server-side) --}}
      <div class="col-md-6 col-lg-3">
        <div class="sc">
          <div class="si" style="background: linear-gradient(135deg,#ede9fe,#ddd6fe);">
            <i class="fa-solid fa-star-half-stroke" style="color:#7c3aed"></i>
          </div>
          <div style="flex:1">
            <div class="sv" id="stat-score"><span class="stat-loading"></span></div>
            <div class="sl">Glory points</div>
          </div>
        </div>
      </div>

    </div>
    <div class="hw-xp-wrap">
      <div class="hw-xp-head">
        <span class="hw-xp-title"><i class="fa-solid fa-bolt"></i> Engagement XP</span>
        <span class="hw-xp-label" id="hw-xp-label">Loading…</span>
      </div>
      <div class="hw-xp-track" aria-hidden="true">
        <div class="hw-xp-fill" id="hw-xp-fill"></div>
      </div>
    </div>
  </section>


  {{-- SECTION 2 · Filters — same visual weight as overview -------------------------------- --}}
  <section class="hw-panel hw-section">
    <div class="hw-filter-bar">
      <header class="hw-filter-intro">
        <h2 class="hw-panel-title">Choose your arena</h2>
        <p class="hw-panel-desc mb-0">Pick class, section, and subject—optional task type—then hit <strong>Deploy</strong> to load charts and the quest log.</p>
      </header>

      {{-- Filters left / Deploy + Reset flush right (within .hw-panel / .page-content) --}}
      <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-end gap-3 hw-filter-toolbar-row">
        <div class="flex-grow-1 min-w-0 mw-100">
          <div class="row g-2">
        {{-- Class --}}
        <div class="col-6 col-md-6 col-xl-3">
          <div class="hw-filter-field">
            <label class="filter-label" for="filter-class">
              <i class="fa-solid fa-school me-1" style="color:var(--bp)"></i>Class
            </label>
            <select id="filter-class" class="form-select form-select-sm hw-select" autocomplete="off">
                <option value="">All Classes</option>
                @foreach($data['classes'] ?? [] as $item)
                  @if(!empty($item->class))
                    <option value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                  @endif
                @endforeach
              </select>
          </div>
        </div>

        {{-- Section --}}
        <div class="col-6 col-md-6 col-xl-3">
          <div class="hw-filter-field">
            <label class="filter-label" for="filter-section">
              <i class="fa-solid fa-layer-group me-1" style="color:var(--bp)"></i>Section
            </label>
            <select id="filter-section" class="form-select form-select-sm hw-select" autocomplete="off">
                <option value="">All Sections</option>
              </select>
          </div>
        </div>

        {{-- Subject --}}
        <div class="col-6 col-md-6 col-xl-3">
          <div class="hw-filter-field">
            <label class="filter-label" for="filter-subject">
              <i class="fa-solid fa-book me-1" style="color:var(--bp)"></i>Subject
            </label>
            <select id="filter-subject" class="form-select form-select-sm hw-select" autocomplete="off">
                <option value="">All Subjects</option>
              </select>
          </div>
        </div>

        {{-- Task Type --}}
        <div class="col-6 col-md-6 col-xl-3">
          <div class="hw-filter-field">
            <label class="filter-label" for="filter-task-type">
              <i class="fa-solid fa-tags me-1" style="color:var(--bp)"></i>Task Type
            </label>
            <select id="filter-task-type" class="form-select form-select-sm hw-select" autocomplete="off">
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
          </div>{{-- /inner row --}}
        </div>

        <div class="flex-shrink-0 ms-lg-auto align-self-stretch align-self-lg-end">
          <div class="hw-filter-actions">
              <button type="button" class="btn-proceed" id="proceed-btn">
                <i class="fa-solid fa-rocket"></i>
                Deploy
              </button>
              <button type="button" class="btn-reset" id="reset-filters-btn" title="Reset filters">
                <i class="fa-solid fa-rotate-left"></i>
              </button>
          </div>
        </div>

      </div>{{-- /hw-filter-toolbar-row --}}
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
      <p class="hw-eyebrow">Loot drop</p>
      <h2 class="hw-block-title">Scoreboards &amp; quest log</h2>
      <p class="hw-block-desc">Charts show how your filtered quests are going; the table is your live mission list.</p>
    </div>

    <div class="row g-3 g-lg-4 mb-0">

      {{-- Donut Chart · col-md-6 --}}
      <div class="col-md-6">
        <div class="cc">
          <div class="cc-title">
            <i class="fa-solid fa-chart-pie me-1" style="color:var(--bp)"></i>
            Quest status radar
          </div>
          <div class="hw-chart-canvas-wrap"><canvas id="donut-chart-filtered"></canvas></div>
        </div>
      </div>

      {{-- Line Chart · col-md-6 --}}
      <div class="col-md-6">
        <div class="cc">
          <div class="cc-title">
            <i class="fa-solid fa-chart-line me-1" style="color:var(--bp)"></i>
            Power curve
          </div>
          <div class="cc-sub" style="color:rgba(199,210,254,0.85)">Blue = graded average · Gray = % of class submitted</div>
          <div class="hw-chart-canvas-wrap"><canvas id="line-chart-filtered"></canvas></div>
        </div>
      </div>

    </div>{{-- /charts row --}}

    {{-- ── Homework Table ────────── --}}
    <div class="hw-table-card">

      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-4 py-3 hw-table-toolbar">
        <div class="results-section-label mb-0 flex-grow-1 me-2">
          <span class="d-inline-flex align-items-center justify-content-center rounded-2 me-2"
                style="width:36px;height:36px;background:rgba(34,211,238,0.2);color:#22d3ee;border:1px solid rgba(167,139,250,0.4)">
            <i class="fa-solid fa-scroll" style="font-size:14px"></i>
          </span>
          <span>Quest log</span>
        </div>
        <span id="table-count-badge"
              class="badge rounded-pill px-3 py-2">
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
              <td colspan="8" class="hw-empty">
                <i class="fa-solid fa-wand-magic-sparkles me-2" style="opacity:.65"></i>
                Choose filters and tap <strong>Deploy</strong> to load your quest log.
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
      .append('<option value="">All Sections</option>');
    $('#filter-subject').val('').empty()
      .append('<option value="">All Subjects</option>');

    if (!classId) return;
    loadSectionsByClass(classId);
  });

  $('#filter-section').on('change', function () {
    const classId   = $('#filter-class').val();
    const sectionId = $(this).val();

    $('#filter-subject').val('').empty()
      .append('<option value="">All Subjects</option>');

    if (!classId || !sectionId) return;
    loadSubjectsBySection(classId, sectionId);
  });

  /* native selects (no niceSelect) */
  $('#proceed-btn').on('click', function () {
    getFilteredReport();
  });

  /* ── Reset button naz ── */
  $('#reset-filters-btn').on('click', function () {
    $('#filter-class').val('');
    $('#filter-section').empty().append('<option value="">All Sections</option>');
    $('#filter-subject').empty().append('<option value="">All Subjects</option>');
    $('#filter-task-type').val('all');
    if (donutChartInstance) { donutChartInstance.destroy(); donutChartInstance = null; }
    if (lineChartInstance)  { lineChartInstance.destroy();  lineChartInstance  = null; }
    $('#results-container').stop(true, true).hide().css({ visibility: '', opacity: '' });
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
          var t = parseInt(s.total_tasks_assigned, 10) || 0;
          var sub = parseInt(s.total_submitted, 10) || 0;
          var pct = t > 0 ? Math.min(100, Math.round((sub / t) * 100)) : 0;
          $('#hw-xp-fill').css('width', pct + '%');
          $('#hw-xp-label').text(
            t > 0
              ? pct + '% submitted · ' + sub + ' / ' + t + ' quests cleared'
              : 'Deploy filters to start earning XP'
          );
        }
      },
      error: function () {
        ['#stat-total-tasks','#stat-submitted','#stat-pending','#stat-score']
          .forEach(function (sel) { $(sel).text('—'); });
        $('#hw-xp-fill').css('width', '0%');
        $('#hw-xp-label').text('Stats unavailable');
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
                  borderColor: '#312e81',
                  hoverOffset: 4
                }]
              },
              options: {
                cutout: '65%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                  legend: {
                    position: 'bottom',
                    labels: { font: { size: 11, family: 'Fredoka, Plus Jakarta Sans, sans-serif' }, padding: 14, usePointStyle: true, color: '#cbd5e1' }
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
              x: { grid: { display: false, color: 'rgba(148,163,184,0.15)' }, ticks: { font: { size: 10, family: 'Plus Jakarta Sans' }, color: '#94a3b8' } },
              y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                grid: { color: 'rgba(148,163,184,0.12)' },
                ticks: { font: { size: 10, family: 'Plus Jakarta Sans' }, color: '#94a3b8' },
                title: {
                  display: true,
                  font: { size: 10, weight: '600', family: 'Fredoka, sans-serif' },
                  text: td.y_left_title || 'Score',
                  color: '#a5b4fc'
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
                ticks: { font: { size: 10, family: 'Plus Jakarta Sans' }, color: '#94a3b8' },
                title: {
                  display: true,
                  font: { size: 10, weight: '600', family: 'Fredoka, sans-serif' },
                  text: td.y_right_title || '% submitted',
                  color: '#a5b4fc'
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
                    pointRadius: 3,
                    pointBackgroundColor: bc,
                    pointHoverRadius: 4
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

          $results.css({ visibility: 'visible', opacity: 1 });
          window.requestAnimationFrame(function () {
            if (donutChartInstance && typeof donutChartInstance.resize === 'function') donutChartInstance.resize();
            if (lineChartInstance && typeof lineChartInstance.resize === 'function') lineChartInstance.resize();
          });
        }
      },
      error: function (xhr) {
        console.error('Failed to load filtered report:', xhr);
        alert('Error loading report. Please check your filters and try again.');
      },
      complete: function () {
        $btn.html('<i class="fa-solid fa-rocket"></i> Deploy').prop('disabled', false);
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
