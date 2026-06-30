<?php use App\Helper\AdminChecker; ?>
@extends('othes.layout') {{-- import layout --}}

@section('title', 'Bayani Quest Admin Panel - Teachers') {{-- title  --}}


{{-- head  --}}
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
    <link rel="stylesheet" href="{{ asset('assets/css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/navigation.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endsection


@section('css')

@endsection
{{-- head  --}}



{{-- body  --}}
@section('content')


    {{-- included navigation bar
    @include('othes.nav')

    <!-- Table -->
    <div class="container mt-5">
        <h1 class="fw-bold mb-3">
            BAYANI QUEST TEACHERS
            <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addModal">
                ADD TEACHER
            </button>
        </h1>

        <div class="table-responsive">
            <table class="table table-striped" id="user_table">
                <thead class="text-center">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th class="text-success text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td>{{ $teacher->id }}</td>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td>{{ $teacher->contact }}</td>
                            <td class="d-flex align-items-center justify-content-center">
                                <button class="btn btn-secondary me-2 edit-btn" style="width: 100px">
                                    EDIT
                                </button>
                                <form action="{{ route('teacher.delete') }}" method="POST" class="delete-form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $teacher->id }}">
                                    <button class="btn btn-danger delete-btn" type="button" style="width: 100px">
                                        DELETE
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No Available Teacher from the List Yet</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
    <!-- /Table --> --}}

    <button class="backBtn" onclick="window.location.href='{{ route('dashboard') }}'">
        <i class="fa-solid fa-left-long fa-xl"></i>
    </button>

    {{-- <button class="addBtn" data-bs-toggle="modal" data-bs-target="#add_modal"> Add Teacher</button> --}}
    <button class="addBtn" type="button"data-bs-toggle="modal" data-bs-target="#addModal">
        ADD TEACHER
    </button>

    <!-- content_wrapper -->
    <div class="content_wrapper">
        <!-- contents -->
        <div class="contents">
            @nav('Teachers')

            <div class="content"><!-- table -->
                {{-- Upload Form --}}
                <form action="{{ route('teacher.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" accept=".csv, .txt, .xlsx, .xls" required>
                        @error('file')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Upload & Import</button>
                </form>
                <table class="table mt-5 table-striped table-borderless table-hover" id="section_table" border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Profile</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Birthday</th>
                            <th>Promotion</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teachers as $teacher)
                            <tr>
                                <td>{{ $teacher->id }}</td>
                                <td>
                                    <img src="{{ asset('assets/images/profiles/' . ($teacher->profile ?? 'user.png')) }}"
                                        alt="User Profile" style="width:50px; height:50px; object-fit:cover;">
                                </td>
                                <td>{{ $teacher->name }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>{{ $teacher->contact }}</td>
                                <td>{{ $teacher->birthday }}</td>
                                <td>
                                    <form action="{{ route('teacher.promotion') }}" method="POST" class="delete-form">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $teacher->id }}">
                                        <button
                                            class="btn btn-{{ AdminChecker::isAdmin($teacher->id) ? 'danger' : 'primary' }} promotion-btn"
                                            type="button" style="width: 100px">
                                            {{ AdminChecker::isAdmin($teacher->id) ? 'DEMOTE' : 'PROMOTE' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="d-flex gap-2">
                                    <button class="btn btn-primary edit-btn" data-bs-toggle="modal"
                                        data-bs-target="#edit_modal">
                                        <i class="fa-solid fa-user-pen" style="color: white"></i>
                                    </button>
                                    <form action="{{ route('teacher.delete') }}" method="POST" class="delete-form">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $teacher->id }}">
                                        <button class="btn btn-danger delete-btn" type="button">
                                            <i class="fa-solid fa-trash" style="color: white"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <!-- table -->
            </div>
        </div>
        <!-- contents -->

    </div>
    <!-- content_wrapper -->


    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('teacher.add') }}" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header bg-dark">
                    <h1 class="modal-title fs-5 text-white" id="addModalLabel">
                        ADD NEW TEACHER
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile" class="form-label">Profile</label>
                        <input type="file" name="profile" id="profile" accept=".jpg,.png,.jpeg"
                            class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Name</label>
                        <input type="text" name="username" id="username" class="form-control" />
                        @csrf
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="bdate" class="form-label">Birth Date</label>
                        <input type="date" name="bdate" id="bdate" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact</label>
                        <input type="text" name="contact" id="contact" class="form-control" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-dark">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('teacher.update') }}" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header bg-dark">
                    <h1 class="modal-title fs-5 text-white" id="editModalLabel">
                        EDIT TEACHER INFO
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editprofile" class="form-label">Profile</label>
                        <input type="file" name="profile" id="editprofile" accept=".jpg,.png,.jpeg"
                            class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Name</label>
                        <input type="text" id="editUsername" name="name" class="form-control" />
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editID" name="id" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" id="editEmail" name="email" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="editBdate" class="form-label">Birth Date</label>
                        <input type="date" name="bdate" id="editBdate" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="editContact" class="form-label">Contact</label>
                        <input type="text" id="editContact" name="contact" class="form-control" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-dark">Update Info</button>
                </div>
            </form>
        </div>
    </div>
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

            // Handle edit button click
            $(".edit-btn").on("click", function() {
                const row = $(this).closest("tr");
                const cols = row.find("td");
                $("#editModal").modal("show");

                $("#editID").val(cols.eq(0).text());
                $("#editUsername").val(cols.eq(2).text());
                $("#editEmail").val(cols.eq(3).text());
                $("#editPassword").val("");
                $("#editContact").val(cols.eq(4).text());
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

            // SweetAlert for delete confirmation
            $(".promotion-btn").on("click", function() {
                const button = $(this);
                const form = button.closest("form");

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you wish to continue!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, do it!",
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
