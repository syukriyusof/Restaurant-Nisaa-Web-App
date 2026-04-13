<?php
require_once __DIR__ . "/../includes/navbar_customer.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

require_login();

$message = "";
$errors = [];
$user_id = $_SESSION["user_id"];


// Handle reservation submission
$totalTables = 10;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $reservation_date = $_POST["reservation_date"] ?? "";
    $reservation_time = $_POST["reservation_time"] ?? "";
    $pax = (int) ($_POST["pax"] ?? 0);

    if ($reservation_date === "" || $reservation_time === "" || $pax <= 0) {
        $errors[] = "All fields are required.";
    } else {
        // Check current availability
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total_reserved
            FROM reservations
            WHERE reservation_date = ?
              AND reservation_time = ?
              AND status IN ('pending', 'confirmed', 'completed')
        ");
        $stmt->bind_param("ss", $reservation_date, $reservation_time);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        $reserved = (int)($row['total_reserved'] ?? 0);
        $available = max($totalTables - $reserved, 0);

        if ($available <= 0) {
            $errors[] = "Selected slot is fully booked. Please choose another time.";
        } else {
            $status = "confirmed"; // automatic confirmation

            $stmt = $conn->prepare("
                INSERT INTO reservations (user_id, reservation_date, reservation_time, pax, status)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("issis", $user_id, $reservation_date, $reservation_time, $pax, $status);

            if ($stmt->execute()) {
                $message = "Reservation confirmed successfully!";
            } else {
                $errors[] = "Failed to submit reservation.";
            }
            $stmt->close();
        }
    }
}

// Get customer's reservation history
$stmt = $conn->prepare("
    SELECT reservation_id, reservation_date, reservation_time, pax, status, created_at
    FROM reservations
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>
<?php require_once __DIR__ . "/../includes/navbar_customer.php"; ?>

<div class="container page-section" style="max-width: 900px;">
    <h2 class="mb-4">Reservation</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Reservation Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong>Make a New Reservation</strong>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Reservation Date</label>
                        <input type="date" name="reservation_date" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Reservation Time</label>
                        <input type="time" name="reservation_time" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Number of Pax</label>
                        <input type="number" name="pax" class="form-control" min="1" required>
                    </div>
                </div>

                <button class="btn btn-primary">Submit Reservation</button>
                    <div class="mt-3">
                     <div id="availability-box" class="alert alert-secondary mb-0">
                        Select reservation date and time to check table availability.
                 </div>
            </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
    <div class="card-header">
        <strong>Table Availability Information</strong>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Total Tables</th>
                <td>10</td>
            </tr>
            <tr>
                <th>Availability Status</th>
                <td id="availability-status">Select date and time to view availability</td>
            </tr>
        </table>
    </div>
</div>

    <!-- Reservation History -->
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>My Reservation Records</strong>
        </div>
        <div class="card-body">
            <?php if ($reservations->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Reservation ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Pax</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $reservations->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row["reservation_id"] ?></td>
                                    <td><?= htmlspecialchars($row["reservation_date"]) ?></td>
                                    <td><?= htmlspecialchars($row["reservation_time"]) ?></td>
                                    <td><?= htmlspecialchars($row["pax"]) ?></td>
                                    <td>
                                        <?php
                                        $status = $row["status"];
                                        $badgeClass = "secondary";

                                        if ($status === "pending") $badgeClass = "warning";
                                        elseif ($status === "confirmed") $badgeClass = "primary";
                                        elseif ($status === "completed") $badgeClass = "success";
                                        elseif ($status === "cancelled") $badgeClass = "danger";
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>">
                                            <?= ucfirst(htmlspecialchars($status)) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row["created_at"]) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No reservation records found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const dateInput = document.querySelector('input[name="reservation_date"]');
const timeInput = document.querySelector('input[name="reservation_time"]');
const availabilityBox = document.getElementById('availability-box');
const availabilityStatus = document.getElementById('availability-status');

function checkAvailability() {
    const date = dateInput.value;
    const time = timeInput.value;

    if (!date || !time) {
        availabilityBox.className = 'alert alert-secondary mb-0';
        availabilityBox.textContent = 'Select reservation date and time to check table availability.';
        availabilityStatus.textContent = 'Select date and time to view availability';
        return;
    }

    fetch(`/fyp-nisaa/pages/check_availability.php?date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                availabilityBox.className = 'alert alert-danger mb-0';
                availabilityBox.textContent = data.message;
                return;
            }

            if (data.is_available) {
                availabilityBox.className = 'alert alert-success mb-0';
                availabilityBox.innerHTML = `
                    <strong>Available Tables:</strong> ${data.available_tables} / ${data.total_tables}<br>
                    <small>${data.reserved_tables} table(s) already reserved for this slot.</small>
                `;
            } else {
                availabilityBox.className = 'alert alert-danger mb-0';
                availabilityBox.innerHTML = `<strong>Fully Booked</strong>`;
                availabilityStatus.textContent = 'Fully booked';
            }
        })
        .catch(() => {
            availabilityBox.className = 'alert alert-danger mb-0';
            availabilityBox.textContent = 'Failed to check availability.';
        });
}

dateInput.addEventListener('change', checkAvailability);
timeInput.addEventListener('change', checkAvailability);
</script>

</body>
</html>