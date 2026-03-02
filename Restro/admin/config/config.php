<?php
// Database credentials - Use environment variables in production
$username = getenv('DB_USER') ?: "minexx";
$password = getenv('DB_PASSWORD') ?: "Minexx2024$";
$database = getenv('DB_NAME') ?: "roBao_test";
$database_invetory=getenv('DB_NAME')?:"Btest_1";
// GCP Cloud SQL configuration
$connectionName = getenv('CLOUD_SQL_CONNECTION_NAME') ?: "minexx-dashboard:us-central1:minexx-database";
$publicIP = getenv('DB_HOST') ?: "34.44.186.153";

// Check if running on GCP (App Engine or Compute Engine)
$isGCP = (getenv('GAE_INSTANCE') !== false) || file_exists('/cloudsql/' . $connectionName);

if ($isGCP) {
    // Running on GCP - Use Unix socket connection
    $host = "localhost";
    $socket = "/cloudsql/" . $connectionName;
    
    $mysqli = mysqli_connect($host, $username, $password, $database, null, $socket);
    
    
    $mysqli_hotel=mysqli_connect($host, $username, $password, $database, null, $socket);
    $mysqli_inventory=mysqli_connect($host, $username, $password, $database_invetory, null, $socket);
    $connectionType = "GCP Unix Socket";

} else {
    // Running locally - Use localhost connection
    $host = "localhost";
    $username = "root";  
    $password = "Password";
    $database = "rposystem";
    
    $mysqli = mysqli_connect($host, $username, $password, $database);
    $connectionType = "Local Development: " . $host;

    $db_hotel="hotelms";

    $mysqli_hotel= mysqli_connect($host, $username, $password, $db_hotel);

    $db_inventory="larapos";
    $mysqli_inventory= mysqli_connect($host, $username, $password, $db_inventory);
}


// Check connection
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error() . "<br>Attempted connection type: " . $connectionType);
}
elseif(!$mysqli_hotel)
    {
        die("Connection failed: " . mysqli_connect_error() . "<br>Attempted connection type: " . $connectionType);

    }
elseif(!$mysqli_inventory)
    {
        die("Connection failed: " . mysqli_connect_error() . "<br>Attempted connection type: " . $connectionType);

    }

mysqli_set_charset($mysqli, "utf8mb4");