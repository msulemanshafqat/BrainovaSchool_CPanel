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
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                            <li class="breadcrumb-item">{{ $data['title'] }}</li>
                        </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}


        <div class="col-12">
            <form action="" >
                <div class="card ot-card mb-24 position-relative z_1">
                    <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                        <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                        <div
                            class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">
                            <!-- table_searchBox -->
                            <div class="single_large_selectBox">
                                <select id="getSections"
                                        class="class nice-select niceSelect bordered_style wide @error('class') is-invalid @enderror"
                                        name="class">
                                    <option value="">{{ ___('student_info.select_class') }}</option>
                                    @foreach ($data['classes'] as $item)
                                        <option
                                            {{ old('class', @$data['request']->class) == $item->class->id ? 'selected' : '' }}
                                            value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="single_large_selectBox">
                                <select
                                    class="sections section nice-select niceSelect bordered_style wide @error('section') is-invalid @enderror"
                                    name="section">
                                    <option value="">{{ ___('student_info.select_section') }}</option>
                                    @foreach ($data['sections'] as $item)
                                        <option
                                            {{ old('section', @$data['request']->section) == $item->section->id ? 'selected' : '' }}
                                            value="{{ $item->section->id }}">{{ $item->section->name }}</option>
                                    @endforeach
                                </select>
                                @error('section')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="single_large_selectBox">
                                <input class="form-control ot-input" name="keyword" list="datalistOptions"
                                       id="exampleDataList" placeholder="{{ ___('student_info.enter_keyword') }}"
                                       value="{{ old('keyword', @$data['request']->keyword) }}">
                            </div>

                            <button class="btn btn-lg ot-btn-primary" type="submit">
                                {{ ___('common.Search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <!--  table content start -->
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
                                    <th class="purchase">{{ ___('student_info.admission_no') }}</th>
                                    <th class="purchase">{{ ___('student_info.class') }}</th>
                                    <th class="purchase">{{ ___('student_info.student_name') }}</th>
                                    <th class="purchase">{{ ___('student_info.impression') }}</th>
                                    <th class="purchase">{{ ___('incidents.incidents') }}</th>
                                    <th class="purchase">{{ ___('incidents.last incidents') }}</th>
                                    @if (hasPermission('student_update') || hasPermission('student_delete'))
                                        <th class="action">{{ ___('common.action') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody class="tbody">
                                {{-- @dd($data['students']) --}}
                                @foreach($data['students'] as $key => $student)
                                    @php
                                        $netPoints = $student->behaviourRecord->sum(function ($record) {
                                            if (!$record->incident) return 0;

                                            return $record->incident->type === 'positive'
                                                ? $record->incident->points
                                                : -$record->incident->points;
                                        });

                                        $impression = $netPoints >= 0
                                            ? ___('behaviour_record.Positive')
                                            : ___('behaviour_record.Negative');
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $student->admission_no }}</td>
                                        <td>{{ $student->session_class_student->class->name . " (" .$student->session_class_student->section->name.")" }}</td>
                                        <td>
                                            <div class="">
                                                <a href="{{ route('student.show', $student->id) }}" target="_blank">
                                                    <div class="user-card">
                                                        <div class="user-avatar">
                                                            <img src="{{ @globalAsset(@$student->user->upload->path, '40X40.webp') }}"
                                                                 alt="{{ @$student->first_name }}">
                                                        </div>
                                                        <div class="user-info">
                                                            {{ @$student->first_name }}
                                                            {{ @$student->last_name }}
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $netPoints >= 0 ? 'badge-basic-success-text' : 'badge-basic-danger-text' }}">
                                                {{ $impression . ' ('.$netPoints.')'}}
                                            </span>

                                        </td>
                                        <td>
                                            <ul class="mb-0 ps-3">
                                                @foreach($student->behaviourRecord as $record)
                                                    @if ($record->incident)
                                                        <li>
                                                            <span class="badge {{ $record->incident->type === 'positive' ? 'badge-basic-success-text' : 'badge-basic-danger-text' }}">
                                                                {{ $record->incident->title }} ({{ $record->incident->type === 'positive' ? '+' : '-' }}{{ $record->incident->points }})
                                                            </span>
                                                        </li>
                                                    @endif
                                                @endforeach

                                            </ul>
                                        </td>
                                        <td>
                                            @if($student->behaviourRecord->last()?->incident)
                                                {{ dateFormat($student->behaviourRecord->last()->incident->created_at) }}
                                            @else
                                                <em>-</em>
                                            @endif
                                        </td>

                                        @if (hasPermission('student_update') || hasPermission('student_delete'))
                                            <td class="action">
                                                <button type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#shareModal"
                                                        data-id="{{ $student->id }}" {{-- or incident id, etc. --}}
                                                        class="btn btn-sm ot-btn-primary openShareModal">
                                                    <span><i class="fa-solid fa-plus"></i></span>
                                                    <span>{{ ___('common.add') }}</span>
                                                </button>
                                            </td>

                                        @endif
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                        <!--  table end -->
                        <!--  pagination start -->

                        <div class="ot-pagination pagination-content d-flex justify-content-end align-content-center py-3">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-between">
                                    {!! $data['students']->appends(\Request::capture()->except('page'))->links() !!}
                                </ul>
                            </nav>
                        </div>

                        <!--  pagination end -->
                    </div>

            </div>
        </div>
        <!--  table content end -->

    </div>


    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{___('behaviour_record.Student Incident Log')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <!-- Student Info -->
                    <div class="mb-4">
                        <h6 class="mb-3">{{___('behaviour_record.Student Information')}}</h6>
                        <div class="row">
                            <div class="col-md-3"><strong>{{___('common.Name')}}:</strong> <span data-field="name"></span></div>
                            <div class="col-md-3"><strong>{{___('common.Role')}}:</strong> <span data-field="role"></span></div>
                            <div class="col-md-3"><strong>{{___('common.Section')}}:</strong> <span data-field="section"></span></div>
                            <div class="col-md-3"><strong>{{___('common.Class')}}:</strong> <span data-field="class"></span></div>
                        </div>

                    </div>

                    <form id="addIncidentForm" action="{{ route('incidents.record.store') }}" method="POST" class="row g-2 align-items-end mt-3">
                        @csrf

                        <!-- Incident Select -->
                        <div class="col-md-4">
                            <label for="validationServer04" class="form-label">
                                {{ ___('behaviour_record.Select Incident') }} <span class="fillable">*</span>
                            </label>
                            <select class="nice-select niceSelect bordered_style wide form-control"
                                    name="incident_id" id="validationServer04"
                                    aria-describedby="validationServer04Feedback">
                                <option value="">{{ ___('behaviour_record.Select Incident') }}</option>
                                @foreach($data['incidents'] as $incident)
                                    @php
                                        $sign = $incident->type === 'positive' ? '+' : '-';
                                    @endphp
                                    <option value="{{ $incident->id }}">
                                        {{ $incident->title . ' (' . $sign . $incident->points . ')' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('incident_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Short Note Input -->
                        <div class="col-md-4">
                            <label for="short_note" class="form-label">{{ ___('behaviour_record.Short Note') }}</label>
                            <input type="text" class="form-control ot-input"
                                   name="short_note" id="short_note" placeholder="{{ ___('behaviour_record.Enter note') }}">
                            @error('short_note')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hidden Student ID -->
                        <input type="hidden" name="student_id" id="student_id">

                        <!-- Submit Button -->
                        <div class="col-auto">
                            <button type="submit" class="btn btn-lg ot-btn-primary d-flex align-items-center gap-1">
                                <i class="fa-solid fa-plus"></i>
                                <span>{{ ___('common.add') }}</span>
                            </button>
                        </div>
                    </form>


                    <!-- Incidents Table -->
                    <h6 class="mb-3 mt-3">{{___('behaviour_record.Behavior Incidents')}}</h6>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered role-table">
                            <thead class="table-light">
                            <tr>
                                <th>{{ ___('common.Sl') }}</th>
                                <th>{{ ___('behaviour_record.Incident') }}</th>
                                <th>{{ ___('behaviour_record.Note') }}</th>
                                <th>{{ ___('behaviour_record.Status') }}</th>
                                <th>{{ ___('behaviour_record.Request by') }}</th>
                                <th>{{ ___('behaviour_record.Approved by') }}</th>
                                <th>{{ ___('behaviour_record.Date') }}</th>
                            </tr>
                            </thead>
                            <tbody id="incident-table-body">
                            <!-- Dynamic rows will be injected here -->
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="modal-footer justify-content-center">
                    <p class="mb-0 text-muted small">{{ ___('common.This is a summary of the student\'s behavior record.') }}</p>
                </div>


            </div>
        </div>
    </div>


@endsection
@push('script')

    <script>
        $(document).on('click', '.openShareModal', function () {
            const studentId = $(this).data('id');
            $('#student_id').val(studentId);

            $.ajax({
                url: '/incidents/record/student/' + studentId, // Adjust URL as needed
                method: 'GET',
                success: function (response) {
                    console.log(response);

                    // Fill modal fields
                    $('#shareModal .modal-body').find('[data-field="name"]').text(response.first_name + ' ' + response.last_name);
                    $('#shareModal .modal-body').find('[data-field="role"]').text(response.roll_no);
                    $('#shareModal .modal-body').find('[data-field="section"]').text(response.session_class_student.section.name);
                    $('#shareModal .modal-body').find('[data-field="class"]').text(response.session_class_student.class.name);


                    let incidentRows = '';
                    response.behaviour_record.forEach((record, index) => {
                        if (record.incident) {
                            const sign = record.incident.type === 'positive' ? '+' : '-';
                            const titleWithPoints = `${record.incident.title} (${sign}${record.incident.points})`;
                            const status = record.status ? record.status.charAt(0).toUpperCase() + record.status.slice(1) : '';
                            const statusClass = getStatusClass(status);
                            const shortNote = record.short_note ?? '';
                            const addedBy = record.request_by.name ?? '';
                            const approvedBy = record.approved_by?.name ?? '';
                            const date = new Date(record.created_at).toLocaleDateString();

                            incidentRows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><span class="${record.incident.type === 'positive' ? 'badge-basic-success-text' : 'badge-basic-danger-text'}">${titleWithPoints}</span></td>
                                <td>${shortNote}</td>
                                <td><span class="${statusClass}">${status}</span></td>
                                <td>${addedBy}</td>
                                <td>${approvedBy}</td>
                                <td>${date}</td>
                            </tr>
                        `;
                                        }
                                    });

// Insert rows into table
                    $('#incident-table-body').html(incidentRows);

                },

                error: function (xhr) {
                    console.error(xhr);
                    toastr.error(xhr.responseText);
                }
            });
        });


        function getStatusClass(status) {
            switch(status) {
                case 'Approved':
                    return 'badge-success';
                case 'Initiate':
                    return 'badge-warning';
                case 'Rejected':
                    return 'badge-danger';
                case 'Withdraw':
                    return 'badge-primary';
                default:
                    return 'badge-secondary';
            }
        }

    </script>

@endpush
