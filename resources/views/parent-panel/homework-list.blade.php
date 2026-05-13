@extends('parent-panel.partials.master')
@section('title') {{ ___('common.Homework List') }} @endsection

@push('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ global_asset('backend') }}/assets/css/parent-homework-gamified.css">
@endpush

@section('content')
<div class="page-content pp pp-hw-gamified">
<div class="pp-hw-inner">

{{-- $sLabels defined unconditionally so @push('script') never crashes when no student selected --}}
@php $sLabels=[]; $sDone=[]; $sTotal=[]; $sAvg=[]; @endphp

<div class="pp-hw-hero">
  <div class="row align-items-center g-2">
    <div class="col-12 col-md">
      <h4 class="bradecrumb-title mb-1">{{ ___('settings.homework_list') }}</h4>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Homework</li>
      </ol>
    </div>
  </div>
</div>

{{-- CHILD SELECTOR --}}
<div class="card ot-card mb-4">
  <div class="card-body">
    <h6 class="pp-hw-section-title mb-0">Select your child</h6>
    <form action="{{ route('parent-panel-homeworks.search') }}" method="post" id="cf">
      @csrf
      <input type="hidden" name="student" id="sel_student">

      @if(!empty($data['students'] ?? []))
        <div class="d-flex gap-3 flex-wrap align-items-stretch">
          @foreach($data['students'] ?? [] as $item)
            @php $sel=(old('student',Session::get('student_id'))==$item->id); @endphp
            <div class="child-card {{ $sel?'sel':'' }}" style="min-width:min(100%,200px);max-width:280px" onclick="selChild({{ $item->id }},this)">
              <div class="child-av">{{ strtoupper(substr($item->first_name,0,1)) }}</div>
              <div class="text-start min-w-0">
                <div style="font-weight:700;font-size:14px;line-height:1.25">{{ $item->first_name }} {{ $item->last_name }}</div>
                <div style="font-size:11.5px;color:var(--sl)">{{ $item->admission_no??'' }}</div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-muted mb-0" style="font-size:13px">
          <i class="fa-solid fa-circle-info me-2"></i>
          No children are linked to this account. Please contact the school administrator.
        </p>
      @endif

    </form>
  </div>
</div>

@if(!empty($data['student']))
@php
  $hw      = $data['homeworks'] ?? collect();
  $total   = $hw->count();
  $done    = $hw->filter(fn($h)=>$h->submission_record)->count();
  $pending = $total - $done;
  $overdue = $hw->filter(fn($h)=>!$h->submission_record&&$h->submission_date&&\Carbon\Carbon::parse($h->submission_date)->isPast())->count();
  $scores  = $hw->filter(fn($h)=>$h->submission_record&&$h->submission_record->marks!==null)->pluck('submission_record.marks');
  $avg     = $scores->count()>0 ? round($scores->avg(),1) : null;
  $bySubj  = $hw->groupBy('subject_id');

  $subjStats = [];
  foreach($bySubj as $sid=>$items){
    $sLabels[] = $items->first()->subject->name??'S';
    $sTotal[]  = $items->count();
    $d=0; foreach($items as $h){if($h->submission_record)$d++;} $sDone[]=$d;
    $sc = $items->filter(fn($h)=>$h->submission_record&&$h->submission_record->marks!==null)->map(fn($h)=>$h->submission_record->marks);
    $subAvg = $sc->count()>0 ? round($sc->avg(),1) : null;
    $sAvg[] = $subAvg ?? 0;
    $subjStats[] = ['name'=>$items->first()->subject->name??'S','avg'=>$subAvg,'done'=>$d,'total'=>$items->count()];
  }
  $pct = $total>0 ? round(($done/$total)*100) : 0;
@endphp

@if($overdue>0)
<div class="alert-bar">
  <i class="fa-solid fa-triangle-exclamation"></i>
  <span><strong>{{ $overdue }}</strong> task{{ $overdue>1?'s':'' }} are past due and have not been submitted yet.
  Please remind {{ $data['student']->first_name }} to complete them.</span>
</div>
@endif

{{-- STATS ROW --}}
<div class="row g-3 mb-4 align-items-stretch">
  <div class="col-6 col-md-3 d-flex"><div class="sc w-100"><div class="si" style="background:var(--pl);color:var(--p)"><i class="fa-solid fa-layer-group"></i></div><div class="min-w-0"><div class="sv">{{$total}}</div><div class="sl-t">Total Tasks</div></div></div></div>
  <div class="col-6 col-md-3 d-flex"><div class="sc w-100"><div class="si" style="background:var(--gl);color:var(--g)"><i class="fa-solid fa-circle-check"></i></div><div class="min-w-0"><div class="sv">{{$done}}</div><div class="sl-t">Submitted</div></div></div></div>
  <div class="col-6 col-md-3 d-flex"><div class="sc w-100"><div class="si" style="background:var(--al);color:var(--a)"><i class="fa-solid fa-hourglass-half"></i></div><div class="min-w-0"><div class="sv">{{$pending}}</div><div class="sl-t">Pending</div></div></div></div>
  <div class="col-6 col-md-3 d-flex"><div class="sc w-100"><div class="si" style="background:#e0e7ff;color:#4f46e5"><i class="fa-solid fa-star"></i></div><div class="min-w-0"><div class="sv">{{$avg??'—'}}</div><div class="sl-t">Avg Score</div></div></div></div>
</div>

<div class="row g-3 mb-4 align-items-stretch">
  <div class="col-lg-8 d-flex">
    <div class="cc w-100">
      <div class="cc-t"><i class="fa-solid fa-chart-column me-1"></i>Performance by subject</div>
      @if(count($sLabels)>0)
        <div class="pp-hw-chart-wrap flex-grow-1">
          <canvas id="sc1"></canvas>
        </div>
      @else
        <p class="text-center text-muted py-4 mb-0 flex-grow-1 d-flex align-items-center justify-content-center" style="font-size:13px">No graded results yet</p>
      @endif
    </div>
  </div>
  <div class="col-lg-4 d-flex">
    <div class="cc w-100">
      <div class="cc-t"><i class="fa-solid fa-signal me-1"></i>Subject strength</div>
      @php
        $sorted = collect($subjStats)->filter(fn($s)=>$s['avg']!==null)->sortByDesc('avg');
      @endphp
      @if($sorted->isNotEmpty())
        <div class="flex-grow-1">
        @foreach($sorted as $s)
        @php
          $pctScore = $s['avg'] > 0 ? min(100, round($s['avg'])) : 0;
          $cls = $pctScore>=70 ? 'subj-strong' : ($pctScore>=40 ? 'subj-mid' : 'subj-weak');
          $icon= $pctScore>=70 ? 'arrow-trend-up' : ($pctScore>=40 ? 'minus' : 'arrow-trend-down');
          $clr = $pctScore>=70 ? 'var(--g)' : ($pctScore>=40 ? 'var(--sl)' : 'var(--r)');
        @endphp
        <div class="subj-indicator {{ $cls }}">
          <span style="font-weight:600">{{ $s['name'] }}</span>
          <span style="color:{{$clr}};font-weight:700;font-size:12px">
            <i class="fa-solid fa-{{$icon}} me-1"></i>{{ $s['avg'] }}
          </span>
        </div>
        @endforeach
        </div>
      @else
        <p class="text-muted text-center py-3 mb-0 flex-grow-1" style="font-size:12.5px">No scored results yet</p>
      @endif
      @if($avg!==null)
      <div style="background:var(--gl);border-radius:10px;padding:10px 14px;margin-top:auto;border:1px solid rgba(16,185,129,0.25)">
        <div style="font-size:11px;color:#065f46;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Overall average</div>
        <div style="font-size:22px;font-weight:800;color:var(--g)">{{ $avg }}</div>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- HOMEWORK LIST --}}
<div class="card ot-card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h5 class="mb-0">Homework — <span style="color:var(--p)">{{ $data['student']->first_name }}</span></h5>
    <span class="text-muted" style="font-size:13px">{{ $total }} tasks this term</span>
  </div>
  <div class="card-body p-3">
    @forelse($hw as $row)
    @php
      $rec  = $row->submission_record;
      $isOv = !$rec&&$row->submission_date&&\Carbon\Carbon::parse($row->submission_date)->isPast();
      $imap = [
        'quiz'       => ['tq',    'circle-question', 'dbeafe'],
        'homework'   => ['tbg-hw','book-open',       'd1fae5'],
        'project'    => ['tp',    'folder-open',     'fce7f3'],
        'activity'   => ['ta',    'person-running',  'ede9fe'],
        'game'       => ['tg',    'gamepad',         'fef3c7'],
        'assignment' => ['ts',    'file-pen',        'e0f2fe'],
      ];
      $ti = $imap[$row->task_type??'homework'] ?? ['tbg-hw','book-open','d1fae5'];
    @endphp
    <div class="hw-row">
      <div class="hw-row-icon" style="background:#{{$ti[2]}};color:#0f172a">
        <i class="fa-solid fa-{{$ti[1]}}"></i>
      </div>
      <div class="flex-grow-1 min-w-0">
        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
          <div class="min-w-0">
            <div style="font-weight:700;font-size:13.5px;color:#0f172a">{{ $row->title??'—' }}</div>
            <div style="font-size:11.5px;color:var(--sl);margin-top:2px">
              <span class="sbg {{ $ti[0] }}">{{ $row->task_type??'hw' }}</span>
              <span class="ms-2">{{ $row->subject->name??'' }}</span>
            </div>
          </div>
          <div class="text-end flex-shrink-0">
            @if($rec&&$rec->marks!==null)
              <span class="score-pill"><i class="fa-solid fa-star me-1"></i>{{ $rec->marks }} / {{ $row->marks??'?' }}</span>
            @elseif($rec)
              <span style="font-size:12px;color:var(--sl)">Awaiting result</span>
            @elseif($isOv)
              <span class="od-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i>Overdue</span>
            @endif
          </div>
        </div>
        <div class="d-flex gap-3 mt-2 flex-wrap align-items-center" style="font-size:11.5px;color:var(--sl)">
          <span><i class="fa-solid fa-calendar-days me-1"></i>{{ $row->date }}</span>
          <span><i class="fa-solid fa-clock me-1"></i>Due: <strong>{{ $row->submission_date??'—' }}</strong></span>
          <span><i class="fa-solid fa-bullseye me-1"></i>{{ $row->marks??'—' }} marks</span>
          @if($rec)
            <span style="color:var(--g)"><i class="fa-solid fa-check-circle me-1"></i>Submitted {{ $rec->date??'' }}</span>
          @else
            <span style="color:{{ $isOv?'var(--r)':'var(--a)' }}"><i class="fa-solid fa-{{ $isOv?'circle-xmark':'hourglass-half' }} me-1"></i>{{ $isOv?'Not submitted':'Pending' }}</span>
          @endif
        </div>
      </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted"><i class="fa-solid fa-inbox fs-2 d-block opacity-20 mb-2"></i>No homework found.</div>
    @endforelse
  </div>
</div>

@else
<div class="card ot-card"><div class="card-body text-center py-5 text-muted">
  <i class="fa-solid fa-child-reaching fs-2 d-block opacity-20 mb-3"></i>
  <p class="mb-0">Select your child above to view their homework and performance.</p>
</div></div>
@endif

</div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function selChild(id, el) {
  document.querySelectorAll('.child-card').forEach(c=>c.classList.remove('sel'));
  el.classList.add('sel');
  document.getElementById('sel_student').value = id;
  document.getElementById('cf').submit();
}
@if(!empty($data['student']) && count($sLabels) > 0)
(function(){
  var el = document.getElementById('sc1');
  if (!el || typeof Chart === 'undefined') return;
  new Chart(el,{
    type:'bar',
    data:{
      labels:@json($sLabels),
      datasets:[
        {label:'Avg score', data:@json($sAvg), backgroundColor:'#0284c7', borderRadius:6},
        {label:'Submitted', data:@json($sDone), backgroundColor:'#22c55e', borderRadius:6},
        {label:'Total',     data:@json($sTotal),backgroundColor:'#bae6fd', borderRadius:6}
      ]
    },
    options:{
      responsive:true,
      maintainAspectRatio:false,
      scales:{
        y:{beginAtZero:true,ticks:{font:{size:10}},grid:{color:'rgba(14,165,233,0.12)'}},
        x:{ticks:{font:{size:10}},grid:{display:false}}
      },
      plugins:{legend:{labels:{font:{size:10},padding:10}}}
    }
  });
})();
@endif
</script>
@endpush
