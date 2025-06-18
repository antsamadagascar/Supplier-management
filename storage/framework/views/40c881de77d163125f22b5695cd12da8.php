<div class="container-fluid">
    <div class="row">
        <!-- Sidebar à gauche -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar offcanvas offcanvas-start offcanvas-md" tabindex="-1" aria-labelledby="sidebarMenuLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="sidebarMenuLabel">CRM Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="sidebar-header text-center py-4">
                    <img src="<?php echo e(asset('logos/logo.png')); ?>" alt="Logo" class="img-fluid mb-2" style="max-height: 60px;">
                    <h5 class="mb-0">CRM Management</h5>
                </div>
                <hr>
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->is('dashboard*') ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->is('tableau*') ? 'active' : ''); ?>" href="<?php echo e(route('tableau')); ?>">
                                <i class="fas fa-ticket-alt"></i> Tableau
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->is('formulaire*') ? 'active' : ''); ?>" href="<?php echo e(route('formulaire')); ?>">
                                <i class="fas fa-funnel-dollar"></i> Formulaire
                            </a>
                        </li>
                        <hr>
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Configuration</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->is('settings*') ? 'active' : ''); ?>" href="#">
                                <i class="fas fa-cog"></i> Paramètres
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Contenu principal -->
        <main class="col-md-9 ms-md-auto col-lg-10 px-md-4">
            <!-- Votre contenu principal ici -->
        </main>
    </div>
</div><?php /**PATH C:\Users\Ny Antsa\Documents\Fianarana\semestre6\Evaluation\template_laravel\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>