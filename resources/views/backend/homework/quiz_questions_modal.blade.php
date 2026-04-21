{{-- backend/homework/quiz_questions_modal.blade.php --}}
{{-- 
    Renders the modal content for viewing a homework quiz's questions.
    Data source: homework_quiz_questions table (Brainova custom).
    This is COMPLETELY SEPARATE from the online-exam question_banks table.
--}}
<div class="modal-content" id="modalWidth">
    <div class="modal-header modal-header-image">
        <h5 class="modal-title">
            <i class="fa-solid fa-list-check"></i>
            Quiz Questions — {{ $homework->title ?? 'Homework Quiz' }}
        </h5>
        <button type="button"
                class="m-0 btn-close d-flex justify-content-center align-items-center"
                data-bs-dismiss="modal" aria-label="Close">
            <i class="fa fa-times text-white"></i>
        </button>
    </div>
    <div class="modal-body p-4">
        @if ($questions->isEmpty())
            <div class="alert alert-warning">
                No questions found for this quiz. Please check the uploaded CSV.
            </div>
        @else
            <div class="table-responsive">
                <table class="table ot-table-bg table-bordered">
                    <thead class="thead">
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Question</th>
                            <th>Options</th>
                            <th>Correct Answer</th>
                            <th>Hint</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
                        @foreach ($questions as $i => $q)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $q->question }}</strong></td>
                            <td>
                                <ol type="A" class="mb-0 ps-3">
                                    <li>{{ $q->option_a }}</li>
                                    <li>{{ $q->option_b }}</li>
                                    <li>{{ $q->option_c }}</li>
                                    <li>{{ $q->option_d }}</li>
                                </ol>
                            </td>
                            <td>
                                <span class="badge bg-success text-white px-2 py-1">
                                    {{ $q->correct_answer }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $q->hint ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary py-2 px-4"
                data-bs-dismiss="modal">Close</button>
    </div>
</div>
