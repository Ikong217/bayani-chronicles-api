<?php use App\Helper\AdminChecker; ?>
@extends('othes.layout') {{-- import layout --}}

@section('title', 'Bayani Quest Sections') {{-- title  --}}


{{-- head  --}}
@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/section.css') }}">
@endsection


@section('css')

@endsection
{{-- head  --}}



{{-- body  --}}
@section('content')
    <!-- wrapper -->
    <div class="wrapper">
        <!-- content_wrapper -->
        <div class="content_wrapper">
            <button class="backBtn" onclick="window.location.href='{{ route('dashboard') }}'">
                <i class="fa-solid fa-left-long fa-xl"></i>
            </button>

            @if (AdminChecker::isAdmin(AdminChecker::getUser()->id))
                <button class="addBtn" data-bs-toggle="modal" data-bs-target="#add_modal"> Add Section</button>
            @endif

            <!-- contents -->
            <div class="contents">
                @nav('Sections')

                <div class="content">
                    @foreach ($sections as $section)
                        @if (AdminChecker::isAdmin(AdminChecker::getUser()->id) || AdminChecker::allowCurrentTeacher($section->id))
                            <!-- navigation -->
                            <div class="navigation">
                                <div class="grid">
                                    <h4>{{ $section->grade_level }}</h4>
                                    @if (AdminChecker::isAdmin(AdminChecker::getUser()->id))
                                        <form action="{{ route('section.delete') }}" method="POST" class="delete-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $section->id }}">
                                            <button class=" delete-btn" type="button">
                                                <i class="fa-solid fa-xmark fa-lg" style="color: #ffffff;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="grid" style="background-image: url('{{ asset('assets/images/book.jpg') }}');">
                                    @if (AdminChecker::isAdmin(AdminChecker::getUser()->id))
                                        <button data-id="{{ $section->id }}" data-teacher="{{ $section->teacher_id }}"
                                            data-grade="{{ $section->grade_level }}"
                                            data-section={{ $section->section_name }} onclick="ShowUpdate(this)">
                                            Update
                                        </button>
                                    @endif

                                    <div class="section_info">
                                        <h3>{{ $section->teacher->name }}</h3>
                                        <h1
                                            onclick="window.location.href='{{ route('section.user', ['id' => $section->id]) }}'">
                                            {{ $section->section_name }}
                                        </h1>
                                    </div>
                                </div>
                            </div>
                            <!-- navigation -->
                        @endif
                    @endforeach

                </div>
            </div>
            <!-- contents -->

        </div>
        <!-- content_wrapper -->

    </div>
    <!-- wrapper -->

    @if (AdminChecker::isAdmin(AdminChecker::getUser()->id))

        <!-- add Modal -->
        <form method="POST" action="{{ route('section.create') }}" class="modal fade" id="add_modal" tabindex="-1"
            aria-labelledby="add_modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="add_modal">Add New Section</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        @csrf
                    </div>

                    <!-- modal-body -->
                    <div class="modal-body">

                        <!-- edit_grid -->
                        <div class="edit_grid row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <select name="teacher" id="teacher" class="form-select">
                                        @forelse ($teachers as $teacher)
                                            @if (!AdminChecker::isAdmin($teacher->id))
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endif
                                        @empty
                                            <option value="">No Teacher Available</option>
                                        @endforelse
                                    </select>
                                    <label for="teacher">Teacher:</label>
                                </div>
                            </div>

                        </div>
                        <!-- edit_grid -->


                        <!-- edit_grid -->
                        <div class="edit_grid row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <select name="grade" id="grade" class="form-select">
                                        <option value="Grade - 7">Grade - 7</option>
                                        <option value="Grade - 8">Grade - 8</option>
                                        <option value="Grade - 9">Grade - 9</option>
                                        <option value="Grade - 10">Grade - 10</option>
                                        <option value="Grade - 11">Grade - 11</option>
                                        <option value="Grade - 12">Grade - 12</option>
                                    </select>
                                    <label for="grade">Grade:</label>
                                </div>
                            </div>
                        </div>
                        <!-- edit_grid -->

                        <!-- edit_grid -->
                        <div class="edit_grid row g-2 ">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" name="section" id="section" placeholder="Rizal"
                                        class="form-control" />
                                    <label for="section">Section:</label>
                                </div>
                            </div>

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

        <!-- edit Modal -->
        <form method="POST" action="{{ route('section.update') }}" class="modal fade" id="edit_modal" tabindex="-1"
            aria-labelledby="edit_modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="edit_modal">Update Section</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        @csrf
                        <input type="hidden" name="id" id="edit_id" value="">
                    </div>

                    <!-- modal-body -->
                    <div class="modal-body">

                        <!-- edit_grid -->
                        <div class="edit_grid row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <select name="teacher" id="edit_teacher" class="form-select">
                                        @forelse ($teachers as $teacher)
                                            @if (!AdminChecker::isAdmin($teacher->id))
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endif
                                        @empty
                                            <option value="">No Teacher Available</option>
                                        @endforelse
                                    </select>
                                    <label for="edit_teacher">Teacher:</label>
                                </div>
                            </div>
                        </div>
                        <!-- edit_grid -->


                        <!-- edit_grid -->
                        <div class="edit_grid row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <select name="grade" id="edit_grade" class="form-select">
                                        <option value="Grade - 7">Grade - 7</option>
                                        <option value="Grade - 8">Grade - 8</option>
                                        <option value="Grade - 9">Grade - 9</option>
                                        <option value="Grade - 10">Grade - 10</option>
                                        <option value="Grade - 11">Grade - 11</option>
                                        <option value="Grade - 12">Grade - 12</option>
                                    </select>
                                    <label for="edit_grade">Grade:</label>
                                </div>
                            </div>
                        </div>
                        <!-- edit_grid -->

                        <!-- edit_grid -->
                        <div class="edit_grid row g-2 ">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" name="section" id="edit_section" placeholder="Rizal"
                                        class="form-control" />
                                    <label for="edit_section">Section:</label>
                                </div>
                            </div>

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
    @endif

    </div>


@endsection


@section('others')
    @include('othes.swal')
@endsection


@section('scripts')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MRCW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {
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

        function ShowUpdate(button) {
            // Get modal element
            const modal = document.getElementById("edit_modal");

            // Fill form fields
            document.getElementById("edit_id").value = button.getAttribute("data-id");
            document.getElementById("edit_teacher").value = button.getAttribute("data-teacher");
            document.getElementById("edit_grade").value = button.getAttribute("data-grade");
            document.getElementById("edit_section").value = button.getAttribute("data-section");

            // Show modal (Bootstrap 5)
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    </script>
@endsection
{{-- body  --}}
