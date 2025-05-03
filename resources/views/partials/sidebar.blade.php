    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar à gauche -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar offcanvas offcanvas-start offcanvas-md" tabindex="-1" aria-labelledby="sidebarMenuLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="sidebarMenuLabel">ERP Management</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="sidebar-header text-center py-4">
                        <img src="{{ asset('logos/logo.png') }}" alt="Logo" class="img-fluid mb-2" style="max-height: 60px;">
                        <h5 class="mb-0">ERP Management</h5>
                    </div>
                    <hr>
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
    
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('suppliers.index') }}">
                                <i class="fas fa-users"></i> Fournisseurs
                            </a>
                        </li>


                            <hr>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-md-auto col-lg-10 px-md-4">
                <!-- Notifications -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Contenu dynamique -->
                @yield('content')
            </main>
        </div>
    </div>
