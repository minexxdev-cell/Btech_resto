<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['make_multiple'])) {
  if (empty($_POST["order_code"]) || empty($_POST["customer_name"])) {
    $err = "Blank Values Not Accepted";
  } else {
    $order_code = $_POST['order_code'];
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $customer_source = isset($_POST['customer_source']) ? $_POST['customer_source'] : 'POS'; // Get customer source with default
    $order_status = 'Pending';
    
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
    
    $allSuccess = true;
    $products = json_decode($_POST['products_data'], true);
    
    foreach ($products as $index => $product) {
      $prod_qty = $_POST['prod_qty_' . $index];
      
      if (!empty($prod_qty) && $prod_qty > 0) {
        $order_id = $alpha . '-' . $beta . '-' . uniqid();
        $prod_id = $product['prod_id'];
        $prod_name = $product['prod_name'];
        $prod_price = $product['prod_price'];
        $done_by=$_SESSION['staff_email'];
        
        $postQuery = "INSERT INTO rpos_orders (prod_qty, order_id, order_code, customer_id, customer_name, prod_id, prod_name, prod_price, order_status,Done_by) VALUES(?,?,?,?,?,?,?,?,?,?)";
        $postStmt = $mysqli->prepare($postQuery);
        $rc = $postStmt->bind_param('ssssssssss', $prod_qty, $order_id, $order_code, $customer_id, $customer_name, $prod_id, $prod_name, $prod_price, $order_status,$done_by);
        $postStmt->execute();
        
        if (!$postStmt) {
          $allSuccess = false;
        }
      }
    }
    
if ($allSuccess) {
    $success = "Orders Submitted Successfully";
    // Store order code in session for the print page
    $_SESSION['last_order_code'] = $order_code;
    echo "<script>
        setTimeout(function() {
            window.open('print_order.php?order_code=" . $order_code . "', '_blank');
            window.location.href = 'print_order.php?order_code=" . $order_code . "';
        }, 1000);
    </script>";
}
    else {
          $err = "Some orders failed. Please try again.";
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
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Place Multiple Orders</h3>
            </div>
            <div class="card-body">
              <?php if (isset($success)) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
              <?php } ?>
              <?php if (isset($err)) { ?>
                <div class="alert alert-danger"><?php echo $err; ?></div>
              <?php } ?>
              
              <form method="POST" id="multiOrderForm"  onsubmit="return handleFormSubmit(event)">
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
                <h4>Selected Products</h4>
                <div id="productsContainer"></div>
                
                <input type="hidden" name="products_data" id="productsData">
                
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <button type="submit" name="make_multiple" class="btn btn-success">
                      <i class="fas fa-check"></i> Place All Orders
                    </button>
                    <a href="orders.php" class="btn btn-secondary">
                      <i class="fas fa-arrow-left"></i> Back
                    </a>
                  </div>
                  <div class="col-md-6 text-right">
                    <h4>Total: <span id="totalAmount">0</span> Rwf</h4>
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
    // Load selected products from session storage
    window.addEventListener('DOMContentLoaded', function() {
      var productsData = sessionStorage.getItem('selectedProducts');
      
      if (!productsData) {
        alert('No products selected. Redirecting...');
        window.location.href = 'orders.php';
        return;
      }
      
      var products = JSON.parse(productsData);
      document.getElementById('productsData').value = productsData;
      
      var container = document.getElementById('productsContainer');
      var html = '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Product</th><th>Price (Rwf)</th><th>Quantity</th><th>Subtotal</th></tr></thead><tbody>';
      
      products.forEach(function(product, index) {
        html += '<tr>';
        html += '<td>' + product.prod_name + '<br><small class="text-muted">' + product.prod_code + '</small></td>';
        html += '<td>' + product.prod_price + ' Rwf</td>';
        html += '<td><input type="number" name="prod_qty_' + index + '" class="form-control qty-input" data-price="' + product.prod_price + '" min="1" value="1" required></td>';
        html += '<td class="subtotal">' + product.prod_price + ' Rwf</td>';
        html += '</tr>';
      });
      
      html += '</tbody></table></div>';
      container.innerHTML = html;
      
      // Add event listeners for quantity changes
      document.querySelectorAll('.qty-input').forEach(function(input) {
        input.addEventListener('input', calculateTotal);
      });
      
      calculateTotal();
    });

    // Calculate total amount
    function calculateTotal() {
      var total = 0;
      document.querySelectorAll('.qty-input').forEach(function(input) {
        var qty = parseInt(input.value) || 0;
        var price = parseFloat(input.getAttribute('data-price'));
        var subtotal = qty * price;
        
        input.closest('tr').querySelector('.subtotal').textContent = subtotal.toFixed(2) + ' Rwf';
        total += subtotal;
      });
      
      document.getElementById('totalAmount').textContent = total.toFixed(2);
    }

    // Get customer details
    function getCustomer(customerName) {
      if (customerName == "") {
        document.getElementById("customerID").value = "";
        document.getElementById("customerSourceHidden").value = "";
        return;
      }
      
      var selectElement = document.getElementById("custName");
      var selectedOption = selectElement.options[selectElement.selectedIndex];
      var customerId = selectedOption.getAttribute("data-customer-id");
      var customerSource = selectedOption.getAttribute("data-source");
      
      document.getElementById("customerID").value = customerId;
      document.getElementById("customerSourceHidden").value = customerSource;
    }

    // Handle form submission
    function handleFormSubmit(event) {
      // Store the order code for later use
      var orderCode = document.querySelector('input[name="order_code"]').value;
      sessionStorage.setItem('pendingOrderCode', orderCode);
      
      // Let the form submit normally
      return true;
    }

    // Check if order was successful and handle redirect/print
    <?php if (isset($openPrintAndRedirect) && $openPrintAndRedirect) { ?>
      // Open print page in new tab (this happens right after page reload from successful submission)
      var printWindow = window.open('print_order.php?order_code=<?php echo $order_code; ?>', '_blank');
      
      // Redirect current page to payments
      setTimeout(function() {
        // Clear the pending order code
        sessionStorage.removeItem('pendingOrderCode');
        window.location.href = 'payments.php';
      }, 500);
    <?php } ?>
  </script>
</body>
</html>