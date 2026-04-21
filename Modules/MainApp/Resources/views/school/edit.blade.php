@extends('mainapp::layouts.backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection


@section('css')
    <style>
        .form-control:disabled {
            background-color: #e9ecef !important;
        }
    </style>
@endsection


@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> {{ ___('mainapp_common.home') }}
                                </a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('school.index') }}">{{ ___('mainapp_schools.schools') }}</a></li>
                            <li class="breadcrumb-item">{{ ___('mainapp_common.edit') }}</li>

                        </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}
        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('school.update', @$data['school']->id) }}" enctype="multipart/form-data"
                    method="post" id="visitForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.name') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('name') is-invalid @enderror" name="name"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter name') }}"
                                value="{{ old('name', @$data['school']->name) }}">
                            @error('name')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="validationServer04" class="form-label">{{ ___('mainapp_common.Package') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input" placeholder="{{ ___('mainapp_common.Enter phone') }}"
                                value="{{ @$data['school']->package->name }}" disabled>
                            {{-- <select class="nice-select niceSelect bordered_style wide @error('package') is-invalid @enderror"
                            name="package" id="validationServer04"
                            aria-describedby="validationServer04Feedback" @disabled(true)>
                                <option value="">{{ ___('common.Select package') }}</option>
                                @foreach ($data['packages'] as $item)
                                    <option {{ old('package', @$data['school']->package_id) == $item->id ? 'selected':'' }} value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                            @error('package')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror --}}
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.phone') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('phone') is-invalid @enderror" name="phone"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter phone') }}"
                                value="{{ old('phone', @$data['school']->phone) }}" disabled>
                            @error('phone')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">{{ ___('mainapp_common.email') }} <span class="fillable">*</span></label>
                            <input class="form-control ot-input @error('email') is-invalid @enderror" name="email"
                                type="email" id="email"
                                placeholder="{{ ___('mainapp_common.Enter email') }}"
                                value="{{ old('email', @$data['school']->email) }}" disabled>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">{{ ___('mainapp_schools.Country') }}</label>
                            <select class="nice-select niceSelect bordered_style wide @error('country') is-invalid @enderror"
                                name="country" id="getCity" aria-describedby="validationServer04Feedback">
                                <option value="">{{ ___('mainapp_schools.Select Country') }}</option>
                                @foreach ($data['countries'] as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('country', @$data['school']->country->id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="citySelect" class="form-label">{{ ___('mainapp_schools.City') }}</label>
                            <select class="nice-select niceSelect cities bordered_style wide @error('city') is-invalid @enderror"
                                id="citySelect" name="city" aria-describedby="validationServer04Feedback">
                                <option value="">{{ ___('mainapp_schools.Select City') }}</option>
                                @if (!empty($data['cities']))
                                    @foreach ($data['cities'] as $city)
                                        <option value="{{ $city->id }}"
                                            {{ old('city', @$data['school']->city_id) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('city')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.Sub domain key') }}
                                <span class="fillable">*</span></label>
                            <input class="form-control ot-input @error('sub_domain_key') is-invalid @enderror"
                                name="sub_domain_key" readonly list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter sub domain key') }}"
                                value="{{ old('sub_domain_key', @$data['school']->sub_domain_key) }}" disabled>
                            @error('sub_domain_key')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="validationServer04" class="form-label">{{ ___('mainapp_common.status') }} <span
                                    class="fillable">*</span></label>
                            <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                name="status" id="validationServer04" aria-describedby="validationServer04Feedback">
                                <option
                                    {{ old('status', @$data['school']->status) == App\Enums\Status::ACTIVE ? 'selected' : '' }}
                                    value="{{ App\Enums\Status::ACTIVE }}">{{ ___('mainapp_common.active') }}</option>
                                <option
                                    {{ old('status', @$data['school']->status) == App\Enums\Status::INACTIVE ? 'selected' : '' }}
                                    value="{{ App\Enums\Status::INACTIVE }}">{{ ___('mainapp_common.inactive') }}
                                </option>
                            </select>

                            @error('status')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.address') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('address') is-invalid @enderror" name="address"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter address') }}"
                                value="{{ old('address', @$data['school']->address) }}" disabled>
                            @error('address')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12 mt-24">
                            <div class="text-end">
                                <button class="btn btn-lg ot-btn-primary"><span><i class="fa-solid fa-save"></i>
                                    </span>{{ ___('mainapp_common.submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
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
        // End get section
    </script>
@endpush
