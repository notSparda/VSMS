<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "service_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$provider_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($provider_id) {
    $check_sql = "SELECT name FROM service_providers WHERE id = $provider_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $provider_data = $check_result->fetch_assoc();
        $provider_name = $provider_data["name"];
        
        $services_check_sql = "SELECT COUNT(*) as service_count FROM services WHERE provider_id = $provider_id";
        $services_result = $conn->query($services_check_sql);
        $service_count = 0;
        
        if ($services_result->num_rows > 0) {
            $service_data = $services_result->fetch_assoc();
            $service_count = $service_data["service_count"];
        }
        
        $sql = "DELETE FROM service_providers WHERE id = $provider_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Service provider '$provider_name' deleted successfully!";
            $message_type = "success";
            
            if ($service_count > 0) {
                $message .= " Note: $service_count associated service(s) now have no provider assigned.";
            }
        } else {
            $message = "Error deleting service provider: " . $conn->error;
            $message_type = "error";
        }
    } else {
        $message = "Service provider not found.";
        $message_type = "error";
    }
} else {
    $message = "No provider ID provided.";
    $message_type = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Service Provider - Service Management System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
    <h1>VSMS</h1>
  <nav>
        <a href="../index.php"><b>Home</b></a>
        <a href="../about.php"><b>Who we are</b></a>
        <a href="../clients/list.php"><b>Clients</b></a>
        <a href="../services/list.php"><b>Services</b></a>
  </nav>
</header>
    <div class="container">
        <h1>Delete Service Provider</h1>
        
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="list.php" class="btn">Back to Providers List</a>
        </div>
        
    </div>
    
    <?php
    $conn->close();
    ?>
<footer class="footer">
        <p class="footer-text">Copyright @ VSMS - PHP 2021.</p>
        <p class="footer-text">Developed By: Raza Jawaid Nabi</p>
    </footer>


    <div class="admin-links">
        <a href="https://www.linkedin.com/in/raza-jawaid-162a42319/">linkedin</a>
        <a href="https://www.facebook.com/share/17FVE2HpxP/">Facebook</a>
        <a>bscs2312384@szabist.pk</a>
    </div>
</body>
</html>
