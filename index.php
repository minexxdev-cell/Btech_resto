<?php
// List of allowed IP addresses
// $allowed_ips = [
//     '41.186.168.136',
//     '102.22.171.204',
//     '41.204.44.163',
//     '41.186.139.15',
// ];

// Get the real client IP from Cloud Run
// $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

// X-Forwarded-For may contain multiple IPs, get the first one
// if (strpos($client_ip, ',') !== false) {
//     $client_ip = trim(explode(',', $client_ip)[0]);
// }

// Check if the IP is in the allowed list
// if (!in_array($client_ip, $allowed_ips)) {
//     http_response_code(403);
//     die('Access Denied - Your IP is not authorized');
// }
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- For more projects: Visit codeastro.com  -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Restro POS System</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<!-- For more projects: Visit codeastro.com  -->
<body>
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md">
                Restaurant POS
            </div>

            <div class="links">
			<!-- For more projects: Visit codeastro.com  -->
                <a href="Restro/admin/">Admin Log In</a>
                <a href="Restro/cashier/">Cashier Log In</a>
                <a href="Restro/customer">Customer Log In</a>
            </div>
        </div>
    </div>
</body>
<!-- For more projects: Visit codeastro.com  -->
</html>