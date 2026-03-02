<?php
// Language Dropdown Component
?>
<li class="nav-item dropdown">
    <a class="nav-link nav-link-icon" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="ni ni-world-2"></i> 
        <span class="d-none d-md-inline-block"><?php echo __('language'); ?></span>
    </a>
    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right" aria-labelledby="navbar-language">
        <a class="dropdown-item <?php echo ($_SESSION['language'] == 'en' ? 'active' : ''); ?>" href="?lang=en">
            <i class="ni ni-world"></i>
            <span>English</span>
        </a>
        <a class="dropdown-item <?php echo ($_SESSION['language'] == 'fr' ? 'active' : ''); ?>" href="?lang=fr">
            <i class="ni ni-world"></i>
            <span>Français</span>
        </a>
        <a class="dropdown-item <?php echo ($_SESSION['language'] == 'rw' ? 'active' : ''); ?>" href="?lang=rw">
            <i class="ni ni-world"></i>
            <span>Kinyarwanda</span>
        </a>
    </div>
</li>
