<?php
include 'db/connection.php';

// Handle form submission for adding a new business hack
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_hack'])) {
    $hack = $_POST['business_hack'];
    $name = $_POST['business_name'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO business_hacks (business_hack, business_name, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $hack, $name, $phone);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Business hack submitted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Handle deletion of business hack
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM business_hacks WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Business hack deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Handle update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_hack'])) {
    $update_id = $_POST['update_id'];
    $updated_hack = $_POST['business_hack'];
    $updated_name = $_POST['business_name'];
    $updated_phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE business_hacks SET business_hack = ?, business_name = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("sssi", $updated_hack, $updated_name, $updated_phone, $update_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Business hack updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Hacks</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <!-- Form Section for Adding Business Hack -->
        <h1>Submit Your Business Hack</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="business_hack" class="form-label">Business Hack</label>
                <textarea class="form-control" id="business_hack" name="business_hack" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="business_name" class="form-label">Business Name</label>
                <input type="text" class="form-control" id="business_name" name="business_name" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <button type="submit" name="submit_hack" class="btn btn-primary">Post Hack</button>
        </form>

        <!-- Display Business Hacks -->
        <div class="mt-5">
            <h2>Business Hacks</h2>
            <?php
            // Fetch business hacks from the database
            $result = $conn->query("SELECT * FROM business_hacks ORDER BY created_at DESC");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='business-hack-container'>";
                    echo "<div class='business-hack-title'>" . htmlspecialchars($row['business_hack']) . "</div>";
                    echo "<div class='business-hack-meta'>by <a href='https://wa.me/" . htmlspecialchars($row['phone']) . "' target='_blank'>" . htmlspecialchars($row['business_name']) . "</a></div>";
                    // Edit and Delete buttons
                    echo "<a href='?edit_id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                    echo "<a href='?delete_id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this hack?\")'>Delete</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No business hacks submitted yet. Be the first!</p>";
            }
            ?>
        </div>

        <!-- Display Edit Form if editing a hack -->
        <?php
        if (isset($_GET['edit_id'])) {
            $edit_id = $_GET['edit_id'];
            $result = $conn->query("SELECT * FROM business_hacks WHERE id = $edit_id");
            $row = $result->fetch_assoc();
        ?>
        <div class="mt-5">
            <h2>Edit Business Hack</h2>
            <form method="POST" action="">
                <input type="hidden" name="update_id" value="<?php echo $row['id']; ?>">
                <div class="mb-3">
                    <label for="business_hack" class="form-label">Business Hack</label>
                    <textarea class="form-control" id="business_hack" name="business_hack" rows="3" required><?php echo htmlspecialchars($row['business_hack']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="business_name" class="form-label">Business Name</label>
                    <input type="text" class="form-control" id="business_name" name="business_name" required value="<?php echo htmlspecialchars($row['business_name']); ?>">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" required value="<?php echo htmlspecialchars($row['phone']); ?>">
                </div>
                <button type="submit" name="update_hack" class="btn btn-primary">Update Hack</button>
            </form>
        </div>
        <?php } ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
