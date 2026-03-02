<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $adn = "DELETE FROM  rpos_products  WHERE  prod_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('s', $id);
  $stmt->execute();
  $stmt->close();
  if ($stmt) {
    $success = "Deleted" && header("refresh:1; url=products.php");
  } else {
    $err = "Try Again Later";
  }
}

// Get low stock products from inventory database
$low_stock_products = [];
$stock_query = "SELECT nama_produk, stok FROM produk WHERE stok <= 1";
$stock_result = $mysqli_inventory->query($stock_query);
if ($stock_result) {
  while ($row = $stock_result->fetch_assoc()) {
    $low_stock_products[] = $row;
  }
}

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
      
      <!-- Low Stock Notifications -->
      <?php if (count($low_stock_products) > 0): ?>
      <div class="row mb-4">
        <div class="col">
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <span class="alert-text">
              <strong><?php echo __('low_stock_alert'); ?></strong><br>
              <?php echo __('running_low_on_stock'); ?>
              <ul class="mt-2 mb-0">
                <?php foreach ($low_stock_products as $product): ?>
                  <li>
                    <strong><?php echo htmlspecialchars($product['nama_produk']); ?></strong> 
                    - <?php echo __('quantity'); ?>: <span class="badge badge-danger"><?php echo $product['stok']; ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            </span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col">
                  <a href="add_product.php" class="btn btn-outline-success">
                    <i class="fas fa-utensils"></i>
                    <?php echo __('add_new_product'); ?>
                  </a>
                  <?php if (count($low_stock_products) > 0): ?>
                  <span class="badge badge-warning badge-pill ml-2">
                    <i class="fas fa-exclamation-circle"></i> <?php echo count($low_stock_products); ?> <?php echo __('low_stock_items'); ?>
                  </span>
                  <?php endif; ?>
                </div>
                <div class="col">
                  <input type="text" id="searchInput" class="form-control" placeholder="<?php echo __('search_products'); ?>">
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush" id="productTable">
                <thead class="thead-light">
                  <tr>
                    <th scope="col"><?php echo __('image'); ?></th>
                    <th scope="col"><?php echo __('product_code'); ?></th>
                    <th scope="col"><?php echo __('name'); ?></th>
                    <th scope="col"><?php echo __('price'); ?></th>
                    <th scope="col"><?php echo __('actions'); ?></th>
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
                        <?php
                        if ($prod->prod_img) {
                          echo "<img src='assets/img/products/$prod->prod_img' height='60' width='60 class='img-thumbnail'>";
                        } else {
                          echo "<img src='assets/img/products/default.jpg' height='60' width='60 class='img-thumbnail'>";
                        }
                        ?>
                      </td>
                      <td><?php echo $prod->prod_code; ?></td>
                      <td><?php echo $prod->prod_name; ?></td>
                      <td> <?php echo $prod->prod_price; ?> Rwf</td>
                      <td>
                        <a href="products.php?delete=<?php echo $prod->prod_id; ?>">
                          <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                            <?php echo __('delete'); ?>
                          </button>
                        </a>

                        <a href="update_product.php?update=<?php echo $prod->prod_id; ?>">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                            <?php echo __('update'); ?>
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
  
  <!-- Search Script -->
  <script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
      var input = this.value.toLowerCase();
      var table = document.getElementById('productTable');
      var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
      
      for (var i = 0; i < rows.length; i++) {
        var code = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
        var name = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
        var price = rows[i].getElementsByTagName('td')[3].textContent.toLowerCase();
        
        if (code.indexOf(input) > -1 || name.indexOf(input) > -1 || price.indexOf(input) > -1) {
          rows[i].style.display = '';
        } else {
          rows[i].style.display = 'none';
        }
      }
    });
  </script>
</body>

</html>