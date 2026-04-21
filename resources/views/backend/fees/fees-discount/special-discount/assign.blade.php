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
                        <li class="breadcrumb-item"><a href="#">{{ ___('fee.Special Discount') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-body">
                {{-- Filtering Form --}}
                <form action="" method="GET" id="filterForm">
                    <div class="row mb-3">
                        {{-- class --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">{{ ___('student_info.class') }} </label>
                            <select id="getSections" class="nice-select niceSelect bordered_style wide" name="class">
                                <option value="">{{ ___('student_info.select_class') }}</option>
                                @foreach ($data['classes'] as $item)
                                    <option value="{{ $item->class->id }}" {{ request('class') == $item->class->id ? 'selected' : '' }}>
                                        {{ $item->class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- section --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">{{ ___('student_info.section') }} </label>
                            <select id="section" class="nice-select niceSelect bordered_style wide" name="section">
                                <option value="">{{ ___('student_info.select_section') }}</option>
                                @foreach ($data['sections'] as $item)
                                    <option value="{{ $item->id }}" {{ request('section') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- gender --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">{{ ___('fees.gender') }}</label>
                            <select id="gender" class="nice-select niceSelect bordered_style wide" name="gender">
                                <option value="">{{ ___('student_info.select_gender') }}</option>
                                @foreach ($data['genders'] as $item)
                                    <option value="{{ $item->id }}" {{ request('gender') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- student_category --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">{{ ___('student_info.student_category') }}</label>
                            <select id="student_category" class="nice-select niceSelect bordered_style wide" name="student_category">
                                <option value="">{{ ___('fees.select_student_category') }}</option>
                                @foreach ($data['categories'] as $item)
                                    <option value="{{ $item->id }}" {{ request('student_category') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- staff --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">{{ ___('student_info.Staff') }}</label>
                            <select id="student_department" class="nice-select niceSelect bordered_style wide" name="staff">
                                <option value="">{{ ___('fees.Select Staff') }}</option>
                                @foreach ($data['staff'] as $item)
                                    <option value="{{ $item->id }}" {{ request('staff') == $item->id ? 'selected' : '' }}>
                                        {{ $item->first_name . ' ' . $item->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- guardian --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">{{ ___('student_info.Guardian') }}</label>
                            <select id="parent" class="nice-select niceSelect bordered_style wide" name="parent">
                                <option value="">{{ ___('fees.Select Guardian') }}</option>
                                @foreach ($data['parents'] as $item)
                                    <option value="{{ $item->id }}" {{ request('parent') == $item->id ? 'selected' : '' }}>
                                        {{ $item->guardian_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Buttons --}}
                        <div class="col-md-12 text-end mt-3">
                            @if(request()->hasAny(['class', 'section', 'gender', 'student_category', 'staff', 'parent']))
                                <a href="{{route('special-fees-discount.assign')}}" class="btn ot-btn-secondary ms-2">
                                    <i class="fa fa-times"></i> {{ ___('common.clear') }}
                                </a>
                            @endif

                            <button type="submit" class="btn ot-btn-info">
                                <i class="fa fa-filter"></i> {{ ___('common.filter') }}
                            </button>
                        </div>

                    </div>
                </form>

                {{-- ===== FILTER FORM ===== --}}
                <form action="{{ route('fees-assign.index') }}" method="GET" id="filterForm">
                    {{-- Filtering inputs here --}}
                </form>

                {{-- ===== ASSIGN DISCOUNT FORM ===== --}}
                <form action="{{ route('special-fees-discount.assign') }}" method="POST" enctype="multipart/form-data" id="assignForm">
                    @csrf
                    {{-- Students Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered role-table">
                            <thead>
                            <tr>
                                <th>{{ ___('common.SL') }}</th>
                                <th>{{ ___('student_info.admission_no') }}</th>
                                <th>{{ ___('student_info.student_name') }}</th>
                                <th>{{ ___('academic.class') }} ({{ ___('academic.section') }})</th>
                                <th>{{ ___('student_info.guardian_name') }}</th>
                                <th>
                                    {{ ___('common.discount') }}
                                    <i class="fa fa-info-circle ms-1 text-primary"
                                       style="cursor: pointer;"
                                       data-bs-toggle="offcanvas"
                                       data-bs-target="#discountDetailsModal"
                                       title="{{ ___('common.discount_details') }}"></i>
                                </th>


                                <th>{{ ___('student_info.short note') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data['students'] as $index => $student)
                                @php
                                    $assignedDiscountId = $data['assignedDiscounts'][$student->student->id]->special_discount_id ?? null;
                                    $shortNote = $data['assignedDiscounts'][$student->student->id]->short_description ?? '';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->student->admission_no }}</td>
                                    <td>{{ ($student->student->first_name . ' ' . $student->student->last_name) }}</td>
                                    <td>{{ $student->class->name ?? '' }} ({{ $student->section->name ?? '' }})</td>
                                    <td>{{ $student->student->parent->guardian_name ?? '' }}</td>
                                    <td>
                                        <select name="discounts[{{ $student->student->id }}]" class="nice-select niceSelect bordered_style wide">
                                            <option value="">{{ ___('student_info.select_discount') }}</option>
                                            @foreach($data['discounts'] as $discount)
                                                <option value="{{ $discount->id }}"
                                                    {{ $assignedDiscountId == $discount->id ? 'selected' : '' }}>
                                                    {{ $discount->name }}
                                                    ({{ $discount->type === 'F' ? Setting('currency_symbol') . $discount->discount : $discount->discount . '%' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text"
                                               class="form-control ot-input"
                                               name="short_notes[{{ $student->student->id }}]"
                                               placeholder="{{ ___('student_info.write_a_short_note') }}"
                                               value="{{ $shortNote }}">
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="ot-pagination d-flex justify-content-end py-3">
                        <nav>
                            <ul class="pagination">
                                {!! $data['students']->appends(\Request::capture()->except('page'))->links() !!}
                            </ul>
                        </nav>
                    </div>

                    {{-- Submit Button --}}
                    <div class="text-end mt-4">
                        <button class="btn btn-lg ot-btn-primary" type="submit">
                            <i class="fa-solid fa-save"></i> {{ ___('common.submit') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>


    <!-- Offcanvas Right: Discount Breakdown -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="discountDetailsModal" aria-labelledby="discountDetailsLabel">
        <div class="offcanvas-header d-flex justify-content-between align-items-center">
            <h5 class="offcanvas-title" id="discountDetailsLabel">{{ ___('common.discount_details') }}</h5>

            <button type="button" class="btn btn-sm btn-light border-0" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="fa fa-xmark fa-lg text-dark"></i>
            </button>
        </div>


        <div class="offcanvas-body">
            @forelse($data['discounts'] as $discount)
                <div class="card mb-3 shadow-sm border-start border-info border-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary">{{ $discount->name }}</h5>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>{{ ___('common.type') }}:</strong>
                                {{ $discount->type == 'F' ? 'Fixed' : 'Percentage' }}
                            </li>

                            <li class="list-group-item">
                                <strong>{{ ___('common.discount_amount') }}:</strong>
                                {{ $discount->type == 'F'
                                    ? Setting('currency_symbol') . number_format($discount->discount, 2)
                                    : number_format($discount->discount, 2) . '%' }}
                            </li>

                            @if($discount->min_discount_amount)
                                <li class="list-group-item">
                                    <strong>{{ ___('common.min_discount_amount') }}:</strong>
                                    {{ Setting('currency_symbol') . number_format($discount->min_discount_amount, 2) }}
                                </li>
                            @endif

                            @if($discount->max_discount_amount)
                                <li class="list-group-item">
                                    <strong>{{ ___('common.max_discount_amount') }}:</strong>
                                    {{ Setting('currency_symbol') . number_format($discount->max_discount_amount, 2) }}
                                </li>
                            @endif

                            @if($discount->min_eligible_amount)
                                <li class="list-group-item">
                                    <strong>{{ ___('common.min_eligible_amount') }}:</strong>
                                    {{ Setting('currency_symbol') . number_format($discount->min_eligible_amount, 2) }}
                                </li>
                            @endif

                            @if($discount->max_eligible_amount)
                                <li class="list-group-item">
                                    <strong>{{ ___('common.max_eligible_amount') }}:</strong>
                                    {{ Setting('currency_symbol') . number_format($discount->max_eligible_amount, 2) }}
                                </li>
                            @endif

                            @if($discount->short_description)
                                <li class="list-group-item">
                                    <strong>{{ ___('common.short_description') }}:</strong> {{ $discount->short_description }}
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">{{ ___('common.no_discount_available') }}</div>
            @endforelse
        </div>
    </div>

@endsection

