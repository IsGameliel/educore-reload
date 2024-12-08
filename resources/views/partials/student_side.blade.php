<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ Auth::user()->profile_photo_url }}" alt="profile" />
                    <span class="login-status online"></span>
                    <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
                    <span class="text-secondary text-small">{{ Auth::user()->usertype }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('home') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-title">Academics</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-school menu-icon"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('student/courses/registration') }}">Course Registeration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('student/courses/{semester}') }}">View Registered Course</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/ui-features/dropdowns.html">Class Timetable</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/ui-features/typography.html">Course Material</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/ui-features/typography.html">Assigments & Submission</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#exam" aria-expanded="false" aria-controls="auth">
                <span class="menu-title">Exams & Results</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-pen menu-icon"></i>
            </a>
            <div class="collapse" id="exam">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/blank-page.html"> Exam Schedule </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/login.html"> View Results </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/register.html"> Download Result </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#fee" aria-expanded="false" aria-controls="auth">
                <span class="menu-title">Fees & Payments</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-wallet menu-icon"></i>
            </a>
            <div class="collapse" id="fee">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/blank-page.html"> Fee Structure </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/login.html"> Payment History </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/register.html"> Make a Payment </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/register.html"> Generate Fee Receipts </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#lib" aria-expanded="false" aria-controls="auth">
                <span class="menu-title">Library</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-library menu-icon"></i>
            </a>
            <div class="collapse" id="lib">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/blank-page.html"> Search for Books </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/login.html"> Borrowed Books </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/register.html"> Due Dates & Fines</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#event" aria-expanded="false" aria-controls="auth">
                <span class="menu-title">Events & Activities</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-calendar-search menu-icon"></i>
            </a>
            <div class="collapse" id="event">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/blank-page.html"> Upcoming Events </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/samples/login.html"> Club/Association Memberships </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
