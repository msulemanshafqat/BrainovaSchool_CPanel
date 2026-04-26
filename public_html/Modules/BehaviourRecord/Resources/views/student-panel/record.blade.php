@extends('student-panel.partials.master')
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
                                    <th class="purchase">{{ ___('student_info.impression') }}</th>
                                    <th class="purchase">{{ ___('incidents.incidents') }}</th>
                                    <th class="purchase">{{ ___('common.Description') }}</th>
                                    <th class="purchase">{{ ___('incidents.last incidents') }}</th>
                                    @if (true)
                                        <th class="action">{{ ___('common.action') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody class="tbody">
                                @foreach($data['records'] as $key => $record)
                                    @php
                                        $netPoints = $record->student->behaviourRecord->sum(function ($record) {
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
                                        <td>
                                            <span class="badge {{ $netPoints >= 0 ? 'badge-basic-success-text' : 'badge-basic-danger-text' }}">
                                                {{ $impression . ' ('.$netPoints.')'}}
                                            </span>

                                        </td>
                                        <td>
                                            <ul class="mb-0 ps-3">
                                                    @if ($record->incident)
                                                        <li>
                                                            <span class="badge {{ $record->incident->type === 'positive' ? 'badge-basic-success-text' : 'badge-basic-danger-text' }}">
                                                                {{ $record->incident->title }} ({{ $record->incident->type === 'positive' ? '+' : '-' }}{{ $record->incident->points }})
                                                            </span>
                                                        </li>
                                                    @endif

                                            </ul>
                                        </td>
                                        <td>{{$record->short_note}}</td>
                                        <td>
                                            @if($record->student->behaviourRecord->last()?->incident)
                                                {{ dateFormat($record->student->behaviourRecord->last()->incident->created_at) }}
                                            @else
                                                <em>-</em>
                                            @endif
                                        </td>

                                        @if (true)
                                            <td class="action">
                                                <div class="dropdown dropdown-action">
                                                    <button type="button" class="btn-dropdown"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa-solid fa-ellipsis"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <!-- If no appeal exists, show Appeal button -->
                                                        @if (!$record->appeal)
                                                            <li>
                                                                <button type="button" class="dropdown-item"
                                                                        onclick="openAppealModal({{ $record->student->id }}, {{ $record->id }})">
                                                                    <span class="icon mr-8"><i class="fa-solid fa-rotate-left"></i></span>
                                                                    {{ ___('common.Appeal') }}
                                                                </button>
                                                            </li>
                                                        @endif

                                                        <!-- If appeal exists -->
                                                        @if ($record->appeal)
                                                            <!-- View Appeal button -->
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('student.appeal.details', $record->appeal->id) }}">
                                                                    <span class="icon mr-8"><i class="fa-solid fa-eye text-primary"></i></span>
                                                                    <span>{{ ___('common.View appeal') }}</span>
                                                                </a>
                                                            </li>

                                                            <!-- Withdraw button only if status is NOT withdraw or granted -->
                                                            @if (!in_array($record->appeal->status, ['withdraw', 'granted']))
                                                                <li>
                                                                    <a class="dropdown-item" href="{{route('student.appeal.withdraw', $record->appeal->id)}}">
                                                                        <span class="icon mr-8"><i class="fa-solid fa-check text-success"></i></span>
                                                                        <span>{{ ___('common.Withdraw') }}</span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endif
                                                    </ul>

                                                </div>
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
                                    {!! $data['records']->appends(\Request::capture()->except('page'))->links() !!}
                                </ul>
                            </nav>
                        </div>

                        <!--  pagination end -->
                    </div>

            </div>
        </div>
        <!--  table content end -->

    </div>


    <div class="modal fade" id="appealModal" tabindex="-1" aria-labelledby="appealModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="appealModalLabel">{{ ___('behaviour_record.Appeal Request') }}</h5>
                </div>

                <form id="appealForm" method="POST" action="{{route('student.behaviourrecord.appeal.store')}}">
                    @csrf
                    <div class="modal-body">

                        <!-- Hidden Fields -->
                        <input type="hidden" name="student_id" id="student_id">
                        <input type="hidden" name="record_id" id="assign_id">

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ ___('common.Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control ot-input" id="title" name="title" placeholder="Write an title" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">{{ ___('common.Description') }}</label>
                            <textarea class="form-control ot-input" id="description" name="description" placeholder="Write an description" rows="4"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-lg ot-btn-primary" type="submit"><span><i class="fa-solid fa-save"></i>
                         </span>{{___('common.Submit')}}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


@endsection
@push('script')

    <script>

        function openAppealModal(studentId, assignId) {
            document.getElementById('student_id').value = studentId;
            document.getElementById('assign_id').value = assignId;
            var myModal = new bootstrap.Modal(document.getElementById('appealModal'));
            myModal.show();
        }


    </script>

@endpush
