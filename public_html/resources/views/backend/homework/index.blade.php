{{-- ============================================================
     mod by nazish shafqat
     TEMPLATE INHERITANCE
     Extends the main backend layout (master.blade.php).
     Sets the browser tab title dynamically from $data['title'].
     ============================================================ --}}
@extends('backend.master')
{{-- =thinking again--}}
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
    overflow: hidden !important; /* Hides the table when the container shrinks */
    transition: max-height 0.3s ease-in-out; /* Ensures the open/close is a smooth slide */
}
/* END: Accordion Collapse Fix */
{{-- 1. Font family applied to the whole portal wrapper --}}
.hw-portal{font-family:'Plus Jakarta Sans',sans-serif}

{{-- ── CSS Custom Properties (Design Tokens) ──
     --bp  = brand primary (blue)       --bpl = primary light tint
     --ba  = amber/warning              --bal = amber light tint
     --bg  = green/success              --bgl = green light tint
     --br  = red/danger                 --brl = red light tint
     --bs  = slate/muted text           --bb  = border base color
     --bt  = body text (dark navy)      --rr  = global border-radius
     Table-specific border tokens follow so table colours are never
     inherited from elsewhere accidentally.
--}}
:root{
  --bp:#1d4ed8;--bpl:#dbeafe;--ba:#f59e0b;--bal:#fef3c7;
  --bg:#059669;--bgl:#d1fae5;--br:#dc2626;--brl:#fee2e2;
  --bs:#64748b;--bb:#e2e8f0;--bt:#0f172a;--rr:12px;
  /* Table border tokens — explicit so nothing inherits ambiguously */
  --tb-outer: #c7d2e0;   /* outer wrap border  */
  --tb-head:  #b8c5d6;   /* header bottom line */
  --tb-row:   #e2e8f0;   /* between data rows  */
  --tb-col:   #e8edf5;   /* between columns    */
}

{{-- ── Stat Cards (.sc) ──
     White cards with an icon box (.si), a big number (.sv),
     and a small uppercase label (.sl).
     Lift slightly on hover for interactivity feel.
--}}
/* ── Stat cards ── */
.sc{background:#fff;border-radius:var(--rr);border:1px solid var(--bb);padding:18px 20px;display:flex;align-items:center;gap:14px;transition:transform .15s,box-shadow .15s}
.sc:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.07)}
.si{width:46px;height:46px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:19px}
.sv{font-size:26px;font-weight:800;line-height:1;color:var(--bt)}
.sl{font-size:11px;color:var(--bs);font-weight:600;margin-top:2px;text-transform:uppercase;letter-spacing:.04em}

{{-- ── Chart Cards (.cc) ──
     Reusable container for Chart.js canvases and sidebar widgets.
--}}
/* ── Chart cards ── */
.cc{background:#fff;border:1px solid var(--bb);border-radius:var(--rr);padding:20px}
.cc h6{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--bs);font-weight:700;margin-bottom:14px}

{{-- ── Sticky Filter Bar ──
     Sticks to the top of the scroll container so the user can
     always change class / section / subject / type filters.
--}}
/* ── Sticky filter bar ── */
.hw-filter-sticky{position:sticky;top:0;z-index:20;background:#f8fafc;padding:10px 0;margin-bottom:12px;border-bottom:1px solid var(--bb)}
/* Force nice-select generated <div> to fill the flex wrapper div.
   Without this, the plugin renders its own fixed-width div and ignores the parent's flex:1. */
.hw-filter-sticky .nice-select{width:100%!important;min-width:0!important;box-sizing:border-box}
/* Force nice-select generated divs to fill their wrapper div completely.
   nice-select replaces <select> with a <div class="nice-select">.
   Without this, that div uses its own internal default width. */
.hw-filter-sticky .nice-select{width:100% !important;box-sizing:border-box;font-size:12px !important}
.hw-filter-sticky .nice-select .list{width:100% !important}

{{-- ── Table Wrapper (.hw-wrap) ──
     Clips rounded corners on the table and draws the outer border.
     border-collapse:collapse inside needs overflow:hidden here
     so the rounded corners still show on the wrapper.
--}}
/* ── Table wrapper ── */
.hw-wrap{
  border-radius:var(--rr);
  overflow:hidden;
  border:2px solid var(--tb-outer);   /* solid outer frame — was 1px, now 2px */
  background:#fff;
  margin-bottom:0;
  box-shadow:0 1px 4px rgba(0,0,0,.06);
}

{{-- ── Base Table (.ht) ──
     border-collapse:collapse is required for column border lines to work.
     table-layout:auto lets the browser size columns to content.
--}}
/* ── Table base ── */
.ht{
  width:100%;
  border-collapse:collapse;           /* must be collapse for column borders to show */
  font-size:13.5px;
  table-layout:auto;
}

{{-- ── Table Header Row ──
     Light blue-grey background, thick bottom border separates
     header from data rows, thin right borders between columns.
--}}
/* ── Header row ── */
.ht thead th{
  background:#f0f4fa;                 /* slightly stronger than body for contrast */
  padding:11px 13px;
  font-size:10.5px;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:.07em;
  color:var(--bs);
  border-bottom:2px solid var(--tb-head);   /* thick bottom line under header */
  border-right:1px solid var(--tb-col);     /* column separator */
  white-space:nowrap;
}
.ht thead th:last-child{border-right:none}

{{-- ── Table Data Cells ──
     Every cell gets a bottom and right border for a full grid look.
     Last column has no right border to avoid double-edge with wrapper.
--}}
/* ── Data rows ── */
.ht tbody td{
  padding:11px 13px;
  border-bottom:1px solid var(--tb-row);    /* every row has a visible bottom border */
  border-right:1px solid var(--tb-col);     /* every cell has a right column separator */
  vertical-align:middle;
  background:#fff;
}
.ht tbody td:last-child{border-right:none}

{{-- ── Row Alternating & Hover States ──
     Even rows get a faint blue tint; last row drops its bottom border
     so it doesn't double with the wrapper border.
     Hover overrides all backgrounds with !important.
--}}
/* ── Alternating row tint ── */
.ht tbody tr:nth-child(even) td{background:#fafbff}
.ht tbody tr:last-child td{border-bottom:none}
/* ── Hover ── */
.ht tbody tr:hover td{background:#eff4ff !important;}

{{-- .hh = "hidden by filter" — JS toggles this class on rows
     that don't match the active type filter pill. --}}
.ht tr.hh{display:none!important}

{{-- ── Type Badges (.tbg) ──
     Coloured pill labels (quiz, homework, project, etc.)
     Each type gets its own background/text colour pair.
--}}
/* ── Type badges ── */
.tbg{display:inline-block;padding:2px 9px;border-radius:20px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
.tbg-quiz      {background:#dbeafe;color:#1e40af}
.tbg-hw        {background:#d1fae5;color:#065f46}
.tbg-project   {background:#fce7f3;color:#9d174d}
.tbg-activity  {background:#ede9fe;color:#5b21b6}
.tbg-game      {background:#fef3c7;color:#92400e}
.tbg-assignment{background:#e0f2fe;color:#075985}

{{-- .od = overdue indicator — red pill shown next to a late due date --}}
.od{background:#fee2e2;color:#dc2626;border-radius:4px;padding:1px 6px;font-size:10.5px;font-weight:700}

{{-- ── Filter Pills (.fb) ──
     Rounded toggle buttons for task-type filtering (All / Quiz / HW …).
     .on / :hover both apply the active (blue) style.
--}}
/* ── Filter pills ── */
.fb{padding:5px 13px;border-radius:20px;font-size:11.5px;font-weight:700;border:1.5px solid var(--bb);background:#fff;cursor:pointer;color:var(--bs);transition:all .15s}
.fb.on,.fb:hover{background:var(--bp);border-color:var(--bp);color:#fff}

{{-- ── Action Button (.ab) ──
     Small square icon button (⋮ menu trigger) that turns blue on hover.
--}}
/* ── Action button ── */
.ab{background:none;border:1.5px solid var(--bb);border-radius:6px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--bs);font-size:12px;transition:all .15s}
.ab:hover{border-color:var(--bp);color:var(--bp);background:var(--bpl)}

{{-- ── Collapsible Section Headers (.sect-hdr) ──
     Clickable bar that collapses/expands the table below it.
     margin-top:16px creates a visual gap between stacked tables.
     .caret rotates -90deg when the section is collapsed.
--}}
/* ── Collapsible section headers ──
   Clear gap above each section so it separates visually from the table above it */
.sect-hdr{
  display:flex;align-items:center;gap:10px;
  padding:9px 14px;
  border-radius:8px;
  cursor:pointer;
  margin-top:16px;     /* breathing room between table bottom and next section */
  margin-bottom:6px;
  font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
  user-select:none;
  border:1.5px solid var(--bb);
  background:#f8fafc;
}
.sect-hdr:hover{background:#f1f5f9}
.sect-hdr .sect-cnt{margin-left:auto;font-size:11px;font-weight:600;background:#fff;border-radius:10px;padding:1px 8px;border:1px solid var(--bb)}
.sect-hdr .caret{transition:transform .2s;font-size:10px;margin-left:6px}
.sect-hdr.collapsed .caret{transform:rotate(-90deg)}

{{-- max-height animation drives the open/close accordion effect --}}
.sect-body{overflow:hidden;transition:max-height .3s ease}

{{-- ── Alert Bar ──
     Red info strip shown at the top when pending evaluations exist.
--}}
/* ── Alert bar ── */
.alert-bar{background:var(--brl);border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;font-size:12.5px;color:var(--br);display:flex;align-items:center;gap:8px;margin-bottom:12px}
</style>
@endpush

{{-- ============================================================
     MAIN PAGE CONTENT
     ============================================================ --}}
@section('content')
<div class="page-content hw-portal">

{{-- Hidden input stores the app's base URL so JavaScript can
     build AJAX endpoint URLs without hard-coding the domain. --}}
<input type="hidden" id="url" value="{{ url('/') }}">

{{-- ── Page Header ──
     Left side: page title + breadcrumb trail.
     Right side: "New Task" button (only shown if user has homework_create permission).
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

{{-- ── PHP Variable Extraction ──
     Pull individual stat values out of the $data array for
     cleaner use in the template below.
     $rate = submission percentage (0–100), calculated here.
--}}
@php
  $stats=$data['stats']??[];$total=$stats['total']??0;$quizzes=$stats['quizzes']??0;
  $subm=$stats['submitted']??0;$pend=$stats['pending_eval']??0;$byType=$stats['by_type']??[];
  $rate=$total>0?round(($subm/$total)*100):0;
@endphp

{{-- ── Per-Class Score Line Chart Data Prep ──
     Groups all homeworks by class name.
     Each class becomes one line; X positions are task numbers 1,2,3…
     Marks value per homework = the Y point for that class's line.
     Shorter class arrays are padded with null so Chart.js draws gaps.
--}}
@php
  $lineColors  = ['#2563eb','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899','#f97316'];
  $classGroups = ($data['homeworks'] ?? collect())->groupBy(fn($r) => ($r->class->name ?? 'Unknown'));
  $maxHw       = $classGroups->max(fn($g) => $g->count()) ?: 1;
  $lineLabels  = array_map(fn($i) => '#'.$i, range(1, $maxHw));
  $lineDatasets = [];
  $lci = 0;
  foreach($classGroups as $className => $items){
    $marks = $items->values()->map(fn($h) => $h->marks)->toArray();
    while(count($marks) < $maxHw) $marks[] = null;
    $lineDatasets[] = ['label'=>$className, 'data'=>$marks, 'color'=>$lineColors[$lci % count($lineColors)]];
    $lci++;
  }
@endphp


    {{-- Full-width card above the table. Shows task counts by category
     (Total, Quizzes, Submissions, Pending Eval, Overdue) as a bar chart.
     Alert for pending eval is embedded as a slim strip inside the card.
--}}
<div style="background:#fff;border:1.5px solid #d1dae8;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.07);padding:20px 24px;margin-bottom:20px">

  {{-- Card header row: title left, alert pill right --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px">
    <div>
      <div style="font-size:14px;font-weight:800;color:#0f172a;letter-spacing:-.01em">Overview</div>
      <div style="font-size:11px;color:#64748b;margin-top:2px">Spring 2026 · All Classes</div>
    </div>
    @if($pend>0)
    <div style="display:inline-flex;align-items:center;gap:7px;background:#fff7ed;border:1.5px solid #fed7aa;border-radius:20px;padding:6px 14px;font-size:12px;font-weight:600;color:#9a3412">
      <i class="fa-solid fa-triangle-exclamation" style="font-size:11px"></i>
      {{$pend}} submission{{ $pend>1?'s':'' }} awaiting evaluation
    </div>
    @endif
  </div>

  {{-- Bar chart + stat legend side by side --}}
  <div style="display:flex;align-items:stretch;gap:24px;flex-wrap:wrap">

    {{-- Bar chart canvas --}}
    <div style="flex:1;min-width:260px;position:relative;height:160px">
      <canvas id="hwBarChart"></canvas>
    </div>

    {{-- Stat legend: 4 numbers aligned vertically --}}
    <div style="display:flex;flex-direction:column;justify-content:space-around;gap:8px;min-width:150px">
      <div style="display:flex;align-items:center;gap:10px">
        <span style="width:12px;height:12px;border-radius:3px;background:#1d4ed8;flex-shrink:0;display:inline-block"></span>
        <span style="font-size:11px;color:#64748b;flex:1">Total Tasks</span>
        <span style="font-size:16px;font-weight:800;color:#0f172a">{{$total}}</span>
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <span style="width:12px;height:12px;border-radius:3px;background:#f59e0b;flex-shrink:0;display:inline-block"></span>
        <span style="font-size:11px;color:#64748b;flex:1">Quizzes</span>
        <span style="font-size:16px;font-weight:800;color:#0f172a">{{$quizzes}}</span>
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <span style="width:12px;height:12px;border-radius:3px;background:#10b981;flex-shrink:0;display:inline-block"></span>
        <span style="font-size:11px;color:#64748b;flex:1">Submissions</span>
        <span style="font-size:16px;font-weight:800;color:#0f172a">{{$subm}}</span>
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <span style="width:12px;height:12px;border-radius:3px;background:#ef4444;flex-shrink:0;display:inline-block"></span>
        <span style="font-size:11px;color:#64748b;flex:1">Pending Eval</span>
        <span style="font-size:16px;font-weight:800;color:#0f172a">{{$pend}}</span>
      </div>
    </div>

  </div>
</div>

{{-- ── Two-Column Layout ──
     Left  (col-lg-8): filter bar + collapsible task tables + pagination
     Right (col-lg-4): charts sidebar (donut chart, submission rate bar, quick actions)
--}}
<div class="row g-3">
  <div class="col-lg-8">

    {{-- STICKY FILTER BAR (Tier 1) --}}
    {{-- ── Sticky Filter Bar ──
         Three <select> dropdowns (Class → Section → Subject) that each
         auto-submit the form on change to server-side filter the results.
         Type-filter pills (All / Quiz / HW …) work client-side via JS —
         they do NOT submit the form, they just show/hide rows instantly.
    --}}
    <div class="hw-filter-sticky">
      <form action="{{ route('homework.search') }}" method="post" style="display:flex;align-items:center;gap:6px;width:100%" id="hf">
        @csrf
        {{-- Each wrapper is 1/4 of the bar width. No flex-wrap so all 4 stay on one line.
             min-width:0 prevents flex children from overflowing their container. --}}
        <div style="flex:1;min-width:0">
          <select name="class" id="getSections" class="nice-select niceSelect sections bordered_style" style="width:100%;font-size:12px" onchange="this.form.submit()">
            <option value="">All Classes</option>
            @foreach($data['classes']??[] as $item)
              @if(!empty($item->class))
              <option value="{{$item->class->id}}" {{request('class')==$item->class->id?'selected':''}}>{{$item->class->name}}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div style="flex:1;min-width:0">
          <select name="section" id="getSubjects" class="nice-select niceSelect sections bordered_style" style="width:100%;font-size:12px" onchange="this.form.submit()">
            <option value="">All Sections</option>
          </select>
        </div>
        <div style="flex:1;min-width:0">
          <select name="subject" id="subject" class="nice-select niceSelect subjects bordered_style" style="width:100%;font-size:12px" onchange="this.form.submit()">
            <option value="">All Subjects</option>
          </select>
        </div>
        <div style="flex:1;min-width:0">
          <select id="typeFilter" name="task_type" class="nice-select niceSelect bordered_style" style="width:100%;font-size:12px">
            <option value="all">All Types</option>
            <option value="quiz">Quiz</option>
            <option value="homework">Homework</option>
            <option value="project">Project</option>
            <option value="activity">Activity</option>
            <option value="game">Game</option>
            <option value="assignment">Assignment</option>
          </select>
        </div>
      </form>
    </div>

    {{-- COLLAPSIBLE SECTIONS: Active → Overdue → Completed (Tier 1) --}}
    {{-- ── Section Data Preparation ──
         Split the paginated homework collection into two groups:
         • $hwActive  — tasks whose due date is today or in the future
         • $hwOverdue — tasks whose due date has already passed
         Both groups are stored in the $sections array so the same
         Blade loop can render both tables with different colours/icons.
    --}}
    @php
      $hwActive   = $data['homeworks']->filter(fn($r)=>!$r->submission_date||!\Carbon\Carbon::parse($r->submission_date)->isPast());
      $hwOverdue  = $data['homeworks']->filter(fn($r)=>$r->submission_date&&\Carbon\Carbon::parse($r->submission_date)->isPast());
      $sections   = [
        ['id'=>'sec-active',  'label'=>'Active Tasks',  'color'=>'var(--bg)',  'icon'=>'bolt',         'rows'=>$hwActive],
        ['id'=>'sec-overdue', 'label'=>'Overdue',       'color'=>'var(--br)',  'icon'=>'circle-exclamation', 'rows'=>$hwOverdue],
      ];
    @endphp

    {{-- ── Collapsible Section Loop ──
         Iterates over $sections (Active, Overdue).
         Skips a section entirely if it has no rows to show.
         Each section renders:
           1. A clickable header bar (.sect-hdr) with icon + label + count badge
           2. A collapsible body (.sect-body) containing the data table
    --}}
    @foreach($sections as $sec)
    @if($sec['rows']->count()>0)
    <div class="sect-hdr" onclick="toggleSect('{{$sec['id']}}',this)" style="color:{{$sec['color']}}">
      <i class="fa-solid fa-{{$sec['icon']}} me-2"></i>
      {{$sec['label']}}
      <span class="sect-cnt">{{$sec['rows']->count()}}</span>
      <span class="caret ms-1"><i class="fa-solid fa-chevron-down"></i></span>
    </div>
    <div class="sect-body" id="{{$sec['id']}}">
    {{-- Outer wrapper: 2px border + shadow so the table has a clear visible frame --}}
    <div style="border-radius:10px;overflow:hidden;border:2px solid #b8c5d6;box-shadow:0 2px 8px rgba(0,0,0,.08);margin-bottom:12px;background:#fff">
      {{--
        TABLE DESIGN: 6 columns only (reduced from 8).
        Subject merged into Class/Section cell.
        Type badge shown inside the Title cell.
        Title truncated with ellipsis + tooltip — no vertical text wrapping.
        table-layout:auto so browser sizes columns naturally.
      --}}
      {{-- ── Task Data Table ──
           5-column table: # | Title | Due Date | Marks | Actions
           data-section attribute links rows to their parent section for JS toggling.
           .hw-tbl-section class is used by the type-filter JS to find all rows.
      --}}
      <table style="width:100%;border-collapse:collapse;font-size:13px;table-layout:auto" data-section="{{$sec['id']}}" class="hw-tbl-section">
        <thead>
          <tr style="background:#eef2f9">
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap;width:32px">#</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0">Title</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap">Due Date</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;white-space:nowrap;width:55px">Marks</th>
            <th style="padding:9px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#4a5568;border-bottom:2px solid #b8c5d6;white-space:nowrap;width:44px">Act.</th>
          </tr>
        </thead>
        <tbody>

        {{-- ── Row Loop ──
             Loops over each homework/task row in the current section.
             Per-row PHP prepares:
               $ov      — boolean: is the task overdue?
               $typeMap — maps task_type strings to CSS badge key suffixes
               $typeKey — resolved CSS class suffix for the type badge
               $rowBg   — alternating white / light-blue row background
        --}}
        @foreach($sec['rows'] as $row)
        @php
          $ov      = ($row->submission_date && \Carbon\Carbon::parse($row->submission_date)->isPast());
          $typeMap = ['homework'=>'hw','quiz'=>'quiz','project'=>'project','activity'=>'activity','game'=>'game','assignment'=>'assignment'];
          $typeKey = $typeMap[$row->task_type ?? 'homework'] ?? 'hw';
          $rowBg   = $loop->even ? '#f7f9ff' : '#ffffff';
        @endphp

        {{-- data-ft attribute lets the JS type-filter show/hide this row.
             Inline hover handlers swap background to highlight the row. --}}
        <tr data-ft="{{$row->task_type}}" style="background:{{$rowBg}}" class="hw-tbl-row"
            onmouseover="this.style.background='#eef4ff'" onmouseout="this.style.background='{{$rowBg}}'">

          {{-- Col 1: Row number --}}
          <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;font-size:11px;color:#94a3b8;vertical-align:middle;text-align:center">{{$loop->iteration}}</td>

          {{-- Col 2: Title only (truncated with ellipsis + tooltip on hover) --}}
          <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;vertical-align:middle;max-width:200px">
            <div style="font-weight:600;font-size:12.5px;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:190px"
                 title="{{$row->title ?? ''}}">{{$row->title ?? '—'}}</div>
          </td>

          {{-- Col 4: Due Date --}}
          <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;white-space:nowrap;vertical-align:middle;font-size:12.5px;color:#334155">
            {{$row->submission_date ?? '—'}}
            @if($ov)<br><span class="od" style="font-size:10px">Late</span>@endif
          </td>

          {{-- Col 5: Marks --}}
          <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;font-weight:700;font-size:14px;color:#1d4ed8;vertical-align:middle;text-align:center">{{$row->marks ?? '—'}}</td>

          {{-- Col 6: Action menu --}}
          {{-- ── Row Action Dropdown ──
               ⋮ button triggers a Bootstrap dropdown menu.
               Menu items are conditionally shown based on:
                 • Permission checks (homework_update / homework_delete)
                 • Task type (quiz rows get "View Questions" and "Quiz Analytics")
                 • Whether an upload attachment exists (non-quiz rows)
               openEval() / vQ() are JS functions defined in the @push('script') below.
          --}}
          <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;vertical-align:middle;text-align:center">
            <div class="dropdown">
              <button class="ab" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis-vertical"></i></button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size:13px;min-width:175px">
                @if(hasPermission('homework_update'))
                <li><a class="dropdown-item" href="{{ route('homework.edit',$row->id) }}"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit</a></li>
                @endif
                @if($row->task_type==='quiz')
                  <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#mQ" onclick="vQ({{$row->id}})"><i class="fa-solid fa-list-check me-2 text-info"></i>View Questions</a></li>
                @elseif($row->upload)
                  <li><a class="dropdown-item" href="{{ url($row->upload->path) }}" target="_blank"><i class="fa-solid fa-eye me-2 text-secondary"></i>View Document</a></li>
                @endif
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#mEv" onclick="openEval({{$row->id}})"><i class="fa-solid fa-{{$row->task_type==='quiz'?'chart-bar':'check-double'}} me-2 text-success"></i>{{$row->task_type==='quiz'?'Check Scores':'Evaluate'}}</a></li>
                @if($row->task_type==='quiz')
                  <li><a class="dropdown-item" href="{{ route('homework.quiz-analytics', $row->id) }}" target="_blank"><i class="fa-solid fa-magnifying-glass-chart me-2 text-info"></i>Quiz Analytics</a></li>
                @endif
                <li><a class="dropdown-item" href="{{ route('homework.export-results', $row->id) }}"><i class="fa-solid fa-file-csv me-2 text-secondary"></i>Export CSV</a></li>
                @if(hasPermission('homework_delete'))
                  <li><hr class="dropdown-divider m-1"></li>
                  <li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="delete_row('homework/delete',{{$row->id}})"><i class="fa-solid fa-trash-can me-2"></i>Delete</a></li>
                @endif
              </ul>
            </div>
          </td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    </div>
    @endif
    @endforeach

  </div>

  {{-- CHARTS SIDEBAR --}}
  {{-- ── Right Sidebar (Charts & Quick Actions) ──
       Three stacked cards:
       1. Donut chart — tasks broken down by type
       2. Submission rate — big % number + progress bar
       3. Quick actions — New Task button + CSV Template download
  --}}
  <div class="col-lg-4 d-flex flex-column gap-3">
    <div class="cc">
      <h6><i class="fa-solid fa-chart-pie me-1"></i>Tasks by Type</h6>
      @if($total>0)<canvas id="tC" height="190"></canvas>
      @else<p class="text-center text-muted py-4" style="font-size:13px">No data yet</p>@endif
    </div>
    {{-- ── Per-Class Score Line Chart ── --}}
    <div class="cc">
      <h6><i class="fa-solid fa-chart-line me-1"></i>Scores per Class</h6>
      <div style="font-size:10px;color:#94a3b8;margin-bottom:10px;margin-top:-8px">
        Marks assigned per homework task · each line = one class
      </div>
      @if($classGroups->count() > 0)
        <canvas id="clC" height="220"></canvas>
      @else
        <p class="text-center text-muted py-4" style="font-size:13px">No data yet</p>
      @endif
    </div>
    <div class="cc">
      <h6><i class="fa-solid fa-paper-plane me-1"></i>Submission Rate</h6>
      <div class="d-flex align-items-end gap-3 mb-3">
        <span style="font-size:38px;font-weight:800;color:var(--bp);line-height:1">{{$rate}}%</span>
        <span style="font-size:12px;color:var(--bs);padding-bottom:4px">{{$subm}} of {{$total}}</span>
      </div>
      <div style="background:#f1f5f9;border-radius:6px;height:8px"><div style="background:var(--bp);height:100%;width:{{$rate}}%;border-radius:6px"></div></div>
    </div>
    <div class="cc">
      <h6><i class="fa-solid fa-bolt me-1"></i>Quick Actions</h6>
      <div class="d-grid gap-2">
        <a href="{{ route('homework.create') }}" class="btn btn-sm ot-btn-primary"><i class="fa-solid fa-plus me-1"></i>New Task</a>
        <a href="{{ route('homework.download-sample') }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-download me-1"></i>CSV Template</a>
      </div>
    </div>
  </div>
</div>{{-- /row g-3 (two-column layout) --}}

{{-- ── Pagination ──
     Placed outside the two-column row so it spans full width below
     both the table column and the sidebar.
     appends() preserves current filter query-string params (class,
     section, subject) so filters are kept when changing pages.
--}}
<div class="d-flex justify-content-end pt-2">
  {!! $data['homeworks']->appends(\Request::capture()->except('page'))->links() !!}
</div>

</div>{{-- /page-content hw-portal --}}

{{-- ============================================================
     MODALS
     ============================================================ --}}

{{-- ── Evaluation Modal (#mEv) ──
     Large modal opened when the teacher clicks "Evaluate" or "View Scores"
     on a row. The modal body (#ev-body) starts with a spinner and is
     replaced by an AJAX response (student submission list / grade form).
     The hidden input #hw_id carries the homework ID to the form submission.
--}}
<div class="modal fade" id="mEv" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-header modal-header-image"><h5 class="modal-title">Evaluation</h5><button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center" data-bs-dismiss="modal"><i class="fa fa-times text-white"></i></button></div>
    <form action="{{ route('homework.evaluation.submit') }}" method="post">@csrf<input type="hidden" name="homework_id" id="hw_id">
      <div class="modal-body p-4" id="ev-body"><div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fs-3 text-primary"></i></div></div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn ot-btn-primary"><i class="fa-solid fa-check me-1"></i>Save Marks</button></div>
    </form>
  </div></div>
</div>

{{-- ── Quiz Questions Modal (#mQ) ──
     Empty shell — inner HTML is loaded dynamically via vQ() AJAX call.
     The modal-dialog div is the injection target.
--}}
<div class="modal fade" id="mQ" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl"></div></div>
@endsection

{{-- ============================================================
     PAGE-SPECIFIC JAVASCRIPT (pushed to bottom of <body>)
     ============================================================ --}}
@push('script')

{{-- Reusable delete confirmation + AJAX delete partial --}}
@include('backend.partials.delete-ajax')

{{-- Chart.js library loaded from CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>

{{-- ── Donut Chart (Tasks by Type) ──
     Only rendered when there is at least one task ($byType is non-empty).
     Labels = ucfirst task type names; data = counts per type.
     Custom colour array maps to the same order as $byType keys.
     cutout:'65%' creates the donut hole; legend sits below the chart.
--}}
@if(!empty($byType))
new Chart(document.getElementById('tC'),{type:'doughnut',data:{labels:@json(array_map('ucfirst',array_keys($byType))),datasets:[{data:@json(array_values($byType)),backgroundColor:['#1d4ed8','#f59e0b','#10b981','#8b5cf6','#ef4444','#06b6d4'],borderWidth:0,hoverOffset:5}]},options:{cutout:'65%',plugins:{legend:{position:'bottom',labels:{font:{size:11},padding:10}}}}});
@endif

// ── Per-Class Score Line Chart ──
// X axis: homework task numbers (#1, #2, #3…)
// Y axis: marks value for that homework
// One line per class; null values create gaps where a class has no task at that position
@if(count($lineDatasets) > 0)
(function(){
  var ctx = document.getElementById('clC');
  if (!ctx) return;
  var datasets = @json($lineDatasets);
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: @json($lineLabels),
      datasets: datasets.map(function(ds, i) {
        return {
          label:                ds.label,
          data:                 ds.data,
          borderColor:          ds.color,
          backgroundColor:      ds.color + '18',
          borderWidth:          2,
          pointRadius:          4,
          pointHoverRadius:     6,
          pointBackgroundColor: ds.color,
          fill:                 false,
          tension:              0.35,
          spanGaps:             false,
        };
      })
    },
    options: {
      responsive: true,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: { size: 10 }, padding: 8, usePointStyle: true, pointStyleWidth: 8 }
        },
        tooltip: {
          backgroundColor: '#0f172a',
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            title: function(items) { return 'Task ' + items[0].label; },
            label: function(ctx) {
              return ctx.raw === null
                ? ' ' + ctx.dataset.label + ': —'
                : ' ' + ctx.dataset.label + ': ' + ctx.raw + ' pts';
            }
          }
        }
      },
      scales: {
        x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
        y: {
          beginAtZero: true,
          border: { display: false, dash: [4, 4] },
          grid: { color: '#f1f5f9' },
          ticks: { font: { size: 10 }, color: '#94a3b8', callback: function(v) { return Number.isInteger(v) ? v : null; } }
        }
      }
    }
  });
})();
@endif


// Shows 4 key metrics side by side as coloured bars so the teacher
// gets an instant visual snapshot of task health at the top of the page.
(function(){
  const ctx = document.getElementById('hwBarChart');
  if(!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Total Tasks', 'Quizzes', 'Submissions', 'Pending Eval'],
      datasets: [{
        data: [{{$total}}, {{$quizzes}}, {{$subm}}, {{$pend}}],
        backgroundColor: [
          'rgba(29,78,216,0.85)',
          'rgba(245,158,11,0.85)',
          'rgba(16,185,129,0.85)',
          'rgba(239,68,68,0.85)'
        ],
        borderColor: [
          'rgba(29,78,216,1)',
          'rgba(245,158,11,1)',
          'rgba(16,185,129,1)',
          'rgba(239,68,68,1)'
        ],
        borderWidth: 0,
        borderRadius: 8,
        borderSkipped: false,
        barThickness: 38,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => ' ' + ctx.parsed.y + ' tasks'
          },
          backgroundColor: '#0f172a',
          padding: 10,
          cornerRadius: 8,
          titleFont: { size: 12 },
          bodyFont: { size: 13, weight: '700' }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          border: { display: false },
          ticks: { font: { size: 11, weight: '600' }, color: '#64748b' }
        },
        y: {
          beginAtZero: true,
          border: { display: false, dash: [4,4] },
          grid: { color: '#f1f5f9', drawBorder: false },
          ticks: {
            stepSize: 1,
            font: { size: 11 },
            color: '#94a3b8',
            // Only show whole numbers
            callback: v => Number.isInteger(v) ? v : null
          }
        }
      }
    }
  });
})();

// Type filter — wrapped in document.ready so it runs AFTER nice-select has initialised.
// nice-select triggers jQuery's 'change' event on the original hidden <select>,
// so $(document).on('change') correctly catches it.
$(document).ready(function(){
  $(document).on('change', '#typeFilter', function(){
    const ft = $(this).val();
    $('.hw-tbl-section tbody tr[data-ft]').each(function(){
      $(this).toggleClass('hh', ft !== 'all' && $(this).data('ft') !== ft);
    });
  });
});

// ── Section dropdown population via AJAX ──
// When a class is selected, fetch its sections and rebuild the section dropdown.
// When a section is selected, fetch its subjects and rebuild the subject dropdown.
// These use the same AJAX routes the rest of the app uses for niceSelect.
$(document).on('change','#getSections', function(){
  var classId = $(this).val();
  var url     = $('#url').val() + '/get-sections';
  $('#getSubjects').empty().append('<option value="">All Sections</option>');
  $('#subject').empty().append('<option value="">All Subjects</option>');
  if(!classId) return;
  $.post(url, {class_id: classId, _token: $('meta[name="csrf-token"]').attr('content')}, function(data){
    $.each(data, function(i, item){
      $('#getSubjects').append('<option value="'+ item.id +'">'+ item.name +'</option>');
    });
    // Reinitialise nice-select so the new options show in the custom dropdown UI
    if(typeof $.fn.niceSelect === 'function'){
      $('#getSubjects').niceSelect('update');
      $('#subject').niceSelect('update');
    }
  });
});

$(document).on('change','#getSubjects', function(){
  var sectionId = $(this).val();
  var url       = $('#url').val() + '/get-subjects';
  $('#subject').empty().append('<option value="">All Subjects</option>');
  if(!sectionId) return;
  $.post(url, {section_id: sectionId, _token: $('meta[name="csrf-token"]').attr('content')}, function(data){
    $.each(data, function(i, item){
      $('#subject').append('<option value="'+ item.id +'">'+ item.name +'</option>');
    });
    if(typeof $.fn.niceSelect === 'function'){
      $('#subject').niceSelect('update');
    }
  });
});

// Collapsible sections
{{-- ── Accordion Toggle (toggleSect) ──
     Uses max-height animation (CSS transition defined above).
     Open  → sets max-height to the element's natural scrollHeight.
     Closed → sets max-height to '0px'.
     The header element gets/removes the .collapsed class to rotate the caret.
--}}
function toggleSect(id,hdr){
  // Grab the clicked header, the icon inside it, and the exact table body below it
    const $icon = $(hdr).find('i');
    const $body = $(hdr).next('.sect-body');

    // jQuery safely shrinks the table, margins, and padding, then hides it completely
    $body.slideToggle(300, function() {
        // Optional: Remove any inline max-height if it was stuck
        $(this).css('max-height', ''); 
    });

    // Rotate the arrow icon smoothly
    if ($body.hasClass('is-closed')) {
        $body.removeClass('is-closed');
        $icon.css({'transform': 'rotate(0deg)', 'transition': 'transform 0.3s ease'});
    } else {
        $body.addClass('is-closed');
        $icon.css({'transform': 'rotate(-90deg)', 'transition': 'transform 0.3s ease'});
    }
}
// Init all sections open
{{-- Set each section's max-height to its full scrollHeight on page load
     so all sections start in the expanded state. --}}


{{-- ── openEval(id) ──
     Called when the teacher clicks "Evaluate" or "View Scores".
     1. Stores the homework ID in the hidden form field.
     2. Shows a loading spinner in the modal body.
     3. POSTs to /homework/students with the homework_id.
     4. On success: injects the returned HTML view (grade form / scores).
     5. On failure: shows an inline error message.
--}}
function openEval(id){
  $('#hw_id').val(id);
  $('#ev-body').html('<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fs-3 text-primary"></i></div>');
  $.post($('#url').val()+'/homework/students',{homework_id:id,_token:$('meta[name="csrf-token"]').attr('content')},d=>$('#ev-body').html(d.view)).fail(()=>$('#ev-body').html('<p class="text-danger p-3">Failed to load.</p>'));
}

{{-- ── vQ(id) — View Quiz Questions ──
     GETs the quiz question HTML from /homework/view-questions?id={id}
     and injects it directly into the #mQ modal-dialog container.
     The response is expected to be a full Bootstrap modal-content fragment.
--}}
function vQ(id){
  $.ajax({type:'GET',dataType:'html',data:{id:id},headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},url:$('#url').val()+'/homework/view-questions',success:d=>$('#mQ .modal-dialog').html(d)});
}
</script>
@endpush