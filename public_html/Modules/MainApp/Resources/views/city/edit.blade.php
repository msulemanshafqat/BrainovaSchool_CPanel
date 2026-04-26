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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> {{ ___('mainapp_common.home') }} </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('faq.index') }}">{{ ___('mainapp_city.faqs') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('mainapp_common.edit') }}</li>

                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}
        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('city.update', @$data['city']->id) }}" enctype="multipart/form-data" method="post"
                    id="visitForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Country Select -->
                        <div class="col-md-4 mb-3">
                            <label for="country_id" class="form-label">{{ ___('mainapp_city.Country') }} <span
                                    class="fillable">*</span></label>
                            <select
                                class="nice-select niceSelect bordered_style wide @error('country_id') is-invalid @enderror"
                                name="country_id" id="country_id" aria-describedby="validationCountryFeedback">
                                <option value="">{{ ___('common.Select Country') }}</option>
                                @foreach ($data['countries'] as $country)
                                    <option value="{{ $country->id }}"
                                        {{ old('country_id', $data['city']->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <div id="validationCountryFeedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- City Name Input -->
                        <div class="col-md-4 mb-3">
                            <label for="name" class="form-label">{{ ___('mainapp_city.City Name') }} <span
                                    class="fillable">*</span></label>
                            <input type="text" id="name" name="name"
                                class="form-control ot-input @error('name') is-invalid @enderror"
                                placeholder="{{ ___('mainapp_city.Enter city name') }}"
                                value="{{ old('name', $data['city']->name ?? '') }}">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>



                        <div class="col-md-4 mb-3">
                            <label for="validationServer04" class="form-label">{{ ___('mainapp_common.status') }} <span
                                    class="fillable">*</span></label>
                            <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                name="status" id="validationServer04" aria-describedby="validationServer04Feedback">
                                <option
                                    {{ old('status', @$data['faq']->status) == App\Enums\Status::ACTIVE ? 'selected' : '' }}
                                    value="{{ App\Enums\Status::ACTIVE }}">{{ ___('mainapp_common.active') }}</option>
                                <option
                                    {{ old('status', @$data['faq']->status) == App\Enums\Status::INACTIVE ? 'selected' : '' }}
                                    value="{{ App\Enums\Status::INACTIVE }}">{{ ___('mainapp_common.inactive') }}
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
