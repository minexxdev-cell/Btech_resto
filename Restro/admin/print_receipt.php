<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Currency conversion rates (base currency: RWF)
$exchangeRates = [
    'RWF' => 1,
    'USD' => 0.00074,  // 1 RWF = 0.00074 USD
    'EUR' => 0.00068   // 1 RWF = 0.00068 EUR
];

// Get selected currency from URL parameter, default to RWF
$selectedCurrency = isset($_GET['currency']) ? $_GET['currency'] : 'RWF';
$conversionRate = $exchangeRates[$selectedCurrency];

// Currency symbol
$currencySymbol = '';
if ($selectedCurrency === 'USD') {
    $currencySymbol = '$';
} else if ($selectedCurrency === 'EUR') {
    $currencySymbol = '€';
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
    <meta name="author" content="MartDevelopers Inc">
    <title>Restaurant Point Of Sale - Receipt</title>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/icons/favicon-16x16.png">
    <link rel="manifest" href="assets/img/icons/site.webmanifest">
    <link rel="mask-icon" href="assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link href="assets/css/bootstrap.css" rel="stylesheet" id="bootstrap-css">
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/jquery.js"></script>
    <style>
        body {
            margin-top: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <?php
    $order_code = $_GET['order_code'];
    
    // Get all items for this order (ordered by latest created_at)
    $ret = "SELECT * FROM rpos_orders WHERE order_code = ? ORDER BY created_at DESC";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $order_code);
    $stmt->execute();
    $res = $stmt->get_result();
    
    // Store all order items
    $orderItems = [];
    $subtotal = 0;
    $customerName = '';
    $orderDate = '';
    
    while ($order = $res->fetch_object()) {
        $customerName = $order->customer_name;
        $orderDate = $order->created_at;
        
        // Convert prices from RWF to selected currency
        $unitPrice = $order->prod_price * $conversionRate;
        $itemTotal = ($order->prod_price * $order->prod_qty) * $conversionRate;
        
        $orderItems[] = [
            'name' => $order->prod_name,
            'qty' => $order->prod_qty,
            'unit_price' => $unitPrice,
            'total' => $itemTotal
        ];
        
        $subtotal += $itemTotal;
    }
    
    // Calculate tax
    $tax = $subtotal * 0.18; // 18% tax
    
    // Get tip from payment record
    $tip = 0;
    $tipPercent = 0;
    $payment_query = "SELECT tip FROM rpos_payments WHERE order_code = ?";
    $payment_stmt = $mysqli->prepare($payment_query);
    $payment_stmt->bind_param('s', $order_code);
    $payment_stmt->execute();
    $payment_res = $payment_stmt->get_result();
    
    if ($payment_row = $payment_res->fetch_object()) {
        $tip = $payment_row->tip * $conversionRate; // Convert tip to selected currency
        // Calculate tip percentage based on subtotal
        if ($subtotal > 0) {
            $tipPercent = round(($tip / $subtotal) * 100);
        }
    }
    
    // Calculate grand total with tip
    $grandTotal = $subtotal + $tip;
    ?>

    <?php
    $rravatst = "SELECT * FROM RRA ORDER BY created_at DESC 
                        LIMIT 1";
    $rrastmt = $mysqli_hotel->prepare($rravatst);
    
    $rrastmt->execute();
    $result = $rrastmt->get_result();
    $rrares = $result->fetch_assoc();   // <-- THIS WAS MISSING
    ?>

    <div class="container"> 
        <div class="row">
            <div id="Receipt" class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <address>
                            <strong>Baobab Hotel</strong>
                            <br>
                            KG 48 St, Kigali
                            <br>
                            +(250) 781 088 725
                        </address>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                        <p>
                            <em>Date: <?php echo date('d/M/Y g:i', strtotime($orderDate)); ?></em>
                        </p>
                        <p>
                            <em class="text-success">Receipt #: <?php echo $order_code; ?></em>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="text-center">
                        <h2>Receipt</h2>
                        <p><strong>Customer: <?php echo $customerName; ?></strong></p>
                        <p><strong>Currency: <?php echo $selectedCurrency; ?></strong></p>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $item) { ?>
                                <tr>
                                    <td class="col-md-9"><em><?php echo $item['name']; ?></em></td>
                                    <td class="col-md-1" style="text-align: center"><?php echo $item['qty']; ?></td>
                                    <td class="col-md-1 text-center">
                                        <?php echo $currencySymbol . number_format($item['unit_price'], 2); ?> 
                                        <?php echo ($selectedCurrency == 'RWF') ? 'Rwf' : ''; ?>
                                    </td>
                                    <td class="col-md-1 text-center">
                                        <?php echo $currencySymbol . number_format($item['total'], 2); ?> 
                                        <?php echo ($selectedCurrency == 'RWF') ? 'Rwf' : ''; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            
                            <tr>
                                <td colspan="2"></td>
                                <td class="text-right">
                                    <p><strong>Subtotal:</strong></p>
                                    <p><strong>RRA VAT Tax:</strong></p>
                                    <?php if ($tip > 0) { ?>
                                        <p><strong>Tip :</strong></p>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <p>
                                        <strong>
                                            <?php echo $currencySymbol . number_format($subtotal); ?> 
                                            <?php echo ($selectedCurrency == 'RWF') ? 'Rwf' : ''; ?>
                                        </strong>
                                    </p>
                                    <p>
                                        <strong>
                                            <?php echo $rrares['vat_rate'] ?>% 
                                            
                                        </strong>
                                    </p>
                                    <?php if ($tip > 0) { ?>
                                        <p>
                                            <strong class="text-success">
                                                <?php echo $currencySymbol . number_format($tip, 2); ?> 
                                                <?php echo ($selectedCurrency == 'RWF') ? 'Rwf' : ''; ?>
                                            </strong>
                                        </p>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td class="text-right">
                                    <h4><strong>Total:</strong></h4>
                                </td>
                                <td class="text-center text-danger">
                                    <h4>
                                        <strong>
                                            <?php echo $currencySymbol . number_format($grandTotal, 2); ?> 
                                            <?php echo ($selectedCurrency == 'RWF') ? 'Rwf' : ''; ?>
                                        </strong>
                                    </h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center">
                        <?php if ($tip > 0) { ?>
                            <p><em class="text-success">Thank you for the generous tip!</em></p>
                        <?php } ?>
                        <p><em>Thank you for your business!</em></p>
                        <p class="text-muted"><small>Items: <?php echo count($orderItems); ?></small></p>
                    </div>
                </div>
            </div>
            <div class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3 no-print">
                <button id="print" onclick="printContent('Receipt');" class="btn btn-success btn-lg text-justify btn-block">
                    Print <span class="fas fa-print"></span>
                </button>
                <a href="receipts.php" class="btn btn-secondary btn-lg text-justify btn-block">
                    Back to Receipts
                </a>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    function printContent(el) {
        var restorepage = $('body').html();
        var printcontent = $('#' + el).clone();
        $('body').empty().html(printcontent);
        window.print();
        $('body').html(restorepage);
    }
</script>