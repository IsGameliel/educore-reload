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
                        action="{{ $questionIndex + 1 < $test->questions->count()
                        ? route('student.tests.storeAnswer', [$test->id, $questionIndex])
                        : route('student.tests.submit', $test->id) }}"

                            method="POST"
                            id="question-form">
                            @csrf

                            <!-- Question options -->
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
                                @if ($questionIndex > 0)
                                    <a href="{{ route('student.tests.start', [$test->id, $questionIndex - 1]) }}"
                                    class="btn btn-secondary">
                                        Previous
                                    </a>
                                @endif
                                <button type="submit" class="btn btn-primary">
                                    {{ $questionIndex + 1 < $test->questions->count() ? 'Next' : 'Submit Test' }}
                                </button>
                            </div>
                            </form>


                    </div>
                </div>

                <script>
                    const endTime = new Date("{{ $end_time }}").getTime();

                    const timer = setInterval(() => {
                        const now = new Date().getTime();
                        const remaining = endTime - now;

                        if (remaining <= 0) {
                            clearInterval(timer);
                            document.getElementById('question-form').submit(); // Auto-submit on time out
                        }

                        const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
                        document.getElementById('timer').innerText = `Time Remaining: ${minutes}:${seconds}`;
                    }, 1000);
                </script>
            </div>
        </div>
    </div>
</div>
</div
</div>

@endsection
