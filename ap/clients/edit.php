<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "service_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$firstname = $lastname = $email = $phone = $address = "";
$message = "";
$message_type = "";
$client_id = "";

if (isset($_GET['remove_service']) && isset($_GET['id'])) {
    $service_assignment_id = $_GET['remove_service'];
    $temp_client_id = $_GET['id'];
    $sql = "DELETE FROM client_services WHERE id = $service_assignment_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: edit.php?id=$temp_client_id&message=Service assignment removed successfully!");
        exit();
    }
}

if (isset($_POST['assign_service']) && isset($_POST['new_service_id']) && !empty($_POST['new_service_id'])) {
    $new_service_id = $_POST['new_service_id'];
    $temp_client_id = $_POST['client_id'];
    $sql = "INSERT INTO client_services (client_id, service_id, status, notes) 
            VALUES ($temp_client_id, $new_service_id, 'pending', 'Service assigned')";
    if ($conn->query($sql) === TRUE) {
        header("Location: edit.php?id=$temp_client_id&message=Service assigned successfully!");
        exit();
    } else {
        $message = "Error assigning service: " . $conn->error;
        $message_type = "error";
    }
}

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $message_type = "success";
}

if (isset($_GET['id'])) {
    $client_id = $_GET['id'];
    
    $sql = "SELECT * FROM clients WHERE id = $client_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstname = $row["firstname"];
        $lastname = $row["lastname"];
        $email = $row["email"];
        $phone = $row["phone"];
        $address = $row["address"];
    } else {
        $message = "Client not found.";
        $message_type = "error";
    }
} else {
    $message = "No client ID provided.";
    $message_type = "error";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["assign_service"])) {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    
    $is_valid = true;
    $validation_errors = array();
    
    if (empty(trim($firstname))) {
        $is_valid = false;
        $validation_errors[] = "First name is required";
    } else {
        $firstname_valid = true;
        for ($i = 0; $i < strlen($firstname); $i++) {
            $char = $firstname[$i];
            if (!(($char >= 'a' && $char <= 'z') || 
                  ($char >= 'A' && $char <= 'Z') || 
                  $char == ' ')) {
                $firstname_valid = false;
                break;
            }
        }
        if (!$firstname_valid) {
            $is_valid = false;
            $validation_errors[] = "First name can only contain letters";
        }
    }
    
    if (empty(trim($lastname))) {
        $is_valid = false;
        $validation_errors[] = "Last name is required";
    } else {
        $lastname_valid = true;
        for ($i = 0; $i < strlen($lastname); $i++) {
            $char = $lastname[$i];
            if (!(($char >= 'a' && $char <= 'z') || 
                  ($char >= 'A' && $char <= 'Z') || 
                  $char == ' ')) {
                $lastname_valid = false;
                break;
            }
        }
        if (!$lastname_valid) {
            $is_valid = false;
            $validation_errors[] = "Last name can only contain letters";
        }
    }
    
    if (!empty(trim($phone))) {
        $phone_valid = true;
        for ($i = 0; $i < strlen($phone); $i++) {
            $char = $phone[$i];
            if (!($char >= '0' && $char <= '9')) {
                $phone_valid = false;
                break;
            }
        }
        if (!$phone_valid) {
            $is_valid = false;
            $validation_errors[] = "Phone number can only contain numbers";
        }
        
        if (strlen($phone) != 11) {
            $is_valid = false;
            $validation_errors[] = "Phone number must have exactly 11 digits";
        }
        
        $check_phone_sql = "SELECT id FROM clients WHERE phone = '$phone' AND id != $client_id";
        $check_phone_result = $conn->query($check_phone_sql);
        if ($check_phone_result->num_rows > 0) {
            $is_valid = false;
            $validation_errors[] = "This phone number is already registered to another client";
        }
    }
    
    $check_email_sql = "SELECT id FROM clients WHERE email = '$email' AND id != $client_id";
    $check_email_result = $conn->query($check_email_sql);
    if ($check_email_result->num_rows > 0) {
        $is_valid = false;
        $validation_errors[] = "This email address is already registered to another client";
    }
    
    if ($is_valid) {
        $sql = "UPDATE clients SET 
                firstname = '$firstname', 
                lastname = '$lastname', 
                email = '$email', 
                phone = '$phone', 
                address = '$address',
                updated_at = CURRENT_TIMESTAMP
                WHERE id = $client_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Client updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating client: " . $conn->error;
            $message_type = "error";
        }
    } else {
        $message = "Error: " . implode(", ", $validation_errors);
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client - Service Management System</title>
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
        <h1>Edit Client</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
                <br><br>
                <a href="list.php" class="btn">Back to Clients List</a>
            </div>
        <?php endif; ?>
        
        <?php if ($client_id && !$message_type): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="firstname">First Name *</label>
                    <input type="text" id="firstname" name="firstname" 
                           value="<?php echo $firstname; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="lastname">Last Name *</label>
                    <input type="text" id="lastname" name="lastname" 
                           value="<?php echo $lastname; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo $email; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo $phone; ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"><?php echo $address; ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Update Client</button>
                <a href="list.php" class="clear_btn">Cancel</a>
            </form>
            
            <div class="message">
                <h3>Client Information:</h3>
                <p><strong>Client ID:</strong> <?php echo $client_id; ?></p>
                <p><strong>Current Name:</strong> <?php echo $firstname . " " . $lastname; ?></p>
                <p><strong>Current Email:</strong> <?php echo $email; ?></p>
            </div>
            
            <div class="message">
                <h3>Assigned Services</h3>
                <?php
                $services_sql = "SELECT cs.id, s.name, cs.status, s.price 
                                FROM client_services cs 
                                JOIN services s ON cs.service_id = s.id 
                                WHERE cs.client_id = $client_id";
                $services_result = $conn->query($services_sql);
                
                if ($services_result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($service = $services_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $service["name"]; ?></td>
                                    <td>$<?php echo number_format($service["price"], 2); ?></td>
                                    <td><?php echo ucfirst($service["status"]); ?></td>
                                    <td>
                                        <a href="?id=<?php echo $client_id; ?>&remove_service=<?php echo $service["id"]; ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Remove this service assignment?')">Remove</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No services assigned to this client.</p>
                <?php endif; ?>
                
                <h4 style="margin-top: 20px;">Assign New Service</h4>
                <form method="POST" action="">
                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
                    <div class="form-group">
                        <label for="new_service_id">Select Service:</label>
                        <select id="new_service_id" name="new_service_id">
                            <option value="">Choose a service...</option>
                            <?php
                            $all_services_sql = "SELECT id, name, price FROM services ORDER BY name";
                            $all_services_result = $conn->query($all_services_sql);
                            while($service = $all_services_result->fetch_assoc()) {
                                echo "<option value='" . $service["id"] . "'>" . 
                                     $service["name"] . " - $" . $service["price"] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="assign_service" class="btn btn-success">Assign Service</button>
                </form>
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
