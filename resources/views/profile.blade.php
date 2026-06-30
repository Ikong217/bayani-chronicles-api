<?php use App\Helper\AdminChecker; ?>
@extends('othes.layout')

@section('title', 'Bayani Chronicles Profiles')

@section('head')
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- DataTables -->
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
        table.dataTable thead th,
        table.dataTable tbody td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        table.dataTable,
        table.dataTable th,
        table.dataTable td {
            border: none !important;
        }

        /* Fix profile image display */
        #profilePreview {
            object-fit: cover;
        }

        /* Conditional Styling based on Admin Status */
        .profile-theme-card {
            background-color: {{ AdminChecker::isMeAdmin() ? '#5D4037' : '#D7CCC8' }} !important;
            color: {{ AdminChecker::isMeAdmin() ? '#FFFFFF' : '#4E342E' }} !important;
            border-color: {{ AdminChecker::isMeAdmin() ? '#4E342E' : '#A1887F' }} !important;
        }

        .profile-theme-btn-primary {
            background-color: {{ AdminChecker::isMeAdmin() ? '#4E342E' : '#8D6E63' }} !important;
            border-color: {{ AdminChecker::isMeAdmin() ? '#3E2723' : '#6D4C41' }} !important;
            color: #FFFFFF !important;
        }

        .profile-theme-btn-primary:hover {
            background-color: {{ AdminChecker::isMeAdmin() ? '#3E2723' : '#6D4C41' }} !important;
            border-color: {{ AdminChecker::isMeAdmin() ? '#2E1B17' : '#5D4037' }} !important;
        }

        .profile-theme-btn-outline {
            background-color: transparent !important;
            border-color: {{ AdminChecker::isMeAdmin() ? '#4E342E' : '#8D6E63' }} !important;
            color: {{ AdminChecker::isMeAdmin() ? '#4E342E' : '#8D6E63' }} !important;
        }

        .profile-theme-btn-outline:hover {
            background-color: {{ AdminChecker::isMeAdmin() ? '#4E342E' : '#8D6E63' }} !important;
            color: #FFFFFF !important;
        }

        .profile-theme-text {
            color: {{ AdminChecker::isMeAdmin() ? '#FFFFFF' : '#4E342E' }} !important;
        }

        .profile-theme-modal {
            background-color: {{ AdminChecker::isMeAdmin() ? '#5D4037' : '#EFEBE9' }} !important;
            color: {{ AdminChecker::isMeAdmin() ? '#FFFFFF' : '#4E342E' }} !important;
        }

        .profile-theme-modal-header {
            background-color: {{ AdminChecker::isMeAdmin() ? '#4E342E' : '#D7CCC8' }} !important;
            border-bottom-color: {{ AdminChecker::isMeAdmin() ? '#3E2723' : '#A1887F' }} !important;
        }

        .profile-theme-input {
            background-color: {{ AdminChecker::isMeAdmin() ? '#6D4C41' : '#FFFFFF' }} !important;
            border-color: {{ AdminChecker::isMeAdmin() ? '#5D4037' : '#CCCCCC' }} !important;
            color: {{ AdminChecker::isMeAdmin() ? '#FFFFFF' : '#333333' }} !important;
        }

        .profile-theme-input:focus {
            background-color: {{ AdminChecker::isMeAdmin() ? '#7B5B51' : '#FFFFFF' }} !important;
            border-color: {{ AdminChecker::isMeAdmin() ? '#8D6E63' : '#8D6E63' }} !important;
            color: {{ AdminChecker::isMeAdmin() ? '#FFFFFF' : '#333333' }} !important;
            box-shadow: 0 0 0 0.2rem {{ AdminChecker::isMeAdmin() ? 'rgba(141, 110, 99, 0.25)' : 'rgba(141, 110, 99, 0.25)' }} !important;
        }

        .profile-theme-input::placeholder {
            color: {{ AdminChecker::isMeAdmin() ? '#BCAAA4' : '#999999' }} !important;
        }
    </style>
@endsection

@section('content')
    @include('othes.nav')

    <div class="content_wrapper mt-4">
        <div class="card border-0 shadow-sm rounded-3 p-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-semibold text-primary mb-0">
                    <i class="fa-solid fa-clipboard-list me-2"></i> Profile
                </h4>
            </div>

            <!-- Profile Section -->
            <div class="row">
                <!-- Profile Picture Section -->
                <div class="col-md-4 text-center mb-4">
                    <form id="profilePicForm" enctype="multipart/form-data">
                        @csrf
                        <img src="{{ asset((AdminChecker::getUser()->profile ? 'assets/images/profiles/' . AdminChecker::getUser()->profile : 'assets/images/profiles/user.png')) }}"
                            alt="Profile" class="rounded-circle border shadow-sm mb-3" width="150" height="150"
                            id="profilePreview">

                        <div class="mb-3">
                            <input type="file" name="profile" id="profileInput" class="form-control profile-theme-input" accept="image/*"
                                hidden>
                            <button type="button" class="btn profile-theme-btn-outline w-100" id="changeProfileBtn">
                                <i class="bi bi-camera me-1"></i> Change Profile
                            </button>
                        </div>

                        <button type="submit" class="btn profile-theme-btn-primary w-100 d-none" id="uploadProfileBtn">
                            <i class="bi bi-upload me-1"></i> Upload New Picture
                        </button>
                    </form>
                </div>

                <!-- Profile Details Section -->
                <div class="col-md-8">
                    <form id="profileForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold profile-theme-text">Full Name</label>
                                <input type="text" class="form-control profile-theme-input" name="name"
                                    value="{{ AdminChecker::getUser()->name }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold profile-theme-text">Email</label>
                                <input type="email" class="form-control profile-theme-input" name="email"
                                    value="{{ AdminChecker::getUser()->email }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold profile-theme-text">Contact Number</label>
                                <input type="text" class="form-control profile-theme-input" name="contact"
                                    value="{{ AdminChecker::getUser()->contact ?? '' }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold profile-theme-text">Birthday</label>
                                <input type="date" class="form-control profile-theme-input" name="birthday"
                                    value="{{ AdminChecker::getUser()->birthday }}" disabled>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" id="editBtn" class="btn profile-theme-btn-primary">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>

                            <button type="button" class="btn profile-theme-btn-outline" data-bs-toggle="modal"
                                data-bs-target="#changePassModal">
                                <i class="bi bi-lock me-1"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Confirmation Modal -->
            <div class="modal fade" id="confirmPasswordModal" tabindex="-1" aria-labelledby="confirmPasswordModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-3 profile-theme-modal">
                        <div class="modal-header profile-theme-modal-header">
                            <h5 class="modal-title fw-semibold profile-theme-text"><i class="bi bi-shield-lock me-2"></i>Confirm Password</h5>
                            <button type="button" class="btn-close {{ AdminChecker::isMeAdmin() ? 'btn-close-white' : '' }}" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold profile-theme-text">Enter Password</label>
                                <input type="password" class="form-control profile-theme-input" id="confirmPasswordInput"
                                    placeholder="Enter your password">
                            </div>
                        </div>
                        <div class="modal-footer profile-theme-modal-header">
                            <button class="btn profile-theme-btn-outline" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn profile-theme-btn-primary" id="confirmSaveBtn">Confirm & Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Modal -->
            <div class="modal fade" id="changePassModal" tabindex="-1" aria-labelledby="changePassModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form id="changePassForm">
                        @csrf
                        <div class="modal-content rounded-3 profile-theme-modal">
                            <div class="modal-header profile-theme-modal-header">
                                <h5 class="modal-title fw-semibold profile-theme-text"><i class="bi bi-key me-2"></i>Change Password</h5>
                                <button type="button" class="btn-close {{ AdminChecker::isMeAdmin() ? 'btn-close-white' : '' }}" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label profile-theme-text">Current Password</label>
                                    <input type="password" name="currpass" class="form-control profile-theme-input" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label profile-theme-text">New Password</label>
                                    <input type="password" name="password" class="form-control profile-theme-input" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label profile-theme-text">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control profile-theme-input" required>
                                </div>
                            </div>
                            <div class="modal-footer profile-theme-modal-header">
                                <button class="btn profile-theme-btn-outline" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn profile-theme-btn-primary" type="submit">Save Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
            const editBtn = $("#editBtn");
            const profileInputs = $("#profileForm input");
            const confirmModal = new bootstrap.Modal('#confirmPasswordModal');
            const confirmInput = $("#confirmPasswordInput");
            const uploadBtn = $("#uploadProfileBtn");
            const changeProfileBtn = $("#changeProfileBtn");
            const profileForm = $("#profileForm");
            const profilePicForm = $("#profilePicForm");
            const changePassForm = $("#changePassForm");

            // Disable inputs by default
            profileInputs.prop("disabled", true);

            // Edit → Save toggle
            editBtn.on("click", function() {
                if (editBtn.html().includes("pencil-square")) {
                    // Switch to Edit mode
                    profileInputs.prop("disabled", false);
                    editBtn.html('<i class="bi bi-save me-1"></i> Save');
                } else {
                    // Switch to Save mode - show password confirmation
                    confirmInput.val('');
                    confirmModal.show();
                }
            });

            // Confirm password and save profile
            $("#confirmSaveBtn").on("click", function() {
                const password = confirmInput.val().trim();
                if (!password) {
                    Swal.fire("Error", "Please enter your password.", "error");
                    return;
                }

                const formData = profileForm.serialize() + `&password=${encodeURIComponent(password)}`;

                $.ajax({
                    url: "{{ route('profile.saveInfo') }}",
                    method: "POST",
                    data: formData,
                    success: function(res) {
                        if (res.status === "success") {
                            Swal.fire("Success", res.message, "success");
                            profileInputs.prop("disabled", true);
                            editBtn.html('<i class="bi bi-pencil-square me-1"></i> Edit');
                            confirmModal.hide();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function(xhr) {
                        let message = "Something went wrong.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire("Error", message, "error");
                    }
                });
            });

            // Profile Picture Upload
            changeProfileBtn.click(() => $("#profileInput").click());

            $("#profileInput").change(function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => $("#profilePreview").attr("src", e.target.result);
                    reader.readAsDataURL(file);
                    uploadBtn.removeClass("d-none");
                }
            });

            profilePicForm.on("submit", function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('profile.savePic') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status === "success") {
                            Swal.fire("Success", res.message, "success");
                            uploadBtn.addClass("d-none");
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function(xhr) {
                        let message = "Upload failed.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire("Error", message, "error");
                    }
                });
            });

            // Change Password
            changePassForm.on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('profile.changePass') }}",
                    method: "POST",
                    data: changePassForm.serialize(),
                    success: function(res) {
                        if (res.status === "success") {
                            Swal.fire("Success", res.message, "success");
                            $("#changePassModal").modal("hide");
                            changePassForm.trigger("reset");
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function(xhr) {
                        let message = "Something went wrong.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire("Error", message, "error");
                    }
                });
            });
        });
    </script>
@endsection
