<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand text-light font-weight-bold" href="{{ route('dashboard') }}">
            <i class="fas fa-network-wired text-info mr-2"></i>CirrusAPI
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            @auth
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item {{ ($viewName ?? '') == 'dashboard' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-chart-line mr-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item {{ ($viewName ?? '') == 'sync_api' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('sync-api.create') }}"><i class="fas fa-plus-circle mr-1"></i> Create Sync API</a>
                    </li>
                    <li class="nav-item {{ in_array(($viewName ?? ''), ['sync_api_view', 'sync_api_edit']) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('sync-api.index') }}"><i class="fas fa-edit mr-1"></i> Update Sync API</a>
                    </li>
                    <li class="nav-item {{ ($viewName ?? '') == 'apiConfig' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('api-configurations.index') }}"><i class="fas fa-cogs mr-1"></i> API Config</a>
                    </li>
                    <li class="nav-item {{ ($viewName ?? '') == 'index' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('db-configurations.index') }}"><i class="fas fa-database mr-1"></i> DB Config</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <span class="navbar-text mr-3 text-light">
                        <i class="fas fa-user-circle mr-1 text-secondary"></i> Logged in as <strong>{{ Auth::user()->username }}</strong>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </div>
            @else
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt mr-1"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus mr-1"></i> Register</a>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>
