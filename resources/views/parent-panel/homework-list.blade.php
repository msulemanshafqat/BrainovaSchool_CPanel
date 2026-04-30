@extends('parent-panel.partials.master')
@section('title') {{ ___('common.Homework List') }} @endsection

@push('style')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
.pp{font-family:'Plus Jakarta Sans',sans-serif}
:root{--p:#1d4ed8;--pl:#dbeafe;--a:#f59e0b;--al:#fef3c7;--g:#059669;--gl:#d1fae5;--r:#dc2626;--rl:#fee2e2;--sl:#64748b;--bd:#e2e8f0;--rr:12px}
.child-card{background:#fff;border:2px solid var(--bd);border-radius:var(--rr);padding:16px 20px;display:flex;align-items:center;gap:14px;cursor:pointer;transition:all .15s}
.child-card.sel,.child-card:hover{border-color:var(--p);box-shadow:0 4px 14px rgba(29,78,216,.12)}
.child-av{width:44px;height:44px;border-radius:50%;background:var(--pl);color:var(--p);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;flex-shrink:0}
.sc{background:#fff;border-radius:var(--rr);border:1px solid var(--bd);padding:16px 18px;display:flex;align-items:center;gap:12px}
.si{width:42px;height:42px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:17px}
.sv{font-size:24px;font-weight:800;line-height:1;color:#0f172a}
.sl-t{font-size:11px;color:var(--sl);font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:1px}
.cc{background:#fff;border:1px solid var(--bd);border-radius:var(--rr);padding:20px}
.cc-t{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--sl);font-weight:700;margin-bottom:14px}
.hw-row{background:#fff;border-radius:10px;border:1px solid var(--bd);padding:14px 16px;margin-bottom:10px;display:flex;align-items:flex-start;gap:14px;transition:box-shadow .15s}
.hw-row:hover{box-shadow:0 4px 12px rgba(0,0,0,.06)}
.hw-row-icon{width:36px;height:36px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:15px}
.sbg{display:inline-block;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase}
.tq{background:#dbeafe;color:#1e40af}.tbg-hw{background:#d1fae5;color:#065f46}.tp{background:#fce7f3;color:#9d174d}
.ta{background:#ede9fe;color:#5b21b6}.tg{background:#fef3c7;color:#92400e}.ts{background:#e0f2fe;color:#075985}
.score-pill{background:var(--gl);color:#065f46;border-radius:20px;padding:2px 10px;font-size:12px;font-weight:700}
.od-pill{background:var(--rl);color:var(--r);border-radius:6px;padding:2px 7px;font-size:11px;font-weight:700}
.subj-indicator{display:flex;align-items:center;justify-content:space-between;padding:7px 10px;border-radius:8px;margin-bottom:6px;font-size:12.5px}
.subj-strong{background:var(--gl);border:1px solid #6ee7b7}
.subj-weak{background:var(--rl);border:1px solid #fca5a5}
.subj-mid{background:#f8fafc;border:1px solid var(--bd)}
.alert-bar{background:var(--rl);border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;font-size:12.5px;color:var(--r);display:flex;align-items:center;gap:8px;margin-bottom:16px}
</style>
@endpush

@section('content')
<div class="page-content pp">

{{-- $sLabels defined unconditionally so @push('script') never crashes when no student selected --}}
@php $sLabels=[]; $sDone=[]; $sTotal=[]; $sAvg=[]; @endphp

<div class="page-header">
  <div class="row">
    <div class="col-sm-6">
      <h4 class="bradecrumb-title mb-1">{{ ___('settings.homework_list') }}</h4>
      <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li><li class="breadcrumb-item active">Homework</li></ol>
    </div>
  </div>
</div>

{{-- CHILD SELECTOR --}}
<div class="card ot-card mb-4">
  <div class="card-body">
    <h6 class="mb-3 fw-bold" style="font-size:13px;text-transform:uppercase;letter-spacing:.06em;color:var(--sl)">Select Your Child</h6>
    <form action="{{ route('parent-panel-homeworks.search') }}" method="post" id="cf">
      @csrf
      <input type="hidden" name="student" id="sel_student">

      {{-- FIX: Guard against $data['students'] being null/false.
           ?? [] prevents "Trying to access array offset on false" crash
           when the parent has no children linked in the database. --}}
      @if(!empty($data['students'] ?? []))
        <div class="d-flex gap-3 flex-wrap">
          @foreach($data['students'] ?? [] as $item)
            @php $sel=(old('student',Session::get('student_id'))==$item->id); @endphp
            <div class="child-card {{ $sel?'sel':'' }}" style="min-width:180px" onclick="selChild({{ $item->id }},this)">
              <div class="child-av">{{ strtoupper(substr($item->first_name,0,1)) }}</div>
              <div>
                <div style="font-weight:700;font-size:14px">{{ $item->first_name }} {{ $item->last_name }}</div>
                <div style="font-size:11.5px;color:var(--sl)">{{ $item->admission_no??'' }}</div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        {{-- Shown when the parent account has no children linked --}}
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
  // $hw is a Collection so filter() works on all records
  $hw      = $data['homeworks'] ?? collect();
  $total   = $hw->count();
  $done    = $hw->filter(fn($h)=>$h->submission_record)->count();
  $pending = $total - $done;
  $overdue = $hw->filter(fn($h)=>!$h->submission_record&&$h->submission_date&&\Carbon\Carbon::parse($h->submission_date)->isPast())->count();
  $scores  = $hw->filter(fn($h)=>$h->submission_record&&$h->submission_record->marks!==null)->pluck('submission_record.marks');
  $avg     = $scores->count()>0 ? round($scores->avg(),1) : null;
  $bySubj  = $hw->groupBy('subject_id');

  // Build chart arrays + weak/strong subject detection
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

{{-- Overdue alert --}}
@if($overdue>0)
<div class="alert-bar">
  <i class="fa-solid fa-triangle-exclamation"></i>
  <strong>{{ $overdue }}</strong> task{{ $overdue>1?'s':'' }} are past due and have not been submitted yet.
  Please remind {{ $data['student']->first_name }} to complete them.
</div>
@endif

{{-- STATS ROW --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="sc"><div class="si" style="background:var(--pl);color:var(--p)"><i class="fa-solid fa-layer-group"></i></div><div><div class="sv">{{$total}}</div><div class="sl-t">Total Tasks</div></div></div></div>
  <div class="col-6 col-md-3"><div class="sc"><div class="si" style="background:var(--gl);color:var(--g)"><i class="fa-solid fa-circle-check"></i></div><div><div class="sv">{{$done}}</div><div class="sl-t">Submitted</div></div></div></div>
  <div class="col-6 col-md-3"><div class="sc"><div class="si" style="background:var(--al);color:var(--a)"><i class="fa-solid fa-hourglass-half"></i></div><div><div class="sv">{{$pending}}</div><div class="sl-t">Pending</div></div></div></div>
  <div class="col-6 col-md-3"><div class="sc"><div class="si" style="background:#f5f3ff;color:#7c3aed"><i class="fa-solid fa-star"></i></div><div><div class="sv">{{$avg??'—'}}</div><div class="sl-t">Avg Score</div></div></div></div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-8">
    <div class="cc">
      <div class="cc-t"><i class="fa-solid fa-chart-column me-1"></i>Performance by Subject</div>
      @if(count($sLabels)>0)<canvas id="sc1" height="230"></canvas>
      @else<p class="text-center text-muted py-4" style="font-size:13px">No graded results yet</p>@endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="cc" style="height:100%">
      <div class="cc-t"><i class="fa-solid fa-signal me-1"></i>Subject Strength</div>
      @php
        $sorted = collect($subjStats)->filter(fn($s)=>$s['avg']!==null)->sortByDesc('avg');
      @endphp
      @if($sorted->isNotEmpty())
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
      @else
        <p class="text-muted text-center py-3" style="font-size:12.5px">No scored results yet</p>
      @endif
      @if($avg!==null)
      <div style="background:var(--gl);border-radius:8px;padding:10px 14px;margin-top:10px">
        <div style="font-size:11px;color:#065f46;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Overall Average</div>
        <div style="font-size:22px;font-weight:800;color:var(--g)">{{ $avg }}</div>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- HOMEWORK LIST --}}
<div class="card ot-card">
  <div class="card-header d-flex justify-content-between align-items-center">
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
      <div class="hw-row-icon" style="background:#{{$ti[2]}};color:var(--sl)">
        <i class="fa-solid fa-{{$ti[1]}}"></i>
      </div>
      <div class="flex-grow-1" style="min-width:0">
        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
          <div>
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
        <div class="d-flex gap-3 mt-2 flex-wrap" style="font-size:11.5px;color:var(--sl)">
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
new Chart(document.getElementById('sc1'),{
  type:'bar',
  data:{
    labels:@json($sLabels),
    datasets:[
      {label:'Avg Score', data:@json($sAvg), backgroundColor:'#1d4ed8', borderRadius:5},
      {label:'Submitted', data:@json($sDone), backgroundColor:'#10b981', borderRadius:5},
      {label:'Total',     data:@json($sTotal),backgroundColor:'#dbeafe', borderRadius:5}
    ]
  },
  options:{responsive:true,scales:{y:{beginAtZero:true,ticks:{font:{size:10}},grid:{color:'#f1f5f9'}},x:{ticks:{font:{size:10}},grid:{display:false}}},plugins:{legend:{labels:{font:{size:10},padding:8}}}}
});
@endif
</script>
@endpush
