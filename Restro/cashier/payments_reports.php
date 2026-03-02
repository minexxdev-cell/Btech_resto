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
                                <div class="col">
                                    Payment Reports
                                </div>
                                <div class="col">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search by payment code, method, order code, amount, or date...">
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary" onclick=printPage();>Print<i class="fas fa-print"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush" id="paymentsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Payment Code</th>
                                        <th scope="col">Payment Method</th>
                                        <th class="text-success" scope="col">Order Code</th>
                                        <th scope="col">Amount Paid</th>
                                        <th scope="col">Done By</th>
                                        <th class="text-success" scope="col">Date Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM  rpos_payments ORDER BY `created_at` DESC ";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($payment = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <th class="text-success" scope="row">
                                                <?php echo $payment->pay_code; ?>
                                            </th>
                                            <th scope="row">
                                                <?php echo $payment->pay_method; ?>
                                            </th>
                                            <td class="text-success">
                                                <?php echo $payment->order_code; ?>
                                            </td>
                                            <td>
                                                 <?php echo $payment->pay_amt; ?>
                                                 Rwf
                                            </td>
                                             <td>
                                                 <?php echo $payment->Done_by; ?>
                                                 
                                            </td>
                                            <td class="text-success">
                                                <?php echo date('d/M/Y g:i', strtotime($payment->created_at)) ?>
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
            var table = document.getElementById('paymentsTable');
            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (var i = 0; i < rows.length; i++) {
                var paymentCode = rows[i].getElementsByTagName('th')[0].textContent.toLowerCase();
                var paymentMethod = rows[i].getElementsByTagName('th')[1].textContent.toLowerCase();
                var orderCode = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
                var amount = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
                var date = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
                
                if (paymentCode.indexOf(input) > -1 || 
                    paymentMethod.indexOf(input) > -1 || 
                    orderCode.indexOf(input) > -1 || 
                    amount.indexOf(input) > -1 || 
                    date.indexOf(input) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
        function printPage()
        {
            window.print();
        }
    </script>
</body>

</html>