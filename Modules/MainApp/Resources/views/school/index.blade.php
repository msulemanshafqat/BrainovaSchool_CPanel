@extends('mainapp::layouts.backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('css')
    <style>
        .loader {
            width: 28px;
            height: 28px;
            border: 5px solid #c49c1c;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection
@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('mainapp_common.home') }}</a>
                        </li>
                        <li class="breadcrumb-item">{{ ___('mainapp_schools.School List') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="row">
            <div class="col-12">
                <form action="{{ url()->current() }}" id="marksheed" enctype="multipart/form-data">
                    <div class="card ot-card mb-24 position-relative z_1">
                        <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                            <h3 class="mb-0">{{ ___('mainapp_common.Filtering') }}</h3>

                            <div
                                class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">
                                <!-- table_searchBox -->
                                <div class="single_large_selectBox">
                                    <select name="country"  id="getCity"
                                        class="class nice-select niceSelect bordered_style wide @error('class') is-invalid @enderror">
                                        <option value="">{{ ___('mainapp_schools.Select Country') }}</option>
                                        @foreach ($data['countries'] as $item)
                                            <option {{ request('country') == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="single_large_selectBox">
                                    <select  class="nice-select niceSelect cities bordered_style wide @error('city') is-invalid @enderror" id="getCity"
                                    name="city">
                                        <option value="">{{ ___('mainapp_schools.Select city') }}</option>
                                        @if (request('city'))
                                            <option {{ request('city') == $data['cities']->id ? 'selected' : '' }}
                                                value="{{ $data['cities']->id }}">{{ $data['cities']->name }}</option>
                                        @endif

                                    </select>
                                </div>
                                <div class="single_large_selectBox">
                                    <select name="package"
                                        class="class nice-select niceSelect bordered_style wide @error('class') is-invalid @enderror"
                                        name="class">
                                        <option value="">{{ ___('mainapp_schools.Select package') }}</option>
                                        @foreach ($data['packages'] as $item)
                                            <option {{ request('package') == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="single_large_selectBox">
                                    <input class="form-control ot-input" name="keyword" list="datalistOptions"
                                        id="exampleDataList" placeholder="{{ ___('student_info.Search by name, email, phone, subdomain') }}"
                                        value="{{ request('keyword') }}">
                                </div>
                                <button class="btn btn-lg ot-btn-primary" type="submit">
                                    {{ ___('mainapp_common.Search') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!--  table content start -->
        <div class="table-content table-basic">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('mainapp_schools.School List') }}</h4>
                    <a href="{{ route('school.create') }}" class="btn btn-lg ot-btn-primary">
                        <span><i class="fa-solid fa-plus"></i> </span>
                        <span class="">{{ ___('mainapp_common.add') }}</span>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered school-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('mainapp_common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('mainapp_schools.Sub domain') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.name') }}</th>
                                    <th class="purchase">{{ ___('mainapp_schools.Package') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.phone') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.email') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Country(City)') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.address') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.status') }}</th>
                                    <th class="action">{{ ___('mainapp_common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse ($data['schools'] as $key => $row)
                                    <tr id="row_{{ $row->id }}">
                                        <td class="serial">{{ ++$key }}</td>
                                        <td>
                                            @if ($row->status == App\Enums\Status::ACTIVE && $row->tenant)
                                                <a href="https://{{ $row->sub_domain_key . '.' . env('APP_MAIN_APP_URL') }}"
                                                    target="_blank">
                                                    {{ $row->sub_domain_key . '.' . env('APP_MAIN_APP_URL') }}
                                                </a>
                                            @else
                                                <span class="loader"></span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="user-card">
                                                <div class="user-avatar">
                                                    <img src="{{ @globalAsset(@$row->logo, '40X40.webp') }}"
                                                        alt="{{ @$row->name }}">
                                                </div>
                                                <div class="user-info">
                                                    {{ @$row->name }}
                                                </div>

                                            </div>
                                        </td>



                                        <td>{{ $row->package->name }}</td>

                                        <td>{{ $row->phone }}</td>
                                        <td>{{ $row->email }}</td>
                                        <td>{{ @$row->country->name }} ({{ @$row->city->name }})</td>
                                        <td>{{ $row->address }}</td>
                                        <td>
                                            @if ($row->status == App\Enums\Status::ACTIVE && $row->tenant)
                                                <span
                                                    class="badge-basic-success-text">{{ ___('mainapp_common.active') }}</span>
                                            @else
                                                <span
                                                    class="badge-basic-danger-text">{{ ___('mainapp_common.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="action">
                                            <div class="dropdown dropdown-action">
                                                <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end ">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('school.edit', $row->id) }}"><span
                                                                class="icon mr-8"><i
                                                                    class="fa-solid fa-pen-to-square"></i></span>
                                                            {{ ___('mainapp_common.edit') }}</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                            onclick="delete_row('school/delete', {{ $row->id }})">
                                                            <span class="icon mr-8"><i
                                                                    class="fa-solid fa-trash-can"></i></span>
                                                            <span>{{ ___('mainapp_common.delete') }}</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center gray-color">
                                            <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary"
                                                width="100">
                                            <p class="mb-0 text-center">{{ ___('mainapp_common.no_data_available') }}</p>
                                            <p class="mb-0 text-center text-secondary font-size-90">
                                                {{ ___('mainapp_common.please_add_new_entity_regarding_this_table') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!--  table end -->
                    <!--  pagination start -->

                    <div class="ot-pagination pagination-content d-flex justify-content-end align-content-center py-3">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-between">
                                {!! $data['schools']->links() !!}
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

@push('script')
    @include('backend.partials.delete-ajax')


    <script>
        // Start get section
        $("#getCity").on('change', function(e) {
            var countryId = $("#getCity").val();
            var url = $('#url').val();
            var formData = {
                id: countryId,
            }
            $.ajax({
                type: "POST",
                dataType: 'html',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url + '/get-countries',
                success: function(data) {

                    var city_option = '';
                    var city_li = '';

                    $.each(JSON.parse(data), function(i, item) {
                        console.log(item);
                        city_option += "<option value=" + item.id + ">" + item.name +
                            "</option>";
                        city_li += "<li data-value=" + item.id + " class='option'>" + item
                            .name + "</li>";
                    });

                    // console.log(city_option);


                    $("select.cities option").not(':first').remove();
                    $("select.cities").append(city_option);

                    $("div .cities .current").html($("div .cities .list li:first").html());
                    $("div .cities .list li").not(':first').remove();
                    $("div .cities .list").append(city_li);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
    </script>
@endpush
