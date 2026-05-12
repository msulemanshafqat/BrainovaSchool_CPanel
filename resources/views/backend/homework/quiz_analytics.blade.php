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
.qa-card { background:#fff; border-radius:var(--rr); border:1px solid var(--bb); padding:18px 20px; }
.q-card {
    background:#fff; border-radius:var(--rr); border:1px solid var(--bb);
    padding:20px; margin-bottom:16px; position:relative; overflow:hidden;
}
.q-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; }
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
.acc-badge {
    display:inline-block; padding:3px 10px; border-radius:20px;
    font-size:11px; font-weight:700; text-transform:uppercase;
}
.acc-h { background:var(--bgl); color:#065f46; }
.acc-m { background:#fef3c7; color:#92400e; }
.acc-l { background:var(--brl); color:var(--br); }
.chart-wrap { position:relative; min-height:220px; }
</style>
@endpush

@section('content')
<div class="page-content qa-portal">

@php
    $a               = $data['analytics'];
    $questions       = $a['questions'];
    $homework        = $a['homework'];
    $submitted       = (int) ($a['submitted_students'] ?? 0);
    $maxMarks        = (float) ($a['max_marks'] ?? 0);
    $avgScore        = $a['avg_score'];
    $avgScorePct     = $a['avg_score_pct'];
    $studentScores   = $a['student_scores'] ?? [];
    $histLabels      = $a['histogram_labels'] ?? [];
    $histData        = $a['histogram_data'] ?? [];
    $avgQuestionAcc  = (int) ($a['avg_question_accuracy'] ?? 0);
    $totalQ          = count($questions);
    $hardestQ        = $totalQ ? collect($questions)->sortBy('accuracy_pct')->first() : null;
    $easiestQ        = $totalQ ? collect($questions)->sortByDesc('accuracy_pct')->first() : null;
@endphp

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-sm-8">
            <h4 class="bradecrumb-title mb-1">Quiz analytics</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('homework.index') }}">Homework</a></li>
                <li class="breadcrumb-item active">Analytics</li>
            </ol>
        </div>
        <div class="col-sm-4 text-end">
            <a href="{{ route('homework.export-results', $homework->id) }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fa-solid fa-download me-1"></i>Export CSV
            </a>
            <a href="{{ route('homework.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>
</div>

{{-- Summary: cumulative average across all students who submitted --}}
<div class="qa-card mb-4">
    <div class="row align-items-stretch g-3">
        <div class="col-lg-5">
            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:700">Quiz</div>
            <h5 class="fw-bold mb-1">{{ $homework->title ?? 'Untitled quiz' }}</h5>
            <div class="text-muted" style="font-size:13px">
                Max marks: <strong>{{ $maxMarks > 0 ? rtrim(rtrim(number_format($maxMarks, 2, '.', ''), '0'), '.') : '—' }}</strong>
                &nbsp;|&nbsp; Questions: <strong>{{ $totalQ }}</strong>
                &nbsp;|&nbsp; Submissions: <strong>{{ $submitted }}</strong>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="row g-3 text-center">
                <div class="col-6 col-md-3">
                    <div style="font-size:26px;font-weight:800;color:var(--bp)">
                        {{ $avgScore !== null ? rtrim(rtrim(number_format($avgScore, 2, '.', ''), '0'), '.') : '—' }}
                    </div>
                    <div style="font-size:11px;color:var(--bs);text-transform:uppercase;letter-spacing:.05em">Avg score (all)</div>
                </div>
                <div class="col-6 col-md-3">
                    <div style="font-size:26px;font-weight:800;color:#7c3aed">
                        {{ $avgScorePct !== null ? $avgScorePct . '%' : '—' }}
                    </div>
                    <div style="font-size:11px;color:var(--bs);text-transform:uppercase;letter-spacing:.05em">Avg % of max</div>
                </div>
                <div class="col-6 col-md-3">
                    <div style="font-size:26px;font-weight:800;color:var(--ba)">{{ $avgQuestionAcc }}%</div>
                    <div style="font-size:11px;color:var(--bs);text-transform:uppercase;letter-spacing:.05em">Avg item accuracy</div>
                </div>
                <div class="col-6 col-md-3">
                    <div style="font-size:26px;font-weight:800;color:var(--bg)">{{ $submitted > 0 ? $submitted : '—' }}</div>
                    <div style="font-size:11px;color:var(--bs);text-transform:uppercase;letter-spacing:.05em">Students</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($submitted === 0)
<div class="qa-card text-center py-5 text-muted mb-4">
    <i class="fa-solid fa-user-group fs-2 d-block opacity-25 mb-3"></i>
    <p class="mb-1 fw-semibold">No submissions yet</p>
    <p class="mb-0 small">Per-question charts will populate after students submit the quiz. New submissions also store each answer for this report.</p>
</div>
@else

@if(count($histLabels) && array_sum($histData) > 0)
<div class="qa-card mb-4">
    <h6 class="mb-3" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--bs);font-weight:700">
        <i class="fa-solid fa-chart-simple me-1"></i>Score distribution (% of max marks)
    </h6>
    <div class="chart-wrap"><canvas id="histChart" height="90"></canvas></div>
</div>
@endif

@if(count($studentScores))
<div class="qa-card mb-4">
    <h6 class="mb-3" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--bs);font-weight:700">
        <i class="fa-solid fa-chart-bar me-1"></i>Each student’s score (% of max)
    </h6>
    <div class="chart-wrap" style="min-height:{{ max(220, min(count($studentScores) * 28, 520)) }}px">
        <canvas id="studentBarChart"></canvas>
    </div>
</div>

<div class="qa-card mb-4">
    <h6 class="mb-3" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--bs);font-weight:700">
        <i class="fa-solid fa-table me-1"></i>Scores by student
    </h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th class="text-end">Marks</th>
                    <th class="text-end">% of max</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentScores as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['student_name'] }}</td>
                    <td class="text-end">{{ $row['marks'] !== null ? rtrim(rtrim(number_format($row['marks'], 2, '.', ''), '0'), '.') : '—' }}</td>
                    <td class="text-end">{{ $row['pct'] !== null ? $row['pct'] . '%' : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endif

@if($totalQ > 0)
<div class="qa-card mb-4">
    <div class="row g-3">
        <div class="col-md-6">
            <h6 class="mb-2" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--bs);font-weight:700">
                <i class="fa-solid fa-bullseye me-1"></i>Item accuracy by question
            </h6>
            <div class="chart-wrap"><canvas id="accChart" height="100"></canvas></div>
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <div class="text-center py-3">
                <div class="text-muted small text-uppercase fw-bold mb-1">Hardest item</div>
                <div class="fs-3 fw-bold text-danger">{{ $hardestQ ? $hardestQ['accuracy_pct'] . '%' : '—' }}</div>
            </div>
            <div class="text-center py-3 border-top">
                <div class="text-muted small text-uppercase fw-bold mb-1">Easiest item</div>
                <div class="fs-3 fw-bold text-success">{{ $easiestQ ? $easiestQ['accuracy_pct'] . '%' : '—' }}</div>
            </div>
        </div>
    </div>
</div>
@endif

@if(empty($questions))
<div class="qa-card text-center py-5 text-muted mb-4">
    <i class="fa-solid fa-circle-question fs-2 d-block opacity-20 mb-2"></i>
    <p class="mb-0">No questions are defined for this quiz.</p>
</div>
@else

@foreach($questions as $i => $q)
@php
    $pct   = $q['accuracy_pct'];
    $cls   = $pct >= 70 ? 'acc-high' : ($pct >= 40 ? 'acc-mid' : 'acc-low');
    $accBg = $pct >= 70 ? 'acc-h'    : ($pct >= 40 ? 'acc-m'   : 'acc-l');
    $wrongCounts = [
        'a' => $q['option_counts']['a'],
        'b' => $q['option_counts']['b'],
        'c' => $q['option_counts']['c'],
        'd' => $q['option_counts']['d'],
    ];
    foreach(['a','b','c','d'] as $letter) {
        $optionText = $q['option_'.$letter] ?? '';
        if(strtolower(trim((string)$optionText)) === strtolower(trim((string)($q['correct_answer'] ?? '')))) {
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
                <p class="mb-0 fw-semibold" style="font-size:14px;color:var(--bt)">{{ $q['question'] }}</p>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span class="acc-badge {{ $accBg }}">{{ $pct }}% correct</span>
                    <span class="text-muted" style="font-size:12px">{{ $q['correct_count'] }} / {{ max($q['total_attempts'], 1) }} responses</span>
                </div>
            </div>
            @if($q['hint'])
            <div class="text-muted mb-2" style="font-size:12px">
                <i class="fa-solid fa-lightbulb me-1 text-warning"></i>Hint: {{ $q['hint'] }}
            </div>
            @endif
        </div>
    </div>

    <div class="ps-md-5">
        @foreach($opts as $letter => $opt)
        @php
            $isCorrect   = strtolower(trim((string)$opt['text'])) === strtolower(trim((string)($q['correct_answer'] ?? '')));
            $isMostWrong = ($letter === $mostWrongLetter && !$isCorrect && $opt['count'] > 0);
            $barClass    = $isCorrect ? 'opt-correct' : ($isMostWrong ? 'opt-most-wrong' : 'opt-wrong');
            $barWidth    = $maxCount > 0 ? round(($opt['count'] / $maxCount) * 100) : 0;
            $den         = $submitted > 0 ? $submitted : max($q['total_attempts'], 1);
            $countPct    = $den > 0 ? round(($opt['count'] / $den) * 100) : 0;
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

        @if($q['option_counts']['skipped'] > 0)
        @php $skipPct = $submitted > 0 ? round(($q['option_counts']['skipped'] / $submitted) * 100) : 0; @endphp
        <div class="opt-bar-wrap" style="opacity:.65">
            <span class="opt-label text-muted">—</span>
            <div class="opt-bar-bg opt-wrong" style="flex:1">
                <div class="opt-bar-fill" style="width:{{ min(100, $skipPct) }}%;background:#cbd5e1"></div>
            </div>
            <span class="opt-count">{{ $q['option_counts']['skipped'] }}</span>
            <span class="opt-pct text-muted">({{ $skipPct }}%)</span>
            <span class="text-muted" style="font-size:11px">no response / not stored</span>
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
(function () {
    const histEl = document.getElementById('histChart');
    const histLabels = @json($histLabels);
    const histData   = @json($histData);
    if (histEl && histLabels.length && histData.some(n => n > 0)) {
        new Chart(histEl, {
            type: 'bar',
            data: {
                labels: histLabels,
                datasets: [{
                    label: 'Students',
                    data: histData,
                    backgroundColor: '#6366f1',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    const studentEl = document.getElementById('studentBarChart');
    const students = @json($studentScores);
    if (studentEl && students.length) {
        const labels = students.map(s => s.student_name);
        const pcts   = students.map(s => s.pct !== null ? s.pct : 0);
        new Chart(studentEl, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '% of max marks',
                    data: pcts,
                    backgroundColor: pcts.map(p => p >= 70 ? '#059669' : (p >= 40 ? '#f59e0b' : '#dc2626')),
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: c => ' ' + c.raw + '%' } }
                },
                scales: {
                    x: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
                    y: { ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    const accEl = document.getElementById('accChart');
    const questions = @json($questions);
    if (accEl && questions.length) {
        const labels = questions.map((_, i) => 'Q' + (i + 1));
        new Chart(accEl, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Accuracy %',
                    data: questions.map(q => q.accuracy_pct),
                    backgroundColor: questions.map(q => q.accuracy_pct >= 70 ? '#059669' : (q.accuracy_pct >= 40 ? '#f59e0b' : '#dc2626')),
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
                    x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' ' + ctx.raw + '% correct' } }
                }
            }
        });
    }
})();
</script>
@endpush
