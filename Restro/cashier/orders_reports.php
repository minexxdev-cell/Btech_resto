<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');

// Get date range from request or set defaults
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : 'all';

// Build date condition for queries
$date_condition = "AND DATE(created_at) >= ? AND DATE(created_at) <= ?";

// Build status condition
$status_condition = "";
if ($status_filter !== 'all') {
    $status_condition = "AND order_status = ?";
}

// Calculate totals
$total_paid_query = "SELECT SUM(prod_price * prod_qty) as total 
                     FROM rpos_orders 
                     WHERE order_status = 'Paid' $date_condition";
$total_paid_stmt = $mysqli->prepare($total_paid_query);
$total_paid_stmt->bind_param('ss', $start_date, $end_date);
$total_paid_stmt->execute();
$total_paid_result = $total_paid_stmt->get_result();
$total_paid = $total_paid_result->fetch_object()->total ?? 0;

$total_pending_query = "SELECT SUM(prod_price * prod_qty) as total 
                        FROM rpos_orders 
                        WHERE order_status = 'Pending' $date_condition";
$total_pending_stmt = $mysqli->prepare($total_pending_query);
$total_pending_stmt->bind_param('ss', $start_date, $end_date);
$total_pending_stmt->execute();
$total_pending_result = $total_pending_stmt->get_result();
$total_pending = $total_pending_result->fetch_object()->total ?? 0;

// Calculate today's totals (from 8 PM yesterday to 8 PM today)
$today_paid_query = "SELECT SUM(prod_price * prod_qty) as total 
                     FROM rpos_orders 
                     WHERE order_status = 'Paid' 
                     AND created_at >= DATE_SUB(CURDATE(), INTERVAL 4 HOUR)
                     AND created_at < DATE_ADD(CURDATE(), INTERVAL 20 HOUR)";
$today_paid_stmt = $mysqli->prepare($today_paid_query);
$today_paid_stmt->execute();
$today_paid_result = $today_paid_stmt->get_result();
$today_paid = $today_paid_result->fetch_object()->total ?? 0;

$today_pending_query = "SELECT SUM(prod_price * prod_qty) as total 
                        FROM rpos_orders 
                        WHERE order_status = 'Pending' 
                        AND created_at >= DATE_SUB(CURDATE(), INTERVAL 4 HOUR)
                        AND created_at < DATE_ADD(CURDATE(), INTERVAL 20 HOUR)";
$today_pending_stmt = $mysqli->prepare($today_pending_query);
$today_pending_stmt->execute();
$today_pending_result = $today_pending_stmt->get_result();
$today_pending = $today_pending_result->fetch_object()->total ?? 0;
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
                    <!-- Date Range Filter -->
                    <div class="row mb-4">
                        <div class="col">
                            <div class="card bg-gradient-default">
                                <div class="card-body">
                                    <form method="GET" class="form-inline">
                                        <div class="form-group mr-3">
                                            <label class="text-white mr-2" for="start_date">Start Date:</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                                   value="<?php echo $start_date; ?>" required>
                                        </div>
                                        <div class="form-group mr-3">
                                            <label class="text-white mr-2" for="end_date">End Date:</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                                   value="<?php echo $end_date; ?>" required>
                                        </div>
                                        <div class="form-group mr-3">
                                            <label class="text-white mr-2" for="status_filter">Status:</label>
                                            <select class="form-control" id="status_filter" name="status_filter">
                                                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Orders</option>
                                                <option value="Paid" <?php echo $status_filter == 'Paid' ? 'selected' : ''; ?>>Paid Only</option>
                                                <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending Only</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="?" class="btn btn-secondary ml-2">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                         <button type="button" class="btn btn-primary ml-2" onclick="printPage();">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                    </form>
                                    <div class="text-white mt-2">
                                        <small>Showing 
                                            <?php 
                                            if ($status_filter == 'all') {
                                                echo '<strong>All Orders</strong>';
                                            } elseif ($status_filter == 'Paid') {
                                                echo '<strong>Paid Orders</strong>';
                                            } else {
                                                echo '<strong>Pending Orders</strong>';
                                            }
                                            ?>
                                            from <strong><?php echo date('M d, Y', strtotime($start_date)); ?></strong> 
                                            to <strong><?php echo date('M d, Y', strtotime($end_date)); ?></strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Total Paid</h5>
                                            <span class="h2 font-weight-bold mb-0"><?php echo number_format($total_paid); ?> Rwf</span>
                                            <p class="mt-2 mb-0 text-muted text-sm">
                                                <span class="text-nowrap">Selected Period</span>
                                            </p>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Total Pending</h5>
                                            <span class="h2 font-weight-bold mb-0"><?php echo number_format($total_pending); ?> Rwf</span>
                                            <p class="mt-2 mb-0 text-muted text-sm">
                                                <span class="text-nowrap">Selected Period</span>
                                            </p>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Today Paid</h5>
                                            <span class="h2 font-weight-bold mb-0"><?php echo number_format($today_paid); ?> Rwf</span>
                                            <p class="mt-2 mb-0 text-muted text-sm">
                                                <span class="text-nowrap">8 PM - 8 PM</span>
                                            </p>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Today Pending</h5>
                                            <span class="h2 font-weight-bold mb-0"><?php echo number_format($today_pending); ?> Rwf</span>
                                            <p class="mt-2 mb-0 text-muted text-sm">
                                                <span class="text-nowrap">8 PM - 8 PM</span>
                                            </p>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Orders Records</h3>
                                </div>
                                <div class="col">
                                    <input type="text" id="searchInput" class="form-control no-print" placeholder="Search by code, customer, status, or date...">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush" id="ordersTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Code</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Products</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Status</th>
                                        <th>Done By</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get all orders grouped by order_code with date and status filter
                                    $ret = "SELECT order_code, customer_name, order_status, Done_by,
                                            MIN(created_at) as created_at,
                                            SUM(prod_price * prod_qty) as total_price,
                                            COUNT(*) as item_count
                                            FROM rpos_orders 
                                            WHERE DATE(created_at) >= ? AND DATE(created_at) <= ? $status_condition
                                            GROUP BY order_code, customer_name, order_status, Done_by
                                            ORDER BY created_at DESC";
                                    
                                    $stmt = $mysqli->prepare($ret);
                                    
                                    if ($status_filter !== 'all') {
                                        $stmt->bind_param('sss', $start_date, $end_date, $status_filter);
                                    } else {
                                        $stmt->bind_param('ss', $start_date, $end_date);
                                    }
                                    
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    
                                    $grand_total = 0;
                                    while ($order = $res->fetch_object()) {
                                        $grand_total += $order->total_price;
                                    ?>
                                        <tr>
                                            <th class="text-success" scope="row"><?php echo $order->order_code; ?></th>
                                            <td><?php echo $order->customer_name; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info no-print" type="button" 
                                                        data-toggle="collapse" 
                                                        data-target="#items_<?php echo md5($order->order_code); ?>" 
                                                        aria-expanded="false">
                                                    <i class="fas fa-list"></i> View Items (<?php echo $order->item_count; ?>)
                                                </button>
                                                <div class="collapse mt-2" id="items_<?php echo md5($order->order_code); ?>">
                                                    <div class="card card-body">
                                                        <?php
                                                        // Get individual items for this order
                                                        $items_query = "SELECT prod_name, prod_price, prod_qty 
                                                                       FROM rpos_orders 
                                                                       WHERE order_code = ?";
                                                        $items_stmt = $mysqli->prepare($items_query);
                                                        $items_stmt->bind_param('s', $order->order_code);
                                                        $items_stmt->execute();
                                                        $items_res = $items_stmt->get_result();
                                                        
                                                        echo '<table class="table table-sm table-bordered">';
                                                        echo '<thead><tr><th>Product</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th></tr></thead>';
                                                        echo '<tbody>';
                                                        
                                                        while ($item = $items_res->fetch_object()) {
                                                            $subtotal = $item->prod_price * $item->prod_qty;
                                                            echo '<tr>';
                                                            echo '<td class="text-success">' . $item->prod_name . '</td>';
                                                            echo '<td>' . number_format($item->prod_price) . ' Rwf</td>';
                                                            echo '<td class="text-success">' . $item->prod_qty . '</td>';
                                                            echo '<td>' . number_format($subtotal) . ' Rwf</td>';
                                                            echo '</tr>';
                                                        }
                                                        
                                                        echo '</tbody></table>';
                                                        ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><strong><?php echo number_format($order->total_price); ?> Rwf</strong></td>
                                            <td>
                                                <?php 
                                                if ($order->order_status == 'Pending') {
                                                    echo "<span class='badge badge-warning'>Pending</span>";
                                                } elseif ($order->order_status == 'Paid') {
                                                    echo "<span class='badge badge-success'>Paid</span>";
                                                } else {
                                                    echo "<span class='badge badge-danger'>Cancelled</span>";
                                                } 
                                                ?>
                                            </td>
                                            <td><?php echo $order->Done_by; ?></td>
                                            <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr class="font-weight-bold">
                                        <td colspan="3" class="text-right">GRAND TOTAL:</td>
                                        <td colspan="4"><strong><?php echo number_format($grand_total); ?> Rwf</strong></td>
                                    </tr>
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
            var table = document.getElementById('ordersTable');
            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (var i = 0; i < rows.length - 1; i++) { // -1 to skip grand total row
                var code = rows[i].getElementsByTagName('th')[0].textContent.toLowerCase();
                var customer = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
                var totalPrice = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
                var status = rows[i].getElementsByTagName('td')[3].textContent.toLowerCase();
                var date = rows[i].getElementsByTagName('td')[5].textContent.toLowerCase();
                
                if (code.indexOf(input) > -1 || 
                    customer.indexOf(input) > -1 || 
                    totalPrice.indexOf(input) > -1 || 
                    status.indexOf(input) > -1 || 
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
    
    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .sidebar, .topnav, .header, .footer {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</body>

</html>