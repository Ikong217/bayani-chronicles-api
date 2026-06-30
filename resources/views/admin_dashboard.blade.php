<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bayani Quest Admin Panel</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Optional: Your root styles -->
    <link rel="stylesheeet" href="css/root.css" />

    <!-- DataTables CSS -->
    <link
      rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"
    />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body>
    <!-- Navbar -->
    <nav
      class="navbar navbar-expand-lg bg-dark navbar-dark"
      style="padding-inline: 30px"
    >
      <div class="container-fluid">
        <a class="navbar-brand" href="#">BAYANI QUEST ADMIN</a>
        <div class="collapse navbar-collapse justify-content-end">
          <ul class="navbar-nav">
            <li class="nav-item dropdown dropstart">
              <a
                class="nav-link dropdown-toggle text-white"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                >MENU</a
              >
              <ul class="dropdown-menu">
                <!-- nav -->
                <li>
                  <a class="dropdown-item" href="user_dashboard.html"
                    >Users List</a
                  >
                </li>
                <!-- nav -->

                <!-- nav -->
                <li>
                  <a class="dropdown-item" href="teachers_dashboard.html"
                    >Teachers List</a
                  >
                </li>
                <!-- nav -->

                <!-- nav -->
                <li>
                  <a class="dropdown-item" href="questions_dashboard.html"
                    >Questions List</a
                  >
                </li>
                <!-- nav -->

                <li><hr class="dropdown-divider" /></li>

                <!-- nav -->
                <li>
                  <a class="dropdown-item" href="admin_dashboard.html"
                    >Admin Accounts</a
                  >
                </li>
                <!-- nav -->

                <li><hr class="dropdown-divider" /></li>
                <li>
                  <form method="POST" action="/logout">
                    <button type="submit" class="logout-btn dropdown-item">
                      LOGOUT
                    </button>
                  </form>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- nav -->

    <!-- Table -->
    <div class="container mt-5">
      <h1 class="fw-bold mb-3">
        BAYANI QUEST ADMINS
        <button
          type="button"
          class="btn btn-primary float-end"
          data-bs-toggle="modal"
          data-bs-target="#addModal"
        >
          ADD USER
        </button>
      </h1>

      <div class="table-responsive">
        <table class="table table-striped" id="user_table">
          <thead class="text-center">
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>Password</th>
              <th>Contact</th>
              <th class="text-success text-center">ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>guardian</td>
              <td>contact</td>
              <td>department</td>
              <td>April 30, 2025</td>
              <td>09770113531</td>
              <td class="d-flex align-items-center justify-content-center">
                <button
                  class="btn btn-secondary me-2 edit-btn"
                  style="width: 100px"
                >
                  EDIT
                </button>
                <button class="btn btn-danger delete-btn" style="width: 100px">
                  DELETE
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- /Table -->

    <!-- Add Modal -->
    <div
      class="modal fade"
      id="addModal"
      tabindex="-1"
      aria-labelledby="addModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-dark">
            <h1 class="modal-title fs-5 text-white" id="addModalLabel">
              ADD NEW USER
            </h1>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input
                type="text"
                name="username"
                id="username"
                class="form-control"
              />
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                name="email"
                id="email"
                class="form-control"
              />
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input
                type="password"
                name="password"
                id="password"
                class="form-control"
              />
            </div>

            <div class="mb-3">
              <label for="contact" class="form-label">Contact</label>
              <input
                type="text"
                name="contact"
                id="contact"
                class="form-control"
              />
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
            <button type="button" class="btn btn-dark">Save changes</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div
      class="modal fade"
      id="editModal"
      tabindex="-1"
      aria-labelledby="editModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-dark">
            <h1 class="modal-title fs-5 text-white" id="editModalLabel">
              EDIT ADMIN INFO
            </h1>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label for="editUsername" class="form-label">Username</label>
              <input type="text" id="editUsername" class="form-control" />
            </div>

            <div class="mb-3">
              <label for="editEmail" class="form-label">Email</label>
              <input type="email" id="editEmail" class="form-control" />
            </div>

            <div class="mb-3">
              <label for="editPassword" class="form-label">Password</label>
              <input type="password" id="editPassword" class="form-control" />
            </div>

            <div class="mb-3">
              <label for="editContact" class="form-label">Contact</label>
              <input type="text" id="editContact" class="form-control" />
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
            <button type="button" class="btn btn-dark">Update Info</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
      $(document).ready(function () {
        $("#user_table").DataTable({
          pageLength: 5,
          lengthChange: false,
          ordering: false,
          searching: true,
          info: true,
        });

        // Handle edit button click
        $(".edit-btn").on("click", function () {
          const row = $(this).closest("tr");
          const cols = row.find("td");
          $("#editModal").modal("show");

          $("#editUsername").val(cols.eq(0).text());
          $("#editEmail").val(cols.eq(1).text());
          $("#editPassword").val(cols.eq(2).text());
          $("#editContact").val(cols.eq(4).text());
        });

        // SweetAlert for delete confirmation
        $(".delete-btn").on("click", function () {
          const row = $(this).closest("tr");

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
              row.remove(); // Simulate deletion
              Swal.fire("Deleted!", "The user has been removed.", "success");
            }
          });
        });
      });
    </script>
  </body>
</html>
