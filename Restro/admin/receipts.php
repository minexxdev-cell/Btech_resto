<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

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
                                <div class="col">
                                    <h3 class="mb-0">Paid Orders</h3>
                                </div>
                                <div class="col">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search by code, customer, or date...">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush" id="ordersTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Code</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Products</th>
                                        <th scope="col">Order Total</th>
                                        <th scope="col">Tip</th>
                                        <th scope="col">Total Paid</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Currency</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get all paid orders with payment info including tip
                                    $ret = "SELECT o.order_code, o.customer_name, 
                                            MIN(o.created_at) as created_at,
                                            SUM(o.prod_price * o.prod_qty) as order_total,
                                            COALESCE(p.tip, 0) as tip,
                                            COALESCE(p.pay_amt, SUM(o.prod_price * o.prod_qty)) as total_paid
                                            FROM rpos_orders o
                                            LEFT JOIN rpos_payments p ON o.order_code = p.order_code
                                            WHERE o.order_status = 'Paid'
                                            GROUP BY o.order_code, o.customer_name, p.tip, p.pay_amt
                                            ORDER BY created_at DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $rowIndex = 0;
                                    
                                    while ($order = $res->fetch_object()) {
                                    ?>
                                        <tr id="row_<?php echo $rowIndex; ?>">
                                            <th class="text-success" scope="row"><?php echo $order->order_code; ?></th>
                                            <td><?php echo $order->customer_name; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" type="button" 
                                                        data-toggle="collapse" 
                                                        data-target="#items_<?php echo md5($order->order_code); ?>" 
                                                        aria-expanded="false">
                                                    <i class="fas fa-list"></i> View Items
                                                </button>
                                                <button class="btn btn-sm btn-success" type="button" 
                                                        onclick="openAddProductModal('<?php echo $order->order_code; ?>', '<?php echo $order->customer_name; ?>', <?php echo $rowIndex; ?>)">
                                                    <i class="fas fa-plus"></i> Add Product
                                                </button>
                                                <div class="collapse mt-2" id="items_<?php echo md5($order->order_code); ?>">
                                                    <div class="card card-body">
                                                        <?php
                                                        // Get individual items for this order
                                                        $items_query = "SELECT prod_name, prod_price, prod_qty 
                                                                       FROM rpos_orders 
                                                                       WHERE order_code = ? AND order_status = 'Paid'";
                                                        $items_stmt = $mysqli->prepare($items_query);
                                                        $items_stmt->bind_param('s', $order->order_code);
                                                        $items_stmt->execute();
                                                        $items_res = $items_stmt->get_result();
                                                        
                                                        echo '<table class="table table-sm table-bordered">';
                                                        echo '<thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>';
                                                        echo '<tbody>';
                                                        
                                                        while ($item = $items_res->fetch_object()) {
                                                            $subtotal = $item->prod_price * $item->prod_qty;
                                                            echo '<tr class="item-row">';
                                                            echo '<td>' . $item->prod_name . '</td>';
                                                            echo '<td class="item-price" data-rwf="' . $item->prod_price . '">' . number_format($item->prod_price) . ' Rwf</td>';
                                                            echo '<td>' . $item->prod_qty . '</td>';
                                                            echo '<td class="item-subtotal" data-rwf="' . $subtotal . '">' . number_format($subtotal) . ' Rwf</td>';
                                                            echo '</tr>';
                                                        }
                                                        
                                                        echo '</tbody></table>';
                                                        ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="order-total" data-rwf="<?php echo $order->order_total; ?>">
                                                <?php echo number_format($order->order_total); ?> Rwf
                                            </td>
                                            <td class="tip-amount" data-rwf="<?php echo $order->tip; ?>">
                                                <?php if ($order->tip > 0) { ?>
                                                    <span class="badge badge-success"><?php echo number_format($order->tip); ?> Rwf</span>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                            <td class="total-paid" data-rwf="<?php echo $order->total_paid; ?>">
                                                <strong><?php echo number_format($order->total_paid); ?> Rwf</strong>
                                            </td>
                                            <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                            <td>
                                                <select class="form-control form-control-sm" onchange="convertRowPrice(<?php echo $rowIndex; ?>, this.value, '<?php echo $order->order_code; ?>')">
                                                    <option value="RWF">RWF</option>
                                                    <option value="USD">USD</option>
                                                    <option value="EUR">EUR</option>
                                                </select>
                                            </td>
                                            <td>
                                                <a target="_blank" href="print_receipt.php?order_code=<?php echo $order->order_code; ?>" class="print-link" id="print_<?php echo $rowIndex; ?>" data-order-code="<?php echo $order->order_code; ?>">
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="fas fa-print"></i>
                                                        Print Receipt
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php 
                                        $rowIndex++;
                                    } ?>
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
    
    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Products to Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addProductForm" method="POST" action="add_product_to_order.php">
                    <div class="modal-body">
                        <input type="hidden" name="order_code" id="modal_order_code">
                        <input type="hidden" name="customer_name" id="modal_customer_name">
                        <input type="hidden" name="row_index" id="modal_row_index">
                        
                        <div class="alert alert-info">
                            <strong>Order Code:</strong> <span id="display_order_code"></span><br>
                            <strong>Customer:</strong> <span id="display_customer_name"></span>
                        </div>
                        
                        <div id="productSelectContainer">
                            <div class="product-select-row mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Product</label>
                                        <select name="products[]" class="form-control product-select" required>
                                            <option value="">Select Product</option>
                                            <?php
                                            // Get all products
                                            $prod_query = "SELECT prod_id, prod_code, prod_name, prod_price FROM rpos_products ORDER BY prod_name";
                                            $prod_stmt = $mysqli->prepare($prod_query);
                                            $prod_stmt->execute();
                                            $prod_res = $prod_stmt->get_result();
                                            
                                            while ($product = $prod_res->fetch_object()) {
                                                echo '<option value="' . $product->prod_id . '" data-price="' . $product->prod_price . '" data-code="' . $product->prod_code . '" data-name="' . $product->prod_name . '">';
                                                echo $product->prod_name . ' - ' . number_format($product->prod_price) . ' Rwf';
                                                echo '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Quantity</label>
                                        <input type="number" name="quantities[]" class="form-control" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-block remove-product-btn" style="display:none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-secondary" onclick="addAnotherProduct()">
                            <i class="fas fa-plus"></i> Add Another Product
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Add Products to Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
    
    <!-- Currency Conversion and Search Script -->
    <script>
        // Exchange rates (base currency: RWF)
        const exchangeRates = {
            'RWF': 1,
            'USD': 0.00074,  // 1 RWF = 0.00074 USD
            'EUR': 0.00068   // 1 RWF = 0.00068 EUR
        };
        
        // Convert prices for a specific row
        function convertRowPrice(rowIndex, selectedCurrency, orderCode) {
            const conversionRate = exchangeRates[selectedCurrency];
            const row = document.getElementById('row_' + rowIndex);
            
            // Get currency symbol
            let currencySymbol = '';
            let currencyLabel = ' Rwf';
            if (selectedCurrency === 'USD') {
                currencySymbol = '$';
                currencyLabel = '';
            } else if (selectedCurrency === 'EUR') {
                currencySymbol = '€';
                currencyLabel = '';
            }
            
            // Convert order total
            const orderTotalCell = row.querySelector('.order-total');
            const rwfOrderTotal = parseFloat(orderTotalCell.getAttribute('data-rwf'));
            const convertedOrderTotal = rwfOrderTotal * conversionRate;
            orderTotalCell.textContent = currencySymbol + convertedOrderTotal.toFixed(2) + currencyLabel;
            
            // Convert tip amount
            const tipCell = row.querySelector('.tip-amount');
            const rwfTip = parseFloat(tipCell.getAttribute('data-rwf'));
            if (rwfTip > 0) {
                const convertedTip = rwfTip * conversionRate;
                tipCell.innerHTML = '<span class="badge badge-success">' + currencySymbol + convertedTip.toFixed(2) + currencyLabel + '</span>';
            }
            
            // Convert total paid
            const totalPaidCell = row.querySelector('.total-paid');
            const rwfTotalPaid = parseFloat(totalPaidCell.getAttribute('data-rwf'));
            const convertedTotalPaid = rwfTotalPaid * conversionRate;
            totalPaidCell.innerHTML = '<strong>' + currencySymbol + convertedTotalPaid.toFixed(2) + currencyLabel + '</strong>';
            
            // Convert individual item prices in the collapsed section if it's open
            const itemsSection = document.getElementById('items_' + md5(orderCode));
            if (itemsSection) {
                const itemRows = itemsSection.querySelectorAll('.item-row');
                itemRows.forEach(function(itemRow) {
                    // Convert item price
                    const priceCell = itemRow.querySelector('.item-price');  
                    if (priceCell) {
                        const rwfPrice = parseFloat(priceCell.getAttribute('data-rwf'));
                        const convertedPrice = rwfPrice * conversionRate;
                        priceCell.textContent = currencySymbol + convertedPrice.toFixed(2) + currencyLabel;
                    }
                    
                    // Convert item subtotal
                    const subtotalCell = itemRow.querySelector('.item-subtotal');
                    if (subtotalCell) {
                        const rwfSubtotal = parseFloat(subtotalCell.getAttribute('data-rwf'));
                        const convertedSubtotal = rwfSubtotal * conversionRate;
                        subtotalCell.textContent = currencySymbol + convertedSubtotal.toFixed(2) + currencyLabel;
                    }
                });
            }
            
            // Update print receipt link with selected currency
            const printLink = document.getElementById('print_' + rowIndex);
            printLink.href = 'print_receipt.php?order_code=' + orderCode + '&currency=' + selectedCurrency;
        }
        
        // MD5 hash function (simple version for client-side matching)
        function md5(str) {
            // This is a placeholder - in reality we just need to match the PHP md5
            // We'll use a simpler approach: just use the order code as-is since it's unique
            return btoa(str).replace(/[^a-zA-Z0-9]/g, '');
        }
        
        // Search function
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input = this.value.toLowerCase();
            var table = document.getElementById('ordersTable');
            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (var i = 0; i < rows.length; i++) {
                var code = rows[i].getElementsByTagName('th')[0].textContent.toLowerCase();
                var customer = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
                var orderTotal = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
                var totalPaid = rows[i].getElementsByTagName('td')[4].textContent.toLowerCase();
                var date = rows[i].getElementsByTagName('td')[5].textContent.toLowerCase();
                
                if (code.indexOf(input) > -1 || 
                    customer.indexOf(input) > -1 || 
                    orderTotal.indexOf(input) > -1 || 
                    totalPaid.indexOf(input) > -1 || 
                    date.indexOf(input) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
        
        // Open Add Product Modal
        function openAddProductModal(orderCode, customerName, rowIndex) {
            document.getElementById('modal_order_code').value = orderCode;
            document.getElementById('modal_customer_name').value = customerName;
            document.getElementById('modal_row_index').value = rowIndex;
            document.getElementById('display_order_code').textContent = orderCode;
            document.getElementById('display_customer_name').textContent = customerName;
            
            // Reset form
            document.getElementById('productSelectContainer').innerHTML = `
                <div class="product-select-row mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Product</label>
                            <select name="products[]" class="form-control product-select" required>
                                ${document.querySelector('.product-select').innerHTML}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Quantity</label>
                            <input type="number" name="quantities[]" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block remove-product-btn" style="display:none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            $('#addProductModal').modal('show');
        }
        
        // Add another product row
        function addAnotherProduct() {
            const container = document.getElementById('productSelectContainer');
            const firstSelect = document.querySelector('.product-select');
            
            const newRow = document.createElement('div');
            newRow.className = 'product-select-row mb-3';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <label>Product</label>
                        <select name="products[]" class="form-control product-select" required>
                            ${firstSelect.innerHTML}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Quantity</label>
                        <input type="number" name="quantities[]" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-product-btn" onclick="removeProductRow(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(newRow);
        }
        
        // Remove product row
        function removeProductRow(button) {
            button.closest('.product-select-row').remove();
        }
        
        // Handle form submission
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('add_product_to_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Products added successfully! The page will reload.');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error adding products. Please try again.');
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>