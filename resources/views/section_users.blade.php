<?php use App\Helper\AdminChecker; ?>

@extends('othes.layout') {{-- import layout --}}

@section('title', 'Bayani Chronicles Admin Panel - Users') {{-- title --}}

@section('head')
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.dataTables.min.css" rel="stylesheet" />

    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/af-2.7.0/b-3.2.4/b-html5-3.2.4/date-1.5.6/r-3.0.5/datatables.min.css"
        rel="stylesheet" integrity="sha384-PEtbirWf6d/J8jRJhnuVK3eE/ezCiRaLlGTWvvZmfMb2DKaJyknWJ3OhNDxjAlrk"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">

    <style>
        /* 🔹 Center text properly */
        table.dataTable thead th,
        table.dataTable tbody td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        /* 🔹 Remove DataTables borders */
        table.dataTable,
        table.dataTable th,
        table.dataTable td {
            border: none !important;
        }
    </style>

    <style>
        /* 🔹 Center text properly */
        table.dataTable thead th,
        table.dataTable tbody td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        /* 🔹 Remove DataTables borders */
        table.dataTable,
        table.dataTable th,
        table.dataTable td {
            border: none !important;
        }

        /* 🎮 LEADERBOARD STYLES */
        .leaderboard-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: linear-gradient(145deg, #8b6f47, #a0826d);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            position: relative;
            border: 8px solid #6b5638;
        }

        .leaderboard-header {
            text-align: center;
            background: linear-gradient(145deg, #d4a574, #c9955e);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.2);
            border: 3px solid #8b6f47;
        }

        .leaderboard-header h2 {
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .leaderboard-header .grade-info {
            color: #f9f3e8;
            font-size: 1.1rem;
            margin-top: 5px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
        }

        .leaderboard-item {
            display: flex;
            align-items: center;
            padding: 18px 20px;
            margin-bottom: 12px;
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .leaderboard-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        /* Rank Colors */
        .rank-1 {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            border: 3px solid #b8860b;
        }

        .rank-2 {
            background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
            border: 3px solid #a8a8a8;
        }

        .rank-3 {
            background: linear-gradient(135deg, #cd7f32, #e39c5f);
            border: 3px solid #8b5a2b;
        }

        .rank-other {
            background: linear-gradient(135deg, #5a7c8f, #6b8fa3);
            border: 3px solid #3e5a6b;
        }

        .rank-number {
            font-size: 2rem;
            font-weight: bold;
            min-width: 50px;
            text-align: center;
            margin-right: 20px;
        }

        .rank-1 .rank-number {
            color: #8b6914;
            text-shadow: 2px 2px 4px rgba(255, 215, 0, 0.5);
        }

        .rank-2 .rank-number {
            color: #5a5a5a;
            text-shadow: 2px 2px 4px rgba(192, 192, 192, 0.5);
        }

        .rank-3 .rank-number {
            color: #6b3e1e;
            text-shadow: 2px 2px 4px rgba(205, 127, 50, 0.5);
        }

        .rank-other .rank-number {
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .player-info {
            flex: 1;
        }

        .player-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: #2c1810;
            margin-bottom: 3px;
        }

        .rank-other .player-name {
            color: #fff;
        }

        .player-section {
            font-size: 0.95rem;
            color: #5a4a3a;
            opacity: 0.9;
        }

        .rank-other .player-section {
            color: #e0e0e0;
        }

        .stats {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.3rem;
            font-weight: bold;
            color: #2c1810;
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: center;
        }

        .rank-other .stat-value {
            color: #fff;
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #5a4a3a;
            opacity: 0.8;
        }

        .rank-other .stat-label {
            color: #e0e0e0;
        }

        .star-icon {
            color: #ffd700;
            filter: drop-shadow(0 0 3px rgba(255, 215, 0, 0.6));
        }

        .empty-leaderboard {
            text-align: center;
            padding: 40px;
            color: #f9f3e8;
            font-size: 1.2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
        }

        @media (max-width: 768px) {
            .stats {
                flex-direction: column;
                gap: 8px;
            }

            .leaderboard-item {
                flex-direction: column;
                text-align: center;
            }

            .rank-number {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
@endsection

@section('content')
    @include('othes.nav')

    <!-- wrapper -->
    <div class="content_wrapper mt-4">
        <div class="card border-0 shadow-sm rounded-3 p-4">

            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="fw-semibold text-primary mb-0">
                    <i class="fa-solid fa-users me-2"></i> Section Users List
                </h4>

                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#add_modal">
                        <i class="fa-solid fa-user-plus me-1"></i> Add User
                    </button>
                </div>
                {{-- <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary px-3" onclick="window.location.href='{{ route('section.data.summative',['id'=>$section->id]) }}'">
                    <i class="fa-solid fa-user-plus me-1"></i> Summative
                </button>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary px-3" onclick="window.location.href='{{ route('section.data.quiz',['id'=>$section->id]) }}'">
                    <i class="fa-solid fa-user-plus me-1"></i> Quiz
                </button>
            </div> --}}
            </div>

            <!-- ✅ Responsive Table -->
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center mb-0" id="section_table">
                    <thead class="table-light text-center">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->user->id }}</td>
                                <td>{{ $user->user->username }}</td>
                                <td>{{ strtoupper($user->user->gender) }}.</td>
                                <td>{{ $user->user->email }}</td>
                                <td>
                                    <a href="{{ route('users.scores.show', ['id' => urlEncode(Crypt::encrypt($user->user->id))]) }}"
                                        class="btn btn-success">Scores</a>
                                </td>
                                <td class="d-flex gap-2 justify-content-center">
                                    <form action="{{ route('section.user.remove') }}" method="POST" class="delete-form">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $user->user->id }}">
                                        <button class="btn btn-danger delete-btn" type="button">
                                            <i class="fa-solid fa-trash" style="color: white"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    No Users Found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- ✅ End Responsive Table -->
        </div>
    </div>
    <!-- wrapper -->
    <!-- wrapper -->


    <!-- 🏆 LEADERBOARD SECTION -->
    <div class="leaderboard-container">
        <div class="leaderboard-header">
            <h2>🏆 LEADERBOARD 🏆</h2>
            <p class="grade-info">{{ $grade_level ?? 'All Grades' }}</p>
        </div>

        @if (isset($leaderboard) && count($leaderboard) > 0)
            @foreach ($leaderboard as $entry)
                <div class="leaderboard-item rank-{{ $entry['ranking'] <= 3 ? $entry['ranking'] : 'other' }}">
                    <div class="rank-number">{{ $entry['ranking'] }}</div>

                    <div class="player-info">
                        <div class="player-name">{{ $entry['username'] }}</div>
                        <div class="player-section">{{ $entry['section'] }}</div>
                    </div>

                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-value">
                                <span class="star-icon">⭐</span>
                                {{ $entry['stars'] }}
                            </div>
                            <div class="stat-label">Stars</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-value">{{ $entry['total_score'] }}</div>
                            <div class="stat-label">Total Score</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-value">{{ $entry['average'] }}</div>
                            <div class="stat-label">Average</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-value">{{ $entry['attempts'] }}</div>
                            <div class="stat-label">Attempts</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-leaderboard">
                <p>📊 No leaderboard data available yet.</p>
                <p>Students need to complete levels to appear here!</p>
            </div>
        @endif
    </div>
    <!-- END LEADERBOARD -->

    <!-- add Modal -->
    <form method="POST" action="{{ route('section.user.add') }}" class="modal fade" id="add_modal" tabindex="-1"
        aria-labelledby="add_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add_modal">Add New User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $section->id }}">
                </div>

                <!-- modal-body -->
                <div class="modal-body">

                    <!-- edit_grid -->
                    <div class="edit_grid">
                        <label for="name">Name:</label>
                        <select name="id" id="name">
                            @foreach ($students as $student)
                                @if (!AdminChecker::userHasSection($student->id))
                                    <option value="{{ $student->id }}">{{ $student->username }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <!-- edit_grid -->

                </div>
                <!-- modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </div>
    </form>
    <!-- Modal -->
@endsection


@section('others')

    @include('othes.swal')

@endsection


@section('scripts')
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="{{ asset('assets/js/table.js') }}"></script>

    <script>
        $(document).ready(function() {
            $("#user_table").DataTable({
                pageLength: 5,
                lengthChange: false,
                ordering: false,
                searching: true,
                info: true,
            });

            // BAN user
            $(".ban-btn").on("click", function() {
                const button = $(this);
                const form = button.closest("form");

                if (button.text().trim() === "Ban") {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to BAN this user!!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, Ban them.",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    form.submit();
                }
            });

            // DROP user
            $(".drop-btn").on("click", function() {
                const button = $(this);
                const form = button.closest("form");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This action cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });


        // edit function
        $(document).ready(function() {
            $("#user_table").DataTable({
                pageLength: 5,
                lengthChange: false,
                ordering: false,
                searching: true,
                info: true,
            });

            // Handle edit button click
            $(".edit-btn").on("click", function() {
                const row = $(this).closest("tr");
                const cols = row.find("td");
                $("#editModal").modal("show");
                $("#edit_id").val(cols.eq(0).text());
                $("#edit_username").val(cols.eq(1).text());
                $("#edit_email").val(cols.eq(3).text());
            });

            // SweetAlert for delete confirmation
            $(".delete-btn").on("click", function() {
                const button = $(this);
                const form = button.closest("form");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This action cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
{{-- body  --}}
