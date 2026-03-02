<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["pay_code"]) || empty($_POST["pay_amt"]) || empty($_POST['pay_method'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $pay_code = $_POST['pay_code'];
    $order_code = $_GET['order_code'];
    $customer_id = $_GET['customer_id'];
    $order_amt  = $_POST['pay_amt']; // Original order amount
    $tip = !empty($_POST['tip']) ? $_POST['tip'] : 0; // Handle tip, default to 0 if empty
    $pay_amt = $order_amt + $tip; // Total payment amount including tip
    $pay_method = $_POST['pay_method'];
    $pay_id = $_POST['pay_id'];
    $order_status = $_GET['order_status'];
    $done_by = $_SESSION['admin_email'];

    //Insert Captured information to a database table (added tip column)
    $postQuery = "INSERT INTO rpos_payments (pay_id, pay_code, order_code, customer_id, pay_amt, pay_method, tip, Done_by) VALUES(?,?,?,?,?,?,?,?)";
    $upQry = "UPDATE rpos_orders SET order_status =? WHERE order_code =?";

    $postStmt = $mysqli->prepare($postQuery);
    $upStmt = $mysqli->prepare($upQry);
    //bind paramaters (added 's' for tip)

    $rc = $postStmt->bind_param('ssssssss', $pay_id, $pay_code, $order_code, $customer_id, $pay_amt, $pay_method, $tip, $done_by);
    $rc = $upStmt->bind_param('ss', $order_status, $order_code);

    $postStmt->execute();
    $upStmt->execute();
    //declare a varible which will be passed to alert function
    if ($upStmt && $postStmt) {
      $success = "Paid" && header("refresh:1; url=receipts.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
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
    <?php
    require_once('partials/_topnav.php');
    
    $order_code = $_GET['order_code'];
    
    // Get all order items and calculate total
    $ret = "SELECT * FROM rpos_orders WHERE order_code = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $order_code);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $order_items = [];
    $grand_total = 0;
    $customer_name = '';
    
    while ($order = $res->fetch_object()) {
      $subtotal = ($order->prod_price * $order->prod_qty);
      $grand_total += $subtotal;
      $customer_name = $order->customer_name;
      
      $order_items[] = [
        'prod_name' => $order->prod_name,
        'prod_price' => $order->prod_price,
        'prod_qty' => $order->prod_qty,
        'subtotal' => $subtotal
      ];
    }
    ?>
    
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
              <h3>Payment Details</h3>
              <p class="text-muted mb-0">Order Code: <strong><?php echo $order_code; ?></strong></p>
              <p class="text-muted mb-0">Customer: <strong><?php echo $customer_name; ?></strong></p>
            </div>
            <div class="card-body">
              
              <!-- Order Items Summary -->
              <div class="mb-4">
                <h4>Order Items</h4>
                <div class="table-responsive">
                  <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                      <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($order_items as $item) { ?>
                        <tr>
                          <td><?php echo $item['prod_name']; ?></td>
                          <td><?php echo number_format($item['prod_price']); ?> Rwf</td>
                          <td><?php echo $item['prod_qty']; ?></td>
                          <td><?php echo number_format($item['subtotal']); ?> Rwf</td>
                        </tr>
                      <?php } ?>
                      <tr class="table-success">
                        <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                        <td><strong><?php echo number_format($grand_total); ?> Rwf</strong></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <hr>

              <!-- Payment Form -->
              <h4>Complete Payment</h4>
              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Payment ID</label>
                    <input type="text" name="pay_id" readonly value="<?php echo $payid; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Payment Code</label>
                    <input type="text" name="pay_code" value="<?php echo $mpesaCode; ?>" class="form-control">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Order Amount (Rwf)</label>
                    <input type="text" name="pay_amt" readonly value="<?php echo $grand_total; ?>" class="form-control form-control-lg font-weight-bold text-success">
                    <small class="form-text text-muted">
                      Total for <?php echo count($order_items); ?> item(s)
                    </small>
                  </div>
                  <div class="col-md-6">
                    <label>Tip (Rwf) - Optional</label>
                    <input type="number" name="tip" class="form-control" min="0" step="1" placeholder="Enter tip amount" id="tipInput">
                    <small class="form-text text-muted">Add a gratuity for excellent service</small>
                  </div>
                </div>
                <div class="form-row mt-3">
                  <div class="col-md-6">
                    <label>Payment Method</label>
                    <select class="form-control" name="pay_method" required>
                      <option value="">Select Payment Method</option>
                      <option value="Cash">Cash</option>
                      <option value="Paypal">Paypal</option>
                      <option value="Credit Card">Credit Card</option>
                      <option value="Mobile Money">Mobile Money</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label>Grand Total (with tip)</label>
                    <input type="text" id="grandTotal" readonly class="form-control form-control-lg font-weight-bold text-primary" value="<?php echo number_format($grand_total); ?> Rwf">
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="pay" value="Complete Payment" class="btn btn-success btn-lg btn-block">
                  </div>
                  <div class="col-md-6">
                    <a href="payments.php" class="btn btn-secondary btn-lg btn-block">
                      <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                  </div>
                </div>
              </form>
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
  
  <!-- Calculate grand total with tip -->
  <script>
    document.getElementById('tipInput').addEventListener('input', function() {
      const orderTotal = <?php echo $grand_total; ?>;
      const tip = parseFloat(this.value) || 0;
      const grandTotal = orderTotal + tip;
      
      document.getElementById('grandTotal').value = grandTotal.toLocaleString() + ' Rwf';
    });
  </script>
</body>
</html>