<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="{{ route('admin.dashboard') }}" class="nav-link">Home</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a
        id="navbarDropdown"
        class="nav-link dropdown-toggle"
        href="#"
        role="button"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      >
        {{ auth()->user()?->name ?? 'User' }}
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
        <a
          class="dropdown-item"
          href="#"
          onclick="event.preventDefault();document.getElementById('logout-form').submit();"
        >
          Logout
        </a>

        <form id="logout-form" action="/logout" method="POST" style="display: none">
          @csrf
        </form>
      </div>
    </li>
  </ul>
</nav>
