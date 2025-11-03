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
    $where_clause = "WHERE name LIKE '%$search_term%' OR contact_person LIKE '%$search_term%' OR email LIKE '%$search_term%' OR phone LIKE '%$search_term%' OR specialization LIKE '%$search_term%'";
}

$sql = "SELECT * FROM service_providers $where_clause ORDER BY name";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Providers List - Service Management System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
    <h1>VSMS</h1>
  <nav>
        <a href="../index.php"><b>Home</b></a>
        <a href="../about.php"><b>Who we are</b></a>
        <a href="../clients/list.php"><b>Clients</b></a>
        <a href="list.php"><b>Service Providers</b></a>
        <a href="../services/list.php"><b>Services</b></a>
  </nav>
</header>
    <div class="container">
        <h1>Service Providers Management</h1>

        <div class="search-form">
            <form method="GET" action="">
                <label for="search">Search Service Providers:</label>
                <input type="text" id="search" name="search" 
                       placeholder="Search by name, contact person, email, phone, or specialization..." 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="btn">Search</button>
                <a href="list.php" class="clear_btn">Clear Search</a>
            </form>
        </div>
        
        <?php if ($search_term): ?>
            <div class="message">
                <p>Search results for: "<strong><?php echo htmlspecialchars($search_term); ?></strong>" 
                   (<?php echo $result->num_rows; ?> provider(s) found)</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <a href="add.php" class="btn btn-success">Add New Service Provider</a>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Specialization</th>
                        <th>Rating</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . ($row["contact_person"] ? $row["contact_person"] : "N/A") . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . ($row["phone"] ? $row["phone"] : "N/A") . "</td>";
                        echo "<td>" . ($row["specialization"] ? $row["specialization"] : "N/A") . "</td>";
                        echo "<td>" . $row["rating"] . "/5.0</td>";
                        echo "<td>" . date('Y-m-d', strtotime($row["created_at"])) . "</td>";
                        echo "<td class='action-links'>";
                        echo "<a href='edit.php?id=" . $row["id"] . "'>Edit</a>";
                        echo "<a href='delete.php?id=" . $row["id"] . "' onclick='return confirm(\"Are you sure you want to delete this service provider?\")'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="message">
                <p><strong>No service providers found!</strong></p>
            </div>
        <?php endif; ?>
        
        <div class="message">
            <p>Total service providers: <strong><?php echo $result->num_rows; ?></strong></p>
        </div>
        
        <div class="message">
            <h3>Provider Statistics</h3>
            <?php
            $stats_sql = "SELECT 
                COUNT(*) as total_providers,
                AVG(rating) as avg_rating,
                COUNT(CASE WHEN specialization IS NOT NULL AND specialization != '' THEN 1 END) as providers_with_specialization
                FROM service_providers";
            
            $stats_result = $conn->query($stats_sql);
            if ($stats_result->num_rows > 0) {
                $stats = $stats_result->fetch_assoc();
                echo "<p><strong>Total Providers:</strong> " . $stats["total_providers"] . "</p>";
                echo "<p><strong>Average Rating:</strong> " . number_format($stats["avg_rating"], 2) . "/5.0</p>";
                echo "<p><strong>Providers with Specialization:</strong> " . $stats["providers_with_specialization"] . "</p>";
            }
            ?>
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
