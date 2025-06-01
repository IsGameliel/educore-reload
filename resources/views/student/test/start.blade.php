@extends('layouts.dash')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Course Registration
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> Register for Courses
                        <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Registration Form -->
        <div class="card">
            <div class="card-body">
                <div class="card">
                    <div class="card-body">
                        <h5>Question {{ $questionIndex + 1 }}</h5>
                        <p><strong>{{ $question->question_text }}</strong></p>
                        <div id="timer" class="alert alert-warning text-center mt-3"></div>
                        <!-- Form for submitting answers and moving to the next question -->
                        <form
                            action="{{ route('student.tests.storeAnswer', [$test->id, $questionIndex]) }}"
                            method="POST"
                            id="question-form">
                            @csrf

                            @foreach ($question->options as $key => $option)
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $key }}"
                                        id="option-{{ $question->id }}-{{ $key }}"
                                        required>
                                    <label class="form-check-label" for="option-{{ $question->id }}-{{ $key }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach

                            <div class="mt-3">
                                <button type="button" id="next-btn" class="btn btn-primary">
                                    {{ $questionIndex + 1 < $test->questions->count() ? 'Next' : 'Submit Test' }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

                <script>
                    const endTime = new Date("{{ $end_time }}").getTime();

                    function startTimer() {
                        const storedTime = sessionStorage.getItem('remainingTime');
                        let remainingTime = storedTime ? parseInt(storedTime) : endTime - new Date().getTime();

                        const timer = setInterval(() => {
                            const minutes = Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);

                            document.getElementById('timer').innerText = `Time Remaining: ${minutes}:${seconds}`;
                            sessionStorage.setItem('remainingTime', remainingTime);

                            if (remainingTime <= 0) {
                                clearInterval(timer);
                                document.getElementById('question-form').submit();
                            }

                            remainingTime -= 1000;
                        }, 1000);
                    }

                    document.addEventListener('DOMContentLoaded', () => {
                        document.getElementById('next-btn').addEventListener('click', () => {
                            const form = document.getElementById('question-form');
                            const formData = new FormData(form);

                            // Log the form data before sending
                            for (let [key, value] of formData.entries()) {
                                console.log(key, value);  // This will log the form data
                            }

                            fetch(form.action, {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                },
                                body: formData,
                            })
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.success) {
                                    window.location.href = data.nextUrl;
                                } else {
                                    alert(data.message || "An error occurred. Please try again.");
                                }
                            })
                            .catch((error) => {
                                console.error("Error:", error);
                                alert("An error occurred. Please try again.");
                            });
                        });

                    });


                </script>
            </div>
        </div>
    </div>
</div>
</div>
</div>

@endsection
