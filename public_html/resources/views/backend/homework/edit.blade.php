@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- thinking bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a
                                href="{{ route('homework.index') }}">{{ $data['title'] }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ___('common.edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}
        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('homework.update', [$data['homework']->id]) }}" enctype="multipart/form-data" method="post" id="markRegister">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">

                                <div class="col-md-4 mb-3">
                                    <label for="validationServer04" class="form-label">{{ ___('student_info.class') }} <span
                                        class="fillable">*</span></label>
                                    <select id="getSections"
                                        class="nice-select niceSelect bordered_style wide class @error('class') is-invalid @enderror"
                                        name="class" id="validationServer04" required
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_class') }}</option>
                                        @foreach ($data['classes'] as $item)
                                            <option {{ old('class', $data['homework']->classes_id) == $item->class->id ? 'selected' : '' }}
                                                value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('class')
                                        <div id="validationServer04Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="validationServer04" class="form-label">{{ ___('student_info.section') }} <span
                                        class="fillable">*</span></label>
                                    <select id="getSubjects"
                                        class="nice-select niceSelect sections bordered_style wide section @error('section') is-invalid @enderror"
                                        name="section" id="validationServer04" required
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_section') }}</option>
                                        @foreach ($data['sections'] as $item)
                                            <option {{ old('section', $data['homework']->section_id) == $item->section_id ? 'selected' : '' }}
                                                value="{{ $item->section_id }}">{{ $item->section->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('section')
                                        <div id="validationServer04Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="validationServer04" class="form-label">{{ ___('academic.subject') }} <span
                                        class="fillable">*</span></label>
                                    <select id="subject"
                                        class="nice-select niceSelect subjects bordered_style wide @error('subject') is-invalid @enderror"
                                        name="subject" id="validationServer04" required
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('examination.select_subject') }}</option>
                                        @foreach ($data['subjects'] as $item)
                                            <option {{ old('subject', $data['homework']->subject_id) == $item->subject_id ? 'selected' : '' }}
                                                value="{{ $item->subject_id }}">{{ $item->subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('subject')
                                        <div id="validationServer04Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ADDED: Task Category --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ ___('common.Task Category') }} <span class="fillable">*</span></label>
                                    <select id="task_type" class="nice-select niceSelect bordered_style wide @error('task_type') is-invalid @enderror" name="task_type" required>
                                        <option value="homework" {{ $data['homework']->task_type == 'homework' ? 'selected' : '' }}>Standard Homework</option>
                                        <option value="project" {{ $data['homework']->task_type == 'project' ? 'selected' : '' }}>Project</option>
                                        <option value="activity" {{ $data['homework']->task_type == 'activity' ? 'selected' : '' }}>Activity</option>
                                        <option value="game" {{ $data['homework']->task_type == 'game' ? 'selected' : '' }}>Educational Game</option>
                                        <option value="assignment" {{ $data['homework']->task_type == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                        <option value="quiz" {{ $data['homework']->task_type == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    </select>
                                    @error('task_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('account.date') }} <span
                                            class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('date') is-invalid @enderror" name="date" type="date"
                                        value="{{ old('date', $data['homework']->date) }}" id="exampleDataList" required
                                        placeholder="{{ ___('common.enter_date') }}">
                                    @error('date')
                                        <div id="validationServer06Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.submission_date') }} <span
                                            class="fillable"></span></label>
                                    <input class="form-control ot-input @error('submission_date') is-invalid @enderror" name="submission_date" type="date"
                                        value="{{ old('submission_date', $data['homework']->submission_date) }}" id="exampleDataList"
                                        placeholder="{{ ___('common.enter_submission_date') }}">
                                    @error('submission_date')
                                        <div id="validationServer06Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- UPDATED: Made Marks Mandatory with HTML required --}}
                                <div class="col-md-4 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('examination.marks') }} <span class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('marks') is-invalid @enderror" name="marks" required
                                        value="{{ old('marks', $data['homework']->marks) }}" id="total-marks"
                                        placeholder="{{ ___('examination.marks') }}">
                                    @error('marks')
                                        <div id="validationServer04Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="exampleDataList"
                                        class="form-label ">{{ ___('common.document') }} <span
                                            class="fillable"></span></label>
                                    <div class="ot_fileUploader left-side mb-3">
                                        <input class="form-control" type="text"
                                            placeholder="{{ ___('common.document') }}" readonly="" id="placeholder">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="btn btn-lg ot-btn-primary"
                                                for="fileBrouse">{{ ___('common.browse') }}</label>
                                            <input type="file" class="d-none form-control" name="document" id="fileBrouse" accept="image/*,.pdf,.doc,.docx">
                                        </button>
                                    </div>
                                </div>

                                {{-- ADDED: Homework Group (Mandatory) --}}
                                <div class="col-md-4 mb-3" id="question_group_div">
                                    <label class="form-label">Homework Group <span class="fillable">*</span></label>
                                    <select id="question_group" class="nice-select niceSelect bordered_style wide" name="question_group" required>
                                        <option value="">{{ ___('online-examination.Select question group') }}</option>
                                        @foreach ($data['question_groups'] ?? [] as $item)
                                            {{-- Assuming column might be question_group_id if it exists, otherwise just lists --}}
                                            <option value="{{ $item->id }}" {{ (isset($data['homework']->question_group_id) && $data['homework']->question_group_id == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="validationServer04" class="form-label">{{ ___('common.status') }} <span class="fillable">*</span></label>
                                    <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                    name="status" id="validationServer04" required
                                    aria-describedby="validationServer04Feedback">
                                        <option value="{{ App\Enums\Status::ACTIVE }}" {{$data['homework']->status == App\Enums\Status::ACTIVE ? 'selected':''}}>{{ ___('common.active') }}</option>
                                        <option value="{{ App\Enums\Status::INACTIVE }}" {{$data['homework']->status == App\Enums\Status::INACTIVE ? 'selected':''}}>{{ ___('common.inactive') }}</option>
                                    </select>
                                    @error('status')
                                        <div id="validationServer04Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ADDED: Homework Title --}}
                                <div class="col-md-12 mb-3">
                                    <label for="homework_title" class="form-label ">Homework Title <span class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('title') is-invalid @enderror" name="title" id="homework_title" value="{{ old('title', $data['homework']->title ?? '') }}" placeholder="Auto-generated title will appear here..." required>
                                    <small class="text-muted">The system generates this name automatically, but you can edit it if needed.</small>
                                </div>

                                {{-- ADDED: DYNAMIC QUIZ INFO BANNER --}}
                                <div class="col-md-12 mb-3 d-none" id="csv_template_hint">
                                    <div class="alert alert-info d-flex align-items-center justify-content-between">
                                        <span><i class="fa-solid fa-circle-info"></i> <strong>Quiz Mode Active:</strong> Please upload your questions using the Browse button above. Only CSV files matching the template are accepted.</span>
                                        <a href="{{ asset('Quiz_Template.csv') }}" class="btn btn-sm btn-primary" download="Quiz_Template.csv">
                                            <i class="fa-solid fa-download"></i> Download CSV Template
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('account.description') }}</label>
                                    <textarea class="form-control ot-textarea @error('description') is-invalid @enderror" name="description"
                                    placeholder="{{ ___('account.enter_description') }}">{{ old('description', $data['homework']->description) }}</textarea>
                                    @error('description')
                                        <div id="validationServer04Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-24">
                                    <div class="text-end">
                                        <button class="btn btn-lg ot-btn-primary"><span><i class="fa-solid fa-save"></i>
                                            </span>{{ ___('common.submit') }}</button>
                                    </div>
                                </div>
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
        $(document).ready(function() {
            // Task Type behavior
            $('body').on('change', '#task_type', function() {
                var selectedTask = $(this).val();
                if (selectedTask === 'quiz') {
                    $('#csv_template_hint').removeClass('d-none');
                    $('#fileBrouse').attr('accept', '.csv, text/csv, application/csv, text/plain, application/vnd.ms-excel'); 
                } else {
                    $('#csv_template_hint').addClass('d-none');
                    $('#fileBrouse').attr('accept', 'image/*,.pdf,.doc,.docx'); 
                }
            });

            $('#task_type').trigger('change');
        });

        // Auto-generate Title based on selections
        function generateTitle() {
            let group = $('#question_group option:selected').text().trim();
            let cls = $('#getSections option:selected').text().trim();
            let subj = $('#subject option:selected').text().trim();
            let task = $('#task_type option:selected').text().trim();
            let date = $('input[name="date"]').val();

            // Ignore default "Select" placeholder text
            if(group.includes('Select') || !group) group = '';
            if(cls.includes('Select') || !cls) cls = '';
            if(subj.includes('Select') || !subj) subj = '';
            
            // Combine them with underscores
            let parts = [group, cls, subj, task, date].filter(Boolean); // removes empty values
            if(parts.length > 0) {
                $('#homework_title').val(parts.join('_'));
            }
        }

        // Trigger generation when any of these dropdowns/inputs change
        $('#question_group, #getSections, #subject, #task_type, input[name="date"]').on('change', generateTitle);
    </script>
@endpush