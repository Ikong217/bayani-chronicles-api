`@extends('othes.layout') {{-- import layout --}}

@section('title', 'Bayani Chronicles Admin Login') {{-- title  --}}


{{-- head  --}}
@section('head')

    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/general.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/auth/login.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/loader.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    {!! NoCaptcha::renderJs() !!}

@endsection


@section('css')

@endsection
{{-- head  --}}



{{-- body  --}}
@section('content')
    <div class="wrapper" style="background-image: url('assets/images/book.jpg');">

        <div class="backdrop"></div>
        <div class="content_wrapper">
            <div class="contents">
                <div class="grid">
                    <div class="info_grid">
                        <h1>Bayani Chronicles</h1>
                    </div>

                    <!--info_grid  -->
                    <form class="info_grid">
                        <div class="input_wrapper" id="login-form">

                            <div class="input">
                                <label for="email">Email: <span id="email-span" class="text-danger">*</span></label>
                                <input type="text" name="email" id="email" placeholder="juan@gmail.com">
                            </div>

                            <div class="input input_with_eye">
                                <label for="password">Password: <span id="pass-span" class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" placeholder="Enter your password">
                                <span class="toggle_eye" onclick="toggleVisibility('password', this)"><i
                                        class="fa-solid fa-eye fa-lg" style="color: black;"></i></span>
                            </div>

                            <div class="input">
                                <div class="remember" title="If you Activate this, You will be automatically be logged in within this week.">
                                    <input type="checkbox" name="remember" id="remember_me" title="If  you click this remember me checkbox, you will automatically Logged in until the next Sunday night.">
                                    <label for="remember_me" >Remember me</label>
                                </div>

                                <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPassModal">
                                    Forgot password?
                                </a>

                            </div>

                            <div class="input">
                                <button type="button" onclick="login()">LOG IN</button>
                            </div>

                            <div class="input">
                                {!! NoCaptcha::display() !!}
                                <span id="captcha-span" class="text-danger">*</span>
                                @csrf
                            </div>
                        </div>
                    </form>
                    <!--info_grid  -->


                </div>
                <div class="grid" style="background-image: url('assets/images/book.jpg');"></div>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div class="modal fade" id="otp-modal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="otp-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="otpModalLabel">Verify OTP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @csrf
                        <p>Your OTP code has been sent to your email.</p>
                        <input type="hidden" name="key" id="key" value="">
                        <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP"
                            required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="resendOtp()">Resend OTP</button>
                        <button type="button" class="btn btn-primary" onclick="verifyOtp()">Verify</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPassModal" tabindex="-1" aria-labelledby="forgotPassLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="forgot-pass-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPassLabel">Forgot Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="forgot-email" class="form-label">Enter your email address</label>
                            <input type="email" class="form-control" id="forgot-email" name="email" required />
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitForgotPass()">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- verify code modal --}}
    <div class="modal fade" id="verifyCodeModal" tabindex="-1" aria-labelledby="verifyCodeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"> <!-- This should wrap all content -->
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyCodeLabel">Enter Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="code-email" id="code-email" class="form-control mb-2"
                        placeholder="Your email" disabled />
                    <input type="text" name="code" id="code" class="form-control"
                        placeholder="Enter the 8-digit code" required />
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="verifyEmailCode()" class="btn btn-success">Verify</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div id="loadingScreen" style="display: none;">
        <div class="loader"></div>
    </div>

@endsection


@section('others')
    @include('othes.swal')
    <script src="{{ asset('assets/js/password.js') }}"></script>
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
@endsection


@section('scripts')
    {{-- jquery  --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- loader script  --}}
    <script>
        function showloader() {
            $('#loadingScreen').show();
        }

        function hideloader() {
            $('#loadingScreen').hide();
        }
    </script>

    {{-- login script async function --}}
    <script>
        async function login() {
            // Clear previous error messages
            document.getElementById('email-span').innerText = '';
            document.getElementById('pass-span').innerText = '';
            document.getElementById('captcha-span').innerText = '';
            document.getElementById('key').value = "";

            console.log("i am here");

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const remember = document.getElementById('remember_me').checked ? 1 : 0;
            const recaptcha = grecaptcha.getResponse();
            const uri = "{{ route('login.submit') }}";
            const token = document.querySelector('input[name="_token"]').value;

            const data = {
                email: email,
                password: password,
                'g-recaptcha-response': recaptcha,
                remember: remember
            };

            showloader();

            try {
                fetch(uri, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": token
                        },
                        body: JSON.stringify(data),
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'invalid') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Login',
                                text: result.message
                            });
                        } else if (result.status === 'success') {
                            const otpModal = new bootstrap.Modal(document.getElementById('otp-modal'));
                            otpModal.show();
                            document.getElementById('key').value = result.data['key'];
                        } else if (result.status === 'error') {
                            const errors = result.errors;
                            if (errors.email) {
                                document.getElementById('email-span').innerText = errors.email[0];
                            }
                            if (errors.password) {
                                document.getElementById('pass-span').innerText = errors.password[0];
                            }
                            if (errors['g-recaptcha-response']) {
                                document.getElementById('captcha-span').innerText = errors['g-recaptcha-response'][
                                    0
                                ];
                            }
                            if (errors['exception']) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Something went wrong',
                                    text: errors['exception']
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error("Unexpected error:", error);
                    })
                    .finally(() => {
                        // Always HIDE the loader when request finishes
                        hideloader();
                        grecaptcha.reset();
                    });
            } catch (err) {
                hideloader();
                console.error('Forgot password error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Unexpected Error',
                    text: 'Please try again later.'
                }).then(() => {
                    window.location.reload();
                });
            }

        }
    </script>

    {{-- otp verification action  --}}
    <script>
        async function resendOtp() {
            const key = document.getElementById('key').value.trim();
            const token = document.querySelector('input[name="_token"]').value;
            const uri = "{{ route('login.otp.resend') }}";

            try {
                showloader();
                const res = await fetch(uri, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    body: JSON.stringify({
                        key,
                    })
                });

                const result = await res.json();

                if (result.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: Object.values(result.errors).flat().join('\n')
                    });
                } else if (result.status === 'warning') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: result.message
                    });
                } else if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: result.message
                    });
                }
                hideloader();
            } catch (error) {
                hideloader();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to Resend OTP. Please try again.'
                });
                console.error('Fetch error:', error);
            }
        }
    </script>

    {{-- Resend OTP  --}}
    <script>
        async function verifyOtp() {
            const key = document.getElementById('key').value.trim();
            const otp = document.getElementById('otp').value.trim();
            const token = document.querySelector('input[name="_token"]').value;
            const uri = "{{ route('login.otp.verify') }}";

            try {
                showloader();
                const res = await fetch(uri, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    body: JSON.stringify({
                        key,
                        otp
                    })
                });

                const result = await res.json();

                if (result.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: Object.values(result.errors).flat().join('\n')
                    }).then(() => {
                        const otpModal = bootstrap.Modal.getInstance(document.getElementById('otp-modal'));
                        if (otpModal) otpModal.hide();
                        document.getElementById('key').value = "";
                        document.getElementById('otp').value = "";
                    });
                } else if (result.status === 'warning') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: result.message
                    });
                } else if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'OTP Verified',
                        text: result.message
                    }).then(() => {
                        window.location.href = "{{ route('dashboard') }}";
                    });
                }
                hideloader();
            } catch (error) {
                hideloader();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to verify OTP. Please try again.'
                });
                console.error('Fetch error:', error);
            }
        }
    </script>

    {{-- submit Forgotpass  --}}
    <script>
        async function submitForgotPass() {
            const email = document.getElementById('forgot-email').value;
            const token = document.querySelector('input[name="_token"]').value;
            const uri = "{{ route('login.forgotpass') }}";

            const data = {
                email
            };

            try {
                showloader();
                const res = await fetch(uri, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Email Sent',
                        text: result.message
                    }).then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('forgotPassModal')).hide();
                        const verifyModal = new bootstrap.Modal(document.getElementById('verifyCodeModal'));
                        verifyModal.show();
                        document.getElementById("code-email").value = email;
                    });

                } else if (result.status === "error") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: Object.values(result.errors).flat().join('\n')
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Could not send email'
                    });
                }
                hideloader();
            } catch (err) {
                hideloader();
                console.error('Forgot password error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Unexpected Error',
                    text: 'Please try again later.'
                });
            }
        }
    </script>

    {{-- Code Verify  --}}
    <script>
        async function verifyEmailCode() {
            const email = document.getElementById('code-email').value;
            const code = document.getElementById('code').value;
            const token = document.querySelector('input[name="_token"]').value;
            const uri = "{{ route('login.forgotpass.verify') }}";

            const data = {
                email,
                code
            };

            try {
                showloader();
                const res = await fetch(uri, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Email Sent',
                        text: result.message
                    }).then(() => {
                        window.location.href = "{{ route('forgotpass.show', ['code' => 'PLACEHOLDER']) }}"
                            .replace('PLACEHOLDER', result.uri);
                    });

                } else if (result.status === "warning") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Failed',
                        text: result.message
                    });
                } else if (result.status === "error") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: Object.values(result.errors).flat().join('\n')
                    }).then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('verifyCodeModal')).hide();
                        const verifyModal = new bootstrap.Modal(document.getElementById('forgotPassModal'));
                        verifyModal.show();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Could not send email'
                    });
                }
                hideloader();
            } catch (err) {
                hideloader();
                console.error('Forgot password error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Unexpected Error',
                    text: 'Please try again later.'
                });
            }

        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
{{-- body  --}}
