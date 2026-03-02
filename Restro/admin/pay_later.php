<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["pay_code"]) || empty($_POST["pay_amt"])) {
    $err = "Blank Values Not Accepted";
  } else {
    $pay_code = $_POST['pay_code'];
    $order_code = $_GET['order_code'];
    $customer_id = $_GET['customer_id'];
    $pay_amt  = $_POST['pay_amt'];
    $pay_method = "Pay Later"; // This is now fixed as "Pay Later"
    $pay_id = $_POST['pay_id'];
    $order_status = "Pending";

    //Insert Captured information to a database table
    $postQuery = "INSERT INTO rpos_payments (pay_id, pay_code, order_code, customer_id, pay_amt, pay_method) VALUES(?,?,?,?,?,?)";
    $upQry = "UPDATE rpos_orders SET order_status = ? WHERE order_code = ?";

    $postStmt = $mysqli->prepare($postQuery);
    $upStmt = $mysqli->prepare($upQry);
    
    //bind parameters
    $postStmt->bind_param('ssssss', $pay_id, $pay_code, $order_code, $customer_id, $pay_amt, $pay_method);
    $upStmt->bind_param('ss', $order_status, $order_code);

    $postStmt->execute();
    $upStmt->execute();
    
    //Check if both queries succeeded
    if ($postStmt && $upStmt) {
      $success = "Payment recorded as 'Pay Later'";
      header("refresh:1; url=receipts.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
    
    // Close statements
    $postStmt->close();
    $upStmt->close();
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
              <h3><?php echo __('pay_later'); ?> - <?php echo __('order_code'); ?>: <?php echo $_GET['order_code']; ?></h3>
            </div>
            <div class="card-body">
              <?php if (isset($success)) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
              <?php } ?>
              <?php if (isset($err)) { ?>
                <div class="alert alert-danger"><?php echo $err; ?></div>
              <?php } ?>
              
              <form method="POST">
                <div class="form-group">
                  <label><?php echo __('payment_id'); ?></label>
                  <input type="text" name="pay_id" class="form-control" value="<?php echo $payid; ?>" readonly>
                </div>
                
                <div class="form-group">
                  <label><?php echo __('payment_code'); ?></label>
                  <input type="text" name="pay_code" class="form-control" value="<?php echo $mpesaCode; ?>" readonly>
                </div>
                
                <div class="form-group">
                  <label><?php echo __('amount_to_pay'); ?></label>
                  <?php
                  // Get total amount for this order
                  $order_code = $_GET['order_code'];
                  $ret = "SELECT SUM(prod_price * prod_qty) as total FROM rpos_orders WHERE order_code = ?";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->bind_param('s', $order_code);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  $order = $res->fetch_object();
                  ?>
                  <input type="text" name="pay_amt" class="form-control" value="<?php echo $order->total; ?>" readonly>
                </div>
                
                <div class="form-group">
                  <label><?php echo __('payment_method'); ?></label>
                  <input type="text" class="form-control" value="<?php echo __('pay_later'); ?>" readonly>
                  <small class="form-text text-muted"><?php echo __('marked_as_pay_later'); ?></small>
                </div>
                
                <button type="submit" name="pay" class="btn btn-primary">
                  <i class="fas fa-check"></i> <?php echo __('confirm_pay_later'); ?>
                </button>
                <a href="payments.php" class="btn btn-secondary">
                  <i class="fas fa-times"></i> <?php echo __('cancel'); ?>
                </a>
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
</body>
</html>