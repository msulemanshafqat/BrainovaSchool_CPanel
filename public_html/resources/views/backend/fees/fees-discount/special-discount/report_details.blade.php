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
                {{--                second card --}}
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">{{ $data['title'] }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong>{{ ___('common.Admission No') }}:</strong>
                                    <p class="mb-0">{{ $data->admission_no }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ ___('common.Roll No') }}:</strong>
                                    <p class="mb-0">{{ $data->roll_no }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ ___('common.Name') }}:</strong>
                                    <p class="mb-0">{{ $data->first_name . ' ' . $data->last_name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ ___('common.Mobile') }}:</strong>
                                    <p class="mb-0">{{ $data->mobile }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ ___('common.Email') }}:</strong>
                                    <p class="mb-0">{{ $data->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ ___('common.Date of Birth') }}:</strong>
                                    <p class="mb-0">{{ $data->dob }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ ___('common.Admission Date') }}:</strong>
                                    <p class="mb-0">{{ $data->admission_date }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ ___('common.Place of Birth') }}:</strong>
                                    <p class="mb-0">{{ $data->place_of_birth }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ ___('common.Nationality') }}:</strong>
                                    <p class="mb-0">{{ $data->nationality }}</p>
                                </div>
                            </div>
                            <div class="table-responsive mt-5">
                                <table class="table table-striped table-hover table-bordered table-sm mb-0">
                                    <thead class="thead">
                                        <tr>
                                            <th>{{ ___('common.Fees Type') }}</th>
                                            <th>{{ ___('fees.Total Amount') }}</th>
                                            <th>{{ ___('fees.Discount Type') }}</th>
                                            <th>{{ ___('fees.Discount Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tbody">
                                        @php
                                            $symbol = Setting('currency_symbol');
                                        @endphp
                                        @forelse ($data->feesMasters as $feesMaster)
                                            @php
                                                $discount = $data['discount'];
                                                $total = $feesMaster->amount ?? 0;

                                                $discountValue =
                                                    $discount->type == 'F'
                                                        ? $discount->discount
                                                        : round(($discount->discount / 100) * $total, 2);
                                            @endphp
                                            <tr>
                                                <td>{{ $feesMaster->type->name ?? 'N/A' }}</td>
                                                <td>{{ $symbol . $total }}</td>
                                                <td>{{ $discount->type == 'F' ? 'Fixed' : 'Percentage' }}</td>
                                                <td>
                                                    {{ $discount->type == 'F'
                                                        ? $symbol . $discountValue
                                                        : $discount->discount . '% (' . $symbol . $discountValue . ')' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">{{ ___('common.no_fees_assigned') }}</td>
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
