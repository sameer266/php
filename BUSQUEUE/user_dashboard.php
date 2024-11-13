<?php
session_start();

// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the username from the session
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BusQueue Dashboard</title>
    <link rel="stylesheet" href="./style/user_dashboard.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>BusQueue</h2>
            <ul>
                <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="map"><i class="fas fa-route"></i> Route</a></li>
                <li><a href="pages/aboutus.html"><i class="fas fa-info-circle"></i> About us</a></li>
                <li><a href="pages/feedback.html"><i class="fas fa-comments"></i> Feedback</a></li>
                <li><a href="./components/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="content">
            <div class="top-bar">
                <h1 style="text-align: center;">Bus Route Map</h1>
                <div class="user-profile">
                    <i class="fas fa-user-circle"></i>
                    <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
                </div>
            </div>

            <!-- Bus Route Data Section -->
            <div class="route-data">
                <h2>Available Bus Routes</h2>
                <table id="routeTable">
                    <thead>
                        <tr>
                            <th>Route Name</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                            <th>Status</th>
                            <th>Estimated Arrival</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamically filled with JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Bus Route Map -->
            <div id="map" style="height: 500px;"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let markers = []; // Array to store markers
        let polyline; // Store the polyline (route line)

        // Function to fetch and display routes
        function fetchRoutes() {
            fetch('./route/routes.php') // Fetch route data from routes.php
                .then(response => response.json())
                .then(routes => {
                    const tableBody = document.getElementById('routeTable').querySelector('tbody');
                    tableBody.innerHTML = ''; // Clear existing rows
                    markers.forEach(marker => marker.remove()); // Remove old markers
                    markers = []; // Clear markers array
                    if (polyline) {
                        polyline.remove(); // Remove old route line
                    }

                    // Loop through the routes and create table rows
                    routes.forEach(route => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${route.route_name}</td>
                            <td>${route.departure}</td>
                            <td>${route.arrival}</td>
                            <td>${route.status}</td>
                            <td>${route.stops[route.stops.length - 1].estimated_time}</td> <!-- Last stop's time -->
                        `;
                        tableBody.appendChild(row);

                        // Plot the bus stops on the map
                        const latLngs = [];
                        route.stops.forEach(stop => {
                            const busIcon = L.icon({
                                iconUrl: 'bus.png',  // Path to your bus image (ensure it's accessible)
                                iconSize: [32, 32],  // Icon size
                                iconAnchor: [16, 16], // Center the icon
                                popupAnchor: [0, -16] // Popup position
                            });

                            const marker = L.marker([stop.lat, stop.lng], { icon: busIcon })
                                .addTo(map)
                                .bindPopup(`<b>${stop.name}</b><br>Estimated Time: ${stop.estimated_time}`)
                                .openPopup();
                            markers.push(marker); // Store marker for future updates

                            // Add the coordinates to the line
                            latLngs.push([stop.lat, stop.lng]);
                        });

                        // Draw the polyline connecting all bus stops
                        polyline = L.polyline(latLngs, { color: 'blue', weight: 4 }).addTo(map);
                        map.fitBounds(polyline.getBounds()); // Zoom map to fit the route
                    });
                })
                .catch(error => {
                    console.error('Error fetching route data:', error);
                });
        }

        // Fetch data immediately on page load
        fetchRoutes();

        // Update data every 10 seconds for real-time updates
        setInterval(fetchRoutes, 10000);

        // Initialize the map (Leaflet.js)
        const map = L.map('map').setView([27.7149, 85.3123], 13); // Default coordinates (adjust as needed)

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    </script>
</body>
</html>
