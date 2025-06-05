@extends('layouts.dash')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Test Portal
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span> Take Test
                        <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Test Question Form -->
        <div class="card">
            <div class="card-body">
                <h5>Question {{ $questionIndex + 1 }} of {{ $test->questions->count() }}</h5>
                <p><strong>{{ $question->question_text }}</strong></p>
                <div id="timer" class="alert alert-warning text-center mt-3"></div>

                <!-- Form for submitting answers -->
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
                        <button type="button" id="next-btn" class="btn btn-primary" disabled>
                            {{ $questionIndex + 1 < $test->questions->count() ? 'Next' : 'Finish' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
    // Timer logic
    const endTime = new Date("{{ $end_time }}").getTime();
    const testId = "{{ $test->id }}";

    function startTimer() {
        const storedTime = sessionStorage.getItem(`remainingTime_${testId}`);
        let remainingTime = storedTime ? parseInt(storedTime) : endTime - new Date().getTime();

        if (remainingTime <= 0) {
            document.getElementById('timer').innerText = 'Time is up!';
            document.getElementById('next-btn').disabled = true;
            submitTest();
            return;
        }

        const timer = setInterval(() => {
            const minutes = Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);

            document.getElementById('timer').innerText = `Time Remaining: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            sessionStorage.setItem(`remainingTime_${testId}`, remainingTime);

            if (remainingTime <= 0) {
                clearInterval(timer);
                document.getElementById('timer').innerText = 'Time is up!';
                document.getElementById('next-btn').disabled = true;
                submitTest();
            }

            remainingTime -= 1000;
        }, 1000);
    }

    function submitTest() {
        const formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('submit', '1');

        fetch("{{ route('student.tests.submit', [$test->id]) }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json().catch(() => ({}))) // Handle non-JSON responses
        .then(data => {
            sessionStorage.removeItem(`remainingTime_${testId}`);
            window.location.href = "{{ route('student.tests.submit', [$test->id]) }}";
        })
        .catch(error => {
            console.error('Error submitting test:', error);
            alert('An error occurred while submitting the test.');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        startTimer();

        // Enable button only when an answer is selected
        const radioButtons = document.querySelectorAll('input[name="answers[{{ $question->id }}]"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', () => {
                document.getElementById('next-btn').disabled = false;
            });
        });

        // Handle form submission
        document.getElementById('next-btn').addEventListener('click', () => {
            const form = document.getElementById('question-form');
            const formData = new FormData(form);
            const btn = document.getElementById('next-btn');
            btn.disabled = true;

            // Client-side validation
            if (!form.querySelector('input[name="answers[{{ $question->id }}]"]:checked')) {
                alert('Please select an answer.');
                btn.disabled = false;
                return;
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.nextUrl;
                } else {
                    alert(data.message || 'An error occurred. Please try again.');
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting your answer.');
                btn.disabled = false;
            });
        });
    });
</script>
    </div>
</div>
</div>
@endsection
