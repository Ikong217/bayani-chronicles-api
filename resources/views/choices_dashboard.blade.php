@extends('othes.layout') {{-- import layout --}}

@section('title', '') {{-- title Bayani Quest Admin Panel --}}


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
    {{-- Included Navigation Bar
    @include('othes.nav')

    <!-- Table -->
    <div class="container mt-5">
        <h1 class="fw-bold mb-3">
            BAYANI QUEST CHOICE
            <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addModal">
                ADD CHOICE
            </button>
        </h1>

        <div class="table-responsive">
            <table class="table table-striped" id="user_table">
                <thead class="text-center">
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Choice</th>
                        <th class="text-success text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($choices as $choice)
                        <tr>
                            <td>{{ $choice->id }}</td>
                            <td>{{ $choice->level->question }}</td>
                            <td>{{ $choice->level->answer }}</td>
                            <td>{{ $choice->choice }}</td>
                            <td class="d-flex align-items-center justify-content-center">
                                <button class="btn btn-secondary me-2 edit-btn" style="width: 100px">
                                    EDIT
                                </button>
                                <form action="{{ route('choices.delete') }}" method="POST" class="delete-form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $choice->id }}">
                                    <button type="button" class="btn btn-danger delete-btn" style="width: 100px">
                                        DELETE
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div> --}}

    <div class="wrapper">
        <button class="backBtn" onclick="window.location.href='{{ route('questions.show') }}'">
            <i class="fa-solid fa-left-long fa-xl"></i>
        </button>

        <button class="addBtn" data-bs-toggle="modal" data-bs-target="#addModal">
            + Choice
        </button>

        <!-- content_wrapper -->
        <div class="content_wrapper">
            <!-- contents -->
            <div class="contents">
                @nav('Additional Choices')

                <div class="content"><!-- table -->
                    <table class="table mt-5 table-striped table-borderless table-hover" id="section_table" border="1">
                        <thead>
                            <tr>
                                <th hidden>ID</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Wrong Answer</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($choices as $choice)
                                <tr>
                                    <td hidden>{{ $choice->id }}</td>
                                    <td>{{ $choice->level->question }}</td>
                                    <td>{{ $choice->level->answer }}</td>
                                    <td>{{ $choice->choice }}</td>
                                    <td class="d-flex gap-2">
                                        <button class="btn btn-primary me-2 edit-btn">
                                            <i
                                            class="fa-solid fa-user-pen"
                                            style="color: white"
                                            ></i>
                                        </button>
                                        <form action="{{ route('choices.delete') }}" method="POST" class="delete-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $choice->id }}">
                                            <button type="button" class="btn btn-danger delete-btn">
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

    </div>
    <!-- wrapper -->

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form method="POST" action="{{ route('choices.add') }}" class="modal-content">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addModalLabel">Add New Choice</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="choiceInput" class="form-label">Choice</label>
                        <input type="text" name="choice" id="choiceInput" class="form-control"
                            placeholder="Enter the choice" required />
                        {{-- @if ($choices && $choices->first()) --}}
                        <input type="hidden" name="level_id" value="{{ $Level_ID }}" required />
                        {{-- @endif --}}

                    </div>
                    {{-- Optional: Include question_id or other fields if needed --}}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Choice</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form method="POST" action="{{ route('choices.update') }}" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editModalLabel">Edit Choice Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="editID" name="id" />
                    <div class="mb-3">
                        <label for="editChoiceInput" class="form-label">Choice</label>
                        <input type="text" name="choice" id="editChoiceInput" class="form-control"
                            placeholder="Edit the choice" required />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Choice</button>
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

            // Edit button logic
            $(".edit-btn").on("click", function() {
                const row = $(this).closest("tr");
                const cols = row.find("td");

                $("#editID").val(cols.eq(0).text());
                $("#editChoiceInput").val(cols.eq(3).text());
                $("#editModal").modal("show");
            });

            // Delete confirmation
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
