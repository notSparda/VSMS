<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Service Management System</title>
    <!-- Include the CSS file for styling -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
<h1>VSMS</h1>
  <nav>
        <a href="clients/list.php"><b>Clients</b></a>
        <a href="service_providers/list.php"><b>Service Providers</b></a>
        <a href="services/list.php"><b>Services</b></a>
  </nav>
</header>

    <!-- Hero Section with Background Illustration -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Vehicle Service Management System</h1>
            <p class="hero-subtitle">We will take care of your vehicle.</p>
            <a href="about.php" class="hero-btn">Who we are</a>
        </div>
    </section>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="content-wrapper">
            <!-- Left Column: We Do Service For -->
            <div class="section-left">
                <h2 class="section-title">We Do Service For:</h2>
                <ul class="vehicle-list">
                    <li class="vehicle-item">
                        <span><strong>CARS</strong></span>
                        <span class="separator"></span>
                    </li>
                    <li class="vehicle-item">
                        <span><strong>MOTORCYCLES</strong></span>
                        <span class="separator"></span>
                    </li>
                    <li class="vehicle-item">
                        <span><strong>TRUCKS</strong></span>
                        <span class="separator"></span>
                    </li>
                    <li class="vehicle-item">
                        <span><strong>BUSES</strong></span>
                        <span class="separator"></span>
                    </li>
                </ul>
            </div>

            <!-- Right Column: Our Services -->
            <div class="section-right">
                <h2 class="section-title">Our Services</h2>

                <!-- Service Cards -->
                <div class="services-grid">
                    <div class="service-card">
                        <h3 class="service-title">Change Oil</h3>
                        <p class="service-description">Keep your engine running smoothly with a quick and clean oil change that improves performance and extends engine life.</p>
                    </div>
                    <div class="service-card">
                        <h3 class="service-title">Engine Tune up</h3>
                        <p class="service-description">Boost your vehicle's power and fuel efficiency with a professional engine tune-up that restores optimal performance.</p>
                    </div>
                    <div class="service-card">
                        <h3 class="service-title">Overall Checkup</h3>
                        <p class="service-description">A complete vehicle inspection to ensure everything is in perfect condition — from fluids and brakes to lights and battery.</p>
                    </div>
                    <div class="service-card">
                        <h3 class="service-title">Tire Replacement</h3>
                        <p class="service-description">Stay safe and enjoy a smooth ride with high-quality tire replacement and proper wheel balancing.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>


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
