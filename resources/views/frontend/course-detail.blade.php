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

<div class="fe-courses-page fe-course-detail-page">
    <div class="breadcrumb_area fe-course-detail-breadcrumb" style="padding-top: 2.5rem; padding-bottom: 1rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="breadcam_wrap text-start">
                        <div class="custom_breadcam mb-2">
                            <a href="{{ url('/') }}" class="breadcrumb-item">{{ ___('frontend.home') }}</a>
                            <span class="breadcrumb-item"><a href="{{ route('frontend.courses') }}">{{ ___('frontend.Courses') }}</a></span>
                            <span class="breadcrumb-item">{{ \Illuminate\Support\Str::limit($course['title'] ?? '', 42) }}</span>
                        </div>
                        <h3 class="text-start">{{ $course['title'] ?? '' }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section_padding pt-2 pb-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @if(!empty($course['image']))
                        <div class="fe-course-detail-hero__media mb-4">
                            <img src="{{ $course['image'] }}" alt="{{ $course['title'] ?? '' }}" loading="eager">
                            <span class="fe-course-card__badge fe-course-detail-hero__badge">{{ $course['badge'] ?? 'Program' }}</span>
                        </div>
                    @endif

                    <div class="fe-course-meta fe-course-meta--detail mb-4">
                        <span><i class="fas fa-user-graduate"></i>{{ $course['age_range'] ?? '' }}</span>
                        <span><i class="fas fa-school"></i>{{ $course['grade'] ?? '' }}</span>
                        <span><i class="fas fa-book-open"></i>{{ $course['lessons'] ?? '' }}</span>
                        <span><i class="far fa-clock"></i>{{ $course['duration'] ?? '' }}</span>
                    </div>

                    @if(!empty($course['enrolled']))
                        <p class="fe-course-enrolled mb-3"><i class="fas fa-users me-2"></i>{{ $course['enrolled'] }}</p>
                    @endif

                    @if(!empty($course['description']))
                        <p class="fe-course-detail-lead mb-4">{{ $course['description'] }}</p>
                    @endif

                    <div class="fe-courses-hero-cta mb-5 pb-2 flex-wrap">
                        <a href="{{ route('frontend.contact') }}" class="fe-btn-pill fe-btn-primary">Enquire about this program</a>
                        <a href="{{ route('frontend.online-admission') }}" class="fe-btn-pill fe-btn-ghost">Apply via admission</a>
                        <a href="{{ route('frontend.courses') }}" class="fe-btn-pill fe-btn-ghost">← All programs</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section_padding pt-0 pb-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    @if(!empty($course['overview']) && is_array($course['overview']))
                        @foreach ($course['overview'] as $para)
                            <p class="fe-course-detail-copy">{{ $para }}</p>
                        @endforeach
                    @endif

                    @if(!empty($course['format']))
                        <div class="fe-course-detail-format">
                            <h4 class="fe-course-detail-heading">Schedule & format</h4>
                            <p class="fe-course-detail-copy mb-0">{{ $course['format'] }}</p>
                        </div>
                    @endif
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0 offset-lg-1">
                    <aside class="fe-course-detail-sidebar">
                        <h4 class="fe-course-detail-heading">What you can expect</h4>
                        @if(!empty($course['highlights']) && is_array($course['highlights']))
                            <ul class="fe-course-detail-highlights">
                                @foreach ($course['highlights'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <div class="fe-course-detail-sidebar-cta mt-4">
                            <p class="small text-muted mb-2">{{ $data['trust']['body'] ?? 'Need placement advice? Admissions can route you quickly.' }}</p>
                            <a href="{{ route('frontend.contact') }}" class="fe-btn-pill fe-btn-primary w-100 text-center justify-content-center">Contact admissions</a>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
