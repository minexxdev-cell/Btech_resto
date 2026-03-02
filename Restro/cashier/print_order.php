<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

$order_code = isset($_GET['order_code']) ? $_GET['order_code'] : '';

if (empty($order_code)) {
    die('Order code not provided');
}

// Get all items for this order
$ret = "SELECT * FROM rpos_orders WHERE order_code = ?";
$stmt = $mysqli->prepare($ret);
$stmt->bind_param('s', $order_code);
$stmt->execute();
$res = $stmt->get_result();

$orderItems = [];
$subtotal = 0;
$customerName = '';
$orderDate = '';

while ($order = $res->fetch_object()) {
    $customerName = $order->customer_name;
    $orderDate = $order->created_at;
    
    $itemTotal = $order->prod_price * $order->prod_qty;
    
    $orderItems[] = [
        'name' => $order->prod_name,
        'qty' => $order->prod_qty,
        'unit_price' => $order->prod_price,
        'total' => $itemTotal
    ];
    
    $subtotal += $itemTotal;
}

$tax = $subtotal * 0.18;
$grandTotal = $subtotal + $tax;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Preview - <?php echo $order_code; ?></title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .order-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .order-info {
            margin-bottom: 20px;
        }
        .order-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .order-table th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }
        .order-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .grand-total {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 1.2em;
        }
        .footer {
            clear: both;
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="order-header">
            <h2>Order Details</h2>
            <p><strong>Baobab Hotel Restaurant</strong></p>
            <p>KG 48 St, Kigali | +(250) 781 088 725</p>
        </div>

        <div class="order-info">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <p><strong>Customer:</strong> <?php echo $customerName; ?></p>
                    <p><strong>Order Code:</strong> <?php echo $order_code; ?></p>
                </div>
                <div style="text-align: right;">
                    <p><strong>Date:</strong> <?php echo date('d/M/Y g:i A', strtotime($orderDate)); ?></p>
                    <p><strong>Status:</strong> <span style="color: #856404;">Pending</span></p>
                </div>
            </div>
        </div>

        <table class="order-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Item</th>
                    <th style="width: 15%; text-align: center;">Quantity</th>
                    <th style="width: 17%; text-align: right;">Unit Price</th>
                    <th style="width: 18%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item) { ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td style="text-align: center;"><?php echo $item['qty']; ?></td>
                        <td class="text-right"><?php echo number_format($item['unit_price'], 0); ?> Rwf</td>
                        <td class="text-right"><?php echo number_format($item['total'], 0); ?> Rwf</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span><?php echo number_format($subtotal, 0); ?> Rwf</span>
            </div>
            <div class="total-row">
                <span>Tax (18%):</span>
                <span><?php echo number_format($tax, 0); ?> Rwf</span>
            </div>
            <div class="total-row grand-total">
                <span>Grand Total:</span>
                <span><?php echo number_format($subtotal, 0); ?> Rwf</span>
            </div>
        </div>

        <div class="footer">
            <p><em>Total Items: <?php echo count($orderItems); ?></em></p>
            <p style="margin-top: 30px;">Thank you for your order!</p>
        </div>

        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                Print Order
            </button>
           <button onclick="window.location.href='orders.php'" class="btn btn-secondary btn-lg">
              Go to Orders
            </button>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            // Uncomment the line below if you want automatic printing
            // window.print();
        };
    </script>
</body>
</html>