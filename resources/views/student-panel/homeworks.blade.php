@extends('student-panel.partials.master')
@section('title') My Homework @endsection

@push('style')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@700;800&display=swap" rel="stylesheet">
<style>
/* Only layout/typography here — all visible borders/backgrounds are inline to beat LMS theme */
.sp-portal { font-family: 'DM Sans', sans-serif; }
.stab       { display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;transition:all .15s; }
.stab.active{ background:#2563eb;color:#fff;border:2px solid #2563eb; }
.stab:not(.active){ background:#fff;color:#64748b;border:2px solid #e2e8f0; }
.stab .cnt  { border-radius:10px;padding:0 6px;font-size:10.5px; }
.stab.active .cnt { background:rgba(255,255,255,.25);color:#fff; }
.stab:not(.active) .cnt { background:#f1f5f9;color:#64748b; }
.status-hdr { transition:background .15s; }
.status-hdr:hover { background:#f1f5f9 !important; }
.hw-card-hover { transition:transform .15s,box-shadow .15s; }
.hw-card-hover:hover { transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,0,0,.10) !important; }
.tbg { display:inline-block;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em; }
.tq  { background:#dbeafe;color:#1e40af; }
.tbg-hw { background:#d1fae5;color:#065f46; }
.tp  { background:#fce7f3;color:#9d174d; }
.ta  { background:#ede9fe;color:#5b21b6; }
.tg  { background:#fef3c7;color:#92400e; }
.ts  { background:#e0f2fe;color:#075985; }
.sp-ok   { background:#d1fae5;color:#065f46;border-radius:20px;padding:2px 9px;font-size:11.5px;font-weight:700;display:inline-block; }
.sp-wait { background:#f1f5f9;color:#64748b;border-radius:20px;padding:2px 9px;font-size:11.5px;display:inline-block; }
.od-badge{ background:#fee2e2;color:#dc2626;border-radius:6px;padding:2px 7px;font-size:11px;font-weight:700;display:inline-block; }
.cc-t { font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:700;margin-bottom:20px;display:block; }
</style>
@endpush

@section('content')
<div class="page-content sp-portal">

@php
  $homeworks  = $data['homeworks'] ?? collect();
  $grouped    = $homeworks->groupBy('subject_id');
  $totalCount = $homeworks->count();
  $doneCount  = $homeworks->filter(fn($h) => $h->check_submitted)->count();
  $pendCount  = $totalCount - $doneCount;
  $overCount  = $homeworks->filter(fn($h) =>
      !$h->check_submitted && $h->submission_date &&
      \Carbon\Carbon::parse($h->submission_date)->isPast()
  )->count();
  $student = Auth::user()->student ?? null;
  $sName   = $student ? trim($student->first_name.' '.$student->last_name) : 'Student';
  $pct     = $totalCount > 0 ? round(($doneCount / $totalCount) * 100) : 0;

  // Sections for collapsible groups
  $hwOverdue  = $homeworks->filter(fn($h) => !$h->check_submitted && $h->submission_date && \Carbon\Carbon::parse($h->submission_date)->isPast());
  $hwPending  = $homeworks->filter(fn($h) => !$h->check_submitted && (!$h->submission_date || \Carbon\Carbon::parse($h->submission_date)->isFuture()));
  $hwDone     = $homeworks->filter(fn($h) => $h->check_submitted);

  // ── Chart data ────────────────────────────────────────────────────────────
  // Line chart: overall running average (all subjects), ordered by when work was graded
  // Donut chart: completion slice per subject (each subject gets its own colour)
  $chartPalette = ['#2563eb','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899','#14b8a6'];
  $cLabels=[];$cDone=[];$cTotal=[];$cColors=[];
  $ci = 0;
  foreach($grouped as $sid => $items){
    $subName = $items->first()->subject->name ?? 'S';
    $cLabels[] = $subName;
    $cTotal[]  = $items->count();
    $cColors[] = $chartPalette[$ci % count($chartPalette)];
    $d=0; foreach($items as $h){ if($h->check_submitted) $d++; } $cDone[]=$d;
    $ci++;
  }

  $computeProgressSeries = function ($collection) {
      $gradedChrono = $collection
          ->filter(fn($h) => $h->check_submitted && $h->check_submitted->marks !== null)
          ->sortBy(function ($h) {
              $s = $h->check_submitted;
              $ts = $s->updated_at ?? $s->created_at ?? null;
              if ($ts) {
                  return \Carbon\Carbon::parse($ts)->timestamp;
              }

              return \Carbon\Carbon::parse($h->submission_date ?? $h->created_at ?? '1970-01-01')->timestamp;
          })
          ->values();

      $labels = [];
      $earned = [];
      $maxAvg = [];
      $cumMarks = 0.0;
      $cumMax = 0.0;
      $step = 0;
      foreach ($gradedChrono as $h) {
          $m = (float) $h->check_submitted->marks;
          $maxMarks = (float) ($h->marks ?? 0);
          $cumMarks += $m;
          $cumMax += $maxMarks;
          $step++;
          $labels[] = '#' . $step;
          $earned[] = round($cumMarks / $step, 1);
          $maxAvg[] = round($cumMax / $step, 1);
      }

      return ['labels' => $labels, 'earned' => $earned, 'maxAvg' => $maxAvg];
  };

  $progressAll = $computeProgressSeries($homeworks);
  $progressLabels = $progressAll['labels'];
  $progressAvgEarned = $progressAll['earned'];
  $progressAvgMax = $progressAll['maxAvg'];

  $progressBySubject = ['all' => $progressAll];
  foreach ($grouped as $sid => $items) {
      $progressBySubject[(string) $sid] = $computeProgressSeries($items);
  }
@endphp

{{-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 1 — HERO / OVERVIEW
     Shows student name, 4 quick-stat pills, completion %, upcoming count.
     Inline styles used throughout so LMS theme cannot override visibility.
═══════════════════════════════════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 55%,#3b82f6 100%);border-radius:14px;padding:24px 28px;color:#fff;margin-bottom:20px;position:relative;overflow:hidden">
  {{-- Decorative circles --}}
  <div style="position:absolute;top:-60px;right:-60px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none"></div>
  <div style="position:absolute;bottom:-80px;right:80px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none"></div>

  <div class="row align-items-center position-relative">
    <div class="col-md-5 mb-3 mb-md-0">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:800;margin-bottom:4px">
        Hello, {{ $sName }}! &#128075;
      </div>
      <div style="opacity:.78;font-size:13.5px">Your homework overview for this term.</div>

      {{-- Completion progress bar --}}
      <div style="margin-top:14px">
        <div style="display:flex;justify-content:space-between;font-size:11px;opacity:.8;margin-bottom:4px">
          <span>Overall Completion</span><span>{{ $pct }}%</span>
        </div>
        <div style="background:rgba(255,255,255,.2);border-radius:6px;height:8px">
          <div style="background:#10b981;height:100%;width:{{ $pct }}%;border-radius:6px;transition:width .6s"></div>
        </div>
      </div>
    </div>

    <div class="col-md-7">
      {{-- Stat pills row --}}
      <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end">

        <div style="background:rgba(255,255,255,.14);border-radius:10px;padding:12px 18px;text-align:center;min-width:68px">
          <div style="font-size:28px;font-weight:800;line-height:1">{{ $totalCount }}</div>
          <div style="font-size:10px;opacity:.8;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">Total</div>
        </div>

        <div style="background:rgba(16,185,129,.22);border-radius:10px;padding:12px 18px;text-align:center;min-width:68px">
          <div style="font-size:28px;font-weight:800;line-height:1">{{ $doneCount }}</div>
          <div style="font-size:10px;opacity:.8;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">Done</div>
        </div>

        <div style="background:rgba(245,158,11,.22);border-radius:10px;padding:12px 18px;text-align:center;min-width:68px">
          <div style="font-size:28px;font-weight:800;line-height:1">{{ $pendCount }}</div>
          <div style="font-size:10px;opacity:.8;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">Pending</div>
        </div>

        @if($overCount > 0)
        <div style="background:rgba(239,68,68,.28);border-radius:10px;padding:12px 18px;text-align:center;min-width:68px">
          <div style="font-size:28px;font-weight:800;line-height:1">{{ $overCount }}</div>
          <div style="font-size:10px;opacity:.8;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">Overdue</div>
        </div>
        @endif

      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 2 — STICKY SUBJECT TABS
     Filters cards client-side without page reload.
═══════════════════════════════════════════════════════════════════════════ --}}
<div style="position:sticky;top:0;z-index:15;background:#f8fafc;padding:8px 0 10px;border-bottom:1px solid #e2e8f0;margin-bottom:14px">
  <div style="display:flex;gap:7px;flex-wrap:wrap">
    <button class="stab active" data-sub="all">
      All <span class="cnt">{{ $totalCount }}</span>
    </button>
    @foreach($grouped as $sid => $items)
      <button class="stab" data-sub="{{ $sid }}">
        {{ $items->first()->subject->name ?? 'Subject' }}
        <span class="cnt">{{ $items->count() }}</span>
      </button>
    @endforeach
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     SECTION 3 — MAIN CONTENT + SIDEBAR
═══════════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3">

  {{-- LEFT: Collapsible homework sections --}}
  <div class="col-lg-8">

    @php
    $sections = [
      ['key'=>'overdue','label'=>'Overdue',         'color'=>'#dc2626','icon'=>'circle-exclamation','items'=>$hwOverdue,'open'=>true],
      ['key'=>'pending','label'=>'Active / Pending', 'color'=>'#f59e0b','icon'=>'hourglass-half',   'items'=>$hwPending,'open'=>true],
      ['key'=>'done',   'label'=>'Completed',        'color'=>'#059669','icon'=>'check-circle',     'items'=>$hwDone,   'open'=>false],
    ];
    @endphp

    @foreach($sections as $sec)
    @if($sec['items']->count() > 0)

    {{-- Section header — inline styles so theme cannot flatten it --}}
    <div class="status-hdr {{ !$sec['open'] ? 'collapsed' : '' }}"
         onclick="toggleSect('s-{{$sec['key']}}', this)"
         style="display:flex;align-items:center;gap:8px;padding:9px 14px;border-radius:8px;cursor:pointer;margin-bottom:8px;user-select:none;border:1.5px solid #d1d9e6;background:#f8fafc;color:{{$sec['color']}}">
      <i class="fa-solid fa-{{$sec['icon']}}"></i>
      <span style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em">
        {{ $sec['label'] }}
      </span>
      <span style="margin-left:auto;font-size:11px;font-weight:600;background:#fff;border-radius:10px;padding:1px 9px;border:1px solid #d1d9e6;color:#64748b">
        {{ $sec['items']->count() }}
      </span>
      <i class="fa-solid fa-chevron-down caret" style="font-size:9px;transition:transform .2s;margin-left:4px"></i>
    </div>

    <div class="status-body" id="s-{{$sec['key']}}" style="{{ $sec['open'] ? '' : 'max-height:0px;overflow:hidden' }}">

    @foreach($sec['items'] as $row)
    @php
      $sub  = $row->check_submitted;
      $isOv = !$sub && $row->submission_date && \Carbon\Carbon::parse($row->submission_date)->isPast();
      $cc   = $sub ? 'c-done' : ($isOv ? 'c-over' : ($row->task_type==='quiz' ? 'c-quiz' : 'c-pending'));
      $stripeColors = ['c-done'=>'#10b981','c-pending'=>'#f59e0b','c-over'=>'#ef4444','c-quiz'=>'#2563eb'];
      $stripeColor  = $stripeColors[$cc] ?? '#94a3b8';
      $tMap = ['quiz'=>'tq','homework'=>'tbg-hw','project'=>'tp','activity'=>'ta','game'=>'tg','assignment'=>'ts'];
      $tc   = $tMap[$row->task_type ?? 'homework'] ?? 'tbg-hw';
    @endphp

    <div class="hw-card-hover" data-sub="{{ $row->subject_id }}"
         style="background:#fff;border-radius:12px;border:1.5px solid #d1d9e6;padding:0;margin-bottom:10px;position:relative;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.05)">

      {{-- Left colour stripe (real div — ::before is overridden by LMS theme) --}}
      <div style="position:absolute;top:0;left:0;width:5px;height:100%;background:{{$stripeColor}}"></div>

      {{-- Card body --}}
      <div style="padding:13px 15px 13px 20px">

        {{-- Row 1: Type badge + Subject + Score/Status pill --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:6px;flex-wrap:wrap">
          <div style="display:flex;align-items:center;gap:7px">
            <span class="tbg {{ $tc }}">{{ $row->task_type ?? 'hw' }}</span>
            <span style="font-size:11.5px;color:#64748b">{{ $row->subject->name ?? '' }}</span>
          </div>
          <div style="flex-shrink:0">
            @if($sub)
              @if($sub->marks !== null)
                <span class="sp-ok"><i class="fa-solid fa-star" style="font-size:9px;margin-right:3px"></i>{{ $sub->marks }} / {{ $row->marks }}</span>
              @else
                <span class="sp-wait">Awaiting result</span>
              @endif
            @elseif($isOv)
              <span class="od-badge"><i class="fa-solid fa-circle-exclamation" style="margin-right:3px"></i>Overdue</span>
            @endif
          </div>
        </div>

        {{-- Row 2: Title --}}
        <div style="font-weight:700;font-size:13.5px;color:#0f172a;margin-bottom:5px;line-height:1.3">
          {{ $row->title ?? '—' }}
        </div>

        @if(filled($row->description))
        <div style="font-size:12.5px;color:#475569;line-height:1.55;margin-bottom:8px;padding:8px 10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;white-space:pre-wrap;word-break:break-word">
          <span style="display:block;font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:700;margin-bottom:4px">Instructions</span>
          {{ $row->description }}
        </div>
        @endif

        {{-- Meta row: due date + marks --}}
        <div style="display:flex;flex-wrap:wrap;gap:12px;font-size:11.5px;color:#64748b;margin-bottom:8px">
          <span><i class="fa-solid fa-clock" style="margin-right:3px"></i>Due: <strong style="color:#334155">{{ $row->submission_date ?? '—' }}</strong></span>
          @if($row->marks)
          <span><i class="fa-solid fa-bullseye" style="margin-right:3px"></i>Marks: <strong style="color:#334155">{{ $row->marks }}</strong></span>
          @endif
        </div>

        {{-- Row 5: Teacher feedback (Tier 2 Feature F) --}}
        @if($sub && $sub->feedback)
        <div style="font-size:12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:7px;padding:6px 10px;margin-bottom:8px">
          <i class="fa-solid fa-comment-dots" style="color:#059669;margin-right:4px"></i>
          <strong style="color:#065f46">Feedback:</strong>
          <span style="color:#166534">{{ $sub->feedback }}</span>
        </div>
        @endif

        {{-- Row 6: Action buttons --}}
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
          @if($row->task_type === 'quiz')
            @if($sub)
              <a href="{{ route('student-panel-homework.take-quiz', $row->id) }}"
                 style="display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:7px;font-size:12.5px;font-weight:600;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;text-decoration:none">
                <i class="fa-solid fa-rotate-left"></i> Review Quiz
              </a>
            @else
              <a href="{{ route('student-panel-homework.take-quiz', $row->id) }}"
                 style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:7px;font-size:12.5px;font-weight:600;background:#2563eb;color:#fff;text-decoration:none">
                <i class="fa-solid fa-play"></i> Take Quiz
              </a>
            @endif
          @else
            @if($row->upload && $row->upload->path)
              <a href="{{ url($row->upload->path) }}" target="_blank"
                 style="display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:7px;font-size:12.5px;font-weight:600;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;text-decoration:none">
                <i class="fa-solid fa-eye"></i> View Task
              </a>
            @endif
            @if(!$sub)
              <button onclick="openSub({{ $row->id }})" data-bs-toggle="modal" data-bs-target="#mSub"
                      style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:7px;font-size:12.5px;font-weight:600;background:#2563eb;color:#fff;border:none;cursor:pointer">
                <i class="fa-solid fa-upload"></i> Submit
              </button>
            @else
              <span style="font-size:12px;color:#059669;font-weight:600">
                <i class="fa-solid fa-check-circle" style="margin-right:3px"></i>Submitted
              </span>
              @if($sub->homeworkUpload && $sub->homeworkUpload->path)
                <a href="{{ url($sub->homeworkUpload->path) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:7px;font-size:11.5px;font-weight:600;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;text-decoration:none">
                  <i class="fa-solid fa-eye"></i> Your Work
                </a>
              @endif
            @endif
          @endif
        </div>

      </div>{{-- end card body --}}
    </div>{{-- end hw-card-hover --}}

    @endforeach
    </div>{{-- end status-body --}}
    @endif
    @endforeach

    @if($homeworks->isEmpty())
    <div style="text-align:center;padding:48px 20px;color:#94a3b8">
      <i class="fa-solid fa-inbox" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:12px"></i>
      <p style="margin:0;font-size:14px">No homework assigned yet.</p>
    </div>
    @endif

  </div>{{-- end col-lg-8 --}}

  {{-- ═══════════════════════════════════════════════════════════════════════
       RIGHT SIDEBAR — Charts + Deadlines
  ═══════════════════════════════════════════════════════════════════════ --}}
  <div class="col-lg-4 d-flex flex-column gap-4">

    {{-- LINE CHART: Overall running average after each graded piece of work (all subjects). --}}
    <div style="background:#fff;border:1.5px solid #d1d9e6;border-radius:12px;padding:18px;box-shadow:0 2px 6px rgba(0,0,0,.05)">
      <div class="cc-t"><i class="fa-solid fa-chart-line" style="margin-right:5px"></i>Overall score progress</div>
      <div style="font-size:10.5px;color:#94a3b8;margin-bottom:14px;line-height:1.45">
        Running average of your marks vs average max marks. Each point adds the next graded assignment (order: when it was marked).
      </div>
      @if(count($progressLabels) > 0)
        <canvas id="pC" height="210"></canvas>
      @else
        <p style="text-align:center;color:#94a3b8;font-size:13px;padding:16px 0">No graded results yet. After your teacher marks your submissions, your overall trend will show here.</p>
      @endif
    </div>

    {{-- SUBJECT-COLOURED DONUT: Each subject gets its own colour slice showing
         how many tasks are completed vs total. Gives an instant visual of which
         subjects have pending work.
         Falls back to a simple done/pending ring if only one subject. --}}
    <div style="background:#fff;border:1.5px solid #d1d9e6;border-radius:12px;padding:18px;box-shadow:0 2px 6px rgba(0,0,0,.05)">
      <div class="cc-t"><i class="fa-solid fa-circle-check" style="margin-right:5px"></i>Completion by Subject</div>
      @if($totalCount > 0)
      <div style="display:flex;align-items:center;gap:14px">
        <div style="position:relative;flex-shrink:0">
          <canvas id="cC" width="110" height="110"></canvas>
          {{-- Centre label --}}
          <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none">
            <div style="font-size:18px;font-weight:800;color:#2563eb;line-height:1">{{ $pct }}%</div>
            <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em">done</div>
          </div>
        </div>
        {{-- Subject legend --}}
        <div style="flex:1;min-width:0">
          @foreach($cLabels as $ci => $lbl)
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;font-size:11.5px">
            <div style="display:flex;align-items:center;gap:6px">
              <span style="width:10px;height:10px;border-radius:50%;background:{{ $cColors[$ci] }};flex-shrink:0;display:inline-block"></span>
              <span style="color:#334155;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:85px">{{ $lbl }}</span>
            </div>
            <span style="color:#64748b;font-size:10.5px;white-space:nowrap">{{ $cDone[$ci] }}/{{ $cTotal[$ci] }}</span>
          </div>
          @endforeach
          @if($overCount > 0)
          <div style="margin-top:8px;font-size:11px;color:#dc2626;font-weight:600">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right:3px"></i>{{ $overCount }} overdue
          </div>
          @endif
        </div>
      </div>
      @else
      <p style="text-align:center;color:#94a3b8;font-size:13px;padding:16px 0">No tasks assigned yet.</p>
      @endif
    </div>

    {{-- Upcoming deadlines --}}
    @php
    $upcoming = $homeworks
      ->filter(fn($h) => !$h->check_submitted && $h->submission_date && \Carbon\Carbon::parse($h->submission_date)->isFuture())
      ->sortBy('submission_date')
      ->take(5);
    @endphp
    @if($upcoming->count() > 0)
    <div style="background:#fff;border:1.5px solid #d1d9e6;border-radius:12px;padding:18px;box-shadow:0 2px 6px rgba(0,0,0,.05)">
      <div class="cc-t"><i class="fa-solid fa-calendar-clock" style="margin-right:5px"></i>Upcoming Deadlines</div>
      @foreach($upcoming as $u)
      @php $days = (int) round(now()->diffInDays(\Carbon\Carbon::parse($u->submission_date), false)); @endphp
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f1f5f9">
        <div>
          <div style="font-size:13px;font-weight:600;color:#0f172a">{{ \Str::limit($u->title ?? $u->subject->name ?? '', 26) }}</div>
          <div style="font-size:11px;color:#64748b">{{ $u->subject->name ?? '' }}</div>
        </div>
        <span style="font-size:12px;font-weight:700;color:{{ $days<=1?'#dc2626':($days<=3?'#f59e0b':'#059669') }};white-space:nowrap">
          {{ $days<=0?'Today':($days===1?'Tomorrow':$days.' days') }}
        </span>
      </div>
      @endforeach
    </div>
    @endif

  </div>{{-- end col-lg-4 --}}

</div>{{-- end row --}}
</div>{{-- end sp-portal --}}

{{-- ═══════════════════════════════════════════════════════════════════════════
     SUBMIT MODAL
═══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mSub" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md"><div class="modal-content">
    <div class="modal-header modal-header-image">
      <h5 class="modal-title">Submit Homework</h5>
      <button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center" data-bs-dismiss="modal">
        <i class="fa fa-times text-white"></i>
      </button>
    </div>
    <form action="{{ route('student-panel.homework.submit') }}" enctype="multipart/form-data" method="post" id="sf">
      @csrf
      <input type="hidden" name="homework_id" id="sid">
      <div class="modal-body p-4">
        <label class="form-label fw-semibold">Upload your work <span class="fillable">*</span></label>
        <div class="ot_fileUploader left-side mb-1">
          <input class="form-control" type="text" placeholder="Choose file..." readonly id="sph">
          <button class="primary-btn-small-input" type="button">
            <label class="btn btn-lg ot-btn-primary" for="sfl">Browse</label>
            <input type="file" class="d-none" name="homework" id="sfl" accept="image/*,.pdf,.doc,.docx">
          </button>
        </div>
        {{-- File preview bar --}}
        <div id="fprev" style="display:none;background:#eff6ff;border:1px solid #bfdbfe;border-radius:7px;padding:7px 12px;font-size:12px;color:#1d4ed8;margin-top:6px">
          <i class="fa-solid fa-file" style="margin-right:6px"></i>
          <span id="fprev-name"></span>
          <span id="fprev-size" style="color:#64748b;margin-left:6px"></span>
        </div>
        <span id="serr" class="text-danger" style="font-size:12px"></span>
        <div style="margin-top:8px;font-size:11.5px;color:#94a3b8">
          <i class="fa-solid fa-circle-info" style="margin-right:4px"></i>
          Accepted: Images, PDF, Word documents (max 10 MB)
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn ot-btn-primary" id="sbtn" onclick="doSub()">
          <i class="fa-solid fa-upload" style="margin-right:5px"></i>Submit
        </button>
      </div>
    </form>
  </div></div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
var PROGRESS_BY_SUBJECT = @json($progressBySubject);
window.lineChartPC = null;

function updateStudentProgressChart(subjectKey) {
  var key = (subjectKey === 'all' || subjectKey === undefined || subjectKey === '') ? 'all' : String(subjectKey);
  var series = PROGRESS_BY_SUBJECT[key];
  if (!series) {
    series = { labels: [], earned: [], maxAvg: [] };
  }
  if (!window.lineChartPC) {
    return;
  }
  var L = series.labels || [];
  var E = series.earned || [];
  var M = series.maxAvg || [];
  window.lineChartPC.data.labels = L.slice();
  window.lineChartPC.data.datasets[0].data = E.slice();
  window.lineChartPC.data.datasets[1].data = M.slice();
  window.lineChartPC.update();
}

// ── SUBJECT TABS ──────────────────────────────────────────────────────────────
document.querySelectorAll('.stab').forEach(function(t) {
  t.addEventListener('click', function() {
    document.querySelectorAll('.stab').forEach(function(x){ x.classList.remove('active'); });
    this.classList.add('active');
    var s = this.dataset.sub;
    document.querySelectorAll('.hw-card-hover').forEach(function(c){
      c.style.display = (s === 'all' || c.dataset.sub === s) ? '' : 'none';
    });
    updateStudentProgressChart(s);
    // Re-open visible sections so collapsed height is recalculated
    document.querySelectorAll('.status-body').forEach(function(b){
      if (b.style.maxHeight && b.style.maxHeight !== '0px') {
        b.style.maxHeight = b.scrollHeight + 'px';
      }
    });
  });
});

// ── COLLAPSIBLE SECTIONS ──────────────────────────────────────────────────────
function toggleSect(id, hdr) {
  var body   = document.getElementById(id);
  var caret  = hdr.querySelector('.caret');
  var isOpen = body.style.maxHeight && body.style.maxHeight !== '0px';
  if (isOpen) {
    body.style.maxHeight  = '0px';
    body.style.overflow   = 'hidden';
    if (caret) caret.style.transform = 'rotate(-90deg)';
    hdr.classList.add('collapsed');
  } else {
    body.style.overflow   = 'hidden';
    body.style.maxHeight  = body.scrollHeight + 'px';
    if (caret) caret.style.transform = '';
    hdr.classList.remove('collapsed');
  }
}
// Init: open sections that have open:true (no max-height set)
document.querySelectorAll('.status-body').forEach(function(b) {
  if (!b.style.maxHeight || b.style.maxHeight !== '0px') {
    b.style.maxHeight = b.scrollHeight + 'px';
  }
});

// ── CHART 1: Line chart — running average (updates when subject tab changes) ───
@if(count($progressLabels) > 0)
window.lineChartPC = new Chart(document.getElementById('pC'), {
  type: 'line',
  data: {
    labels: @json($progressLabels),
    datasets: [
      {
        label: 'Average marks earned',
        data:  @json($progressAvgEarned),
        borderColor:     '#2563eb',
        backgroundColor: 'rgba(37,99,235,.10)',
        borderWidth: 2.5,
        pointBackgroundColor: '#2563eb',
        pointRadius: 5,
        pointHoverRadius: 7,
        fill: true,
        tension: 0.35,
        spanGaps: false,
      },
      {
        label: 'Average max marks',
        data:  @json($progressAvgMax),
        borderColor:     '#d1d5db',
        backgroundColor: 'transparent',
        borderWidth: 1.5,
        borderDash: [5, 4],
        pointBackgroundColor: '#d1d5db',
        pointRadius: 3,
        fill: false,
        tension: 0.35,
      }
    ]
  },
  options: {
    responsive: true,
    interaction: { mode: 'index', intersect: false },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { font: { size: 10 } },
        grid:  { color: '#f1f5f9' }
      },
      x: {
        ticks: { font: { size: 10 }, maxRotation: 45, minRotation: 0 },
        grid:  { display: false }
      }
    },
    plugins: {
      legend: { labels: { font: { size: 10 }, padding: 8, usePointStyle: true } },
      tooltip: {
        callbacks: {
          label: function(ctx) {
            return ' ' + ctx.dataset.label + ': ' + ctx.raw;
          }
        }
      }
    }
  }
});
@endif

// ── CHART 2: Subject-coloured donut — completion by subject ──────────────────
// Each slice = number of completed tasks in that subject.
// Remaining arc = pending/overdue tasks for that subject.
// Each subject has a unique colour matching the legend dots beside the chart.
@if($totalCount > 0)
(function() {
  var labels   = @json($cLabels);
  var done     = @json($cDone);
  var total    = @json($cTotal);
  var colors   = @json($cColors);
  var pending  = total.map(function(t, i){ return t - done[i]; });

  // Build interleaved data: [done_subj1, pending_subj1, done_subj2, pending_subj2...]
  // Pending slices use a faded version of the subject colour (30% opacity via hex)
  var chartData   = [];
  var chartColors = [];
  var chartLabels = [];
  for (var i = 0; i < labels.length; i++) {
    chartData.push(done[i]);
    chartColors.push(colors[i]);
    chartLabels.push(labels[i] + ' done');
    chartData.push(pending[i]);
    chartColors.push(colors[i] + '40');   // hex alpha 40 = 25% opacity
    chartLabels.push(labels[i] + ' pending');
  }

  new Chart(document.getElementById('cC'), {
    type: 'doughnut',
    data: {
      labels:   chartLabels,
      datasets: [{
        data:            chartData,
        backgroundColor: chartColors,
        borderWidth:     2,
        borderColor:     '#fff',
        hoverOffset:     5,
      }]
    },
    options: {
      cutout: '68%',
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(ctx) {
              var raw   = ctx.raw;
              var label = ctx.label || '';
              return ' ' + label + ': ' + raw + ' task' + (raw !== 1 ? 's' : '');
            }
          }
        }
      }
    }
  });
})();
@endif

// ── SUBMIT FLOW ───────────────────────────────────────────────────────────────
function openSub(id) {
  document.getElementById('sid').value  = id;
  document.getElementById('sph').value  = '';
  document.getElementById('sfl').value  = '';
  document.getElementById('serr').textContent = '';
  document.getElementById('fprev').style.display = 'none';
}

document.getElementById('sfl').addEventListener('change', function() {
  var f = this.files[0];
  if (!f) return;
  document.getElementById('sph').value = f.name;
  var kb  = Math.round(f.size / 1024);
  var sz  = kb > 1024 ? (kb/1024).toFixed(1)+' MB' : kb+' KB';
  document.getElementById('fprev-name').textContent = f.name;
  document.getElementById('fprev-size').textContent = '('+sz+')';
  document.getElementById('fprev').style.display = 'block';
  if (f.size > 10*1024*1024) {
    document.getElementById('serr').textContent = 'File exceeds 10 MB limit.';
    document.getElementById('fprev').style.display = 'none';
  } else {
    document.getElementById('serr').textContent = '';
  }
});

function doSub() {
  var f = document.getElementById('sfl').files[0];
  if (!f) { document.getElementById('serr').textContent = 'Please select a file.'; return; }
  if (f.size > 10*1024*1024) { document.getElementById('serr').textContent = 'File exceeds 10 MB limit.'; return; }
  var btn = document.getElementById('sbtn');
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="margin-right:5px"></i>Uploading...';
  btn.disabled  = true;
  document.getElementById('serr').textContent = '';
  var fd = new FormData(document.getElementById('sf'));
  fetch(document.getElementById('sf').action, {
    method:'POST', body:fd,
    headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  })
  .then(function(r){ if(r.ok||r.redirected){ window.location.reload(); } else { throw new Error(r.status); } })
  .catch(function(e){
    document.getElementById('serr').textContent = 'Upload failed. Please try again.';
    btn.innerHTML = '<i class="fa-solid fa-upload" style="margin-right:5px"></i>Submit';
    btn.disabled  = false;
  });
}
</script>
@endpush