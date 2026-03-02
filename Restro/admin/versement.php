<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Add new versement
if (isset($_POST['add_versement'])) {
    // Prevent Posting Blank Values
    if (empty($_POST['amt']) || empty($_POST['who'])) {
        $err = "Blank Values Not Accepted";
    } else {
        $amt = $_POST['amt'];
        $who = $_POST['who'];
        
        $query = "INSERT INTO rpos_versement (amt, who) VALUES (?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ss', $amt, $who);
        $stmt->execute();
        
        if ($stmt) {
            $success = "Versement Added";
            header("refresh:1; url=versement.php");
        } else {
            $err = "Please Try Again Or Try Later";
        }
        $stmt->close();
    }
}

// Delete versement
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM rpos_versement WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    if ($stmt) {
        $success = "Versement Deleted";
        header("refresh:1; url=versement.php");
    } else {
        $err = "Please Try Again Or Try Later";
    }
    $stmt->close();
}

// Date filtering
$date_filter = "";
$start_date = "";
$end_date = "";

if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
    $start_date = $_GET['start_date'];
    $date_filter .= " AND DATE(created_at) >= '$start_date'";
}

if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $end_date = $_GET['end_date'];
    $date_filter .= " AND DATE(created_at) <= '$end_date'";
}

// Calculate total payments with date filter
$payment_query = "SELECT SUM(pay_amt) as total_payments FROM rpos_payments WHERE 1=1" . str_replace('created_at', 'created_at', $date_filter);
$payment_stmt = $mysqli->prepare($payment_query);
$payment_stmt->execute();
$payment_res = $payment_stmt->get_result();
$payment_data = $payment_res->fetch_object();
$total_payments = $payment_data->total_payments ? $payment_data->total_payments : 0;

// Calculate total versements with date filter
$versement_query = "SELECT SUM(amt) as total_versements FROM rpos_versement WHERE 1=1" . $date_filter;
$versement_stmt = $mysqli->prepare($versement_query);
$versement_stmt->execute();
$versement_res = $versement_stmt->get_result();
$versement_data = $versement_res->fetch_object();
$total_versements = $versement_data->total_versements ? $versement_data->total_versements : 0;

// Calculate remaining amount
$remaining_amount = $total_payments - $total_versements;

require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php require_once('partials/_sidebar.php'); ?>
    
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php require_once('partials/_topnav.php'); ?>
        
        <!-- Header -->
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Success/Error Messages -->
            <?php if (isset($success)) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="ni ni-check-bold"></i></span>
                    <span class="alert-text"><strong>Success!</strong> <?php echo $success; ?></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>
            
            <?php if (isset($err)) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="ni ni-fat-remove"></i></span>
                    <span class="alert-text"><strong>Error!</strong> <?php echo $err; ?></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>
            
            <!-- Date Filter Form -->
            <div class="row mb-4">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-body">
                            <form method="GET" action="versement.php" class="form-inline">
                                <div class="form-group mr-3">
                                    <label class="mr-2">From:</label>
                                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                                </div>
                                <div class="form-group mr-3">
                                    <label class="mr-2">To:</label>
                                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="versement.php" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Financial Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-4 col-md-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total Payments</h5>
                                    <span class="h2 font-weight-bold mb-0"><?php echo number_format($total_payments); ?> Rwf</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                                        <i class="ni ni-money-coins"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total Versements</h5>
                                    <span class="h2 font-weight-bold mb-0"><?php echo number_format($total_versements); ?> Rwf</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                                        <i class="ni ni-send"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Remaining Amount</h5>
                                    <span class="h2 font-weight-bold mb-0 <?php echo $remaining_amount < 0 ? 'text-danger' : 'text-success'; ?>">
                                        <?php echo number_format($remaining_amount); ?> Rwf
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                        <i class="ni ni-chart-bar-32"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add Versement Form -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3>Add New Versement</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Amount (Rwf) <span class="text-danger">*</span></label>
                                            <input type="number" name="amt" class="form-control" required min="0" step="0.01" placeholder="Enter amount">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Who (Person/Entity) <span class="text-danger">*</span></label>
                                            <input type="text" name="who" class="form-control" required placeholder="Enter person or entity name">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <button type="submit" name="add_versement" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Add Versement
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            
            <!-- Versements Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    Versement Reports
                                </div>
                                <div class="col">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search by ID, amount, who, or date...">
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary" onclick="printPage();">Print <i class="fas fa-print"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush" id="versementTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">ID</th>
                                        <th scope="col">Amount</th>
                                        <th class="text-success" scope="col">Who</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM rpos_versement WHERE 1=1" . $date_filter . " ORDER BY created_at DESC";
                                    $stmt = $mysqli->prepare($query);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    
                                    if ($res->num_rows > 0) {
                                        while ($versement = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <th class="text-success" scope="row">
                                                <?php echo $versement->id; ?>
                                            </th>
                                            <td>
                                                <?php echo number_format($versement->amt); ?> Rwf
                                            </td>
                                            <td class="text-success">
                                                <?php echo $versement->who; ?>
                                            </td>
                                            <td>
                                                <?php echo date('d/M/Y g:i', strtotime($versement->created_at)); ?>
                                            </td>
                                            <td>
                                                <a href="versement.php?delete=<?php echo $versement->id; ?><?php echo !empty($start_date) ? '&start_date='.$start_date : ''; ?><?php echo !empty($end_date) ? '&end_date='.$end_date : ''; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this versement?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php 
                                        }
                                    } else { ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No versements found</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <?php require_once('partials/_footer.php'); ?>
        </div>
    </div>
    
    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
    
    <!-- Search Script -->
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input = this.value.toLowerCase();
            var table = document.getElementById('versementTable');
            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (var i = 0; i < rows.length; i++) {
                var id = rows[i].getElementsByTagName('th')[0].textContent.toLowerCase();
                var amount = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
                var who = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
                var date = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
                
                if (id.indexOf(input) > -1 || 
                    amount.indexOf(input) > -1 || 
                    who.indexOf(input) > -1 || 
                    date.indexOf(input) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
        
        function printPage() {
            window.print();
        }
    </script>
</body>
</html>