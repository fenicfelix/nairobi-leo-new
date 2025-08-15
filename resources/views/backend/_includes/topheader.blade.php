<!-- Main header -->
      <div id="main-header">
        <nav class="navbar navbar-expand navbar-light bg-white gap-4">
          <button type="button" class="btn btn-light d-none d-xl-flex" data-toggle="mini-sidebar">
            <i class="fas fa-bars"></i>
          </button>
          <button type="button" class="btn btn-light d-flex d-xl-none me-3" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
          <ul class="navbar-nav align-items-center ms-auto">
            <li class="nav-item vr mx-3"></li>
            <li class="nav-item dropdown">
              <a href="#" class="nav-link dropdown-toggle no-caret py-0 pe-0 text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ session("thumbnail") }}" width="32" height="32" alt="User" class="rounded-circle" loading="lazy"><br>
                <small class="">Hi, {{ session('name') }}</small>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                <li>
                  <div class="dropdown-divider"></div>
                </li>
                <li><a id="logout" class="dropdown-item" href="#">Sign out</a></li>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                </form>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
      <!-- /Main header -->