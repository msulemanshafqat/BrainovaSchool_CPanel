{{-- edited by nazish --}}
{{--
    Homework Evaluation Partial
    Loaded via AJAX into the evaluation modal on the homework index page.

    $data['homework']  → Homework model
    $data['students']  → SessionClassStudent collection with homeworkStudent relation

    Fixes applied vs original:
      - REMOVED: count($data['homework']->examQuestions) — this relation belongs
        to the online-exam module, not homework. It crashes for all homework records.
      - FIXED:   globalAsset null crash when homeworkUpload is null (quiz submissions)
      - FIXED:   "Not Submitted Yest" typo → "Not Submitted Yet"
      - ADDED:   Total marks display so teacher knows the grading ceiling
      - ADDED:   Feedback textarea (stored in homework_students.feedback)
      - ADDED:   Export CSV and Quiz Analytics buttons
--}}

{{-- Header: homework title + total marks + action buttons --}}
<div class="d-flex align-items-start justify-content-between mb-4 pb-3 border-bottom flex-wrap gap-2">
    <div>
        <h6 class="mb-1 text-muted text-uppercase fw-bold" style="font-size:11px;letter-spacing:.08em">
            {{ ucfirst($data['homework']->task_type ?? 'homework') }}
        </h6>
        <h5 class="mb-0 fw-bold">{{ $data['homework']->title ?? 'Evaluation' }}</h5>
        <small class="text-muted">
            {{ $data['homework']->subject->name ?? '' }}
            @if($data['homework']->submission_date)
             — Due: {{ $data['homework']->submission_date }}
            @endif
        </small>
    </div>
    <div class="d-flex align-items-center gap-3 flex-wrap">
        {{-- Total marks — visible so teacher knows the ceiling while grading --}}
        <div class="text-end">
            <div class="text-muted" style="font-size:11px">Total Marks</div>
            <div class="fw-bold fs-4 text-primary">{{ $data['homework']->marks ?? '—' }}</div>
        </div>
        {{-- Export results as CSV --}}
        <a href="{{ route('homework.export-results', $data['homework']->id) }}"
           class="btn btn-sm btn-outline-secondary" title="Download results as CSV">
            <i class="fa-solid fa-download me-1"></i>Export CSV
        </a>
        {{-- Quiz analytics (quiz only) --}}
        @if($data['homework']->task_type === 'quiz')
        <a href="{{ route('homework.quiz-analytics', $data['homework']->id) }}"
           class="btn btn-sm btn-outline-info" target="_blank">
            <i class="fa-solid fa-chart-bar me-1"></i>Analytics
        </a>
        @endif
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered role-table"
           style="border-collapse:collapse;border:2px solid #c7d2e0">
        <thead class="thead" style="background:#f0f4fa">
            <tr>
                <th style="border:1px solid #b8c5d6;padding:10px 12px;font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:700">{{ ___('academic.admission_no') }}</th>
                <th style="border:1px solid #b8c5d6;padding:10px 12px;font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:700">{{ ___('academic.student_name') }}</th>
                <th style="border:1px solid #b8c5d6;padding:10px 12px;font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:700;white-space:nowrap">{{ ___('academic.roll_no') }}</th>
                <th style="border:1px solid #b8c5d6;padding:10px 12px;font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:700">Submission</th>
                <th style="border:1px solid #b8c5d6;padding:10px 12px;font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:700;white-space:nowrap">
                    Marks <small class="fw-normal text-muted">/ {{ $data['homework']->marks ?? '?' }}</small>
                </th>
                <th style="border:1px solid #b8c5d6;padding:10px 12px;font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:700">
                    Feedback <small class="fw-normal text-muted">(optional)</small>
                </th>
            </tr>
        </thead>
        <tbody class="tbody">
            @forelse($data['students'] as $row)
            <tr style="background:{{ $loop->even ? '#fafbff' : '#fff' }}">
                <td style="border:1px solid #e2e8f0;padding:10px 12px;font-size:13px;vertical-align:middle">{{ @$row->student->admission_no }}</td>
                <td style="border:1px solid #e2e8f0;padding:10px 12px;font-size:13px;font-weight:600;vertical-align:middle">{{ @$row->student->first_name }} {{ @$row->student->last_name }}</td>
                <td style="border:1px solid #e2e8f0;padding:10px 12px;font-size:13px;vertical-align:middle;text-align:center">{{ @$row->roll }}</td>

                <td style="border:1px solid #e2e8f0;padding:10px 12px;font-size:13px;vertical-align:middle">
                    @if($row->homeworkStudent)
                        <div class="small text-muted mb-1">
                            <i class="fa-solid fa-calendar-check me-1"></i>
                            Submitted: {{ $row->homeworkStudent->date ?? '—' }}
                        </div>

                        @if($data['homework']->task_type === 'quiz')
                            {{-- Quiz: auto-graded on submission, no file to view --}}
                            <span class="badge bg-info text-white">
                                <i class="fa-solid fa-robot me-1"></i>Auto-graded
                            </span>

                        @elseif($row->homeworkStudent->homeworkUpload)
                            {{--
                                Use url() directly — globalAsset() resolves relative paths
                                incorrectly on this server config, causing 404s.
                            --}}
                            <a class="btn btn-sm ot-btn-primary radius_30px"
                               href="{{ url($row->homeworkStudent->homeworkUpload->path) }}"
                               target="_blank">
                                <i class="fa-solid fa-eye me-1"></i>View Work
                            </a>
                        @else
                            <span class="badge bg-warning text-dark">No file uploaded</span>
                        @endif
                    @else
                        {{-- Fixed typo: "Yest" → "Yet" --}}
                        <span class="badge-basic-danger-text">
                            <i class="fa-solid fa-clock me-1"></i>Not Submitted Yet
                        </span>
                    @endif
                </td>

                <td style="border:1px solid #e2e8f0;padding:10px 12px;vertical-align:middle">
                    @if($row->homeworkStudent)
                        <div class="d-flex align-items-center gap-2">
                            <input type="number"
                                   class="form-control ot-input"
                                   style="max-width:85px"
                                   step="0.5" min="0"
                                   max="{{ $data['homework']->marks ?? 9999 }}"
                                   name="marks[]"
                                   value="{{ $row->homeworkStudent->marks ?? '' }}"
                                   placeholder="0" />
                            <input type="hidden" name="students[]" value="{{ $row->student_id }}" />
                            <small class="text-muted">/ {{ $data['homework']->marks ?? '?' }}</small>
                        </div>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>

                <td style="border:1px solid #e2e8f0;padding:10px 12px;vertical-align:middle">
                    @if($row->homeworkStudent)
                        <textarea name="feedback[]"
                                  class="form-control"
                                  rows="2"
                                  style="min-width:160px;font-size:12.5px"
                                  placeholder="Optional feedback...">{{ $row->homeworkStudent->feedback ?? '' }}</textarea>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center gray-color py-4"
                    style="border:1px solid #e2e8f0">
                    <img src="{{ asset('images/no_data.svg') }}" alt="" width="80"
                         class="mb-2 d-block mx-auto">
                    <p class="mb-0">{{ ___('common.no_data_available') }}</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
