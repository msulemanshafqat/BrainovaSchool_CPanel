@extends('student-panel.partials.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
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
                    <li class="breadcrumb-item">{{ $data['title'] }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="table-content table-basic mt-20">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $data['title'] }}</h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered role-table">
                        <thead class="thead">
                            <tr>
                                <th class="serial">{{ ___('common.sr_no') }}</th>
                                <th>{{ ___('academic.subject') }}</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>{{ ___('academic.date') }}</th>
                                <th>{{ ___('academic.submission_date') }}</th>
                                <th>Total Marks</th>
                                <th>Status / Score</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            @forelse ($data['homeworks'] as $key => $row)
                            <tr id="row_{{ $row->id }}">
                                <td class="serial">{{ $loop->iteration }}</td>
                                <td>{{ $row->subject->name }}</td>

                                {{-- Title --}}
                                <td><strong>{{ $row->title ?? '—' }}</strong></td>

                                {{-- Description --}}
                                <td>
                                    @if ($row->description)
                                        <span title="{{ $row->description }}">
                                            {{ \Str::limit($row->description, 60) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- Task type badge --}}
                                <td>
                                    @if ($row->task_type === 'quiz')
                                        <span class="badge bg-info text-white">Quiz</span>
                                    @elseif ($row->task_type)
                                        <span class="badge bg-secondary text-white">{{ ucfirst($row->task_type) }}</span>
                                    @endif
                                </td>

                                <td>{{ $row->date }}</td>
                                <td>{{ $row->submission_date ?? '—' }}</td>

                                {{-- Total marks set by teacher --}}
                                <td>{{ $row->marks ?? '—' }}</td>

                                {{-- Status / Score column --}}
                                <td>
                                    @if ($row->task_type === 'quiz')
                                        @if ($row->check_submitted)
                                            <span class="badge-basic-success-text">Completed</span><br>
                                            {{-- Show earned marks to student --}}
                                            @if ($row->check_submitted->marks !== null)
                                                <strong class="text-success">
                                                    Score: {{ $row->check_submitted->marks }} / {{ $row->marks }}
                                                </strong>
                                            @endif
                                        @else
                                            <span class="badge-basic-danger-text">Not Submitted Yet</span>
                                        @endif
                                    @else
                                        {{-- Standard homework --}}
                                        @if ($row->check_submitted)
                                            <span class="badge-basic-success-text">{{ ___('online-examination.Submitted') }}</span>

                                            {{-- Show document link only for non-quiz submissions --}}
                                            @if ($row->check_submitted->homeworkUpload)
                                                <a class="btn btn-sm ot-btn-primary radius_30px ms-1"
                                                   href="{{ globalAsset($row->check_submitted->homeworkUpload->path, '') }}"
                                                   target="_blank">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            @endif

                                            {{-- Show evaluated marks if teacher has graded --}}
                                            @if ($row->check_submitted->marks)
                                                <br><strong class="text-success">
                                                    Marks: {{ $row->check_submitted->marks }} / {{ $row->marks }}
                                                </strong>
                                            @endif
                                        @else
                                            <span class="badge-basic-danger-text">Not Submitted Yet</span>
                                        @endif
                                    @endif
                                </td>

                                {{-- Action column --}}
                                <td class="action">
                                    @if ($row->task_type === 'quiz')

                                        @if ($row->check_submitted)
                                            {{-- Already submitted: review only, no reattempt --}}
                                            <a href="{{ route('student-panel-homework.take-quiz', $row->id) }}"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fa-solid fa-rotate-left"></i> Review Quiz
                                            </a>
                                        @else
                                            <a href="{{ route('student-panel-homework.take-quiz', $row->id) }}"
                                               class="btn btn-sm ot-btn-primary">
                                                <i class="fa-solid fa-play"></i> Take Quiz
                                            </a>
                                        @endif

                                    @else

                                        {{-- Standard homework: show attachment document (teacher uploaded) if any --}}
                                        @if ($row->upload)
                                            <a class="btn btn-sm ot-btn-primary radius_30px mb-1"
                                               href="{{ globalAsset($row->upload->path, '') }}"
                                               target="_blank">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>
                                        @endif

                                        {{-- Submit button if not yet submitted --}}
                                        @if (!$row->check_submitted)
                                            <button class="btn btn-sm ot-btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalSubmitHomework"
                                                    onclick="openHomeworkModal({{ $row->id }})">
                                                <i class="fa-solid fa-upload"></i> Submit
                                            </button>
                                        @endif

                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%" class="text-center gray-color">
                                    <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                    <p class="mb-0">{{ ___('common.no_data_available') }}</p>
                                    <p class="mb-0 text-secondary font-size-90">
                                        {{ ___('common.please_add_new_entity_regarding_this_table') }}
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="ot-pagination pagination-content d-flex justify-content-end align-content-center py-3">
                    <nav>
                        <ul class="pagination justify-content-between">
                            {!! $data['homeworks']->appends(\Request::capture()->except('page'))->links() !!}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- STANDARD HOMEWORK FILE SUBMISSION MODAL --}}
<div class="modal fade" id="modalSubmitHomework" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-header-image">
                <h5 class="modal-title">{{ ___('common.Homework') }}</h5>
                <button type="button"
                        class="m-0 btn-close d-flex justify-content-center align-items-center"
                        data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-white"></i>
                </button>
            </div>
            <form action="{{ url('student/panel/homework/submit') }}"
                  enctype="multipart/form-data"
                  method="post"
                  id="homework-submit-form">
                @csrf
                <input type="hidden" name="homework_id" id="homework_id">
                <div class="modal-body p-5">
                    <div class="col-md-12">
                        <label class="form-label">
                            {{ ___('common.homework') }} <span class="fillable">*</span>
                        </label>
                        <div class="ot_fileUploader left-side mb-1">
                            <input class="form-control" type="text"
                                   placeholder="{{ ___('common.image') }}"
                                   readonly id="hw_file_placeholder">
                            <button class="primary-btn-small-input" type="button">
                                <label class="btn btn-lg ot-btn-primary" for="hw_fileBrouse">
                                    {{ ___('common.browse') }}
                                </label>
                                <input type="file"
                                       class="d-none form-control"
                                       name="homework"
                                       id="hw_fileBrouse"
                                       accept="image/*,.pdf,.doc,.docx">
                            </button>
                        </div>
                        <span id="homework_error" class="text-danger"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary py-2 px-4"
                            data-bs-dismiss="modal">{{ ___('ui_element.cancel') }}</button>
                    <button type="button"
                            class="btn ot-btn-primary"
                            id="hw_confirm_btn"
                            onclick="homeworkSubmit(event)">
                        {{ ___('ui_element.confirm') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    function openHomeworkModal(homeworkId) {
        $('#homework_id').val(homeworkId);
        $('#hw_file_placeholder').val('');
        $('#hw_fileBrouse').val('');
        $('#homework_error').text('');
    }

    // Show filename in placeholder when file is selected
    $('#hw_fileBrouse').on('change', function () {
        $('#hw_file_placeholder').val($(this).val().split('\\').pop());
    });

    function homeworkSubmit(event) {
        event.preventDefault();

        var file = $('#hw_fileBrouse')[0].files[0];
        if (!file) {
            $('#homework_error').text('Please select a file before submitting.');
            return;
        }

        var submitBtn = $('#hw_confirm_btn');
        var form      = $('#homework-submit-form');
        var formData  = new FormData(form[0]);

        submitBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Uploading...').prop('disabled', true);
        $('#homework_error').text('');

        $.ajax({
            url:         form.attr('action'),
            type:        'POST',
            data:        formData,
            processData: false,
            contentType: false,
            headers:     { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function () {
                window.location.reload();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                $('#homework_error').text('Upload failed. Please check the file and try again.');
                submitBtn.html("{{ ___('ui_element.confirm') }}").prop('disabled', false);
            }
        });
    }
</script>
@endpush
