<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "service_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $description = $price = $duration_hours = $provider_id = "";
$message = "";
$message_type = "";
$service_id = "";

if (isset($_GET['id'])) {
    $service_id = $_GET['id'];
    
    $sql = "SELECT * FROM services WHERE id = $service_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row["name"];
        $description = $row["description"];
        $price = $row["price"];
        $duration_hours = $row["duration_hours"];
        $provider_id = $row["provider_id"];
    } else {
        $message = "Service not found.";
        $message_type = "error";
    }
} else {
    $message = "No service ID provided.";
    $message_type = "error";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    $duration_hours = trim($_POST["duration_hours"]);
    $provider_id = trim($_POST["provider_id"]);
    
    $is_valid = true;
    $validation_errors = array();
    
    if (empty(trim($name))) {
        $is_valid = false;
        $validation_errors[] = "Service name is required";
    } else{
        $name_valid = true;
        for ($i = 0; $i < strlen($name); $i++) {
            $char = $name[$i];
            if (!(($char >= 'a' && $char <= 'z') || 
                  ($char >= 'A' && $char <= 'Z') || 
                  $char == ' ')) {
                $name_valid = false;
                break;
            }
        }
        if (!$name_valid) {
            $is_valid = false;
            $validation_errors[] = "Service name can only contain letters";
        }
    }
    
    if (!empty(trim($price))) {
        if ($price < 0) {
            $is_valid = false;
            $validation_errors[] = "Price cannot be negative";
        }
        if (!is_numeric($price)) {
            $is_valid = false;
            $validation_errors[] = "Price must be a valid number";
        }
    } else {
        $is_valid = false;
        $validation_errors[] = "Price is required";
    }
    
    if (!empty(trim($duration_hours))) {
        if ($duration_hours <= 0) {
            $is_valid = false;
            $validation_errors[] = "Duration must be greater than 0 hours";
        }
        if (!is_numeric($duration_hours)) {
            $is_valid = false;
            $validation_errors[] = "Duration must be a valid number";
        }
        if (strpos($duration_hours, '.') !== false) {
            $is_valid = false;
            $validation_errors[] = "Duration must be a whole number (hours)";
        }
    } else {
        $is_valid = false;
        $validation_errors[] = "Duration is required";
    }
    
    if ($is_valid) {
        $provider_id_sql = empty(trim($provider_id)) ? "NULL" : "'$provider_id'";
        
        $sql = "UPDATE services SET 
                name = '$name', 
                description = '$description', 
                price = '$price', 
                duration_hours = '$duration_hours',
                provider_id = $provider_id_sql,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = $service_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Service updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating service: " . $conn->error;
            $message_type = "error";
        }
    } else {
        $message = "Validation Error: " . implode(", ", $validation_errors);
        $message_type = "error";
    }
}

$providers_sql = "SELECT id, name, specialization FROM service_providers ORDER BY name";
$providers_result = $conn->query($providers_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service - Service Management System</title>
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
        <h1>Edit Service</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
                <br><br>
                <a href="list.php" class="btn">Back to Services List</a>
            </div>
        <?php endif; ?>
        
        <?php if ($service_id && !$message_type): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Service Name *</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo $name; ?>" required
                           placeholder="e.g., Computer Repair, House Cleaning">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              placeholder="Detailed description of the service..."><?php echo $description; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price ($) *</label>
                    <input type="number" id="price" name="price" 
                           value="<?php echo $price; ?>" required
                           min="0" step="0.01"
                           placeholder="75.00">
                </div>
                
                <div class="form-group">
                    <label for="duration_hours">Duration (Hours) *</label>
                    <input type="number" id="duration_hours" name="duration_hours" 
                           value="<?php echo $duration_hours; ?>" required
                           min="1" max="24"
                           placeholder="2">
                </div>
                
                <div class="form-group">
                    <label for="provider_id">Service Provider</label>
                    <select id="provider_id" name="provider_id">
                        <option value="">Select a provider (optional)</option>
                        <?php
                        if ($providers_result->num_rows > 0) {
                            while($provider = $providers_result->fetch_assoc()) {
                                $selected = ($provider_id == $provider["id"]) ? "selected" : "";
                                echo "<option value='" . $provider["id"] . "' $selected>" . 
                                     $provider["name"] . 
                                     ($provider["specialization"] ? " (" . $provider["specialization"] . ")" : "") . 
                                     "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">Update Service</button>
                <a href="list.php" class="clear_btn">Cancel</a>
            </form>
            
            <div class="message">
                <h3>Service Information:</h3>
                <p><strong>Service ID:</strong> <?php echo $service_id; ?></p>
                <p><strong>Current Name:</strong> <?php echo $name; ?></p>
                <p><strong>Current Price:</strong> $<?php echo number_format($price, 2); ?></p>
                <p><strong>Current Duration:</strong> <?php echo $duration_hours; ?> hour(s)</p>
                <?php if ($provider_id): ?>
                    <?php
                    $current_provider_sql = "SELECT name FROM service_providers WHERE id = $provider_id";
                    $current_provider_result = $conn->query($current_provider_sql);
                    if ($current_provider_result->num_rows > 0) {
                        $current_provider = $current_provider_result->fetch_assoc();
                        echo "<p><strong>Current Provider:</strong> " . $current_provider["name"] . "</p>";
                    }
                    ?>
                <?php else: ?>
                    <p><strong>Current Provider:</strong> No provider assigned</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
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
