<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
if (isset($_POST['make'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["order_code"]) || empty($_POST["customer_name"]) || empty($_GET['prod_price'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $order_id = $_POST['order_id'];
    $order_code  = $_POST['order_code'];
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $customer_source = isset($_POST['customer_source']) ? $_POST['customer_source'] : 'POS'; // Get customer source with default
    $prod_id  = $_GET['prod_id'];
    $prod_name = $_GET['prod_name'];
    $prod_price = $_GET['prod_price'];
    $prod_qty = $_POST['prod_qty'];
    $order_status = 'Pending'; // Set default order status

    // Check if customer is from hotel database and doesn't exist in POS database
    if ($customer_source === 'Hotel') {
      // Check if customer exists in rpos_customers
      $checkQuery = "SELECT customer_id FROM rpos_customers WHERE customer_id = ?";
      $checkStmt = $mysqli->prepare($checkQuery);
      $checkStmt->bind_param('s', $customer_id);
      $checkStmt->execute();
      $checkResult = $checkStmt->get_result();
      
      if ($checkResult->num_rows == 0) {
        // Customer doesn't exist in POS database, fetch from hotel database and insert
        if (isset($mysqli_hotel) && $mysqli_hotel) {
          $getCustomerQuery = "SELECT * FROM customer WHERE customer_id = ?";
          $getCustomerStmt = $mysqli_hotel->prepare($getCustomerQuery);
          $getCustomerStmt->bind_param('s', $customer_id);
          $getCustomerStmt->execute();
          $customerData = $getCustomerStmt->get_result()->fetch_object();
          
          if ($customerData) {
            // Insert hotel customer into POS database
            $insertCustomerQuery = "INSERT INTO rpos_customers (customer_id, customer_name, customer_phoneno, customer_email, customer_password) VALUES (?, ?, ?, ?, ?)";
            $insertCustomerStmt = $mysqli->prepare($insertCustomerQuery);
            
            $customer_phoneno = isset($customerData->contact_no) ? $customerData->contact_no : '';
            $customer_email = isset($customerData->email) ? $customerData->email : '';
            $default_password = sha1(md5('password123')); // Default password
            
            $insertCustomerStmt->bind_param('sssss', $customer_id, $customer_name, $customer_phoneno, $customer_email, $default_password);
            $insertCustomerStmt->execute();
          }
        }
      }
    }

    //Insert Captured information to a database table
    $postQuery = "INSERT INTO rpos_orders (prod_qty, order_id, order_code, customer_id, customer_name, prod_id, prod_name, prod_price, order_status) VALUES(?,?,?,?,?,?,?,?,?)";
    $postStmt = $mysqli->prepare($postQuery);
    //bind paramaters
    $rc = $postStmt->bind_param('sssssssss', $prod_qty, $order_id, $order_code, $customer_id, $customer_name, $prod_id, $prod_name, $prod_price, $order_status);
    $postStmt->execute();
    //declare a varible which will be passed to alert function
    if ($postStmt) {
      $success = "Order Submitted" && header("refresh:1; url=payments.php");
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
              <h3>Please Fill All Fields</h3>
            </div>
            <div class="card-body">
              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">

                  <div class="col-md-4">
                    <label>Customer Name</label>
                    <select class="form-control" name="customer_name" id="custName" onChange="getCustomer(this.value)" required>
                      <option value="">Select Customer Name</option>
                      <?php
                      // Get customers from POS database
                      $ret = "SELECT customer_id, customer_name, 'POS' as source FROM rpos_customers ORDER BY customer_name";
                      $stmt = $mysqli->prepare($ret);
                      $stmt->execute();
                      $res = $stmt->get_result();
                      
                      $allCustomers = [];
                      while ($cust = $res->fetch_object()) {
                        $allCustomers[] = $cust;
                      }
                      
                      // Get customers from Hotel database if available
                      if (isset($mysqli_hotel) && $mysqli_hotel) {
                        $ret_hotel = "SELECT customer_id, customer_name, 'Hotel' as source FROM customer ORDER BY customer_name";
                        $stmt_hotel = $mysqli_hotel->prepare($ret_hotel);
                        if ($stmt_hotel) {
                          $stmt_hotel->execute();
                          $res_hotel = $stmt_hotel->get_result();
                          while ($cust = $res_hotel->fetch_object()) {
                            $allCustomers[] = $cust;
                          }
                        }
                      }
                      
                      // Sort all customers by name
                      usort($allCustomers, function($a, $b) {
                        return strcmp($a->customer_name, $b->customer_name);
                      });
                      
                      // Display all customers
                      foreach ($allCustomers as $cust) {
                        $displayName = $cust->customer_name . ' (' . $cust->source . ')';
                      ?>
                        <option value="<?php echo $cust->customer_name; ?>" 
                                data-customer-id="<?php echo $cust->customer_id; ?>"
                                data-source="<?php echo $cust->source; ?>">
                          <?php echo $displayName; ?>
                        </option>
                      <?php } ?>
                    </select>
                    <input type="hidden" name="order_id" value="<?php echo $orderid; ?>" class="form-control">
                    <input type="hidden" name="customer_source" id="customerSourceHidden" value="">
                  </div>

                  <div class="col-md-4">
                    <label>Customer ID</label>
                    <input type="text" name="customer_id" readonly id="customerID" class="form-control" required>
                   
                  </div>

                  <div class="col-md-4">
                    <label>Order Code</label>
                    <input type="text" name="order_code" value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control" readonly>
                  </div>
                </div>
                <hr>
                <?php
                $prod_id = $_GET['prod_id'];
                $ret = "SELECT * FROM rpos_products WHERE prod_id = '$prod_id'";
                $stmt = $mysqli->prepare($ret);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($prod = $res->fetch_object()) {
                ?>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Product Price (Rwf)</label>
                      <input type="text" readonly name="prod_price" value="<?php echo $prod->prod_price; ?> Rwf" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Product Quantity</label>
                      <input type="number" name="prod_qty" class="form-control" min="1" value="1" required>
                    </div>
                  </div>
                <?php } ?>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="make" value="Make Order" class="btn btn-success btn-block">
                  </div>
                  <div class="col-md-6">
                    <a href="orders.php" class="btn btn-secondary btn-block">
                      <i class="fas fa-arrow-left"></i> Back
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
  
  <script>
    // Function to get customer details when name is selected
    function getCustomer(customerName) {
      if (customerName == "") {
        document.getElementById("customerID").value = "";
        document.getElementById("customerSource").textContent = "";
        document.getElementById("customerSourceHidden").value = "";
        return;
      }
      
      // Get the selected option element
      var selectElement = document.getElementById("custName");
      var selectedOption = selectElement.options[selectElement.selectedIndex];
      
      // Get the customer ID and source from the data attributes
      var customerId = selectedOption.getAttribute("data-customer-id");
      var customerSource = selectedOption.getAttribute("data-source");
      
      // Set the customer ID in the readonly field
      document.getElementById("customerID").value = customerId;
      
      // Set the hidden source field
      document.getElementById("customerSourceHidden").value = customerSource;
      
      // Display the source with appropriate badge
      var sourceBadge = customerSource === 'Hotel' 
        ? '<span class="badge badge-info">Hotel</span>' 
        : '<span class="badge badge-primary">POS</span>';
      document.getElementById("customerSource").innerHTML = sourceBadge;
    }
  </script>
</body>

</html>