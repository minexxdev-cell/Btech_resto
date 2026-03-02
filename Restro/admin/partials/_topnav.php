<?php
$admin_id = $_SESSION['admin_id'];
//$login_id = $_SESSION['login_id'];
$ret = "SELECT * FROM  rpos_admin  WHERE admin_id = '$admin_id'";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
while ($admin = $res->fetch_object()) {

?>
    <nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="dashboard.php"><?php echo $admin->admin_name; ?> Dashboard</a>
            <!-- Form -->

            <!-- User -->
            <ul class="navbar-nav align-items-center d-none d-md-flex">
                <!-- Language Dropdown -->
                <li class="nav-item dropdown language-dropdown mr-3">
                    <a class="nav-link nav-link-icon" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php
                        $flag_class = 'flag-en';
                        if ($_SESSION['language'] == 'fr') {
                            $flag_class = 'flag-fr';
                        } elseif ($_SESSION['language'] == 'rw') {
                            $flag_class = 'flag-rw';
                        }
                        ?>
                        <span class="flag-icon <?php echo $flag_class; ?>"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                        <h6 class="dropdown-header px-0"><?php echo __('language'); ?></h6>
                        <a class="dropdown-item <?php echo ($_SESSION['language'] == 'en' ? 'active' : ''); ?>" href="?lang=en">
                            <span class="flag-icon flag-en"></span>
                            <span>English</span>
                        </a>
                        <a class="dropdown-item <?php echo ($_SESSION['language'] == 'fr' ? 'active' : ''); ?>" href="?lang=fr">
                            <span class="flag-icon flag-fr"></span>
                            <span>Français</span>
                        </a>
                        <a class="dropdown-item <?php echo ($_SESSION['language'] == 'rw' ? 'active' : ''); ?>" href="?lang=rw">
                            <span class="flag-icon flag-rw"></span>
                            <span>Kinyarwanda</span>
                        </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media align-items-center">
                            <span class="avatar avatar-sm rounded-circle">
                                <img alt="Image placeholder" src="assets/img/theme/user-a-min.png">
                            </span>
                            <div class="media-body ml-2 d-none d-lg-block">
                                <span class="mb-0 text-sm  font-weight-bold"><?php echo $admin->admin_name; ?></span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                        <div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0"><?php echo __('welcome'); ?></h6>
                        </div>
                        <a href="change_profile.php" class="dropdown-item">
                            <i class="ni ni-single-02"></i>
                            <span><?php echo __('my_profile'); ?></span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <i class="ni ni-user-run"></i>
                            <span><?php echo __('logout'); ?></span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
<?php } ?>