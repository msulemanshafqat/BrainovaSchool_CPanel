@extends('backend.master')
@section('title') {{ @$data['title'] }} @endsection

@section('content')
<div class="page-content">

  {{-- BREADCRUMB --}}
  <div class="page-header">
    <div class="row">
      <div class="col-sm-6">
        <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('homework.index') }}">Homework</a></li>
          <li class="breadcrumb-item active">{{ $data['title'] }}</li>
        </ol>
      </div>
    </div>
  </div>

  <div class="table-content table-basic mt-20">
    <div class="row">

      {{-- LEFT: Add new group form --}}
      <div class="col-md-3">
        <div class="card">
          <div class="card-header">
            <h4 class="mb-0">Add New Group</h4>
          </div>
          <div class="card-body">
            {{--
              Submits to the existing question-group.store route.
              is_homework=1 ensures this group is scoped to homework only
              and does NOT appear in the online-exam question group list.
            --}}
            <form action="{{ route('question-group.store') }}" enctype="multipart/form-data" method="post">
              @csrf
              <input type="hidden" name="is_homework" value="1" />

              <div class="col-md-12 mb-3">
                <label class="form-label">
                  Group Name <span class="fillable">*</span>
                </label>
                <input class="form-control ot-input @error('name') is-invalid @enderror"
                       name="name"
                       placeholder="e.g. Weekly Homework, Science Project"
                       value="{{ old('name') }}">
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-12 mb-3">
                <label class="form-label">
                  Status <span class="fillable">*</span>
                </label>
                <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                        name="status">
                  <option value="{{ App\Enums\Status::ACTIVE }}">{{ ___('common.active') }}</option>
                  <option value="{{ App\Enums\Status::INACTIVE }}">{{ ___('common.inactive') }}</option>
                </select>
                @error('status')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-12 mt-3">
                <div class="text-end">
                  <button class="btn btn-lg ot-btn-primary" type="submit">
                    <i class="fa-solid fa-save me-1"></i>Save Group
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- RIGHT: Existing groups table --}}
      <div class="col-md-9">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $data['title'] }}</h4>
            <span class="text-muted" style="font-size:12.5px">
              Teachers select these groups when creating homework tasks.
            </span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              {{-- Inline borders to beat LMS theme overrides --}}
              <table style="width:100%;border-collapse:collapse;font-size:13px">
                <thead>
                  <tr style="background:#eef2f9">
                    <th style="padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;width:40px">#</th>
                    <th style="padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0">Group Name</th>
                    <th style="padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#4a5568;border-bottom:2px solid #b8c5d6;border-right:1px solid #c7d2e0;width:90px">Status</th>
                    @if(hasPermission('question_group_update') || hasPermission('question_group_delete'))
                    <th style="padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#4a5568;border-bottom:2px solid #b8c5d6;width:60px">Action</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @forelse($data['question_groups'] as $key => $row)
                  @php $bg = $loop->even ? '#fafbff' : '#fff'; @endphp
                  <tr style="background:{{$bg}}" id="row_{{ $row->id }}"
                      onmouseover="this.style.background='#eef4ff'" onmouseout="this.style.background='{{$bg}}'">
                    <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;border-right:1px solid #e8edf5;font-size:11px;color:#94a3b8;text-align:center">{{ ++$key }}</td>
                    <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;border-right:1px solid #e8edf5;font-weight:600;font-size:13px;color:#0f172a">{{ $row->name }}</td>
                    <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;border-right:1px solid #e8edf5">
                      @if($row->status == App\Enums\Status::ACTIVE)
                        <span class="badge-basic-success-text">Active</span>
                      @else
                        <span class="badge-basic-danger-text">Inactive</span>
                      @endif
                    </td>
                    @if(hasPermission('question_group_update') || hasPermission('question_group_delete'))
                    <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0">
                      <div class="dropdown dropdown-action">
                        <button type="button" class="btn-dropdown" data-bs-toggle="dropdown">
                          <i class="fa-solid fa-ellipsis"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size:13px">
                          @if(hasPermission('question_group_update'))
                          <li>
                            <a class="dropdown-item" href="{{ route('question-group.edit', $row->id) }}">
                              <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit
                            </a>
                          </li>
                          @endif
                          @if(hasPermission('question_group_delete'))
                          <li>
                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                               onclick="delete_row('question-group/delete', {{ $row->id }})">
                              <i class="fa-solid fa-trash-can me-2"></i>Delete
                            </a>
                          </li>
                          @endif
                        </ul>
                      </div>
                    </td>
                    @endif
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4" style="text-align:center;padding:40px;color:#94a3b8">
                      <i class="fa-solid fa-folder-open" style="font-size:2rem;opacity:.2;display:block;margin-bottom:10px"></i>
                      No homework groups yet. Add your first group using the form on the left.
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end p-3">
              {!! $data['question_groups']->links() !!}
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('script')
  @include('backend.partials.delete-ajax')
@endpush
