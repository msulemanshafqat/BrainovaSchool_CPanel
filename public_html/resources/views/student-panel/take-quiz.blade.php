@extends('student-panel.partials.master')
@section('title')
    Take Quiz: {{ $data['title'] }}
@endsection

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
            </div>
            <div class="col-sm-6 text-end">
                <h4 id="quiz-timer" class="text-danger fw-bold"><i class="fa-solid fa-clock"></i> 00:00</h4>
            </div>
        </div>
    </div>

    <div class="card ot-card mt-4" id="quiz-container">
        <div class="card-body p-5">
            
            {{-- Progress Bar --}}
            <div class="progress mb-4" style="height: 10px;">
                <div class="progress-bar bg-success" id="quiz-progress" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <h5 class="text-muted mb-4">Question <span id="current-q-num">1</span> of <span id="total-q-num">X</span></h5>
            
            {{-- Question Text --}}
            <h3 class="mb-4 text-dark" id="question-text">Loading Question...</h3>

            {{-- Options --}}
            <div class="options-container mb-4">
                <div class="form-check mb-3 custom-radio-box p-3 border rounded">
                    <input class="form-check-input" type="radio" name="quiz_option" id="opt_a" value="A">
                    <label class="form-check-label w-100 fs-5" for="opt_a" id="label_a">Option A</label>
                </div>
                <div class="form-check mb-3 custom-radio-box p-3 border rounded">
                    <input class="form-check-input" type="radio" name="quiz_option" id="opt_b" value="B">
                    <label class="form-check-label w-100 fs-5" for="opt_b" id="label_b">Option B</label>
                </div>
                <div class="form-check mb-3 custom-radio-box p-3 border rounded">
                    <input class="form-check-input" type="radio" name="quiz_option" id="opt_c" value="C">
                    <label class="form-check-label w-100 fs-5" for="opt_c" id="label_c">Option C</label>
                </div>
                <div class="form-check mb-3 custom-radio-box p-3 border rounded">
                    <input class="form-check-input" type="radio" name="quiz_option" id="opt_d" value="D">
                    <label class="form-check-label w-100 fs-5" for="opt_d" id="label_d">Option D</label>
                </div>
            </div>

            {{-- Hint Section --}}
            <div class="alert alert-warning mb-4 d-none" id="hint-box">
                <strong><i class="fa-solid fa-lightbulb"></i> Hint:</strong> <span id="hint-text"></span>
                <div class="mt-2 text-danger small"><em>Note: Using this hint will deduct 50% of the marks for this question.</em></div>
            </div>

            {{-- Controls --}}
            <div class="d-flex justify-content-between align-items-center mt-5">
                <button class="btn btn-outline-secondary btn-lg" id="btn-prev" onclick="changeQuestion(-1)" disabled><i class="fa-solid fa-arrow-left"></i> Previous</button>
                
                <button class="btn btn-warning" id="btn-hint" onclick="showHint()"><i class="fa-solid fa-lightbulb"></i> Show Hint (-50% marks)</button>
                
                <button class="btn btn-primary btn-lg" id="btn-next" onclick="changeQuestion(1)">Next <i class="fa-solid fa-arrow-right"></i></button>
                <button class="btn btn-success btn-lg d-none" id="btn-submit" onclick="submitQuiz()"><i class="fa-solid fa-check-double"></i> Submit Quiz</button>
            </div>

        </div>
    </div>

    {{-- RESULTS SCREEN (Hidden until submission) --}}
    <div class="card ot-card mt-4 d-none" id="results-container">
        <div class="card-body p-5 text-center">
            <h1 class="display-4 text-success mb-3"><i class="fa-solid fa-trophy"></i> Quiz Complete!</h1>
            <h3 class="mb-4">Your Score: <span id="final-score" class="fw-bold text-primary">0</span> / <span id="max-score">0</span></h3>
            <p class="text-muted">Below is a review of your answers.</p>
            <hr>
            <div id="review-section" class="text-start mt-5">
                {{-- JS will populate this with Red/Green review --}}
            </div>
            <a href="{{ route('student-panel-homeworks.index') }}" class="btn btn-primary btn-lg mt-4">Return to Dashboard</a>
        </div>
    </div>

</div>

<style>
    .custom-radio-box { cursor: pointer; transition: all 0.2s; }
    .custom-radio-box:hover { background-color: #f8f9fa; border-color: #007bff !important; }
    input[type=radio] { cursor: pointer; transform: scale(1.2); margin-right: 10px; }
    .review-card { border-left: 5px solid #ccc; background: #fafafa; }
    .review-correct { border-left-color: #28a745; background: #eafbee; }
    .review-incorrect { border-left-color: #dc3545; background: #fdf2f2; }
</style>

@endsection

@push('script')
<script>
    // Load Data from Laravel Controller
    const questions = @json($data['questions']);
    const homeworkId = {{ $data['homework']->id }};
    
    // Quiz State
    let currentIndex = 0;
    let userAnswers = {}; // Format: { question_id: 'A' }
    let hintsUsed = {};   // Format: { question_id: true }
    
    // Timer setup (e.g., 20 mins)
    let timeRemaining = questions.length * 60; // 1 minute per question default
    let timerInterval;

    $(document).ready(function() {
        $('#total-q-num').text(questions.length);
        startTimer();
        loadQuestion(0);

        // Make whole div click the radio button
        $('.custom-radio-box').click(function() {
            $(this).find('input[type="radio"]').prop('checked', true);
            // Save answer instantly
            userAnswers[questions[currentIndex].id] = $(this).find('input[type="radio"]').val();
        });
    });

    function startTimer() {
        timerInterval = setInterval(function() {
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                submitQuiz(); // Auto submit when time runs out
            } else {
                timeRemaining--;
                let minutes = Math.floor(timeRemaining / 60);
                let seconds = timeRemaining % 60;
                $('#quiz-timer').html(`<i class="fa-solid fa-clock"></i> ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
            }
        }, 1000);
    }

    function loadQuestion(index) {
        if(index < 0 || index >= questions.length) return;
        
        let q = questions[index];
        $('#current-q-num').text(index + 1);
        $('#question-text').text(q.question);
        $('#label_a').text(q.option_a);
        $('#label_b').text(q.option_b);
        $('#label_c').text(q.option_c);
        $('#label_d').text(q.option_d);

        // Reset UI
        $('input[name="quiz_option"]').prop('checked', false);
        $('#hint-box').addClass('d-none');
        
        // Restore previous answer if they clicked "Previous"
        if(userAnswers[q.id]) {
            $(`input[value="${userAnswers[q.id]}"]`).prop('checked', true);
        }

        // Show/Hide Hint Button based on if they already used it or if a hint exists
        if(q.hint && q.hint.trim() !== '' && q.hint !== '-') {
            $('#btn-hint').removeClass('d-none');
            if(hintsUsed[q.id]) {
                $('#hint-text').text(q.hint);
                $('#hint-box').removeClass('d-none');
                $('#btn-hint').addClass('d-none'); // Hide button if already clicked
            }
        } else {
            $('#btn-hint').addClass('d-none'); // No hint available
        }

        // Progress Bar
        let progress = ((index) / questions.length) * 100;
        $('#quiz-progress').css('width', progress + '%');

        // Buttons
        $('#btn-prev').prop('disabled', index === 0);
        
        if (index === questions.length - 1) {
            $('#btn-next').addClass('d-none');
            $('#btn-submit').removeClass('d-none');
        } else {
            $('#btn-next').removeClass('d-none');
            $('#btn-submit').addClass('d-none');
        }
    }

    function changeQuestion(step) {
        currentIndex += step;
        loadQuestion(currentIndex);
    }

    function showHint() {
        let q = questions[currentIndex];
        hintsUsed[q.id] = true;
        $('#hint-text').text(q.hint);
        $('#hint-box').removeClass('d-none');
        $('#btn-hint').addClass('d-none');
    }

    function submitQuiz() {
        clearInterval(timerInterval); // Stop Timer
        
        // Change the button so they know it's saving
        $('#btn-submit').html('<i class="fa-solid fa-spinner fa-spin"></i> Grading...').prop('disabled', true);

        // Grading Logic
        let score = 0;
        let htmlReview = '';

        questions.forEach((q, i) => {
            let userAnswer = userAnswers[q.id] || 'None';
            let isCorrect = (userAnswer.trim().toUpperCase() === q.correct_answer.trim().toUpperCase());
            
            let pointsEarned = 0;
            if (isCorrect) {
                pointsEarned = 1; 
                if(hintsUsed[q.id]) pointsEarned = 0.5; // Penalty!
            }
            score += pointsEarned;

            let statusClass = isCorrect ? 'review-correct' : 'review-incorrect';
            let icon = isCorrect ? '<i class="fa-solid fa-check-circle text-success"></i>' : '<i class="fa-solid fa-times-circle text-danger"></i>';

            htmlReview += `
                <div class="card p-4 mb-3 review-card ${statusClass}">
                    <h5>${i+1}. ${q.question}</h5>
                    <p class="mb-1"><strong>Your Answer:</strong> ${userAnswer}</p>
                    <p class="mb-1"><strong>Correct Answer:</strong> ${q.correct_answer} ${icon}</p>
                    ${hintsUsed[q.id] ? '<p class="text-warning small mb-0"><i class="fa-solid fa-triangle-exclamation"></i> Hint Penalty Applied (-50%)</p>' : ''}
                    ${q.explanation && q.explanation !== '-' ? `<hr><p class="text-info mb-0"><strong>Explanation:</strong> ${q.explanation}</p>` : ''}
                </div>
            `;
        });

        // ==========================================
        // PHASE 3: Send the data to the backend!
        // ==========================================
        let payload = {
            _token: '{{ csrf_token() }}',
            homework_id: homeworkId,
            score: score,
            answers: userAnswers,
            hints: hintsUsed
        };

        $.post("{{ route('student-panel-homework.submit-interactive-quiz') }}", payload, function(response) {
            // SUCCESS! Hide the quiz and show the results!
            $('#quiz-container').addClass('d-none');
            $('#results-container').removeClass('d-none');
            
            $('#final-score').text(score);
            $('#max-score').text(questions.length);
            $('#review-section').html(htmlReview);
            
}).fail(function(xhr) {
            // Print the exact Laravel error to the console and screen!
            console.log(xhr.responseText);
            
            let errorMessage = "Unknown Server Error";
            if(xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else {
                errorMessage = xhr.responseText; 
            }

            alert("LARAVEL ERROR: " + errorMessage);
            $('#btn-submit').html('<i class="fa-solid fa-check-double"></i> Submit Quiz').prop('disabled', false);
        });
    }
</script>
@endpush