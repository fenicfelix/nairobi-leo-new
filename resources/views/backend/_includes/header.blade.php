<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="{{ asset(get_template_url().'img/'.config('cms.app_favicon')) }}" />
  <title>{{ $page_title ?? get_option('ak_app_title')}}</title>

  <!-- Required CSS -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap">
  <link rel="stylesheet" href="{{ asset('theme/backend/css/fontawesome.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('theme/backend/css/simplebar.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/backend/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/backend/vendor/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/backend/css/sidebar-dark.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/backend/css/custom.css?v=1.0.1') }}">
  <link rel="stylesheet" href="{{ asset(get_template_url().'css/admin.css') }}">

  @yield('styles')

</head>