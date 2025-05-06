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
                    <img src="{{ asset('logos/erpnext-logo.svg') }}" alt="Logo" class="img-fluid mb-2" style="max-height: 60px;">
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

    
    </div>
</div>