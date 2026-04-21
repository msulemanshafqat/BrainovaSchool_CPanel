@extends('mainapp::layouts.backend.master')

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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('mainapp_common.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a
                                href="{{ route('school.index') }}">{{ ___('mainapp_schools.School') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ___('mainapp_common.add_new') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('school.store') }}" enctype="multipart/form-data" method="post" id="visitForm">
                    @csrf
                    <input type="hidden" name="source" id="form_type" value="admin"/>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.name') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('name') is-invalid @enderror" name="name" type="text"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter name') }}" value="{{ old('name') }}">
                            @error('name')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="validationServer04" class="form-label">{{ ___('mainapp_schools.Package') }} <span class="fillable">*</span></label>
                            <select class="nice-select niceSelect bordered_style wide @error('package') is-invalid @enderror"
                            name="package" id="validationServer04"
                            aria-describedby="validationServer04Feedback">
                                <option value="">{{ ___('mainapp_schools.Select package') }}</option>
                                @foreach ($data['packages'] as $item)
                                    <option {{ old('package') == $item->id ? 'selected':'' }} value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                            @error('package')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.phone') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('phone') is-invalid @enderror" name="phone" type="text"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter phone') }}" value="{{ old('phone') }}">
                            @error('phone')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.email') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('email') is-invalid @enderror" name="email" type="email"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter email') }}" value="{{ old('email') }}">
                            @error('email')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                         <div class="col-md-6 mb-3">
                            <label for="validationServer04" class="form-label">{{ ___('mainapp_schools.Country') }} </label>
                            <select class="nice-select niceSelect bordered_style wide @error('country') is-invalid @enderror"
                            name="country" id="getCity"
                            aria-describedby="validationServer04Feedback">
                                <option value="">{{ ___('mainapp_schools.Select Country') }}</option>
                                @foreach ($data['countries'] as $item)
                                    <option {{ old('country') == $item->id ? 'selected':'' }} value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                            @error('country')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                         <div class="col-md-6 mb-3">
                            <label for="validationServer04" class="form-label">{{ ___('mainapp_schools.City') }} </label>
                            <select class="nice-select niceSelect cities bordered_style wide @error('city') is-invalid @enderror" id="getCity"
                            name="city"
                            aria-describedby="validationServer04Feedback">
                                <option value="">{{ ___('mainapp_schools.Select City') }}</option>

                            </select>

                            @error('city')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.address') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('address') is-invalid @enderror" name="address" type="text"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter address') }}" value="{{ old('address') }}">
                            @error('address')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sub_domain_key" class="form-label">{{ ___('mainapp_schools.Sub domain key') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('sub_domain_key') is-invalid @enderror" name="sub_domain_key" type="text"
                                list="datalistOptions" id="sub_domain_key"
                                placeholder="{{ ___('mainapp_schools.Enter sub domain key (0-9a-z)') }}" value="{{ old('sub_domain_key') }}">
                            @error('sub_domain_key')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12 justify-content-center">
                           <code> {{___('mainapp_schools.School Superadmin Email Will Be As your Email Input')}} </code><br>
                           <code>{{ ___('mainapp_schools.Default Password Will Be : 123456') }} </code>
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
        const inputField = document.getElementById('sub_domain_key');
        inputField.addEventListener('input', function(event) {
        const currentValue = event.target.value;
        const sanitizedValue = currentValue.replace(/[^0-9a-z]/g, '');
        event.target.value = sanitizedValue;
        });



        // Start get section
    $("#getCity").on('change', function (e) {
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
        success: function (data) {

            var city_option = '';
            var city_li = '';

            $.each(JSON.parse(data), function (i, item) {
                console.log(item);
                city_option += "<option value=" + item.id + ">" + item.name + "</option>";
                city_li += "<li data-value=" + item.id + " class='option'>" + item.name + "</li>";
            });

            // console.log(city_option);


            $("select.cities option").not(':first').remove();
            $("select.cities").append(city_option);

            $("div .cities .current").html($("div .cities .list li:first").html());
            $("div .cities .list li").not(':first').remove();
            $("div .cities .list").append(city_li);


        },
        error: function (data) {
            console.log(data);
        }
    });
});
// End get section

    </script>
@endpush
