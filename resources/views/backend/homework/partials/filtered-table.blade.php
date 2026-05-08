@forelse($homeworks as $row)
@php
  $typeMap = ['homework'=>'hw','quiz'=>'quiz','project'=>'project','activity'=>'activity','game'=>'game','assignment'=>'assignment'];
  $typeKey = $typeMap[$row->task_type ?? 'homework'] ?? 'hw';
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
<tr data-hw-row-id="{{ $row->id }}">
  <td class="hw-quest-num">{{ $loop->iteration }}</td>
  <td data-sort="{{ e($sortTitle) }}">
    <div class="hw-quest-title" title="{{ $row->title ?? '' }}">{{ $row->title ?? '—' }}</div>
  </td>
  <td class="hw-quest-meta" data-sort="{{ e($sortClassSection) }}">
    {{ $row->class->name ?? '—' }} / {{ $row->section->name ?? '—' }}
  </td>
  <td class="hw-quest-meta" data-sort="{{ e($sortSubject) }}">{{ $row->subject->name ?? '—' }}</td>
  <td data-sort="{{ e($sortType) }}">
    <span class="tbg tbg-{{ $typeKey }}">{{ $row->task_type ?? '—' }}</span>
  </td>
  <td class="hw-quest-meta" data-sort="{{ e($dueSort) }}">
    {{ $row->submission_date ?? '—' }}
    @if($isOverdue)
      <span class="od">Late</span>
    @endif
  </td>
  <td class="hw-quest-marks" data-sort="{{ e($marksSort) }}">{{ $row->marks ?? '—' }}</td>
  <td class="hw-act-cell">
    @if(hasPermission('homework_update'))
      <a href="{{ route('homework.edit', $row->id) }}"
         class="ab"
         title="{{ ___('common.edit') }}">
        <i class="fa-solid fa-pencil" style="font-size:11px"></i>
      </a>
      <button type="button"
              class="ab"
              title="Evaluate submissions"
              onclick="openEval({{ $row->id }})">
        <i class="fa-solid fa-pen-to-square" style="font-size:11px"></i>
      </button>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="8" class="hw-empty">
    <i class="fa-solid fa-inbox" style="font-size:2rem;opacity:.25;display:block;margin-bottom:10px"></i>
    No homework found for the selected filters.
  </td>
</tr>
@endforelse
