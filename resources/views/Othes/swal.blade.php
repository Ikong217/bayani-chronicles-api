<!-- content_wrapper -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('message'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Successful  ',
            text: "{{ session('message') }}",
            showConfirmButton: true
        });
    </script>
@endif
@if (session('info'))
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Notice',
            text: "{{ session('info') }}",
            showConfirmButton: true
        });
    </script>
@endif
@if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid',
            text: "{{ session('error') }}",
            showConfirmButton: true
        });
    </script>
@endif
@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'There were some problems with your input',
            html: "<ul>" +
                // Loop through each error and display it in a list
                @foreach ($errors->all() as $error)
                    "<li>{{ $error }}</li>" +
                @endforeach
            "</ul>",
            showConfirmButton: true
        });
    </script>
@endif
