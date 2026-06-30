<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/dec6212617.js" crossorigin="anonymous"></script>

  <style>
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      transition: background 0.5s, color 0.5s;
    }

    /* Noli theme */
    .theme-noli {
      background-image: url("{{ asset('assets/images/monument.jpg') }}");
      color: #4a2c2a;
    }
    .theme-noli .card {
      background-color: rgba(255, 248, 240, 0.9);
      border: 1px solid #d2b48c;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
      color: #4a2c2a;
    }
    .theme-noli .btn-primary {
      background-color: #8b4513;
      border: none;
    }
    .theme-noli .btn-primary:hover {
      background-color: #5c3317;
    }

    /* El Fili theme */
    .theme-elfili {
      background-image: url("{{ asset('assets/images/jose_rizal.jpg') }}");
      color: #f5deb3;
    }
    .theme-elfili .card {
      background-color: rgba(40, 26, 13, 0.9);
      border: 1px solid #6b4226;
      box-shadow: 0 4px 15px rgba(0,0,0,0.8);
      color: #f5deb3;
    }
    .theme-elfili .form-label {
      color: #f5deb3;
    }
    .theme-elfili .form-control {
      background-color: #3b2a26;
      border: 1px solid #6b4226;
      color: #f5deb3;
    }
    .theme-elfili .form-control:focus {
      border-color: #d2b48c;
      box-shadow: none;
    }
    .theme-elfili .btn-primary {
      background-color: #a0522d;
      border: none;
    }
    .theme-elfili .btn-primary:hover {
      background-color: #7b3f1d;
    }
  </style>
</head>

<body class="theme-noli"> <!-- Default theme -->

  <div class="card p-4 rounded-4 shadow-sm" style="max-width: 420px; width: 100%;">
    <h4 class="text-center mb-3">Reset Password</h4>
    <p class="text-center mb-4">Choose a new password to secure your account.</p>

    <form action="{{ route('users.reset.submit') }}" method="post">
      @csrf
      @method('PUT')
      <input type="hidden" name="code" value="{{$rawCode}}">

      <!-- New Password -->
      <div class="mb-3">
        <label for="password" class="form-label">New Password <span id="pass-span" class="text-danger">*</span></label>
        <div class="input-group">
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password"
            required oninput="strong()">
          <button type="button" class="btn btn-outline-secondary" onclick="toggleVisibility('password', this)">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
        <div class="form-text">Must be at least 8 characters with uppercase, lowercase, number, and symbol.</div>
      </div>

      <!-- Confirm Password -->
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password <span id="cpass-span"
            class="text-danger">*</span></label>
        <div class="input-group">
          <input type="password" class="form-control" id="confirm_password" name="password_confirmation"
            placeholder="Re-enter password" required oninput="verify()">
          <button type="button" class="btn btn-outline-secondary" onclick="toggleVisibility('confirm_password', this)">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" id="submit" class="btn btn-primary w-100" disabled>Change Password</button>
    </form>

    <div class="text-center mt-3">
      <button type="button" class="btn btn-link" onclick="toggleTheme()">Switch Theme</button>
    </div>
  </div>

   @include('othes.swal')

  <script>
    const pass = document.getElementById('password');
    const cpass = document.getElementById('confirm_password');
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

    function toggleVisibility(fieldId, btn) {
      const input = document.getElementById(fieldId);
      const icon = btn.querySelector("i");
      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }

    function toggleTheme() {
      document.body.classList.toggle('theme-noli');
      document.body.classList.toggle('theme-elfili');
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
