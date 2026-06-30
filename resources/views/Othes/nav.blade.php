<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark" style="padding-inline: 30px; width:100%">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">BAYANI QUEST ADMIN</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item dropdown dropstart">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">MENU</a>
                    <ul class="dropdown-menu">
                        <!-- nav -->
                        <li>
                            <a class="dropdown-item" href="{{ route('users.show') }}">Users List</a>
                        </li>
                        <!-- nav -->

                        @if (session('isAdmin'))
                            <!-- nav -->
                            <li>
                                <a class="dropdown-item" href="{{ route('teachers.show') }}">Teachers List</a>
                            </li>
                        @endif
                        <!-- nav -->

                        <!-- Sections -->
                        <li>
                            <a href="{{ route('section.show') }}" class="dropdown-item">Sections</a>
                        </li>
                        <!-- Sections -->

                        <!-- nav -->
                        <li>
                            <a class="dropdown-item" href="{{ route('questions.show') }}">Questions List</a>
                        </li>
                        <!-- nav -->

                        {{-- <li><hr class="dropdown-divider" /></li>

          <!-- nav -->
          <li>
            <a class="dropdown-item" href="admin_dashboard.html"
              >Admin Accounts</a
            >
          </li>
          <!-- nav --> --}}

                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="logout-btn dropdown-item">
                                    LOGOUT
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- nav -->
