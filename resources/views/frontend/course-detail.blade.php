@extends('frontend.master')

@php
    $course = $data['course'] ?? [];
    $pageTitle = $course['title'] ?? ___('frontend.Courses');
@endphp

@section('title')
    {{ $pageTitle }}
@endsection

@push('css')
<link rel="stylesheet" href="{{ global_asset('frontend') }}/css/frontend-courses.css">
@endpush

@section('main')

<!-- bradcam::start (same pattern as events / news detail) -->
<div class="breadcrumb_area" data-background="{{ @globalAsset(@$sections['study_at']->upload->path, '1920X700.webp') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-8">
                <div class="breadcam_wrap text-center">
                    <h3>{{ ___('frontend.Courses') }}</h3>
                    <div class="custom_breadcam">
                        <a href="{{ url('/') }}" class="breadcrumb-item">{{ ___('frontend.home') }}</a>
                        <a href="{{ route('frontend.courses') }}" class="breadcrumb-item">{{ ___('frontend.Courses') }}</a>
                        <span class="breadcrumb-item">{{ \Illuminate\Support\Str::limit($course['title'] ?? '', 48) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- bradcam::end -->

<div class="news_page_area section_padding fe-course-detail-page">
    <div class="container">
        <div class="row">
            <div class="col-xl-8">
                <div class="news_page_info mb_25">
                    @if(!empty($course['image']))
                        <div class="news_page_info_banner fe-course-detail-banner">
                            <img src="{{ $course['image'] }}" alt="{{ $course['title'] ?? '' }}" class="img-fluid">
                        </div>
                    @endif

                    <span class="event_tag">{{ $course['badge'] ?? 'Program' }}</span>
                    <h4 class="event_page_title">{{ $course['title'] ?? '' }}</h4>

                    <div class="event_wrap_location_time mb_40">
                        <h4>Program details</h4>
                        <ul>
                            @if(!empty($course['age_range']))
                                <li><strong>Age:</strong> {{ $course['age_range'] }}</li>
                            @endif
                            @if(!empty($course['grade']))
                                <li><strong>Grade:</strong> {{ $course['grade'] }}</li>
                            @endif
                            @if(!empty($course['lessons']))
                                <li><strong>Lessons:</strong> {{ $course['lessons'] }}</li>
                            @endif
                            @if(!empty($course['duration']))
                                <li><strong>Duration:</strong> {{ $course['duration'] }}</li>
                            @endif
                            @if(!empty($course['enrolled']))
                                <li><strong>Enrollment:</strong> {{ $course['enrolled'] }}</li>
                            @endif
                            @if(!empty($course['price']))
                                <li><strong>Fee:</strong> {{ $course['price'] }}</li>
                            @endif
                        </ul>
                    </div>

                    @if(!empty($course['description']))
                        <p class="description_1 mb_24">{{ $course['description'] }}</p>
                    @endif

                    <div class="d-flex flex-wrap gap_15 align-items-center mb_40">
                        <a href="{{ route('frontend.contact') }}" class="theme_btn small_btn3 min_windth_150 text-center">{{ ___('frontend.contact_us') }}</a>
                        <a href="{{ route('frontend.online-admission') }}" class="theme_btn2 small_btn3 min_windth_150 text-center">{{ ___('frontend.online_admission') }}</a>
                        <a href="{{ route('frontend.courses') }}" class="theme_btn2 small_btn3 min_windth_150 text-center">← {{ ___('frontend.Courses') }}</a>
                    </div>

                    @if(!empty($course['overview']) && is_array($course['overview']))
                        <span class="event_tag">{{ ___('frontend.Overview') }}</span>
                        @foreach ($course['overview'] as $para)
                            <p class="description_1 mb_24">{{ $para }}</p>
                        @endforeach
                    @endif

                    @if(!empty($course['format']))
                        <div class="event_wrap_location_time mb_30">
                            <h4>Schedule &amp; format</h4>
                            <p class="description_1 mb-0">{{ $course['format'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-4">
                <div class="news_page_right_sidebar mb_25">
                    @if(!empty($course['price']))
                        <div class="fe-course-sidebar-price mb_30">
                            <h4 class="font_24 f_w_400 mb_15">Online learning program</h4>
                            <p class="fe-course-sidebar-price__val mb-0">{{ $course['price'] }}</p>
                            <p class="small text-muted mt-2 mb-0">Confirm fee and availability with admissions.</p>
                        </div>
                    @endif

                    @if(!empty($course['highlights']) && is_array($course['highlights']))
                        <h4 class="font_24 f_w_400 mb_15">Highlights</h4>
                        <div class="event_page_info_details mb_30">
                            <ul class="event_page_lists">
                                @foreach ($course['highlights'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="description_1 mb_20">{{ $data['trust']['body'] ?? '' }}</p>
                    <a href="{{ route('frontend.contact') }}" class="theme_btn small_btn3 min_windth_150 text-center w-100 d-inline-flex justify-content-center">{{ ___('frontend.contact_us') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
