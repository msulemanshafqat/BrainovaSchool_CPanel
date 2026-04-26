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


        <div class="col-12">
            <form action="" >
                <div class="card ot-card mb-24 position-relative z_1">
                    <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                        <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                        <div
                            class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">
                            <!-- table_searchBox -->
                            <div class="single_large_selectBox">
                                <select id="getSections"
                                        class="class nice-select niceSelect bordered_style wide @error('class') is-invalid @enderror"
                                        name="class">
                                    <option value="">{{ ___('student_info.select_class') }}</option>
                                    @foreach ($data['classes'] as $item)
                                        <option
                                            {{ old('class', @$data['request']->class) == $item->class->id ? 'selected' : '' }}
                                            value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="single_large_selectBox">
                                <select
                                    class="sections section nice-select niceSelect bordered_style wide @error('section') is-invalid @enderror"
                                    name="section">
                                    <option value="">{{ ___('student_info.select_section') }}</option>
                                    @foreach ($data['sections'] as $item)
                                        <option
                                            {{ old('section', @$data['request']->section) == $item->section->id ? 'selected' : '' }}
                                            value="{{ $item->section->id }}">{{ $item->section->name }}</option>
                                    @endforeach
                                </select>
                                @error('section')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="single_large_selectBox">
                                <input class="form-control ot-input" name="keyword" list="datalistOptions"
                                       id="exampleDataList" placeholder="{{ ___('student_info.enter_keyword') }}"
                                       value="{{ old('keyword', @$data['request']->keyword) }}">
                            </div>

                            <button class="btn btn-lg ot-btn-primary" type="submit">
                                {{ ___('common.Search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $data['title'] }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered role-table">
                            <thead class="thead">
                            <tr>
                                <th class="serial">{{ ___('common.sr_no') }}</th>
                                <th class="purchase">{{ ___('student_info.student_name') }}</th>
                                <th class="purchase">{{ ___('student_info.appeal by') }}</th>
                                <th class="purchase">{{ ___('incidents.incidents') }}</th>
                                <th class="purchase">{{ ___('incidents.status') }}</th>
                                <th class="purchase">{{ ___('incidents.Date') }}</th>
                            @if (hasPermission('student_update') || hasPermission('student_delete'))
                                    <th class="action">{{ ___('common.action') }}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody class="tbody">
                            {{-- @dd($data['students']) --}}
                            @foreach($data['appeals'] as $key => $appeals)
                                @php
                                $student = $appeals->student;
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="">
                                            <a href="{{ route('student.show', $student->id) }}" target="_blank">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <img src="{{ @globalAsset(@$student->user->upload->path, '40X40.webp') }}"
                                                             alt="{{ @$student->first_name }}">
                                                    </div>
                                                    <div class="user-info">
                                                        {{ @$student->first_name }}
                                                        {{ @$student->last_name }}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $appeals->requestBy->name }}
{{--                                        <div class="">--}}
{{--                                            <a href="{{ route('user.show', $appeals->requestBy->id) }}" target="_blank">--}}
{{--                                                <div class="user-card">--}}
{{--                                                    <div class="user-avatar">--}}
{{--                                                        <img src="{{ @globalAsset(@$appeals->requestBy->upload->path, '40X40.webp') }}"--}}
{{--                                                             alt="{{ $appeals->requestBy->name }}">--}}
{{--                                                    </div>--}}
{{--                                                    <div class="user-info">--}}
{{--                                                        {{ $appeals->requestBy->name }}--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </a>--}}
{{--                                        </div>--}}
                                    </td>
                                    <td>
                                        <ul class="mb-0 ps-3">
                                            @foreach($student->behaviourRecord as $record)
                                                @if ($record->incident)
                                                    <li>
                                                            <span class="badge {{ $record->incident->type === 'positive' ? 'badge-basic-success-text' : 'badge-basic-danger-text' }}">
                                                                {{ $record->incident->title }} ({{ $record->incident->type === 'positive' ? '+' : '-' }}{{ $record->incident->points }})
                                                            </span>
                                                    </li>
                                                @endif
                                            @endforeach

                                        </ul>
                                    </td>
                                    <td>
                                        @php
                                            $status = strtolower($appeals->status);
                                            $badgeClass = $status === 'pending' ? 'badge-warning' : 'badge-success';
                                        @endphp

                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                    </td>

                                    <td>
                                        {{dateFormat($appeals->created_at)}}
                                    </td>

                                    @if (hasPermission('student_update') || hasPermission('student_delete'))
                                        <td class="action">
                                            <div class="dropdown dropdown-action">
                                                <button type="button" class="btn-dropdown"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <!-- View -->
                                                    <li>
                                                        <a class="dropdown-item" href="{{route('incidents.appeal.details', $appeals->id)}}">
                                                            <span class="icon mr-8"><i class="fa-regular fa-eye"></i></span>
                                                            {{ ___('common.View') }}
                                                        </a>
                                                    </li>

                                                    <!-- Reject -->
                                                    @if (hasPermission('student_update'))
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('incidents.appeal.reject', @$appeals->id) }}">
                                                                <span class="icon mr-8"><i class="fa-solid fa-ban text-danger"></i></span>
                                                                {{ ___('common.Reject') }}
                                                            </a>
                                                        </li>
                                                    @endif

                                                    <!-- Withdraw -->
                                                    @if (hasPermission('student_delete') && $appeals->status != 'withdraw')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('incidents.appeal.withdraw', @$student->id) }}">
                                                                <span class="icon mr-8"><i class="fa-solid fa-check text-success"></i></span>
                                                                <span>{{ ___('common.Withdraw') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>

                                            </div>
                                        </td>


                                    @endif
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    <!--  table end -->
                    <!--  pagination start -->

                    <div class="ot-pagination pagination-content d-flex justify-content-end align-content-center py-3">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-between">
                                {!! $data['appeals']->appends(\Request::capture()->except('page'))->links() !!}
                            </ul>
                        </nav>
                    </div>

                    <!--  pagination end -->
                </div>

            </div>
        </div>
        <!--  table content end -->

    </div>

@endsection
