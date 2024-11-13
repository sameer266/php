<?php
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "bus_queue");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the addition of a new bus route
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bus_number'])) {
    $bus_number = $_POST['bus_number'];
    $route = $_POST['route'];
    $departure_time = $_POST['departure_time'];

    $stmt = $conn->prepare("INSERT INTO routes (bus_number, route, departure_time) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $bus_number, $route, $departure_time);
    $stmt->execute();
    $stmt->close();

    // Redirect to refresh the page after adding bus
    header("Location: admin_dashboard.php");
    exit();
}

// Handle route deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM routes WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to refresh the page after deletion
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all bus routes
$result = $conn->query("SELECT * FROM routes ORDER BY departure_time");
$buses = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bus Routes</title>
    <link rel="stylesheet" href="./style/admin.css">
</head>
<body>
    <h2>Admin Dashboard - Add Bus to Queue</h2>

    <!-- Form to add bus route -->
    <form method="POST">
        <label for="bus_number">Bus Number:</label>
        <input type="text" id="bus_number" name="bus_number" required>

        <label for="route">Route:</label>
        <input type="text" id="route" name="route" required>

        <label for="departure_time">Departure Time:</label>
        <input type="time" id="departure_time" name="departure_time" required>

        <button type="submit">Add Bus</button>
    </form>

    <h3>Current Bus Routes</h3>
    <table>
        <thead>
            <tr>
                <th>Bus Number</th>
                <th>Route</th>
                <th>Departure Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($buses as $bus): ?>
                <tr>
                    <td><?= $bus['bus_number'] ?></td>
                    <td><?= $bus['route'] ?></td>
                    <td><?= $bus['departure_time'] ?></td>
                    <td>
                        <a href="?delete_id=<?= $bus['id'] ?>" onclick="return confirm('Are you sure you want to delete this route?');">
                            <button>Delete</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="logout.php">Logout</a>
</body>
</html>
