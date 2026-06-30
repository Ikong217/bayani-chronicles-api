<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    .theme-noli { background-image: url("{{ asset('assets/images/monument.jpg') }}"); color: #4a2c2a; }
    .theme-elfili { background-image: url("{{ asset('assets/images/jose_rizal.jpg') }}"); color: #f5deb3; }
  </style>
</head>
<body class="theme-noli">

  <!-- Forgot Password Card -->
  <div class="card p-4 rounded-4" style="max-width: 400px; width: 100%;">
    <h4 class="text-center mb-3">Forgot Password</h4>
    <p class="text-center mb-4">Enter your email to reset your password</p>

    <form id="forgotForm">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Send Reset Code</button>
    </form>

    <div class="text-center mt-3">
      <button class="btn btn-link" onclick="toggleTheme()">Switch Theme</button>
    </div>
  </div>

  <!-- Verification Modal -->
  <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4">
        <div class="modal-header">
          <h5 class="modal-title" id="verificationLabel">Enter Verification Code</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="verifyForm">
            @csrf
            <input type="hidden" name="email" id="v-email">
            <div class="mb-3">
              <label for="verificationCode" class="form-label">Verification Code</label>
              <input type="text" class="form-control" id="verificationCode" name="code" placeholder="123456" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  @include('othes.swal')

  <script>
    function toggleTheme() {
      document.body.classList.toggle('theme-noli');
      document.body.classList.toggle('theme-elfili');
    }

    // Forgot Form AJAX
    $("#forgotForm").on("submit", function(e) {
      e.preventDefault();
      $.ajax({
        url: "{{ route('users.forgot.code') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function(res) {
          if (res.status === "success") {
            Swal.fire("Success", res.message, "success");
            $("#v-email").val($("#email").val()); // Pass email to modal
            new bootstrap.Modal(document.getElementById('verificationModal')).show();
          } else if (res.status === "warning") {
            Swal.fire("Warning", res.message, "warning");
          } else {
            let errs = Object.values(res.errors).join("<br>");
            Swal.fire("Error", errs, "error");
          }
        },
        error: function(xhr) {
          Swal.fire("Error", "Server error: " + xhr.statusText, "error");
        }
      });
    });

    // Verify Code AJAX
    $("#verifyForm").on("submit", function(e) {
      e.preventDefault();
      $.ajax({
        url: "{{ route('user.forgot.verify') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function(res) {
          if (res.status === "success") {
            Swal.fire("Verified", res.message, "success").then(() => {
              window.location.href = "{{ url('/users/reset') }}/" + res.uri;
            });
          } else if (res.status === "warning") {
            Swal.fire("Warning", res.message, "warning");
          } else {
            let errs = Object.values(res.errors).join("<br>");
            Swal.fire("Error", errs, "error");
          }
        },
        error: function(xhr) {
          Swal.fire("Error", "Server error: " + xhr.statusText, "error");
        }
      });
    });
  </script>
</body>
</html>
