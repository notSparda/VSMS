<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "service_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$service_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($service_id) {
    $check_sql = "SELECT name, price FROM services WHERE id = $service_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $service_data = $check_result->fetch_assoc();
        $service_name = $service_data["name"];
        $service_price = $service_data["price"];
        
        $assignments_check_sql = "SELECT COUNT(*) as assignment_count FROM client_services WHERE service_id = $service_id";
        $assignments_result = $conn->query($assignments_check_sql);
        $assignment_count = 0;
        
        if ($assignments_result->num_rows > 0) {
            $assignment_data = $assignments_result->fetch_assoc();
            $assignment_count = $assignment_data["assignment_count"];
        }
        
        $sql = "DELETE FROM services WHERE id = $service_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Service '$service_name' ($$service_price) deleted successfully!";
            $message_type = "success";
            
            if ($assignment_count > 0) {
                $message .= " Note: $assignment_count client assignment(s) were also deleted due to cascade delete.";
            }
        } else {
            $message = "Error deleting service: " . $conn->error;
            $message_type = "error";
        }
    } else {
        $message = "Service not found.";
        $message_type = "error";
    }
} else {
    $message = "No service ID provided.";
    $message_type = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Service - Service Management System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
        <header>
<h1>VSMS</h1>
  <nav>
        <a href="../index.php"><b>Home</b></a>
        <a href="../about.php"><b>Who we are</b></a>
        <a href="../clients/list.php"><b>Clients</b></a>
        <a href="../service_providers/list.php"><b>Service Providers</b></a>
  </nav>
</header>
    <div class="container">
        <h1>Delete Service</h1>

        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="list.php" class="btn">Back to Services List</a>
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
