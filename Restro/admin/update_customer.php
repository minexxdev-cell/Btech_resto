<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

// Get source from URL parameter
$source = isset($_GET['source']) ? $_GET['source'] : 'pos';

// Update Customer
if (isset($_POST['updateCustomer'])) {
  // Prevent Posting Blank Values
  if (empty($_POST["customer_name"]) || empty($_POST["contact_no"]) || empty($_POST['email'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $customer_name = $_POST['customer_name'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $id_card_type_id = $_POST['id_card_type_id'];
    $id_card_no = $_POST['id_card_no'];
    $update = $_GET['update'];
    $source = $_GET['source'];

    if ($source === 'hotel' && isset($mysqli_hotel)) {
      // Update in hotel database - customer table
      $postQuery = "UPDATE customer SET customer_name =?, contact_no =?, email =?, address =?, id_card_type_id =?, id_card_no =? WHERE customer_id =?";
      $postStmt = $mysqli_hotel->prepare($postQuery);
      $rc = $postStmt->bind_param('sssssss', $customer_name, $contact_no, $email, $address, $id_card_type_id, $id_card_no, $update);
    } else {
      // Update in POS database - rpos_customers table
      // Map hotel fields to POS fields
      $customer_phoneno = $contact_no;
      $customer_email = $email;
      
      // POS table might have password field, keep it if exists
      if (!empty($_POST['customer_password'])) {
        $customer_password = sha1(md5($_POST['customer_password']));
        $postQuery = "UPDATE rpos_customers SET customer_name =?, customer_phoneno =?, customer_email =?, customer_password =? WHERE customer_id =?";
        $postStmt = $mysqli->prepare($postQuery);
        $rc = $postStmt->bind_param('sssss', $customer_name, $customer_phoneno, $customer_email, $customer_password, $update);
      } else {
        $postQuery = "UPDATE rpos_customers SET customer_name =?, customer_phoneno =?, customer_email =? WHERE customer_id =?";
        $postStmt = $mysqli->prepare($postQuery);
        $rc = $postStmt->bind_param('ssss', $customer_name, $customer_phoneno, $customer_email, $update);
      }
    }
    
    $postStmt->execute();
    
    if ($postStmt) {
      $success = "Customer Updated" && header("refresh:1; url=customes.php");
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
    
    $update = $_GET['update'];
    $source = isset($_GET['source']) ? $_GET['source'] : 'pos';
    
    // Fetch customer data based on source
    if ($source === 'hotel' && isset($mysqli_hotel)) {
      $ret = "SELECT * FROM customer WHERE customer_id = ?";
      $stmt = $mysqli_hotel->prepare($ret);
    } else {
      $ret = "SELECT * FROM rpos_customers WHERE customer_id = ?";
      $stmt = $mysqli->prepare($ret);
    }
    
    $stmt->bind_param('s', $update);
    $stmt->execute();
    $res = $stmt->get_result();
    
    while ($cust = $res->fetch_object()) {
      // Map fields for compatibility
      $customer_name = $cust->customer_name ?? '';
      $contact_no = $cust->contact_no ?? ($cust->customer_phoneno ?? '');
      $email = $cust->email ?? ($cust->customer_email ?? '');
      $address = $cust->address ?? '';
      $id_card_type_id = $cust->id_card_type_id ?? '';
      $id_card_no = $cust->id_card_no ?? '';
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
                <h3>Update Customer Information</h3>
                <p class="text-muted mb-0">
                  Source: 
                  <?php 
                  if ($source === 'hotel') {
                    echo '<span class="badge badge-info">Hotel Database</span>';
                  } else {
                    echo '<span class="badge badge-primary">POS Database</span>';
                  }
                  ?>
                </p>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Customer Name <span class="text-danger">*</span></label>
                      <input type="text" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                      <label>Contact Number <span class="text-danger">*</span></label>
                      <input type="text" name="contact_no" value="<?php echo htmlspecialchars($contact_no); ?>" class="form-control" required>
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Email <span class="text-danger">*</span></label>
                      <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" required>
                    </div>
                    <?php if ($source === 'pos') { ?>
                      <div class="col-md-6">
                        <label>Password <small class="text-muted">(Leave blank to keep current)</small></label>
                        <input type="password" name="customer_password" class="form-control" placeholder="Enter new password">
                      </div>
                    <?php } else { ?>
                      <div class="col-md-6">
                        <label>ID Card Type</label>
                        <select name="id_card_type_id" class="form-control">
                          <option value="">Select ID Type</option>
                          <option value="1" <?php echo ($id_card_type_id == '1') ? 'selected' : ''; ?>>National ID</option>
                          <option value="2" <?php echo ($id_card_type_id == '2') ? 'selected' : ''; ?>>Passport</option>
                          <option value="3" <?php echo ($id_card_type_id == '3') ? 'selected' : ''; ?>>Driver's License</option>
                          <option value="4" <?php echo ($id_card_type_id == '4') ? 'selected' : ''; ?>>Other</option>
                        </select>
                      </div>
                    <?php } ?>
                  </div>
                  <hr>
                  <?php if ($source === 'hotel') { ?>
                    <div class="form-row">
                      <div class="col-md-6">
                        <label>ID Card Number</label>
                        <input type="text" name="id_card_no" value="<?php echo htmlspecialchars($id_card_no); ?>" class="form-control">
                      </div>
                      <div class="col-md-6">
                        <label>Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" class="form-control">
                      </div>
                    </div>
                  <?php } else { ?>
                    <input type="hidden" name="address" value="">
                    <input type="hidden" name="id_card_type_id" value="">
                    <input type="hidden" name="id_card_no" value="">
                  <?php } ?>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="updateCustomer" value="Update Customer" class="btn btn-success btn-block">
                    </div>
                    <div class="col-md-6">
                      <a href="customes.php" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Customers
                      </a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
      <?php
      require_once('partials/_footer.php');
    }
      ?>
      </div>
  </div>
  
  <!-- Argon Scripts -->
  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>