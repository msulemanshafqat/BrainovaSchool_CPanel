@extends('backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                            <li class="breadcrumb-item"><a
                                    href="">{{ ___('examination.homework') }}</a></li>
                            <li class="breadcrumb-item">{{ $data['title'] }}</li>
                        </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ ___('common.add') }} {{ $data['title'] }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('question-group.store') }}" enctype="multipart/form-data" method="post"
                                id="visitForm">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="exampleDataList" class="form-label ">{{ ___('common.name') }}
                                                    <span class="fillable">*</span></label>
                                                <input class="form-control ot-input @error('name') is-invalid @enderror"
                                                    name="name" list="datalistOptions" id="exampleDataList"
                                                    placeholder="{{ ___('common.enter_name') }}"
                                                    value="{{ old('name') }}">
                                                @error('name')
                                                    <div id="validationServer04Feedback" class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12">
                                                <input type="hidden" name="is_homework" value="1" />
                                                <label for="validationServer04"
                                                    class="form-label">{{ ___('common.status') }} <span
                                                        class="fillable">*</span></label>
                                                <select
                                                    class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                                    name="status" id="validationServer04"
                                                    aria-describedby="validationServer04Feedback">
                                                    <option value="{{ App\Enums\Status::ACTIVE }}">
                                                        {{ ___('common.active') }}</option>
                                                    <option value="{{ App\Enums\Status::INACTIVE }}">
                                                        {{ ___('common.inactive') }}
                                                    </option>
                                                </select>

                                                @error('status')
                                                    <div id="validationServer04Feedback" class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>
                                            <div class="col-md-12 mt-24">
                                                <div class="text-end">
                                                    <button class="btn btn-lg ot-btn-primary"><span><i
                                                                class="fa-solid fa-save"></i>
                                                        </span>{{ ___('common.submit') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ $data['title'] }}</h4>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered class-table">
                                    <thead class="thead">
                                        <tr>
                                            <th class="serial">{{ ___('common.sr_no') }}</th>
                                            <th class="purchase">{{ ___('common.name') }}</th>
                                            <th class="purchase">{{ ___('common.status') }}</th>
                                            @if (hasPermission('question_group_update') || hasPermission('question_group_delete'))
                                                <th class="action">{{ ___('common.action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="tbody">
                                        @forelse ($data['question_groups'] as $key => $row)
                                            <tr id="row_{{ $row->id }}">
                                                <td class="serial">{{ ++$key }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td>
                                                    @if ($row->status == App\Enums\Status::ACTIVE)
                                                        <span
                                                            class="badge-basic-success-text">{{ ___('common.active') }}</span>
                                                    @else
                                                        <span
                                                            class="badge-basic-danger-text">{{ ___('common.inactive') }}</span>
                                                    @endif
                                                </td>
                                                @if (hasPermission('question_group_update') || hasPermission('question_group_delete'))
                                                    <td class="action">
                                                        <div class="dropdown dropdown-action">
                                                            <button type="button" class="btn-dropdown"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fa-solid fa-ellipsis"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end ">
                                                                @if (hasPermission('question_group_update'))
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('question-group.edit', $row->id) }}"><span
                                                                                class="icon mr-8"><i
                                                                                    class="fa-solid fa-pen-to-square"></i></span>
                                                                            {{ ___('common.edit') }}</a>
                                                                    </li>
                                                                @endif
                                                                @if (hasPermission('question_group_delete'))
                                                                    <li>
                                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                                            onclick="delete_row('question-group/delete', {{ $row->id }})">
                                                                            <span class="icon mr-8"><i
                                                                                    class="fa-solid fa-trash-can"></i></span>
                                                                            <span>{{ ___('common.delete') }}</span>
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
                                                <td colspan="100%" class="text-center gray-color">
                                                    <img src="{{ asset('images/no_data.svg') }}" alt=""
                                                        class="mb-primary" width="100">
                                                    <p class="mb-0 text-center">{{ ___('common.no_data_available') }}</p>
                                                    <p class="mb-0 text-center text-secondary font-size-90">
                                                        {{ ___('common.please_add_new_entity_regarding_this_table') }}</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!--  table end -->
                            <!--  pagination start -->

                            <div
                                class="ot-pagination pagination-content d-flex justify-content-end align-content-center py-3">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-between">
                                        {!! $data['question_groups']->links() !!}
                                    </ul>
                                </nav>
                            </div>

                            <!--  pagination end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  table content end -->

    </div>
@endsection

@push('script')
    @include('backend.partials.delete-ajax')
@endpush
