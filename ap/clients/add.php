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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $create_service = isset($_POST["create_service"]) ? true : false;
    $service_id = $_POST["service_id"] ?? "";
    
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
    
    if (empty(trim($email))) {
        $is_valid = false;
        $validation_errors[] = "Email is required";
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
        
        $check_phone_sql = "SELECT id FROM clients WHERE phone = '$phone'";
        $check_phone_result = $conn->query($check_phone_sql);
        if ($check_phone_result->num_rows > 0) {
            $is_valid = false;
            $validation_errors[] = "This phone number is already registered";
        }
    }
    
    $check_email_sql = "SELECT id FROM clients WHERE email = '$email'";
    $check_email_result = $conn->query($check_email_sql);
    if ($check_email_result->num_rows > 0) {
        $is_valid = false;
        $validation_errors[] = "This email address is already registered";
    }
    
    if ($is_valid) {
        $sql = "INSERT INTO clients (firstname, lastname, email, phone, address) 
                VALUES ('$firstname', '$lastname', '$email', '$phone', '$address')";
        
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            
            $message = "Client created successfully with ID: " . $last_id;
            $message_type = "success";
            
            if ($create_service && !empty($service_id)) {
                $service_sql = "INSERT INTO client_services (client_id, service_id, status, notes) 
                               VALUES ($last_id, $service_id, 'pending', 'Service assigned during client creation')";
                
                if ($conn->query($service_sql) === TRUE) {
                    $message .= " and service assignment created successfully!";
                } else {
                    $message .= " but service assignment failed: " . $conn->error;
                    $message_type = "error";
                }
            }
            
            $firstname = $lastname = $email = $phone = $address = "";
        } else {
            $message = "Error creating client: " . $conn->error;
            $message_type = "error";
        }
    } else {
        $message = "Error: " . implode(", ", $validation_errors);
        $message_type = "error";
    }
}

$services_sql = "SELECT id, name, price FROM services ORDER BY name";
$services_result = $conn->query($services_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Client - Service Management System</title>
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
        <h1>Add New Client</h1>

        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
                <?php if ($message_type == "error"): ?>
                    <br><br>
                    <a href="list.php" class="btn">Back to Clients List</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="create_service" id="create_service">
                    Assign a service to this client
                </label>
            </div>
            
            <div class="form-group">
                <label for="service_id">Select Service (Optional)</label>
                <select id="service_id" name="service_id">
                    <option value="">Choose a service...</option>
                    <?php
                    if ($services_result->num_rows > 0) {
                        while($service = $services_result->fetch_assoc()) {
                            echo "<option value='" . $service["id"] . "'>" . 
                                 $service["name"] . " - $" . $service["price"] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Add Client</button>
            <a href="list.php" class="clear_btn">Back</a>

        </form>

        
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
