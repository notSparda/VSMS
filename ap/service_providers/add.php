<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "service_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $contact_person = $email = $phone = $specialization = $rating = "";
$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $contact_person = trim($_POST["contact_person"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $specialization = trim($_POST["specialization"]);
    $rating = trim($_POST["rating"]);
    
    $is_valid = true;
    $validation_errors = array();
    
    if (empty(trim($name))) {
        $is_valid = false;
        $validation_errors[] = "Business name is required";
    }
    
    if (!empty(trim($contact_person))) {
        $contact_person_valid = true;
        for ($i = 0; $i < strlen($contact_person); $i++) {
            $char = $contact_person[$i];
            if (!(($char >= 'a' && $char <= 'z') || 
                  ($char >= 'A' && $char <= 'Z') || 
                  $char == ' ')) {
                $contact_person_valid = false;
                break;
            }
        }
        if (!$contact_person_valid) {
            $is_valid = false;
            $validation_errors[] = "Contact person name can only contain letters";
        }
    }
    
    if (empty(trim($specialization))) {
        $is_valid = false;
        $validation_errors[] = "Specialization is required";
    } else {
        $specialization_valid = true;
        for ($i = 0; $i < strlen($specialization); $i++) {
            $char = $specialization[$i];
            if (!(($char >= 'a' && $char <= 'z') || 
                  ($char >= 'A' && $char <= 'Z') || 
                  $char == ' ')) {
                $specialization_valid = false;
                break;
            }
        }
        if (!$specialization_valid) {
            $is_valid = false;
            $validation_errors[] = "Specialization can only contain letters and spaces";
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
        
        $check_phone_sql = "SELECT id FROM service_providers WHERE phone = '$phone'";
        $check_phone_result = $conn->query($check_phone_sql);
        if ($check_phone_result->num_rows > 0) {
            $is_valid = false;
            $validation_errors[] = "This phone number is already registered";
        }
    }
    
    $check_email_sql = "SELECT id FROM service_providers WHERE email = '$email'";
    $check_email_result = $conn->query($check_email_sql);
    if ($check_email_result->num_rows > 0) {
        $is_valid = false;
        $validation_errors[] = "This email address is already registered";
    }
    
    if (!empty(trim($rating))) {
        if ($rating < 0 || $rating > 5) {
            $is_valid = false;
            $validation_errors[] = "Rating must be between 0 and 5";
        }
    }
    
    if ($is_valid) {
        $sql = "INSERT INTO service_providers (name, contact_person, email, phone, specialization, rating) 
                VALUES ('$name', '$contact_person', '$email', '$phone', '$specialization', '$rating')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Service provider created successfully!";
            $message_type = "success";
            
            $name = $contact_person = $email = $phone = $specialization = $rating = "";
        } else {
            $message = "Error creating service provider: " . $conn->error;
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
    <title>Add New Service Provider - Service Management System</title>
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
        <h1>Add New Service Provider</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
                <?php if ($message_type == "error"): ?>
                    <br><br>
                    <a href="list.php" class="btn">Back to Service Providers List</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Business Name *</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo $name; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" id="contact_person" name="contact_person" 
                       value="<?php echo $contact_person; ?>">
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
                <label for="specialization">Specialization</label>
                <input type="text" id="specialization" name="specialization" 
                       value="<?php echo $specialization; ?>"
                       placeholder="e.g., Computer Repair, House Cleaning, Landscaping">
            </div>
            
            <div class="form-group">
                <label for="rating">Rating (0.0 - 5.0)</label>
                <input type="number" id="rating" name="rating" 
                       value="<?php echo $rating; ?>" 
                       min="0" max="5" step="0.1"
                       placeholder="4.5">
            </div>
            
            <button type="submit" class="btn btn-success">Add Service Provider</button>
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
