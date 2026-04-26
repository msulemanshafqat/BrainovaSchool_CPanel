@extends('backend.master')
@section('title') {{ @$data['title'] }} @endsection

@push('style')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
.qa-portal { font-family: 'Plus Jakarta Sans', sans-serif; }
:root {
    --bp:#1d4ed8; --bpl:#dbeafe; --bg:#059669; --bgl:#d1fae5;
    --br:#dc2626; --brl:#fee2e2; --ba:#f59e0b; --bs:#64748b;
    --bb:#e2e8f0; --bt:#0f172a; --rr:12px;
}

/* Summary cards */
.qa-card { background:#fff; border-radius:var(--rr); border:1px solid var(--bb); padding:18px 20px; }

/* Question cards */
.q-card {
    background:#fff; border-radius:var(--rr); border:1px solid var(--bb);
    padding:20px; margin-bottom:16px; position:relative; overflow:hidden;
}
.q-card::before {
    content:''; position:absolute; top:0; left:0; width:4px; height:100%;
}
.q-card.acc-high::before  { background:var(--bg); }
.q-card.acc-mid::before   { background:var(--ba); }
.q-card.acc-low::before   { background:var(--br); }

.q-num {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:50%; background:var(--bpl);
    color:var(--bp); font-size:12px; font-weight:800; flex-shrink:0;
}
.opt-bar-wrap { display:flex; align-items:center; gap:10px; margin-bottom:6px; }
.opt-label { font-size:12px; font-weight:700; width:22px; flex-shrink:0; }
.opt-bar-bg { flex:1; background:#f1f5f9; border-radius:6px; height:18px; overflow:hidden; position:relative; }
.opt-bar-fill { height:100%; border-radius:6px; transition:width .4s; }
.opt-correct .opt-bar-fill { background:var(--bg); }
.opt-wrong   .opt-bar-fill { background:#94a3b8; }
.opt-most-wrong .opt-bar-fill { background:var(--br); }
.opt-count { font-size:11.5px; color:var(--bs); min-width:30px; text-align:right; }
.opt-pct   { font-size:11px; color:var(--bs); min-width:36px; text-align:right; }

/* Accuracy badge */
.acc-badge {
    display:inline-block; padding:3px 10px; border-radius:20px;
    font-size:11px; font-weight:700; text-transform:uppercase;
}
.acc-h { background:var(--bgl); color:#065f46; }
.acc-m { background:#fef3c7; color:#92400e; }
.acc-l { background:var(--brl); color:var(--br); }
</style>
@endpush

@section('content')
<div class="page-content qa-portal">

@php
    $analytics     = $data['analytics'];
    $questions     = $analytics['questions'];
    $totalStudents = $analytics['total_students'];
    $homework      = $analytics['homework'];

    // Overall quiz stats
    $totalQ     = count($questions);
    $avgAccuracy = $totalQ > 0
        ? round(array_sum(array_column($questions, 'accuracy_pct')) / $totalQ)
        : 0;
    $hardestQ   = collect($questions)->sortBy('accuracy_pct')->first();
    $easiestQ   = collect($questions)->sortByDesc('accuracy_pct')->first();
@endphp

{{-- BREADCRUMB --}}
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-8">
            <h4 class="bradecrumb-title mb-1">Quiz Analytics</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('homework.index') }}">Homework</a></li>
                <li class="breadcrumb-item active">Analytics</li>
            </ol>
        </div>
        <div class="col-sm-4 text-end">
            {{-- Feature C: Export from analytics page too --}}
            <a href="{{ route('homework.export-results', $homework->id) }}"
               class="btn btn-sm btn-outline-secondary me-2">
                <i class="fa-solid fa-download me-1"></i>Export CSV
            </a>
            <a href="{{ route('homework.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>
</div>

{{-- QUIZ SUMMARY --}}
<div class="qa-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:700">Quiz</div>
            <h5 class="fw-bold mb-1">{{ $homework->title ?? 'Untitled Quiz' }}</h5>
            <div class="text-muted" style="font-size:13px">
                Total marks: <strong>{{ $homework->marks ?? '—' }}</strong> &nbsp;|&nbsp;
                Questions: <strong>{{ $totalQ }}</strong> &nbsp;|&nbsp;
                Students attempted: <strong>{{ $totalStudents }}</strong>
            </div>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <div class="row g-3">
                <div class="col-4 text-center">
                    <div style="font-size:28px;font-weight:800;color:var(--bp)">{{ $avgAccuracy }}%</div>
                    <div style="font-size:11px;color:var(--bs);text-transform:uppercase;letter-spacing:.05em">Avg Accuracy</div>
                </div>
                <div class="col-4 text-center">
                    <div style="font-size:28px;font-weight:800;color:var(--br)">
                        {{ $hardestQ ? $hardestQ['accuracy_pct'] : '—' }}%
                    </div>
                    <div style="font-size:11px;color:var(--bs);text-transform:uppercase;letter-spacing:.05em">Hardest Q</div>
                </div>
                <div class="col-4 text-center">
                    <div style="font-size:28px;font-weight:800;color:var(--bg)">
                        {{ $easiestQ ? $easiestQ['accuracy_pct'] : '—' }}%
                    </div>
                    <div style="font-size:11px;color:var(--bs);text-transform:uppercase;letter-spacing:.05em">Easiest Q</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- OVERALL ACCURACY CHART --}}
@if($totalQ > 0)
<div class="qa-card mb-4">
    <h6 class="mb-3" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--bs);font-weight:700">
        <i class="fa-solid fa-chart-column me-1"></i>Accuracy Per Question
    </h6>
    <canvas id="accChart" height="100"></canvas>
</div>
@endif

{{-- PER-QUESTION CARDS --}}
@if(empty($questions))
<div class="qa-card text-center py-5 text-muted">
    <i class="fa-solid fa-chart-bar fs-2 d-block opacity-20 mb-2"></i>
    <p class="mb-0">No student answers recorded yet. Students must submit the quiz first.</p>
</div>
@else

@foreach($questions as $i => $q)
@php
    $pct   = $q['accuracy_pct'];
    $cls   = $pct >= 70 ? 'acc-high' : ($pct >= 40 ? 'acc-mid' : 'acc-low');
    $accBg = $pct >= 70 ? 'acc-h'    : ($pct >= 40 ? 'acc-m'   : 'acc-l');

    // Which wrong option was chosen most often?
    $wrongCounts = [
        'a' => $q['option_counts']['a'],
        'b' => $q['option_counts']['b'],
        'c' => $q['option_counts']['c'],
        'd' => $q['option_counts']['d'],
    ];
    // Zero out the correct option so we don't flag it as "most wrong"
    foreach(['a','b','c','d'] as $letter) {
        $optionText = $q['option_'.$letter] ?? '';
        if(strtolower(trim($optionText)) === strtolower(trim($q['correct_answer']))) {
            $wrongCounts[$letter] = -1;
        }
    }
    $mostWrongLetter = array_search(max($wrongCounts), $wrongCounts);

    $opts = [
        'a' => ['label'=>'A','text'=>$q['option_a'],'count'=>$q['option_counts']['a']],
        'b' => ['label'=>'B','text'=>$q['option_b'],'count'=>$q['option_counts']['b']],
        'c' => ['label'=>'C','text'=>$q['option_c'],'count'=>$q['option_counts']['c']],
        'd' => ['label'=>'D','text'=>$q['option_d'],'count'=>$q['option_counts']['d']],
    ];
    $maxCount = max($q['option_counts']['a'], $q['option_counts']['b'], $q['option_counts']['c'], $q['option_counts']['d'], 1);
@endphp

<div class="q-card {{ $cls }}">
    <div class="d-flex align-items-start gap-3 mb-3">
        <span class="q-num">{{ $i + 1 }}</span>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <p class="mb-0 fw-semibold" style="font-size:14px;color:var(--bt)">
                    {{ $q['question'] }}
                </p>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span class="acc-badge {{ $accBg }}">{{ $pct }}% correct</span>
                    <span class="text-muted" style="font-size:12px">
                        {{ $q['correct_count'] }} / {{ $q['total_attempts'] }} students
                    </span>
                </div>
            </div>

            {{-- Hint indicator --}}
            @if($q['hint'])
            <div class="text-muted mb-2" style="font-size:12px">
                <i class="fa-solid fa-lightbulb me-1 text-warning"></i>
                Hint: {{ $q['hint'] }}
            </div>
            @endif
        </div>
    </div>

    {{-- Option bar chart --}}
    <div class="ps-5">
        @foreach($opts as $letter => $opt)
        @php
            $isCorrect   = strtolower(trim($opt['text'])) === strtolower(trim($q['correct_answer']));
            $isMostWrong = ($letter === $mostWrongLetter && !$isCorrect && $opt['count'] > 0);
            $barClass    = $isCorrect ? 'opt-correct' : ($isMostWrong ? 'opt-most-wrong' : 'opt-wrong');
            $barWidth    = $maxCount > 0 ? round(($opt['count'] / $maxCount) * 100) : 0;
            $countPct    = $q['total_attempts'] > 0 ? round(($opt['count'] / $q['total_attempts']) * 100) : 0;
        @endphp
        <div class="opt-bar-wrap">
            <span class="opt-label" style="color:{{ $isCorrect ? 'var(--bg)' : ($isMostWrong ? 'var(--br)' : 'var(--bs)') }}">
                {{ $opt['label'] }}
                @if($isCorrect)<i class="fa-solid fa-check" style="font-size:9px"></i>@endif
            </span>
            <div class="opt-bar-bg {{ $barClass }}">
                <div class="opt-bar-fill" style="width:{{ $barWidth }}%"></div>
            </div>
            <span class="opt-count">{{ $opt['count'] }}</span>
            <span class="opt-pct text-muted">({{ $countPct }}%)</span>
            @if($isMostWrong)
                <span class="badge bg-danger" style="font-size:9px">Most chosen wrong</span>
            @endif
        </div>
        @endforeach

        {{-- Skipped row --}}
        @if($q['option_counts']['skipped'] > 0)
        @php $skipPct = $q['total_attempts']>0 ? round(($q['option_counts']['skipped'] / ($q['total_attempts'] + $q['option_counts']['skipped'])) * 100) : 0; @endphp
        <div class="opt-bar-wrap" style="opacity:.6">
            <span class="opt-label text-muted">—</span>
            <div class="opt-bar-bg opt-wrong" style="flex:1">
                <div class="opt-bar-fill" style="width:{{ $skipPct }}%;background:#cbd5e1"></div>
            </div>
            <span class="opt-count">{{ $q['option_counts']['skipped'] }}</span>
            <span class="opt-pct text-muted">({{ $skipPct }}%)</span>
            <span class="text-muted" style="font-size:11px">skipped</span>
        </div>
        @endif
    </div>
</div>
@endforeach
@endif

</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(!empty($questions))
// Bar chart: accuracy per question
new Chart(document.getElementById('accChart'), {
    type: 'bar',
    data: {
        labels: @json(array_map(fn($i) => 'Q'.($i+1), array_keys($questions))),
        datasets: [{
            label: 'Accuracy %',
            data: @json(array_column($questions, 'accuracy_pct')),
            backgroundColor: @json(array_map(fn($q) => $q['accuracy_pct'] >= 70 ? '#059669' : ($q['accuracy_pct'] >= 40 ? '#f59e0b' : '#dc2626'), $questions)),
            borderRadius: 5,
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true, max: 100,
                ticks: { callback: v => v + '%', font: { size: 11 } },
                grid: { color: '#f1f5f9' }
            },
            x: { ticks: { font: { size: 11 } }, grid: { display: false } }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.raw}% correct`
                }
            }
        }
    }
});
@endif
</script>
@endpush
