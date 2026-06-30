<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/forgot_pass.css') }}">
    <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    {{-- <div class="container mt-5">
        <form action="{{route('forgotpass.submit')}}" method="post" class="card p-4 shadow-sm">
            <h2 class="mb-3">Reset Password</h2>
            <p>Email: <strong>{{$email}}</strong></p>
            @csrf
            @method('PUT')
            <input type="hidden" name="code" value="{{$rawCode}}">

            <div class="mb-3">
                <label for="password" class="form-label">New Password <span id="pass-span" class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" name="password" required oninput="strong()" />
                <div class="form-text">Password must contain uppercase, lowercase, digits, and symbols.</div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password <span id="cpass-span" class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required oninput="verify()" />
            </div>

            <button type="submit" id="submit" class="btn btn-primary" disabled>Change Password</button>
        </form>
    </div> --}}

    <div class="wrapper" style="background-image: url('{{ asset('assets/images') }}/book.jpg');">
        <div class="backdrop"></div>
        <div class="content_wrapper">
            <div class="grid">
                <h2>{{ $email }}</h2>
                <h4>To ensure your account remains protected, choose a new password. Use a combination of letters,
                    numbers, and symbols for better security.</h4>
            </div>
            <form action="{{ route('forgotpass.submit') }}" method="post" class="grid">
                <div class="forgot_grid">
                    <label for="password">Password: <span id="pass-span" class="text-danger">*</span></label>
                    <div class="input_with_eye">
                        <input type="password" name="password" id="password" placeholder="Enter new password" required oninput="strong()">
                        <span class="toggle_eye" onclick="toggleVisibility('password', this)"><i
                                class="fa-solid fa-eye fa-lg" style="color: black;"></i></span>
                    </div>
                </div>

                <div class="forgot_grid">
                    <label for="confirm_password">Confirm Password: <span id="cpass-span"
                            class="text-danger">*</span></label>
                    <div class="input_with_eye">
                        <input type="password" name="password_confirmation" id="confirm_password"
                            placeholder="Re-enter new password" required oninput="verify()">
                        <span class="toggle_eye" onclick="toggleVisibility('confirm_password', this)"><i
                                class="fa-solid fa-eye fa-lg" style="color: black;"></i></span>
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="code" value="{{ $rawCode }}">
                    </div>
                </div>


                <div class="forgot_grid">
                    <button type="submit">CHANGE PASSWORD</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const pass = document.getElementById('password');
        const cpass = document.getElementById('password_confirmation');
        const pass_span = document.getElementById('pass-span');
        const cpass_span = document.getElementById('cpass-span');
        const submit = document.getElementById('submit');

        let strongPass = false;
        let same = false;

        function strong() {
            pass_span.innerText = "";
            strongPass = false;

            if (pass.value === "") {
                pass_span.innerText = "*";
                return;
            }

            if (!detectStrong(pass.value)) {
                pass_span.innerText = " Please make a stronger password.";
            } else {
                strongPass = true;
            }
            verify();
        }

        function detectStrong(str) {
            // Must contain uppercase, lowercase, number, and special character
            const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            return pattern.test(str);
        }

        function verify() {
            cpass_span.innerText = "";
            same = false;

            if (cpass.value === "") {
                cpass_span.innerText = "*";
                return;
            }

            if (cpass.value !== pass.value) {
                cpass_span.innerText = " Passwords do not match.";
            } else {
                same = true;
            }

            enableButton();
        }

        function enableButton() {
            submit.disabled = !(strongPass && same);
        }
    </script>

    <script src="{{asset('assets/js/password.js')}}"></script>
    @include('othes.swal')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
