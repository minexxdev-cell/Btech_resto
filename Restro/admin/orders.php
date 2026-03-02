<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php
  require_once('partials/_sidebar.php');
  ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php
    require_once('partials/_topnav.php');
    ?>
    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
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
                <div class="col-md-4">
                  <h3>Select Products To Order</h3>
                </div>
                <div class="col-md-4">
                  <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
                </div>
                <div class="col-md-4 text-right">
                  <button class="btn btn-success" id="orderSelectedBtn" style="display:none;">
                    <i class="fas fa-shopping-cart"></i> Order Selected (<span id="selectedCount">0</span>)
                  </button>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush" id="productTable">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="selectAll">
                        <label class="custom-control-label" for="selectAll"></label>
                      </div>
                    </th>
                    <th scope="col"><b>Image</b></th>
                    <th scope="col"><b>Product Code</b></th>
                    <th scope="col"><b>Name</b></th>
                    <th scope="col"><b>Price</b></th>
                    <th scope="col"><b>Action</b></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT * FROM  rpos_products ";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($prod = $res->fetch_object()) {
                  ?>
                    <tr>
                      <td>
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input product-checkbox" 
                                 id="prod_<?php echo $prod->prod_id; ?>"
                                 data-prod-id="<?php echo $prod->prod_id; ?>"
                                 data-prod-name="<?php echo htmlspecialchars($prod->prod_name); ?>"
                                 data-prod-price="<?php echo $prod->prod_price; ?>"
                                 data-prod-code="<?php echo $prod->prod_code; ?>">
                          <label class="custom-control-label" for="prod_<?php echo $prod->prod_id; ?>"></label>
                        </div>
                      </td>
                      <td>
                        <?php
                        if ($prod->prod_img) {
                          echo "<img src='assets/img/products/$prod->prod_img' height='60' width='60' class='img-thumbnail'>";
                        } else {
                          echo "<img src='assets/img/products/default.jpg' height='60' width='60' class='img-thumbnail'>";
                        }
                        ?>
                      </td>
                      <td><?php echo $prod->prod_code; ?></td>
                      <td><?php echo $prod->prod_name; ?></td>
                      <td><?php echo $prod->prod_price; ?> Rwf</td>
                      <td>
                        <a href="make_oder.php?prod_id=<?php echo $prod->prod_id; ?>&prod_name=<?php echo $prod->prod_name; ?>&prod_price=<?php echo $prod->prod_price; ?>">
                          <button class="btn btn-sm btn-warning">
                            <i class="fas fa-cart-plus"></i>
                            Single Order
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
      <?php
      require_once('partials/_footer.php');
      ?>
    </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
  
  <!-- Search and Selection Script -->
  <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
      var input = this.value.toLowerCase();
      var table = document.getElementById('productTable');
      var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
      
      for (var i = 0; i < rows.length; i++) {
        var code = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
        var name = rows[i].getElementsByTagName('td')[3].textContent.toLowerCase();
        var price = rows[i].getElementsByTagName('td')[4].textContent.toLowerCase();
        
        if (code.indexOf(input) > -1 || name.indexOf(input) > -1 || price.indexOf(input) > -1) {
          rows[i].style.display = '';
        } else {
          rows[i].style.display = 'none';
        }
      }
    });

    // Select all checkbox functionality
    document.getElementById('selectAll').addEventListener('change', function() {
      var checkboxes = document.querySelectorAll('.product-checkbox');
      checkboxes.forEach(function(checkbox) {
        if (checkbox.closest('tr').style.display !== 'none') {
          checkbox.checked = this.checked;
        }
      }.bind(this));
      updateSelectedCount();
    });

    // Individual checkbox change
    document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
      checkbox.addEventListener('change', updateSelectedCount);
    });

    // Update selected count and button visibility
    function updateSelectedCount() {
      var checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
      var count = checkedBoxes.length;
      document.getElementById('selectedCount').textContent = count;
      
      var btn = document.getElementById('orderSelectedBtn');
      if (count > 0) {
        btn.style.display = 'inline-block';
      } else {
        btn.style.display = 'none';
      }
    }

    // Order selected products
    document.getElementById('orderSelectedBtn').addEventListener('click', function() {
      var selectedProducts = [];
      var checkboxes = document.querySelectorAll('.product-checkbox:checked');
      
      checkboxes.forEach(function(checkbox) {
        selectedProducts.push({
          prod_id: checkbox.getAttribute('data-prod-id'),
          prod_name: checkbox.getAttribute('data-prod-name'),
          prod_price: checkbox.getAttribute('data-prod-price'),
          prod_code: checkbox.getAttribute('data-prod-code')
        });
      });
      
      if (selectedProducts.length > 0) {
        // Store in session storage and redirect
        sessionStorage.setItem('selectedProducts', JSON.stringify(selectedProducts));
        window.location.href = 'make_multiple_orders.php';
      }
    });
  </script>
</body>

</html>