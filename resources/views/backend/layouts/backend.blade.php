@include('backend._includes.header')

<body class="preloading">

  <!-- Wrapper -->
  <div id="wrapper" class="fixed-sidebar fixed-navbar">
    <!-- available options: fixed-sidebar, fixed-navbar, fixed-footer, mini-sidebar -->

    @include('backend._includes.sidebar')

    <!-- Main -->
    <div id="main">

      @include('backend._includes.topheader')

      @yield("main_body")

      @include('backend._includes.copyright')

    </div>
    <!-- /Main -->

  </div>
  <!-- /Wrapper -->

  @include('backend._includes.footer')

  