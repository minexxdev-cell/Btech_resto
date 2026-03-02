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
    $customer_source = isset($_POST['customer_source']) ? $_POST['customer_source'] : 'POS';
    $order_status = 'Pending';
    
    // Check if customer is from hotel database and doesn't exist in POS database
    if ($customer_source === 'Hotel') {
      $checkQuery = "SELECT customer_id FROM rpos_customers WHERE customer_id = ?";
      $checkStmt = $mysqli->prepare($checkQuery);
      $checkStmt->bind_param('s', $customer_id);
      $checkStmt->execute();
      $checkResult = $checkStmt->get_result();
      
      if ($checkResult->num_rows == 0) {
        if (isset($mysqli_hotel) && $mysqli_hotel) {
          $getCustomerQuery = "SELECT * FROM customer WHERE customer_id = ?";
          $getCustomerStmt = $mysqli_hotel->prepare($getCustomerQuery);
          $getCustomerStmt->bind_param('s', $customer_id);
          $getCustomerStmt->execute();
          $customerData = $getCustomerStmt->get_result()->fetch_object();
          
          if ($customerData) {
            $insertCustomerQuery = "INSERT INTO rpos_customers (customer_id, customer_name, customer_phoneno, customer_email, customer_password) VALUES (?, ?, ?, ?, ?)";
            $insertCustomerStmt = $mysqli->prepare($insertCustomerQuery);
            
            $customer_phoneno = isset($customerData->contact_no) ? $customerData->contact_no : '';
            $customer_email = isset($customerData->email) ? $customerData->email : '';
            $default_password = sha1(md5('password123'));
            
            $insertCustomerStmt->bind_param('sssss', $customer_id, $customer_name, $customer_phoneno, $customer_email, $default_password);
            $insertCustomerStmt->execute();
          }
        }
      }
    }
    
    $allSuccess = true;
    $stockErrors = [];
    $products = json_decode($_POST['products_data'], true);
    
    // First, validate stock availability for all products
    foreach ($products as $index => $product) {
      $prod_qty = $_POST['prod_qty_' . $index];
      
      if (!empty($prod_qty) && $prod_qty > 0) {
        $prod_id = $product['prod_id'];
        
        // Get product code from rpos_products
        $getProdCodeQuery = "SELECT prod_code FROM rpos_products WHERE prod_id = ?";
        $getProdCodeStmt = $mysqli->prepare($getProdCodeQuery);
        $getProdCodeStmt->bind_param('s', $prod_id);
        $getProdCodeStmt->execute();
        $prodCodeResult = $getProdCodeStmt->get_result();
        
        if ($prodCodeResult->num_rows > 0) {
          $prodData = $prodCodeResult->fetch_object();
          $prod_code = $prodData->prod_code;
          
          // Check stock in inventory database
          if (isset($mysqli_inventory) && $mysqli_inventory) {
            $checkStockQuery = "SELECT stok FROM produk WHERE kode_produk = ?";
            $checkStockStmt = $mysqli_inventory->prepare($checkStockQuery);
            $checkStockStmt->bind_param('s', $prod_code);
            $checkStockStmt->execute();
            $stockResult = $checkStockStmt->get_result();
            
            if ($stockResult->num_rows > 0) {
              $stockData = $stockResult->fetch_object();
              $available_stock = $stockData->stok;
              
              if ($available_stock < $prod_qty) {
                $stockErrors[] = "Insufficient stock for " . $product['prod_name'] . ". Available: " . $available_stock . ", Requested: " . $prod_qty;
                $allSuccess = false;
              }
            }
          }
        }
      }
    }
    
    // If stock validation fails, show errors
    if (!empty($stockErrors)) {
      $err = implode("<br>", $stockErrors);
    } else {
      // Proceed with order creation and stock updates
      foreach ($products as $index => $product) {
        $prod_qty = $_POST['prod_qty_' . $index];
        
        if (!empty($prod_qty) && $prod_qty > 0) {
          $order_id = $alpha . '-' . $beta . '-' . uniqid();
          $prod_id = $product['prod_id'];
          $prod_name = $product['prod_name'];
          $prod_price = $product['prod_price'];
          $done_by = $_SESSION['admin_email'];
          
          // Insert order
          $postQuery = "INSERT INTO rpos_orders (prod_qty, order_id, order_code, customer_id, customer_name, prod_id, prod_name, prod_price, order_status, Done_by) VALUES(?,?,?,?,?,?,?,?,?,?)";
          $postStmt = $mysqli->prepare($postQuery);
          $rc = $postStmt->bind_param('ssssssssss', $prod_qty, $order_id, $order_code, $customer_id, $customer_name, $prod_id, $prod_name, $prod_price, $order_status, $done_by);
          $postStmt->execute();
          
          if ($postStmt) {
            // Update inventory stock
            $getProdCodeQuery = "SELECT prod_code FROM rpos_products WHERE prod_id = ?";
            $getProdCodeStmt = $mysqli->prepare($getProdCodeQuery);
            $getProdCodeStmt->bind_param('s', $prod_id);
            $getProdCodeStmt->execute();
            $prodCodeResult = $getProdCodeStmt->get_result();
            
            if ($prodCodeResult->num_rows > 0) {
              $prodData = $prodCodeResult->fetch_object();
              $prod_code = $prodData->prod_code;
              
              // Update stock in inventory database
              if (isset($mysqli_inventory) && $mysqli_inventory) {
                $updateStockQuery = "UPDATE produk SET stok_keluar = stok_keluar + ?,stok=stok -?,stok_akhir=stok_akhir - ?, updated_at = NOW() WHERE kode_produk = ?";
                $updateStockStmt = $mysqli_inventory->prepare($updateStockQuery);
                $updateStockStmt->bind_param('isss', $prod_qty, $prod_qty, $prod_qty, $prod_code);
                $updateStockStmt->execute();
                
                if (!$updateStockStmt) {
                  $allSuccess = false;
                  error_log("Failed to update stock for product: " . $prod_code);
                }
              }
            }
          } else {
            $allSuccess = false;
          }
        }
      }
      
      if ($allSuccess) {
        $success = "Orders Submitted Successfully and Stock Updated";
        $_SESSION['last_order_code'] = $order_code;
        echo "<script>
            setTimeout(function() {
                window.open('print_order.php?order_code=" . $order_code . "', '_blank');
                window.location.href = 'print_order.php?order_code=" . $order_code . "';
            }, 1000);
        </script>";
      } else {
        $err = "Some orders or stock updates failed. Please try again.";
      }
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
              
              <form method="POST" id="multiOrderForm" onsubmit="return handleFormSubmit(event)">
                <div class="form-row">
                  <div class="col-md-4">
                    <label>Customer Name</label>
                    <select class="form-control" name="customer_name" id="custName" onChange="getCustomer(this.value)" required>
                      <option value="">Select Customer Name</option>
                      <?php
                      $ret = "SELECT customer_id, customer_name, 'POS' as source FROM rpos_customers ORDER BY customer_name";
                      $stmt = $mysqli->prepare($ret);
                      $stmt->execute();
                      $res = $stmt->get_result();
                      
                      $allCustomers = [];
                      while ($cust = $res->fetch_object()) {
                        $allCustomers[] = $cust;
                      }
                      
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
                      
                      usort($allCustomers, function($a, $b) {
                        return strcmp($a->customer_name, $b->customer_name);
                      });
                      
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
      
      document.querySelectorAll('.qty-input').forEach(function(input) {
        input.addEventListener('input', calculateTotal);
      });
      
      calculateTotal();
    });

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

    function handleFormSubmit(event) {
      var orderCode = document.querySelector('input[name="order_code"]').value;
      sessionStorage.setItem('pendingOrderCode', orderCode);
      return true;
    }

    <?php if (isset($openPrintAndRedirect) && $openPrintAndRedirect) { ?>
      var printWindow = window.open('print_order.php?order_code=<?php echo $order_code; ?>', '_blank');
      
      setTimeout(function() {
        sessionStorage.removeItem('pendingOrderCode');
        window.location.href = 'payments.php';
      }, 500);
    <?php } ?>
  </script>
</body>
</html>