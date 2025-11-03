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
    $where_clause = "WHERE s.name LIKE '%$search_term%' OR s.description LIKE '%$search_term%' OR sp.name LIKE '%$search_term%'";
}

$sql = "SELECT s.*, sp.name as provider_name, sp.specialization 
        FROM services s 
        LEFT JOIN service_providers sp ON s.provider_id = sp.id 
        $where_clause
        ORDER BY s.name";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services List - Service Management System</title>
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
        <a href="list.php"><b>Services</b></a>
  </nav>
</header>
    <div class="container">
        <h1>Services Management</h1>

        <div class="search-form">
            <form method="GET" action="">
                <label for="search">Search Services:</label>
                <input type="text" id="search" name="search" 
                       placeholder="Search by service name, description, or provider..." 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="btn">Search</button>
                <a href="list.php" class="clear_btn">Clear Search</a>
            </form>
        </div>
        
        <?php if ($search_term): ?>
            <div class="message">
                <p>Search results for: "<strong><?php echo htmlspecialchars($search_term); ?></strong>" 
                   (<?php echo $result->num_rows; ?> service(s) found)</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <a href="add.php" class="btn btn-success">Add New Service</a>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Provider</th>
                        <th>Specialization</th>
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
                        echo "<td>" . ($row["description"] ? substr($row["description"], 0, 50) . "..." : "N/A") . "</td>";
                        echo "<td>$" . number_format($row["price"], 2) . "</td>";
                        echo "<td>" . $row["duration_hours"] . " hour(s)</td>";
                        echo "<td>" . ($row["provider_name"] ? $row["provider_name"] : "No Provider") . "</td>";
                        echo "<td>" . ($row["specialization"] ? $row["specialization"] : "N/A") . "</td>";
                        echo "<td>" . date('Y-m-d', strtotime($row["created_at"])) . "</td>";
                        echo "<td class='action-links'>";
                        echo "<a href='edit.php?id=" . $row["id"] . "'>Edit</a>";
                        echo "<a href='delete.php?id=" . $row["id"] . "' onclick='return confirm(\"Are you sure you want to delete this service?\")'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="message">
                <p><strong>No services found!</strong></p>
            </div>
        <?php endif; ?>
        
        <div class="message">
            <p>Total services: <strong><?php echo $result->num_rows; ?></strong></p>
        </div>
        
        <div class="message">
            <h3>Service Statistics</h3>
            <?php
            $stats_sql = "SELECT 
                COUNT(*) as total_services,
                AVG(price) as avg_price,
                MIN(price) as min_price,
                MAX(price) as max_price,
                AVG(duration_hours) as avg_duration,
                COUNT(CASE WHEN provider_id IS NOT NULL THEN 1 END) as services_with_provider
                FROM services";
            
            $stats_result = $conn->query($stats_sql);
            if ($stats_result->num_rows > 0) {
                $stats = $stats_result->fetch_assoc();
                echo "<p><strong>Total Services:</strong> " . $stats["total_services"] . "</p>";
                echo "<p><strong>Average Price:</strong> $" . number_format($stats["avg_price"], 2) . "</p>";
                echo "<p><strong>Price Range:</strong> $" . number_format($stats["min_price"], 2) . " - $" . number_format($stats["max_price"], 2) . "</p>";
                echo "<p><strong>Average Duration:</strong> " . number_format($stats["avg_duration"], 1) . " hours</p>";
                echo "<p><strong>Services with Provider:</strong> " . $stats["services_with_provider"] . "</p>";
            }
            ?>
        </div>
        
        <div class="message">
            <h3>Services by Provider</h3>
            <?php
            $provider_stats_sql = "SELECT 
                sp.name as provider_name,
                COUNT(s.id) as service_count,
                AVG(s.price) as avg_price
                FROM service_providers sp
                LEFT JOIN services s ON sp.id = s.provider_id
                GROUP BY sp.id, sp.name
                ORDER BY service_count DESC";
            
            $provider_stats_result = $conn->query($provider_stats_sql);
            if ($provider_stats_result->num_rows > 0) {
                echo "<table>";
                echo "<thead><tr><th>Provider</th><th>Services</th><th>Avg Price</th></tr></thead>";
                echo "<tbody>";
                while($provider = $provider_stats_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $provider["provider_name"] . "</td>";
                    echo "<td>" . $provider["service_count"] . "</td>";
                    echo "<td>$" . number_format($provider["avg_price"], 2) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
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
