<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Cancel Order (single item)
if (isset($_GET['cancel'])) { 
    $id = $_GET['cancel'];
    $status = "Cancelled";
    $adn = "UPDATE rpos_orders SET order_status = ? WHERE order_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('ss', $status, $id);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
        $success = "Order Cancelled" && header("refresh:1; url=payments.php");
    } else {
        $err = "Try Again Later";
    }
}

// Cancel entire order by order_code
if (isset($_GET['cancel_order'])) {
    $order_code = $_GET['cancel_order'];
    $status = "Cancelled";
    $adn = "UPDATE rpos_orders SET order_status = ? WHERE order_code = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('ss', $status, $order_code);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
        $success = "Order Cancelled" && header("refresh:1; url=payments.php");
    } else {
        $err = "Try Again Later";
    }
}

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
                <div class="header-body"></div>
            </div>
        </div>
        
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <a href="orders.php" class="btn btn-outline-success">
                                <i class="fas fa-plus"></i> <i class="fas fa-utensils"></i>
                                Make A New Order
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Code</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Products</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get all pending orders grouped by order_code
                                    $ret = "SELECT order_code, customer_id, customer_name, 
                                            MIN(created_at) as created_at,
                                            GROUP_CONCAT(CONCAT(prod_name, ' (x', prod_qty, ')') SEPARATOR '<br>') as products,
                                            SUM(prod_price * prod_qty) as total_price
                                            FROM rpos_orders 
                                            WHERE order_status = 'Pending'
                                            GROUP BY order_code, customer_id, customer_name
                                            ORDER BY created_at DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($order = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <th class="text-success" scope="row">
                                                <?php echo $order->order_code; ?>
                                            </th>
                                            <td><?php echo $order->customer_name; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" type="button" 
                                                        data-toggle="collapse" 
                                                        data-target="#items_<?php echo md5($order->order_code); ?>" 
                                                        aria-expanded="false">
                                                    <i class="fas fa-list"></i> View Items
                                                </button>
                                                <div class="collapse mt-2" id="items_<?php echo md5($order->order_code); ?>">
                                                    <div class="card card-body">
                                                        <?php
                                                        // Get individual items for this order
                                                        $items_query = "SELECT prod_name, prod_price, prod_qty 
                                                                       FROM rpos_orders 
                                                                       WHERE order_code = ? AND order_status = 'Pending'";
                                                        $items_stmt = $mysqli->prepare($items_query);
                                                        $items_stmt->bind_param('s', $order->order_code);
                                                        $items_stmt->execute();
                                                        $items_res = $items_stmt->get_result();
                                                        
                                                        echo '<table class="table table-sm table-bordered">';
                                                        echo '<thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>';
                                                        echo '<tbody>';
                                                        
                                                        while ($item = $items_res->fetch_object()) {
                                                            $subtotal = $item->prod_price * $item->prod_qty;
                                                            echo '<tr>';
                                                            echo '<td>' . $item->prod_name . '</td>';
                                                            echo '<td>' . $item->prod_price . ' Rwf</td>';
                                                            echo '<td>' . $item->prod_qty . '</td>';
                                                            echo '<td>' . $subtotal . ' Rwf</td>';
                                                            echo '</tr>';
                                                        }
                                                        
                                                        echo '</tbody></table>';
                                                        ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><strong><?php echo number_format($order->total_price); ?> Rwf</strong></td>
                                            <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                            <td>
                                                <a href="pay_order.php?order_code=<?php echo $order->order_code; ?>&customer_id=<?php echo $order->customer_id; ?>&order_status=Paid">
                                                    <button class="btn btn-sm btn-success">
                                                        <i class="fas fa-handshake"></i>
                                                        Pay Order
                                                    </button>
                                                </a>
                                                  <a href="pay_later.php?order_code=<?php echo $order->order_code; ?>&customer_id=<?php echo $order->customer_id; ?>&order_status=Paid">
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="fas fa-handshake"></i>
                                                        Pay later 
                                                    </button>
                                                </a> 

                                               
                                            </td>
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
</body>
</html>