<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_code = $_POST['order_code'];
    $customer_name = $_POST['customer_name'];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];
    
    // Validate inputs
    if (empty($order_code) || empty($products) || empty($quantities)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    // Check if order exists and is paid
    $check_query = "SELECT order_code, customer_id, Done_by FROM rpos_orders WHERE order_code = ? AND order_status = 'Paid' LIMIT 1";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('s', $order_code);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found or not paid']);
        exit;
    }
    
    $order_data = $check_result->fetch_object();
    $customer_id = $order_data->customer_id;
    $done_by = $order_data->Done_by;
    
    try {
        // Begin transaction
        $mysqli->begin_transaction();
        
        $added_products = 0;
        $total_added_amount = 0;
        
        // Insert each product into the order
        for ($i = 0; $i < count($products); $i++) {
            $prod_id = $products[$i];
            $prod_qty = $quantities[$i];
            
            // Get product details
            $prod_query = "SELECT prod_id, prod_name, prod_price FROM rpos_products WHERE prod_id = ?";
            $prod_stmt = $mysqli->prepare($prod_query);
            $prod_stmt->bind_param('s', $prod_id);
            $prod_stmt->execute();
            $prod_result = $prod_stmt->get_result();
            
            if ($prod_result->num_rows > 0) {
                $product = $prod_result->fetch_object();
                
                // Generate unique order_id
                $order_id = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 20);
                
                // Insert product into order with exact columns from your table
                $insert_query = "INSERT INTO rpos_orders 
                                (order_id, order_code, customer_id, customer_name, prod_id, prod_name, 
                                 prod_price, prod_qty, order_status, Done_by, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Paid', ?, NOW())";
                
                $insert_stmt = $mysqli->prepare($insert_query);
                $insert_stmt->bind_param(
                    'sssssssss',
                    $order_id,
                    $order_code,
                    $customer_id,
                    $customer_name,
                    $product->prod_id,
                    $product->prod_name,
                    $product->prod_price,
                    $prod_qty,
                    $done_by
                );
                
                if ($insert_stmt->execute()) {
                    $added_products++;
                    $total_added_amount += ($product->prod_price * $prod_qty);
                }
            }
        }
        
        // Update payment amount to include new products
        $update_payment_query = "UPDATE rpos_payments 
                                SET pay_amt = pay_amt + ? 
                                WHERE order_code = ?";
        $update_payment_stmt = $mysqli->prepare($update_payment_query);
        $update_payment_stmt->bind_param('ds', $total_added_amount, $order_code);
        $update_payment_stmt->execute();
        
        // Commit transaction
        $mysqli->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => "$added_products product(s) added successfully",
            'total_added' => $total_added_amount
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $mysqli->rollback();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>