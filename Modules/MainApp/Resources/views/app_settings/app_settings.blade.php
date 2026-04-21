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
                            <li class="breadcrumb-item">{{ $data['title'] }}</li>
                        </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-header">
                <h4>{{ ___('settings.app_setting') }}</h4>
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <div class="table-content table-basic mt-20">
                        <div class="card shadow-sm rounded-4 mb-4">
                            <ul class="nav theme_tabs " id="infoTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="personal-tab" data-bs-toggle="tab"
                                        data-bs-target="#personal" type="button"
                                        role="tab">{{ ___('student.Student') }} </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="guardians-tab" data-bs-toggle="tab"
                                        data-bs-target="#guardians" type="button"
                                        role="tab">{{ ___('student.Teacher') }} </button>
                                </li>
                            </ul>
                            <div class="tab-content border border-top-0 " id="infoTabsContent">
                                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                    <form action="{{ route('settings.update-app-settings') }}" enctype="multipart/form-data"
                                        method="post" id="visitForm">
                                        @csrf
                                        <h4 class="p-3">{{ ___('common.Slider') }}</h4>
                                        <input type="hidden" class="row_count" name="row_count"
                                            value="{{ count($data['sliders']->where('user_type', 'student')) }}">
                                            <input type="hidden" name="user_type" value="student">
                                        @foreach ($data['sliders']->where('user_type', 'student') as $key => $menu)
                                            <div class="row p-3" id="dynamicWrap">
                                                <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3 ">
                                                    <label for="inputname" class="form-label">{{ ___('settings.title') }}
                                                        <span class="fillable">*</span></label>
                                                    <input type="text" name="slider_title[]"
                                                        class="form-control ot-input @error('title') is-invalid @enderror"
                                                        value="{{ $menu->title }}"
                                                        placeholder="{{ ___('settings.enter_you_title') }}">
                                                    @error('title')
                                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <input type="hidden" name="slider_slug[]" value="{{ $menu->slug }}">
                                                </div>
                                                <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3">
                                                    <label class="form-label" for="light_logo">{{ ___('settings.icon') }}
                                                        {{ ___('common.(155 x 40 px)') }}</label>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <div class="ot_fileUploader left-side mb-3">
                                                            <input class="form-control" type="text"
                                                                placeholder="{{ ___('settings.browse_icon_path') }}"
                                                                readonly>
                                                            <button class="primary-btn-small-input" type="button">
                                                                <label class="btn btn-lg ot-btn-primary"
                                                                    for="fileBrowse-{{ $loop->index }}">
                                                                    {{ ___('common.browse') }}
                                                                </label>
                                                                <input type="file" class="d-none form-control"
                                                                    name="slider_icon_path[{{ $menu->slug }}][]"
                                                                    id="fileBrowse-{{ $loop->index }}" accept="image/*">
                                                            </button>
                                                        </div>
                                                        <img class="img-thumbnail mb-10 ot-input full-logo setting-image"
                                                            src="{{ @globalAsset($menu->upload->path, '154X38.webp') }}"
                                                            alt="{{ __('light logo') }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-2 col-xl-2 col-lg-2 mb-3">
                                                    <label for="is_active_{{ $menu->slug }}" class="form-label">
                                                        {{ ___('settings.status') }} <span class="fillable">*</span>
                                                    </label>
                                                    <div class="toggle-checkbox-wrapper">
                                                        <input type="hidden" name="slider_is_active[{{ $menu->slug }}]"
                                                            value="{{ App\Enums\Status::INACTIVE }}">

                                                        <input class="toggle-checkbox" type="checkbox"
                                                            id="is_active_{{ $menu->slug }}"
                                                            name="slider_is_active[{{ $menu->slug }}][]"
                                                            value="{{ App\Enums\Status::ACTIVE }}"
                                                            {{ $menu->is_active == App\Enums\Status::ACTIVE ? 'checked' : '' }}>

                                                        <label class="slider-btn"
                                                            for="is_active_{{ $menu->slug }}"></label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-1 col-xl-1 col-lg-1 mb-3">
                                                    @if ($key == 0)
                                                        <div class="ot-contact-form float-end mb-3">
                                                            <button type="button"
                                                                class="add-schedule-btn btn-primary-fill"
                                                                onclick="newSliderSection()">
                                                                +
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="ot-contact-form float-end mb-3">

                                                            <button class="btn-cancel-fill delete_item_btn"
                                                                onclick="removeDynamicItem(this)" type="button">
                                                                -
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        <div id="appendSoftwareSolution"></div>


                                        <h4 class="p-3">{{ ___('common.Menu') }}</h4>
                                        <input type="hidden" name="user_type" value="student">
                                        @foreach ($data['menus']->where('user_type', 'student') as $menu)
                                            <div class="row p-3">
                                                <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3 ">
                                                    <label for="inputname" class="form-label">{{ ___('settings.title') }}
                                                        <span class="fillable">*</span></label>
                                                    <input type="text" name="title[{{ $menu->slug }}][]"
                                                        class="form-control ot-input @error('title') is-invalid @enderror"
                                                        value="{{ $menu->title }}"
                                                        placeholder="{{ ___('settings.enter_you_title') }}">
                                                    @error('title')
                                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <input type="hidden" name="slug[]" value="{{ $menu->slug }}">
                                                </div>
                                                <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3">
                                                    <label class="form-label" for="light_logo">{{ ___('settings.icon') }}
                                                        {{ ___('common.(155 x 40 px)') }}</label>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <div class="ot_fileUploader left-side mb-3">
                                                            <input class="form-control" type="text"
                                                                placeholder="{{ ___('settings.browse_icon_path') }}"
                                                                readonly>
                                                            <button class="primary-btn-small-input" type="button">
                                                                <label class="btn btn-lg ot-btn-primary"
                                                                    for="fileBrowse-{{ $loop->index }}">
                                                                    {{ ___('common.browse') }}
                                                                </label>
                                                                <input type="file" class="d-none form-control"
                                                                    name="icon_path[{{ $menu->slug }}][]"
                                                                    id="fileBrowse-{{ $loop->index }}" accept="image/*">
                                                            </button>
                                                        </div>
                                                        <img class="img-thumbnail mb-10 ot-input full-logo setting-image"
                                                            src="{{ @globalAsset($menu->upload->path, '154X38.webp') }}"
                                                            alt="{{ __('light logo') }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3">
                                                    <label for="is_active_{{ $menu->slug }}" class="form-label">
                                                        {{ ___('settings.status') }} <span class="fillable">*</span>
                                                    </label>
                                                    <div class="toggle-checkbox-wrapper">
                                                        <input type="hidden" name="is_active[{{ $menu->slug }}]"
                                                            value="{{ App\Enums\Status::INACTIVE }}">

                                                        <input class="toggle-checkbox" type="checkbox"
                                                            id="is_active_{{ $menu->slug }}"
                                                            name="is_active[{{ $menu->slug }}][]"
                                                            value="{{ App\Enums\Status::ACTIVE }}"
                                                            {{ $menu->is_active == App\Enums\Status::ACTIVE ? 'checked' : '' }}>

                                                        <label class="slider-btn"
                                                            for="is_active_{{ $menu->slug }}"></label>
                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach
                                        <div class="col-md-12 mt-3">
                                            <div class="text-end">
                                                @if (hasPermission('storage_settings_update'))
                                                    <button class="btn btn-lg ot-btn-primary">
                                                        <span>
                                                            <i class="fa-solid fa-save"></i>
                                                        </span>{{ ___('common.update') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="guardians" role="tabpanel">
                                    <h4 class="p-3">{{ ___('common.Menu') }}</h4>
                                    <form action="{{ route('settings.update-app-settings') }}"
                                        enctype="multipart/form-data" method="post" id="visitForm">
                                        @csrf
                                        <input type="hidden" name="user_type" value="teacher">
                                        @foreach ($data['menus']->where('user_type', 'teacher') as $menu)
                                            <div class="row p-3">
                                                <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3 ">
                                                    <label for="inputname" class="form-label">{{ ___('settings.title') }}
                                                        <span class="fillable">*</span></label>
                                                    <input type="text" name="title[{{ $menu->slug }}][]"
                                                        class="form-control ot-input @error('title') is-invalid @enderror"
                                                        value="{{ $menu->title }}"
                                                        placeholder="{{ ___('settings.enter_you_title') }}">
                                                    @error('title')
                                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <input type="hidden" name="slug[]" value="{{ $menu->slug }}">
                                                </div>
                                                <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3">
                                                    <label class="form-label" for="light_logo">{{ ___('settings.icon') }}
                                                        {{ ___('common.(155 x 40 px)') }}</label>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <div class="ot_fileUploader left-side mb-3">
                                                            <input class="form-control" type="text"
                                                                placeholder="{{ ___('settings.browse_icon_path') }}"
                                                                readonly>
                                                            <button class="primary-btn-small-input" type="button">
                                                                <label class="btn btn-lg ot-btn-primary"
                                                                    for="fileBrowse-{{ $loop->index }}">
                                                                    {{ ___('common.browse') }}
                                                                </label>
                                                                <input type="file" class="d-none form-control"
                                                                    name="icon_path[{{ $menu->slug }}][]"
                                                                    id="fileBrowse-{{ $loop->index }}" accept="image/*">
                                                            </button>
                                                        </div>
                                                        <img class="img-thumbnail mb-10 ot-input full-logo setting-image"
                                                            src="{{ @globalAsset($menu->upload->path, '154X38.webp') }}"
                                                            alt="{{ __('light logo') }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3">
                                                    <label for="is_active_{{ $menu->slug }}" class="form-label">
                                                        {{ ___('settings.status') }} <span class="fillable">*</span>
                                                    </label>
                                                    <div class="toggle-checkbox-wrapper">
                                                        <input type="hidden" name="is_active[{{ $menu->slug }}]"
                                                            value="{{ App\Enums\Status::INACTIVE }}">

                                                        <input class="toggle-checkbox" type="checkbox"
                                                            id="is_active_{{ $menu->slug }}"
                                                            name="is_active[{{ $menu->slug }}][]"
                                                            value="{{ App\Enums\Status::ACTIVE }}"
                                                            {{ $menu->is_active == App\Enums\Status::ACTIVE ? 'checked' : '' }}>

                                                        <label class="slider-btn"
                                                            for="is_active_{{ $menu->slug }}"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-md-12 mt-3">
                                            <div class="text-end">
                                                @if (hasPermission('storage_settings_update'))
                                                    <button class="btn btn-lg ot-btn-primary">
                                                        <span>
                                                            <i class="fa-solid fa-save"></i>
                                                        </span>{{ ___('common.update') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function newSliderSection() {
        let row_count = $(".row_count").val();
        let elementCount = row_count;
        $('#appendSoftwareSolution').append(`
                <div id="dynamicWrap" class="row p-3">

                    <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3 ">
                                                    <label for="inputname" class="form-label">{{ ___('settings.title') }}
                                                        <span class="fillable">*</span></label>
                                                    <input type="text" name="slider_title[]"
                                                        class="form-control ot-input @error('title') is-invalid @enderror"
                                                        value="{{ $menu->title }}"
                                                        placeholder="{{ ___('settings.enter_you_title') }}">
                                                    @error('title')
                                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <input type="hidden" name="slug[]" value="{{ $menu->slug }}">
                                                </div>
                                                <div class="col-12 col-md-4 col-xl-4 col-lg-4 mb-3">
                                                    <label class="form-label" for="light_logo">{{ ___('settings.icon') }}
                                                        {{ ___('common.(155 x 40 px)') }}</label>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <div class="ot_fileUploader left-side mb-3">
                                                            <input class="form-control" type="text"
                                                                placeholder="{{ ___('settings.browse_icon_path') }}"
                                                                readonly>
                                                            <button class="primary-btn-small-input" type="button">
                                                                <label class="btn btn-lg ot-btn-primary"
                                                                    for="fileBrowse">
                                                                    {{ ___('common.browse') }}
                                                                </label>
                                                                <input type="file" class="d-none form-control"
                                                                    name="slider_icon_path[]"
                                                                    id="fileBrowse" accept="image/*">
                                                            </button>
                                                        </div>
                                                        <img class="img-thumbnail mb-10 ot-input full-logo setting-image"
                                                            src="{{ @globalAsset($menu->upload->path, '154X38.webp') }}"
                                                            alt="{{ __('light logo') }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-2 col-xl-2 col-lg-2 mb-3">
                                                    <label for="is_active_{{ $menu->slug }}" class="form-label">
                                                        {{ ___('settings.status') }} <span class="fillable">*</span>
                                                    </label>
                                                    <div class="toggle-checkbox-wrapper">
                                                        <input type="hidden" name="slider_is_active"
                                                            value="{{ App\Enums\Status::INACTIVE }}">

                                                        <input class="toggle-checkbox" type="checkbox"
                                                            id="is_active_{{ $menu->slug }}"
                                                            name="slider_is_active[]"
                                                            value="{{ App\Enums\Status::ACTIVE }}"
                                                            {{ $menu->is_active == App\Enums\Status::ACTIVE ? 'checked' : '' }}>

                                                        <label class="slider-btn"
                                                            for="is_active_{{ $menu->slug }}"></label>
                                                    </div>
                                                </div>


                    <div class="col-12 col-md-1 col-xl-1 col-lg-1 mb-3">
                        <div class="d-flex justify-content-end align-items-end gap-10">

                            <button class="btn-cancel-fill delete_item_btn" onclick="removeDynamicItem(this)" type="button">
                                -
                            </button>
                        </div>
                    </div>
                </div>
            `);
    }

    // Define the removeDynamicItem function
    function removeDynamicItem(element) {
        $(element).closest('#dynamicWrap').remove();
    }


    document.addEventListener("DOMContentLoaded", function() {
        const fileInputs = document.querySelectorAll("input[type='file'][name='icon_path[]']");

        fileInputs.forEach(function(fileInput) {
            fileInput.addEventListener("change", function(event) {
                const wrapper = this.closest('.ot_fileUploader');
                const textInput = wrapper.querySelector("input[type='text']");

                if (textInput && this.files.length > 0) {
                    textInput.placeholder = this.files[0].name;
                }
            });
        });
    });
</script>
