<?php
// Set Content-Type to JSON
header('Content-Type: application/json');

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bus_queue";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Query to fetch all bus routes ordered by departure time
$query = "SELECT * FROM routes ORDER BY departure_time";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $routes = [];

    // Fetch all routes and their respective stops
    while ($row = $result->fetch_assoc()) {
        $route_id = $row['id']; // Assuming 'id' is the primary key of the route
        $stops_query = "SELECT * FROM stops WHERE route_id = $route_id";
        $stops_result = $conn->query($stops_query);

        $stops = [];
        while ($stop = $stops_result->fetch_assoc()) {
            $stops[] = $stop;
        }

        $routes[] = [
            'route_id' => $row['id'],
            'route_name' => $row['route'],
            'departure' => $row['departure_time'],
            'status' => $row['status'],
            'stops' => $stops
        ];
    }

    // Return the routes as a JSON response
    echo json_encode($routes);
} else {
    // If no routes are found, return an error
    echo json_encode(["error" => "No routes found"]);
}

$conn->close();
?>
