<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "service_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search_term = "";
$where_clause = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $where_clause = "WHERE c.firstname LIKE '%$search_term%' OR c.lastname LIKE '%$search_term%' OR c.email LIKE '%$search_term%'";
}

$sql = "SELECT c.*, GROUP_CONCAT(s.name SEPARATOR ', ') as services 
        FROM clients c
        LEFT JOIN client_services cs ON c.id = cs.client_id
        LEFT JOIN services s ON cs.service_id = s.id
        $where_clause
        GROUP BY c.id
        ORDER BY c.lastname, c.firstname";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients List - Service Management System</title>
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
        <h1>Clients Management</h1>

        <div class="search-form">
            <form method="GET" action="">
                <label for="search">Search Clients:</label>
                <input type="text" id="search" name="search" 
                       placeholder="Search by first name, last name, or email..." 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="btn">Search</button>
                <a href="list.php" class="clear_btn">Clear Search</a>
            </form>
        </div>
        
        <?php if ($search_term): ?>
            <div class="message">
                <p>Search results for: "<strong><?php echo htmlspecialchars($search_term); ?></strong>" 
                   (<?php echo $result->num_rows; ?> clients found)</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <a href="add.php" class="btn btn-success">Add New Client</a>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Assigned Services</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["firstname"] . "</td>";
                        echo "<td>" . $row["lastname"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . ($row["phone"] ? $row["phone"] : "N/A") . "</td>";
                        echo "<td>" . ($row["services"] ? $row["services"] : "No services assigned") . "</td>";
                        echo "<td>" . date('Y-m-d', strtotime($row["created_at"])) . "</td>";
                        echo "<td class='action-links'>";
                        echo "<a href='edit.php?id=" . $row["id"] . "'>Edit</a>";
                        echo "<a href='delete.php?id=" . $row["id"] . "' onclick='return confirm(\"Are you sure you want to delete this client?\")'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="message">
                <p><strong> No clients found!</strong></p>
            </div>
        <?php endif; ?>
        
        <div class="message">
            <p>Total clients: <strong><?php echo $result->num_rows; ?></strong></p>
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
