@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
    {{-- Hidden input for base URL, often used by theme AJAX scripts --}}
    <input type="hidden" id="url" value="{{ url('/') }}">
    
    <div class="page-content">
        {{-- ========================================================== --}}
        {{-- BREADCRUMB AREA START --}}
        {{-- ========================================================== --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                            <li class="breadcrumb-item" aria-current="page"><a
                                    href="{{ route('homework.index') }}">{{ $data['title'] }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ ___('common.add_new') }}</li>
                        </ol>
                </div>
            </div>
        </div>
        {{-- BREADCRUMB AREA END --}}
        
        <div class="card ot-card">
            <div class="card-body">
                
                {{-- Form Start: Points to store route, allows file uploads (enctype) --}}
                <form action="{{ route('homework-quiz.store') }}" enctype="multipart/form-data" method="post" id="markRegister">
                    @csrf {{-- Laravel security token --}}
                    
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">

                                {{-- ========================================================== --}}
                                {{-- 1. CLASS DROPDOWN --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4 mb-3">
                                    <label for="validationServer04" class="form-label">{{ ___('student_info.class') }} <span class="fillable">*</span></label>
                                    <select id="getSections" class="nice-select niceSelect bordered_style wide class @error('class') is-invalid @enderror" name="class">
                                        <option value="">{{ ___('student_info.select_class') }}</option>
                                        {{-- Loop through assigned classes. Retain selection if validation fails using old('class') --}}
                                        @foreach ($data['classes'] as $item)
                                            <option {{ old('class') == $item->class->id ? 'selected' : '' }} value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- ========================================================== --}}
                                {{-- 2. SECTION DROPDOWN (Populated via AJAX) --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4 mb-3">
                                    <label for="validationServer04" class="form-label">{{ ___('student_info.section') }} <span class="fillable">*</span></label>
                                    <select id="getSubjects" class="nice-select niceSelect sections bordered_style wide section @error('section') is-invalid @enderror" name="section">
                                        <option value="">{{ ___('student_info.select_section') }}</option>
                                        {{-- Options injected by JS when Class is changed --}}
                                    </select>
                                    @error('section')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 3. SUBJECT DROPDOWN (Populated via AJAX) --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4 mb-3">
                                    <label for="validationServer04" class="form-label">{{ ___('academic.subject') }} <span class="fillable">*</span></label>
                                    <select id="subject" class="nice-select niceSelect subjects bordered_style wide @error('subject') is-invalid @enderror" name="subject">
                                        <option value="">{{ ___('examination.select_subject') }}</option>
                                        {{-- Options injected by JS when Section is changed --}}
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 4. TASK CATEGORY (Determines if Quiz CSV or Document) --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ ___('common.Task Category') }} <span class="fillable">*</span></label>
                                    <select id="task_type" class="nice-select niceSelect bordered_style wide @error('task_type') is-invalid @enderror" name="task_type">
                                        {{-- Retain selection on error using old('task_type') --}}
                                        <option value="homework" {{ old('task_type') == 'homework' ? 'selected' : '' }}>Standard Homework</option>
                                        <option value="project" {{ old('task_type') == 'project' ? 'selected' : '' }}>Project</option>
                                        <option value="activity" {{ old('task_type') == 'activity' ? 'selected' : '' }}>Activity</option>
                                        <option value="game" {{ old('task_type') == 'game' ? 'selected' : '' }}>Educational Game</option>
                                        <option value="assignment" {{ old('task_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                        <option value="quiz" {{ old('task_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    </select>
                                    @error('task_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 5. ASSIGNMENT DATE --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('account.date') }} <span class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('date') is-invalid @enderror" name="date" type="date" value="{{ old('date') }}" id="exampleDataList" placeholder="{{ ___('common.enter_date') }}">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- ========================================================== --}}
                                {{-- 6. SUBMISSION (DUE) DATE --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.submission_date') }} <span class="fillable"></span></label>
                                    <input class="form-control ot-input @error('submission_date') is-invalid @enderror" name="submission_date" type="date" value="{{ old('submission_date') }}" id="exampleDataList" placeholder="{{ ___('common.enter_submission_date') }}">
                                    @error('submission_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- ========================================================== --}}
                                {{-- 7. TOTAL MARKS --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4 mb-3">
                                    <label for="total-marks" class="form-label ">{{ ___('examination.marks') }} <span class="fillable">*</span></label>
                                    <input type="number" class="form-control ot-input @error('marks') is-invalid @enderror" name="marks" id="total-marks" value="{{ old('marks') }}" placeholder="{{ ___('examination.marks') }}">
                                    @error('marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 8. UNIFIED BROWSE BUTTON (Document OR CSV via JS) --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4" id="standard_upload_div">
                                    <label class="form-label ">{{ ___('common.document') }} / CSV</label>
                                    <div class="ot_fileUploader left-side mb-1">
                                        <input class="form-control" type="text" placeholder="{{ ___('common.document') }}" readonly="" id="placeholder">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="btn btn-lg ot-btn-primary" for="fileBrouse">{{ ___('common.browse') }}</label>
                                            <input type="file" class="d-none form-control" name="document" id="fileBrouse" accept="image/*,.pdf,.doc,.docx">
                                        </button>
                                    </div>
                                    {{-- Display Error if standard document fails --}}
                                    @error('document')
                                        <div class="text-danger font-size-12">{{ $message }}</div>
                                    @enderror
                                    {{-- Display Error if CSV fails --}}
                                    @error('bulk_questions_file')
                                        <div class="text-danger font-size-12">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 9. HOMEWORK GROUP DROPDOWN --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4 mb-3" id="question_group_div">
                                    <label class="form-label">Homework Group <span class="fillable">*</span></label>
                                    <select id="question_group" class="nice-select niceSelect bordered_style wide @error('question_group') is-invalid @enderror" name="question_group">
                                        <option value="">{{ ___('online-examination.Select question group') }}</option>
                                        @foreach ($data['question_groups'] as $item)
                                            {{-- FIXED: Added old() logic here so it remembers the group! --}}
                                            <option value="{{ $item->id }}" {{ old('question_group') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('question_group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 10. STATUS DROPDOWN --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-4 mb-3">
                                    <label for="validationServer04" class="form-label">{{ ___('common.status') }} <span class="fillable">*</span></label>
                                    <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror" name="status">
                                        {{-- FIXED: Added old() logic here so it remembers status --}}
                                        <option value="{{ App\Enums\Status::ACTIVE }}" {{ old('status', App\Enums\Status::ACTIVE) == App\Enums\Status::ACTIVE ? 'selected' : '' }}>{{ ___('common.active') }}</option>
                                        <option value="{{ App\Enums\Status::INACTIVE }}" {{ old('status') == App\Enums\Status::INACTIVE ? 'selected' : '' }}>{{ ___('common.inactive') }}</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 11. DYNAMIC HOMEWORK TITLE --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-12 mb-3">
                                    <label for="homework_title" class="form-label ">Homework Title <span class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('title') is-invalid @enderror" name="title" id="homework_title" value="{{ old('title') }}" placeholder="Auto-generated title will appear here..." required>
                                    <small class="text-muted">The system generates this name automatically, but you can edit it if needed.</small>
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 12. DYNAMIC QUIZ INFO BANNER (Hidden by default) --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-12 mb-3 d-none" id="csv_template_hint">
                                    <div class="alert alert-info d-flex align-items-center justify-content-between">
                                        <span><i class="fa-solid fa-circle-info"></i> <strong>Quiz Mode Active:</strong> Please upload your questions using the Browse button above. Only CSV files matching the template are accepted.</span>
                                        <a href="{{ asset('Quiz_Template.csv') }}" class="btn btn-sm btn-primary" download="Quiz_Template.csv">
                                            <i class="fa-solid fa-download"></i> Download CSV Template
                                        </a>
                                    </div>
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 13. QUESTION LIST TABLE (For manual question selection) --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-12 mb-3 d-none" id="question_list_div">
                                    <h5>{{ ___('online-examination.Question list') }}</h5>
                                    <div class="table-responsive">
                                        <input type="hidden" id="page" value="create">
                                        <table class="table table-bordered role-table" id="types_table">
                                            <thead class="thead">
                                                <tr>
                                                    <th class="purchase mr-4">{{ ___('common.Select') }} </th>
                                                    <th class="purchase">{{ ___('online-examination.Question') }}</th>
                                                    <th class="purchase">{{ ___('online-examination.Mark') }}</th>
                                                    <th class="purchase">{{ ___('online-examination.Type') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- AJAX populated rows will go here --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                {{-- ========================================================== --}}
                                {{-- 14. DESCRIPTION AREA --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-12 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('account.description') }}</label>
                                    <textarea class="form-control ot-textarea @error('description') is-invalid @enderror" name="description" placeholder="{{ ___('account.enter_description') }}">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ========================================================== --}}
                                {{-- 15. SUBMIT BUTTON --}}
                                {{-- ========================================================== --}}
                                <div class="col-md-12 mt-4 mb-4">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-lg ot-btn-primary">
                                            <span><i class="fa-solid fa-save"></i> </span>{{ ___('common.submit') }}
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
    $(document).ready(function() {
        
        // =========================================================================
        // SCRIPT 1: RETAIN AJAX DROPDOWNS ON VALIDATION ERROR
        // This grabs the old values from Laravel if the form submission failed
        // =========================================================================
        var oldClass = "{{ old('class') }}";
        var oldSection = "{{ old('section') }}";
        var oldSubject = "{{ old('subject') }}";

        if (oldClass) {
            // Step A: Give the page 300ms to load, then trigger the Class change
            setTimeout(function() {
                $('#getSections').val(oldClass).trigger('change');
                $('#getSections').niceSelect('update'); // Updates the visual UI
                
                // Step B: Wait for the Sections AJAX to finish (approx 800ms)
                setTimeout(function() {
                    if (oldSection) {
                        $('#getSubjects').val(oldSection).trigger('change');
                        $('#getSubjects').niceSelect('update');
                        
                        // Step C: Wait for the Subjects AJAX to finish (approx 800ms)
                        setTimeout(function() {
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
        // SCRIPT 2: HANDLE TASK TYPE CHANGE (Toggle CSV/Document upload)
        // =========================================================================
        $('body').on('change', '#task_type', function() {
            var selectedTask = $(this).val();

            if (selectedTask === 'quiz') {
                // Show the template hint
                $('#csv_template_hint').removeClass('d-none');
                
                // Change Browse input to 'bulk_questions_file' to pass Quiz rules
                $('#fileBrouse').attr('name', 'bulk_questions_file');
                $('#fileBrouse').attr('accept', '.csv, text/csv, application/csv'); 
                $('#placeholder').attr('placeholder', 'Upload Quiz CSV... *');
                
            } else {
                // Hide template hint
                $('#csv_template_hint').addClass('d-none');
                
                // Change Browse input back to 'document' for standard assignments
                $('#fileBrouse').attr('name', 'document');
                $('#fileBrouse').attr('accept', 'image/*,.pdf,.doc,.docx'); 
                $('#placeholder').attr('placeholder', '{{ ___('common.document') }}');
            }
        });

        // Trigger task type logic immediately on page load in case 'Quiz' is old input
        $('#task_type').trigger('change');

        // =========================================================================
        // SCRIPT 3: AUTO-GENERATE HOMEWORK TITLE
        // =========================================================================
        function generateTitle() {
            let group = $('#question_group option:selected').text().trim();
            let cls = $('#getSections option:selected').text().trim();
            let subj = $('#subject option:selected').text().trim();
            let task = $('#task_type option:selected').text().trim();
            let date = $('input[name="date"]').val();

            // Ignore placeholder texts from the dropdowns
            if(group.includes('Select') || !group) group = '';
            if(cls.includes('Select') || !cls) cls = '';
            if(subj.includes('Select') || !subj) subj = '';
            
            // Combine all non-empty parts with underscores
            let parts = [group, cls, subj, task, date].filter(Boolean);
            if(parts.length > 0) {
                $('#homework_title').val(parts.join('_'));
            }
        }

        // Re-generate title whenever any of these specific inputs change
        $('#question_group, #getSections, #subject, #task_type, input[name="date"]').on('change', generateTitle);
    });
</script>
@endpush