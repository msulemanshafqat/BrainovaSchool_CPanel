@forelse($homeworks as $row)
@php
  $typeMap = ['homework'=>'hw','quiz'=>'quiz','project'=>'project','activity'=>'activity','game'=>'game','assignment'=>'assignment'];
  $typeKey = $typeMap[$row->task_type ?? 'homework'] ?? 'hw';
  $isOverdue = $row->submission_date && \Carbon\Carbon::parse($row->submission_date)->isPast();
@endphp
<tr>
  <td class="hw-quest-num">{{ $loop->iteration }}</td>
  <td>
    <div class="hw-quest-title" title="{{ $row->title ?? '' }}">{{ $row->title ?? '—' }}</div>
  </td>
  <td class="hw-quest-meta">
    {{ $row->class->name ?? '—' }} / {{ $row->section->name ?? '—' }}
  </td>
  <td class="hw-quest-meta">{{ $row->subject->name ?? '—' }}</td>
  <td>
    <span class="tbg tbg-{{ $typeKey }}">{{ $row->task_type ?? '—' }}</span>
  </td>
  <td class="hw-quest-meta">
    {{ $row->submission_date ?? '—' }}
    @if($isOverdue)
      <span class="od">Late</span>
    @endif
  </td>
  <td class="hw-quest-marks">{{ $row->marks ?? '—' }}</td>
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
