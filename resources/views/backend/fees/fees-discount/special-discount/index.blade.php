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
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                            <li class="breadcrumb-item">{{ $data['title'] }}</li>
                        </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <!--  table content start -->
        <div class="table-content table-basic mt-20">

            <div class="row">
                <div class="col-3 mb-3">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                        </div>
                        <div class="card-body">
                            <form
                                action="{{ isset($data['edit_discount']->id)
                                ? route('special-fees-discount.update')
                                : route('special-fees-discount.store') }}"
                                enctype="multipart/form-data" method="post" id="visitForm">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="exampleDataList" class="form-label ">{{ ___('common.Name') }}
                                                    <span class="fillable">*</span></label>
                                                <input class="form-control ot-input @error('name') is-invalid @enderror"
                                                    name="name" list="datalistOptions" id="exampleDataList"
                                                    type="text" placeholder="{{ ___('common.Enter Name') }}"
                                                    value="{{ $data['edit_discount']?->name ?? '' }}">
                                                @error('name')
                                                    <div id="validationServer04Feedback" class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="col-md-12 mb-3">

                                                <label for="validationServer04" class="form-label">{{ ___('common.type') }} <span class="fillable">*</span></label>
                                                <select class="nice-select niceSelect bordered_style wide @error('type') is-invalid @enderror"
                                                        name="type" id="validationServer04"
                                                        aria-describedby="validationServer04Feedback">
                                                    <option value="P" {{ strtoupper($data['edit_discount']->type ?? '') === 'P' ? 'selected' : '' }}>
                                                        {{ ___('common.PERCENTAGE') }}
                                                    </option>
                                                    <option value="F" {{ strtoupper($data['edit_discount']->type ?? '') === 'F' ? 'selected' : '' }}>
                                                        {{ ___('common.FIXED') }}
                                                    </option>
                                                </select>


                                                @error('type')
                                                <div id="validationServer04Feedback" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror

                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label for="exampleDataList" class="form-label ">{{ ___('common.Discount amount') }}
                                                    <span class="fillable">*</span></label>
                                                <input class="form-control ot-input @error('discount_amount') is-invalid @enderror"
                                                       name="discount_amount" list="datalistOptions" id="exampleDataList"
                                                       type="number" placeholder="{{ ___('common.Enter amount') }}"
                                                       value="{{ $data['edit_discount']?->discount ?? null }}">
                                                @error('discount_amount')
                                                <div id="validationServer04Feedback" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label for="exampleDataList" class="form-label ">{{ ___('common.Min discount amount') }}
                                                </label>
                                                <input class="form-control ot-input @error('min_amount') is-invalid @enderror"
                                                       name="min_amount" list="datalistOptions" id="exampleDataList"
                                                       type="number" placeholder="{{ ___('common.Enter min amount') }}"
                                                       value="{{ $data['edit_discount']?->min_discount_amount ?? null }}">
                                                @error('min_amount')
                                                <div id="validationServer04Feedback" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label for="exampleDataList" class="form-label ">{{ ___('common.Max discount amount') }}
                                                </label>
                                                <input class="form-control ot-input @error('max_amount') is-invalid @enderror"
                                                       name="max_amount" list="datalistOptions" id="exampleDataList"
                                                       type="number" placeholder="{{ ___('common.Enter max amount') }}"
                                                       value="{{ $data['edit_discount']?->max_discount_amount ?? null }}">
                                                @error('max_amount')
                                                <div id="validationServer04Feedback" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label for="exampleDataList" class="form-label ">{{ ___('common.Min eligible amount') }}
                                                </label>
                                                <input class="form-control ot-input @error('min_eligible_amount') is-invalid @enderror"
                                                       name="min_eligible_amount" list="datalistOptions" id="exampleDataList"
                                                       type="number" placeholder="{{ ___('common.Enter min eligible amount') }}"
                                                       value="{{ $data['edit_discount']?->min_eligible_amount ?? null }}">
                                                @error('min_eligible_amount')
                                                <div id="validationServer04Feedback" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label for="exampleDataList" class="form-label ">{{ ___('common.Max eligible amount') }}
                                                </label>
                                                <input class="form-control ot-input @error('max_eligible_amount') is-invalid @enderror"
                                                       name="max_eligible_amount" list="datalistOptions" id="exampleDataList"
                                                       type="number" placeholder="{{ ___('common.Enter max eligible amount') }}"
                                                       value="{{ $data['edit_discount']?->max_eligible_amount ?? null }}">
                                                @error('max_eligible_amount')
                                                <div id="validationServer04Feedback" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="col-md-12 mb-3">

                                                <label for="validationServer04" class="form-label">{{ ___('common.status') }} <span class="fillable">*</span></label>
                                                <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                                        name="status" id="validationServer04"
                                                        aria-describedby="validationServer04Feedback">
                                                    <option value="{{ App\Enums\Status::ACTIVE }}"
                                                        {{ ($data['edit_status']->status ?? '') == App\Enums\Status::ACTIVE ? 'selected' : '' }}>
                                                        {{ ___('common.active') }}
                                                    </option>
                                                    <option value="{{ App\Enums\Status::INACTIVE }}"
                                                        {{ ($data['edit_status']->status ?? '') == App\Enums\Status::INACTIVE ? 'selected' : '' }}>
                                                        {{ ___('common.inactive') }}
                                                    </option>
                                                </select>


                                                @error('status')
                                                <div id="validationServer04Feedback" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror

                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label for="exampleDataList"
                                                       class="form-label ">{{ ___('fees.Description') }}</label>
                                                <textarea class="form-control ot-textarea mt-0" name="short_desc"
                                                          list="datalistOptions" id="exampleDataList" placeholder="{{ ___('fees.enter_description') }}">{{ $data['edit_discount']?->short_description ?? '' }}</textarea>
                                                @error('short_desc')
                                                <div id="validationServer04Feedback" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <input type="hidden" name="id"
                                                value="{{ $data['edit_discount']?->id ?? '' }}">


                                            @if (hasPermission('content_type_create'))
                                                <div class="col-md-12 mt-24">
                                                    <div class="text-end">
                                                        <button class="btn btn-lg ot-btn-primary"><span><i
                                                                    class="fa-solid fa-save"></i>
                                                            </span>{{ ___('common.submit') }}</button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{--                second card --}}
                <div class="col-9 mb-3">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ $data['title'] }}</h4>
                            @if (hasPermission('special-fees-discount-assign'))
                                <div>
                                    <a href="{{ route('special-fees-discount.assignStudent') }}" class="btn btn-lg ot-btn-primary">
                                        <span><i class="fa-solid fa-plus"></i> </span>
                                        <span class="">{{ ___('common.Assign') }}</span>
                                    </a>

                                </div>
                            @endif
                        </div>


                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered role-table">
                                    <thead class="thead">
                                        <tr>
                                            <th class="serial">{{ ___('common.Si') }}</th>
                                            <th class="purchase">{{ ___('common.Name') }} </th>
                                            <th class="purchase">{{ ___('common.Discount amount') }} </th>
                                            <th class="purchase">{{ ___('common.Condition') }} </th>
                                            <th class="purchase">{{ ___('common.Status') }} </th>
                                            <th class="purchase">{{ ___('common.Short Desc') }}</th>
                                            @if (hasPermission('content_type_update') || hasPermission('content_type_delete'))
                                                <th class="action">{{ ___('common.action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="tbody">
                                        @forelse ($data['discounts'] ?? [] as $key => $row)
                                            <tr id="row_{{ $row->id }}">
                                                <td class="serial">{{ ++$key }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td class="purchase">
                                                    @if($row->type == 'F')
                                                        {{ Setting('currency_symbol') . $row->discount }}
                                                    @else
                                                        {{ $row->discount }} %
                                                    @endif
                                                </td>
                                                <td class="purchase">
                                                    <ul>
                                                        @if($row->min_discount_amount) <li>{{ ___('fees.Min Disc') }} : {{ $row->min_discount_amount }}</li>@endif
                                                        @if($row->max_discount_amount)<li>{{ ___('fees.Max Disc') }} : {{ $row->max_discount_amount }}</li>@endif
                                                        @if($row->min_eligible_amount)<li>{{ ___('fees.Min Eligible') }} : {{ $row->min_eligible_amount }}</li>@endif
                                                        @if($row->max_eligible_amount)<li>{{ ___('fees.Max Eligible') }} : {{ $row->max_eligible_amount }}</li>@endif
                                                    </ul>
                                                </td>
                                                <td>{{$row->active_status == 1 ? 'Active' : 'Inactive'}}</td>
                                                <td>{{ $row->short_description }}</td>
                                                @if (hasPermission('content_type_update') || hasPermission('content_type_delete'))
                                                    <td class="action">
                                                        <div class="dropdown dropdown-action">
                                                            <button type="button" class="btn-dropdown"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fa-solid fa-ellipsis"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end ">
                                                                @if (hasPermission('content_type_update'))
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('special-fees-discount.edit', $row->id) }}"><span
                                                                                class="icon mr-8"><i
                                                                                    class="fa-solid fa-pen-to-square"></i></span>
                                                                            {{ ___('common.edit') }}</a>
                                                                    </li>
                                                                @endif
                                                                @if (hasPermission('content_type_delete'))
                                                                    <li>
                                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                                            onclick="delete_row('special-fees-discount/delete', {{ $row->id }})">
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
                                                        {{ ___('common.please_add_new_entity_regarding_this_table') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!--  table end -->
                            <!--  pagination start -->
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
