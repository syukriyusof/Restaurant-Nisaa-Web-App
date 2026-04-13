<?php
require_once __DIR__ . "/../config/db.php";

$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

$totalTables = 10;

if ($date === '' || $time === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Date and time are required.'
    ]);
    exit();
}

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total_reserved
    FROM reservations
    WHERE reservation_date = ?
      AND reservation_time = ?
      AND status IN ('pending', 'confirmed', 'completed')
");
$stmt->bind_param("ss", $date, $time);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$reserved = (int)($row['total_reserved'] ?? 0);
$available = max($totalTables - $reserved, 0);

echo json_encode([
    'success' => true,
    'total_tables' => $totalTables,
    'reserved_tables' => $reserved,
    'available_tables' => $available,
    'is_available' => $available > 0
]);