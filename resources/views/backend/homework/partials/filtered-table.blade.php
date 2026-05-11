@php
  $pendingMarkIds = collect($homeworkIdsPendingMarks ?? [])->flip()->all();
@endphp
@forelse($homeworks as $row)
@php
  $needsMarking = isset($pendingMarkIds[$row->id]);
  $typeMap = ['homework'=>'hw','quiz'=>'quiz','project'=>'project','activity'=>'activity','game'=>'game','assignment'=>'assignment'];
  $typeKey = $typeMap[$row->task_type ?? 'homework'] ?? 'hw';
  $pillClass = match ($typeKey) {
    'quiz' => 'hw-type-pill--quiz',
    'hw' => 'hw-type-pill--hw',
    'project' => 'hw-type-pill--project',
    'activity' => 'hw-type-pill--activity',
    'game' => 'hw-type-pill--game',
    'assignment' => 'hw-type-pill--assignment',
    default => 'hw-type-pill--default',
  };
  $isOverdue = $row->submission_date && \Carbon\Carbon::parse($row->submission_date)->isPast();
  $dueSort = ($row->submission_date ?? null)
    ? \Carbon\Carbon::parse($row->submission_date)->format('Y-m-d')
    : '';
  $marksRaw = isset($row->marks) ? trim((string) $row->marks) : '';
  $marksSort = preg_match('/^-?\d+(?:\.\d+)?$/', $marksRaw) ? $marksRaw : '';
  $sortTitle = \Illuminate\Support\Str::lower(trim(strip_tags((string) ($row->title ?? ''))));
  $sortClassSection = \Illuminate\Support\Str::lower(trim((string) (($row->class->name ?? '').' '.($row->section->name ?? ''))));
  $sortSubject = \Illuminate\Support\Str::lower(trim((string) ($row->subject->name ?? '')));
  $sortType = (string) ($row->task_type ?? '');
@endphp
<tr data-hw-row-id="{{ $row->id }}" @if($needsMarking) class="hw-row-needs-marking" @endif>
  <td class="serial hw-quest-num">{{ $loop->iteration }}</td>
  <td data-sort="{{ e($sortTitle) }}">
    <strong>{{ $row->title ?? '—' }}</strong>@if($needsMarking)<span class="hw-marking-asterisk" title="Has submitted work awaiting marks" role="img" aria-label="Awaiting marks">*</span>@endif
  </td>
  <td data-sort="{{ e($sortClassSection) }}">
    {{ $row->class->name ?? '—' }} / {{ $row->section->name ?? '—' }}
  </td>
  <td data-sort="{{ e($sortSubject) }}">{{ $row->subject->name ?? '—' }}</td>
  <td data-sort="{{ e($sortType) }}">
    <span class="hw-type-pill {{ $pillClass }}">{{ $row->task_type ?? '—' }}</span>
  </td>
  <td data-sort="{{ e($dueSort) }}">
    {{ $row->submission_date ?? '—' }}
    @if($isOverdue)
      <span class="badge bg-warning text-dark ms-1">Late</span>
    @endif
  </td>
  <td class="text-end" data-sort="{{ e($marksSort) }}">{{ $row->marks ?? '—' }}</td>
  <td class="action">
    @if(hasPermission('homework_update'))
      <div class="d-flex flex-wrap gap-1 justify-content-end">
        <a href="{{ route('homework.edit', $row->id) }}"
           class="btn btn-sm btn-outline-primary"
           title="{{ ___('common.edit') }}">
          <i class="fa-solid fa-pencil"></i>
        </a>
        <button type="button"
                class="btn btn-sm btn-outline-secondary"
                title="Evaluate submissions"
                onclick="openEval({{ $row->id }})">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
      </div>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="8" class="text-center text-muted py-4">
    <i class="fa-solid fa-inbox d-block mb-2 fs-3 opacity-25"></i>
    No homework found for the selected filters.
  </td>
</tr>
@endforelse
