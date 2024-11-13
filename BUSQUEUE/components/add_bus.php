<?php
session_start();
include 'config.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$bus_number = $_POST['bus_number'];
$route = $_POST['route'];
$departure_time = $_POST['departure_time'];

$sql = "INSERT INTO bus_queue (bus_number, route, departure_time) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $bus_number, $route, $departure_time);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
