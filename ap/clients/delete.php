<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "service_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$client_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($client_id) {
    $check_sql = "SELECT firstname, lastname FROM clients WHERE id = $client_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $client_data = $check_result->fetch_assoc();
        $client_name = $client_data["firstname"] . " " . $client_data["lastname"];
        
        $sql = "DELETE FROM clients WHERE id = $client_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Client '$client_name' deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting client: " . $conn->error;
            $message_type = "error";
        }
    } else {
        $message = "Client not found.";
        $message_type = "error";
    }
} else {
    $message = "No client ID provided.";
    $message_type = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Client - Service Management System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
<h1>VSMS</h1>
  <nav>
        <a href="../index.php"><b>Home</b></a>
        <a href="../about.php"><b>Who we are</b></a>
        <a href="../service_providers/list.php"><b>Service Providers</b></a>
        <a href="../services/list.php"><b>Services</b></a>
  </nav>
</header>

    <div class="container">
        <h1>Delete Client</h1>

        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>

        <div class="message">
            <a href="list.php" class="btn">Back to Clients List</a>
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
