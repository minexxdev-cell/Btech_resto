<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Delete Customer
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $source = $_GET['source'] ?? 'pos'; // Determine which database
  
  if ($source === 'hotel' && isset($mysqli_hotel)) {
    // Delete from hotel database - customer table
    $adn = "DELETE FROM customer WHERE customer_id = ?";
    $stmt = $mysqli_hotel->prepare($adn);
  } else {
    // Delete from POS database - rpos_customers table
    $adn = "DELETE FROM rpos_customers WHERE customer_id = ?";
    $stmt = $mysqli->prepare($adn);
  }
  
  $stmt->bind_param('s', $id);
  $stmt->execute();
  $stmt->close();
  
  if ($stmt) {
    $success = "Deleted" && header("refresh:1; url=customes.php");
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
              <div class="row align-items-center">
                <div class="col-md-4">
                  <a href="add_customer.php" class="btn btn-outline-success">
                    <i class="fas fa-user-plus"></i>
                    Add New Customer
                  </a>
                </div>
                <div class="col-md-4">
                  <select id="sourceFilter" class="form-control">
                    <option value="all">All Customers</option>
                    <option value="pos">POS Customers</option>
                    <option value="hotel">Hotel Customers</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <input type="text" id="searchInput" class="form-control" placeholder="Search customers...">
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush" id="customerTable">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Full Name</th>
                    <th scope="col">Contact Number</th>
                    <th scope="col">Email</th>
                    <th scope="col">Source</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Array to store all customers
                  $allCustomers = [];
                  
                  // Get customers from POS database (rposystem.rpos_customers)
                  $ret_pos = "SELECT customer_id, customer_name, customer_phoneno, customer_email, created_at, 'pos' as source FROM rpos_customers ORDER BY created_at DESC";
                  $stmt_pos = $mysqli->prepare($ret_pos);
                  $stmt_pos->execute();
                  $res_pos = $stmt_pos->get_result();
                  
                  while ($cust = $res_pos->fetch_object()) {
                    $allCustomers[] = $cust;
                  }
                  
                  // Get customers from Hotel database (hotelms.customer) if connection exists
                  if (isset($mysqli_hotel) && $mysqli_hotel) {
                    // First check if customer table exists in hotelms database
                    $check_table = "SHOW TABLES LIKE 'customer'";
                    $table_check = $mysqli_hotel->query($check_table);
                    
                    if ($table_check && $table_check->num_rows > 0) {
                      // Get column names from customer table
                      $columns_query = "SHOW COLUMNS FROM customer";
                      $columns_result = $mysqli_hotel->query($columns_query);
                      $columns = [];
                      while ($col = $columns_result->fetch_object()) {
                        $columns[] = $col->Field;
                      }
                      
                      // Build query based on available columns
                      $select_fields = [];
                      
                      // Map common field names
                      $field_mapping = [
                        'customer_id' => ['customer_id', 'id', 'cust_id'],
                        'customer_name' => ['customer_name', 'name', 'full_name', 'cust_name'],
                        'customer_phoneno' => ['customer_phoneno', 'phone', 'phoneno', 'contact', 'phone_number'],
                        'customer_email' => ['customer_email', 'email', 'email_address'],
                        'created_at' => ['created_at', 'created', 'date_created', 'registration_date']
                      ];
                      
                      foreach ($field_mapping as $standard_name => $possible_names) {
                        $found = false;
                        foreach ($possible_names as $possible_name) {
                          if (in_array($possible_name, $columns)) {
                            $select_fields[] = "$possible_name as $standard_name";
                            $found = true;
                            break;
                          }
                        }
                        if (!$found) {
                          // Provide default value if column not found
                          $select_fields[] = "'' as $standard_name";
                        }
                      }
                      
                      $ret_hotel = "SELECT " . implode(", ", $select_fields) . ", 'hotel' as source FROM customer ORDER BY created_at DESC";
                      $stmt_hotel = $mysqli_hotel->prepare($ret_hotel);
                      
                      if ($stmt_hotel) {
                        $stmt_hotel->execute();
                        $res_hotel = $stmt_hotel->get_result();
                        
                        while ($cust = $res_hotel->fetch_object()) {
                          $allCustomers[] = $cust;
                        }
                      }
                    }
                  }
                  
                  // Sort all customers by created_at descending
                  usort($allCustomers, function($a, $b) {
                    return strtotime($b->created_at) - strtotime($a->created_at);
                  });
                  
                  // Display all customers
                  foreach ($allCustomers as $cust) {
                    $sourceBadge = $cust->source === 'hotel' 
                      ? '<span class="badge badge-info">Hotel</span>' 
                      : '<span class="badge badge-primary">POS</span>';
                  ?>
                    <tr data-source="<?php echo $cust->source; ?>">
                      <td><?php echo $cust->customer_name; ?></td>
                      <td><?php echo $cust->customer_phoneno; ?></td>
                      <td><?php echo $cust->customer_email; ?></td>
                      <td><?php echo $sourceBadge; ?></td>
                      <td>
                        <a href="customes.php?delete=<?php echo $cust->customer_id; ?>&source=<?php echo $cust->source; ?>" 
                           onclick="return confirm('Are you sure you want to delete this customer?');">
                          <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete
                          </button>
                        </a>

                        <a href="update_customer.php?update=<?php echo $cust->customer_id; ?>&source=<?php echo $cust->source; ?>">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-user-edit"></i>
                            Update
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
  
  <!-- Search and Filter Script -->
  <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
      filterTable();
    });
    
    // Source filter functionality
    document.getElementById('sourceFilter').addEventListener('change', function() {
      filterTable();
    });
    
    function filterTable() {
      var searchInput = document.getElementById('searchInput').value.toLowerCase();
      var sourceFilter = document.getElementById('sourceFilter').value;
      var table = document.getElementById('customerTable');
      var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
      
      for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var name = row.getElementsByTagName('td')[0].textContent.toLowerCase();
        var phone = row.getElementsByTagName('td')[1].textContent.toLowerCase();
        var email = row.getElementsByTagName('td')[2].textContent.toLowerCase();
        var source = row.getAttribute('data-source');
        
        // Check search criteria
        var matchesSearch = name.indexOf(searchInput) > -1 || 
                           phone.indexOf(searchInput) > -1 || 
                           email.indexOf(searchInput) > -1;
        
        // Check source filter
        var matchesSource = sourceFilter === 'all' || source === sourceFilter;
        
        // Show row if it matches both criteria
        if (matchesSearch && matchesSource) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      }
    }
  </script>
</body>

</html>