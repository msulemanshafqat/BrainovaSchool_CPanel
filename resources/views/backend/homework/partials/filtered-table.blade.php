@forelse($homeworks as $row)
@php
  $typeMap = ['homework'=>'hw','quiz'=>'quiz','project'=>'project','activity'=>'activity','game'=>'game','assignment'=>'assignment'];
  $typeKey = $typeMap[$row->task_type ?? 'homework'] ?? 'hw';
  $isOverdue = $row->submission_date && \Carbon\Carbon::parse($row->submission_date)->isPast();
  $bg = $loop->even ? '#fafbff' : '#fff';
@endphp
<tr style="background:{{ $bg }}"
    onmouseover="this.style.background='#eff4ff'"
    onmouseout="this.style.background='{{ $bg }}'">
  <td style="font-size:11px;color:#94a3b8;text-align:center;font-weight:600">{{ $loop->iteration }}</td>
  <td>
    <div style="font-weight:600;font-size:13px;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px"
         title="{{ $row->title ?? '' }}">{{ $row->title ?? '—' }}</div>
  </td>
  <td style="font-size:12px;white-space:nowrap">
    {{ $row->class->name ?? '—' }} / {{ $row->section->name ?? '—' }}
  </td>
  <td style="font-size:12px">{{ $row->subject->name ?? '—' }}</td>
  <td>
    <span class="tbg tbg-{{ $typeKey }}">{{ $row->task_type ?? '—' }}</span>
  </td>
  <td style="font-size:12px;white-space:nowrap">
    {{ $row->submission_date ?? '—' }}
    @if($isOverdue)
      <span class="od">Late</span>
    @endif
  </td>
  <td style="font-weight:700;font-size:13px;color:#1d4ed8;text-align:center">{{ $row->marks ?? '—' }}</td>
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
  <td colspan="8" style="text-align:center;padding:36px;color:#94a3b8">
    <i class="fa-solid fa-inbox" style="font-size:2rem;opacity:.2;display:block;margin-bottom:10px"></i>
    No homework found for the selected filters.
  </td>
</tr>
@endforelse