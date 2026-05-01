{{-- Partial: Filtered Homework Table Rows --}}
{{-- Rendered by getFilteredHomeworkReport() in the repository --}}
{{-- Returns only the <tbody> rows, wrapped in the parent table by the AJAX client --}}

@forelse($homeworks as $row)
@php
  $ov      = ($row->submission_date && \Carbon\Carbon::parse($row->submission_date)->isPast());
  $typeMap = ['homework'=>'hw','quiz'=>'quiz','project'=>'project','activity'=>'activity','game'=>'game','assignment'=>'assignment'];
  $typeKey = $typeMap[$row->task_type ?? 'homework'] ?? 'hw';
  $rowBg   = $loop->even ? '#f7f9ff' : '#ffffff';
@endphp

<tr data-ft="{{ $row->task_type }}" style="background:{{ $rowBg }}" class="hw-tbl-row"
    onmouseover="this.style.background='#eef4ff'" onmouseout="this.style.background='{{ $rowBg }}'">

  {{-- Col 1: Row number --}}
  <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;font-size:11px;color:#94a3b8;vertical-align:middle;text-align:center">{{ $loop->iteration }}</td>

  {{-- Col 2: Title --}}
  <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;vertical-align:middle;max-width:200px">
    <div style="font-weight:600;font-size:12.5px;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:190px"
         title="{{ $row->title ?? '' }}">{{ $row->title ?? '—' }}</div>
  </td>

  {{-- Col 3: Class / Section --}}
  <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;font-size:12px;color:#334155;vertical-align:middle">
    <span style="font-weight:600">{{ $row->class->name ?? '—' }}</span>
    <div style="font-size:11px;color:#94a3b8">{{ $row->section->name ?? '—' }}</div>
  </td>

  {{-- Col 4: Subject --}}
  <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;font-size:12px;color:#334155;vertical-align:middle;white-space:nowrap">
    {{ $row->subject->name ?? '—' }}
  </td>

  {{-- Col 5: Type Badge --}}
  <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;vertical-align:middle">
    <span class="tbg tbg-{{ $typeKey }}" style="display:inline-block">{{ ucfirst($row->task_type ?? 'homework') }}</span>
  </td>

  {{-- Col 6: Due Date --}}
  <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;white-space:nowrap;vertical-align:middle;font-size:12.5px;color:#334155">
    {{ $row->submission_date ?? '—' }}
    @if($ov)<br><span class="od" style="font-size:10px">Late</span>@endif
  </td>

  {{-- Col 7: Marks --}}
  <td style="padding:10px 10px;border-bottom:1px solid #dde3ed;border-right:1px solid #dde3ed;font-weight:700;font-size:14px;color:#1d4ed8;vertical-align:middle;text-align:center">
    {{ $row->marks ?? '—' }}
  </td>

  {{-- Col 8: Actions --}}
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
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#mEv" onclick="openEval({{$row->id}})"><i class="fa-solid fa-{{$row->task_type==='quiz'?'chart-bar':'check-double'}} me-2 text-success"></i>{{$row->task_type==='quiz'?'View Scores':'Evaluate'}}</a></li>
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

@empty
<tr>
  <td colspan="8" style="padding:20px;text-align:center;color:#94a3b8;font-size:13px">
    No homework records found.
  </td>
</tr>
@endforelse
