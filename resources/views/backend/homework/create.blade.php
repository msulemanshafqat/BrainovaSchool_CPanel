@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
    <input type="hidden" id="url" value="{{ url('/') }}">

    <div class="page-content">

        {{-- BREADCRUMB --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('homework.index') }}">{{ $data['title'] }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ___('common.add_new') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- END BREADCRUMB --}}

        <div class="card ot-card">
            <div class="card-body">

                {{--
                    IMPORTANT: Form submits to homework.store (HomeworkController@store → HomeworkRepository@store).
                    The rogue homework-quiz.store route (HomeworkQuizController) has been removed.
                --}}
                <form action="{{ route('homework.store') }}" enctype="multipart/form-data" method="post">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">

                                {{-- 1. CLASS --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        {{ ___('student_info.class') }} <span class="fillable">*</span>
                                    </label>
                                    <select id="getSections"
                                            class="nice-select niceSelect bordered_style wide class @error('class') is-invalid @enderror"
                                            name="class">
                                        <option value="">{{ ___('student_info.select_class') }}</option>
                                        @foreach ($data['classes'] as $item)
                                            <option value="{{ $item->class->id }}"
                                                {{ old('class') == $item->class->id ? 'selected' : '' }}>
                                                {{ $item->class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 2. SECTION (AJAX populated) --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        {{ ___('student_info.section') }} <span class="fillable">*</span>
                                    </label>
                                    <select id="getSubjects"
                                            class="nice-select niceSelect sections bordered_style wide section @error('section') is-invalid @enderror"
                                            name="section">
                                        <option value="">{{ ___('student_info.select_section') }}</option>
                                    </select>
                                    @error('section')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 3. SUBJECT (AJAX populated) --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        {{ ___('academic.subject') }} <span class="fillable">*</span>
                                    </label>
                                    <select id="subject"
                                            class="nice-select niceSelect subjects bordered_style wide @error('subject') is-invalid @enderror"
                                            name="subject">
                                        <option value="">{{ ___('examination.select_subject') }}</option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 4. TASK CATEGORY --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        {{ ___('common.Task Category') }} <span class="fillable">*</span>
                                    </label>
                                    <select id="task_type"
                                            class="nice-select niceSelect bordered_style wide @error('task_type') is-invalid @enderror"
                                            name="task_type">
                                        <option value="homework"    {{ old('task_type', 'homework') == 'homework'   ? 'selected' : '' }}>Standard Homework</option>
                                        <option value="project"     {{ old('task_type') == 'project'    ? 'selected' : '' }}>Project</option>
                                        <option value="activity"    {{ old('task_type') == 'activity'   ? 'selected' : '' }}>Activity</option>
                                        <option value="game"        {{ old('task_type') == 'game'       ? 'selected' : '' }}>Game</option>
                                        <option value="assignment"  {{ old('task_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                        <option value="quiz"        {{ old('task_type') == 'quiz'       ? 'selected' : '' }}>Quiz</option>
                                    </select>
                                    @error('task_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 5. ASSIGNMENT DATE --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        {{ ___('account.date') }} <span class="fillable">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control ot-input @error('date') is-invalid @enderror"
                                           name="date"
                                           id="hw_date"
                                           value="{{ old('date') }}">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 6. SUBMISSION DATE --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ ___('common.submission_date') }} <span class="fillable">*</span></label>
                                    <input type="date"
                                           class="form-control ot-input @error('submission_date') is-invalid @enderror"
                                           name="submission_date"
                                           value="{{ old('submission_date') }}">
                                    @error('submission_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 7. TOTAL MARKS --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        {{ ___('examination.marks') }} <span class="fillable">*</span>
                                    </label>
                                    <input type="number"
                                           class="form-control ot-input @error('marks') is-invalid @enderror"
                                           name="marks"
                                           id="total-marks"
                                           value="{{ old('marks') }}"
                                           placeholder="{{ ___('examination.marks') }}"
                                           min="0">
                                    @error('marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 8. TOPIC (teacher-entered heading, drives title generation) --}}
                                <div class="col-md-4 mb-3">
                                    <label for="hw_topic" class="form-label">
                                        Homework Topic <span class="fillable">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control ot-input @error('topic') is-invalid @enderror"
                                           name="topic"
                                           id="hw_topic"
                                           value="{{ old('topic') }}"
                                           placeholder="e.g. Solar System, Fractions, World War II">
                                    <small class="text-muted">This will appear at the start of the title.</small>
                                    @error('topic')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 9. STATUS --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        {{ ___('common.status') }} <span class="fillable">*</span>
                                    </label>
                                    <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                            name="status">
                                        <option value="{{ App\Enums\Status::ACTIVE }}"
                                            {{ old('status', App\Enums\Status::ACTIVE) == App\Enums\Status::ACTIVE ? 'selected' : '' }}>
                                            {{ ___('common.active') }}
                                        </option>
                                        <option value="{{ App\Enums\Status::INACTIVE }}"
                                            {{ old('status') == App\Enums\Status::INACTIVE ? 'selected' : '' }}>
                                            {{ ___('common.inactive') }}
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 10. FILE UPLOAD (document OR quiz CSV — toggled by JS) --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" id="file_upload_label">
                                        {{ ___('common.document') }}
                                        <span id="file_required_star"></span>
                                    </label>
                                    <div class="ot_fileUploader left-side mb-1">
                                        <input class="form-control"
                                               type="text"
                                               id="file_placeholder"
                                               placeholder="{{ ___('common.document') }}"
                                               readonly>
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="btn btn-lg ot-btn-primary" for="fileBrouse">
                                                {{ ___('common.browse') }}
                                            </label>
                                            {{--
                                                The name attribute is toggled by JS:
                                                  - Non-quiz → name="document"   (optional)
                                                  - Quiz     → name="bulk_questions_file" (required)
                                            --}}
                                            <input type="file"
                                                   class="d-none form-control"
                                                   name="document"
                                                   id="fileBrouse"
                                                   accept="image/*,.pdf,.doc,.docx">
                                        </button>
                                    </div>
                                    @error('document')
                                        <div class="text-danger font-size-12">{{ $message }}</div>
                                    @enderror
                                    @error('bulk_questions_file')
                                        <div class="text-danger font-size-12">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 11. AUTO-GENERATED TITLE (readonly — built by JS from topic + dropdowns) --}}
                                <div class="col-md-12 mb-3">
                                    <label for="hw_title" class="form-label">
                                        Homework Title <span class="fillable">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control ot-input @error('title') is-invalid @enderror"
                                           name="title"
                                           id="hw_title"
                                           value="{{ old('title') }}"
                                           placeholder="Fill in the Topic, Class, Subject and Date above to generate the title..."
                                           readonly>
                                    <small class="text-muted">
                                        Auto-generated from your inputs. Not editable — ensures a consistent naming standard.
                                    </small>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 12. QUIZ MODE BANNER (hidden by default, shown when task_type = quiz) --}}
                                <div class="col-md-12 mb-3 d-none" id="csv_template_hint">
                                    <div class="alert alert-info d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <span>
                                            <i class="fa-solid fa-circle-info"></i>
                                            <strong>Quiz Mode Active:</strong>
                                            Upload your questions as a CSV file using the Browse button above.
                                            The file must follow the template format.
                                        </span>
                                        <a href="{{ route('homework.download-sample') }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-download"></i> Download CSV Template
                                        </a>
                                    </div>
                                </div>

                                {{-- 13. DESCRIPTION --}}
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{ ___('account.description') }}</label>
                                    <textarea class="form-control ot-textarea @error('description') is-invalid @enderror"
                                              name="description"
                                              placeholder="{{ ___('account.enter_description') }}">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- 14. SUBMIT --}}
                                <div class="col-md-12 mt-4 mb-4">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-lg ot-btn-primary">
                                            <i class="fa-solid fa-save"></i> {{ ___('common.submit') }}
                                        </button>
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
$(document).ready(function () {

    // =========================================================================
    // 1. RESTORE AJAX DROPDOWNS AFTER VALIDATION FAILURE
    // Fires a setTimeout chain so each AJAX call has time to complete before
    // the next dependent dropdown is triggered.
    // =========================================================================
    var oldClass   = "{{ old('class') }}";
    var oldSection = "{{ old('section') }}";
    var oldSubject = "{{ old('subject') }}";

    if (oldClass) {
        setTimeout(function () {
            $('#getSections').val(oldClass).trigger('change');
            $('#getSections').niceSelect('update');

            setTimeout(function () {
                if (oldSection) {
                    $('#getSubjects').val(oldSection).trigger('change');
                    $('#getSubjects').niceSelect('update');

                    setTimeout(function () {
                        if (oldSubject) {
                            $('#subject').val(oldSubject).trigger('change');
                            $('#subject').niceSelect('update');
                        }
                    }, 800);
                }
            }, 800);
        }, 300);
    }

    // =========================================================================
    // 2. TASK TYPE TOGGLE — switches file input between document and CSV mode
    // =========================================================================
    $('#task_type').on('change', function () {
        var taskType = $(this).val();

        if (taskType === 'quiz') {
            // Quiz mode: CSV is mandatory
            $('#csv_template_hint').removeClass('d-none');
            $('#file_upload_label').html('Quiz Question File <span class="fillable">*</span>');
            $('#file_required_star').html(' <span class="fillable">*</span>');
            $('#fileBrouse').attr('name', 'bulk_questions_file');
            $('#fileBrouse').attr('accept', '.csv,text/csv,text/plain');
            $('#file_placeholder').attr('placeholder', 'Upload Quiz CSV File... (required)');
        } else {
            // All other types: document is optional
            $('#csv_template_hint').addClass('d-none');
            $('#file_upload_label').html('{{ ___('common.document') }}');
            $('#file_required_star').html('');
            $('#fileBrouse').attr('name', 'document');
            $('#fileBrouse').attr('accept', 'image/*,.pdf,.doc,.docx');
            $('#file_placeholder').attr('placeholder', '{{ ___('common.document') }} (optional)');
        }

        generateTitle();
    });

    // Show selected filename in the readonly text box
    $('#fileBrouse').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $('#file_placeholder').val(fileName);
    });

    // Run on page load to apply correct state (handles old() input after validation failure)
    $('#task_type').trigger('change');

    // =========================================================================
    // 3. AUTO-GENERATE TITLE
    // Format: {topic}_{ClassShorthand}_{SubjectAbbr}_{TaskAbbr}_{date}
    // Example: solar_3_IPC_prj_2026-04-22
    // Title is readonly — teachers cannot edit it directly.
    // =========================================================================
    var taskAbbr = {
        'homework':   'hw',
        'project':    'prj',
        'activity':   'act',
        'game':       'game',
        'assignment': 'assign',
        'quiz':       'quiz'
    };

    function generateTitle() {
        var topic  = $('#hw_topic').val().trim();
        var cls    = $('#getSections option:selected').text().trim();
        var subj   = $('#subject option:selected').text().trim();
        var task   = $('#task_type').val();
        var date   = $('#hw_date').val();

        // Strip placeholder text from dropdowns
        if (!cls  || cls.toLowerCase().includes('select'))  cls  = '';
        if (!subj || subj.toLowerCase().includes('select')) subj = '';

        // Class shorthand: strip "GRADE-" / "Grade " prefix, keep the number/code
        cls = cls.replace(/grade[\s\-]*/i, '').trim();

        // Task abbreviation
        var abbr = taskAbbr[task] || task;

        var parts = [topic, cls, subj, abbr, date].filter(Boolean);
        $('#hw_title').val(parts.length > 0 ? parts.join('_') : '');
    }

    // Regenerate title whenever any contributing field changes
    $('#hw_topic, #getSections, #subject, #task_type, #hw_date').on('change input', generateTitle);

});
</script>
@endpush
