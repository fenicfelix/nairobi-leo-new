<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="{{ asset(get_template_url().'img/favicon.jpg') }}" />
  <title>{{ get_option('ak_app_title') }}</title>

  <!-- Required CSS -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap">
  <link rel="stylesheet" href="{{ asset('theme/backend/css/style.min.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/backend/css/auth.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/backend/vendor/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset(get_template_url().'css/admin.css') }}">

  @yield('styles')

</head>

<body>

    @yield('content')

    @yield('scripts')

  <script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    void(function() {
      document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }

          form.classList.add('was-validated')
        })
      })
    })()
  </script>

  <script src="{{ asset('theme/backend/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('theme/backend/vendor/toastr/toastr.min.js') }}"></script>

  @if (session('error'))
      <script>
        toastr["error"]("{{ session('error') }}", { closeButton: true, progressBar: true, timeOut: 5000 });
      </script>
  @endif

  @if (session('warning'))
      <script>
        toastr["error"]("{{ session('warning') }}", { closeButton: true, progressBar: true, timeOut: 5000 });
      </script>
  @endif

  @if (session('success'))
      <script>
        toastr["success"]("{{ session('success') }}", { closeButton: true, progressBar: true, timeOut: 5000 });
      </script>
  @endif
</body>

</html>