@extends('othes.layout')

@section('title', 'Users')

@section('head')
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.dataTables.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/navigation.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endsection

@section('content')
    <div class="wrapper">
        <button class="backBtn" onclick="window.location.href='{{ route('dashboard') }}'">
            <i class="fa-solid fa-left-long fa-xl"></i>
        </button>

        <button class="addBtn" data-bs-toggle="modal" data-bs-target="#add_modal">Add User</button>

        <div class="content_wrapper">
            <div class="contents">
                @nav('Users')
                <div class="content">

                    {{-- Upload Form --}}
                    <form action="{{ route('user.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="file" class="form-control" accept=".csv, .txt, .xlsx, .xls"
                                required>
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Upload & Import</button>
                    </form>

                    {{-- Users Table --}}
                    <table class="table mt-5 table-striped table-borderless table-hover" id="user_table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Birthday</th>
                                <th>Section</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="align-middle">
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ strtoupper($user->gender) }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->birthday }}</td>
                                    <td>{{ \App\Helper\AdminChecker::userGetSection($user->id)?->section->section_name ?? 'No Section' }}
                                    </td>
                                    <td>
                                        <form action="{{ route('user.ban') }}" method="POST" class="ban-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $user->id }}">
                                            <button class="btn btn-{{ $user->isBanned ? 'primary' : 'secondary' }} ban-btn"
                                                type="button" style="width:100px">
                                                {{ $user->isBanned ? 'UnBan' : 'Ban' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="d-flex gap-2">
                                        <button class="btn btn-primary edit-btn" data-bs-toggle="modal"
                                            data-bs-target="#edit_modal">
                                            <i class="fa-solid fa-user-pen"></i>
                                        </button>
                                        <form action="{{ route('user.delete') }}" method="POST" class="delete-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $user->id }}">
                                            <button class="btn btn-danger delete-btn" type="button">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No Users</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    {{-- Add User Modal --}}
    <form method="POST" action="{{ route('user.create') }}" class="modal fade" id="add_modal" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add New User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    @csrf
                </div>
                <div class="modal-body">
                    <div class="edit_grid">
                        <label>Username:</label>
                        <input type="text" name="username" placeholder="e.g Juan" required>
                    </div>
                    <div class="edit_grid">
                        <label>Gender:</label>
                        <select name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="edit_grid">
                        <label>Email Address:</label>
                        <input type="email" name="email" placeholder="e.g juandelacruz@gmail.com" required>
                    </div>
                    <div class="edit_grid">
                        <label>Birth Date:</label>
                        <input type="date" name="bdate" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </div>
        </div>
    </form>

    {{-- Edit User Modal --}}
    <form method="POST" action="{{ route('user.update') }}" class="modal fade" id="edit_modal" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    @csrf
                    <input type="hidden" name="id" id="edit_id">
                </div>
                <div class="modal-body">
                    <div class="edit_grid">
                        <label>Username:</label>
                        <input type="text" name="username" id="edit_username" required>
                    </div>
                    <div class="edit_grid">
                        <label>Gender:</label>
                        <select name="gender" id="edit_gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="edit_grid">
                        <label>Email Address:</label>
                        <input type="email" name="email" id="edit_email" required>
                    </div>
                    <div class="edit_grid">
                        <label>Birth Date:</label>
                        <input type="date" name="bdate" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#user_table').DataTable({
                pageLength: 5,
                lengthChange: false,
                searching: true,
                ordering: false
            });

            // Ban Button
            $('.ban-btn').on('click', function() {
                const form = $(this).closest('form');
                if ($(this).text().trim() === 'Ban') {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to BAN this user!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, Ban!"
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                } else {
                    form.submit();
                }
            });

            // Delete Button
            $('.delete-btn').on('click', function() {
                const form = $(this).closest('form');
                Swal.fire({
                    title: "Are you sure?",
                    text: "This action cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // Edit Button
            $('.edit-btn').on('click', function() {
                const row = $(this).closest('tr');
                const cols = row.find('td');
                $('#edit_id').val(cols.eq(0).text());
                $('#edit_username').val(cols.eq(1).text());
                $('#edit_email').val(cols.eq(3).text());
                $('#edit_gender').val(cols.eq(2).text().toLowerCase());
            });
        });
    </script>
@endsection
