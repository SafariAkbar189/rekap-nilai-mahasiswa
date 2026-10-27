<?php
require_once 'connection.php';

if (isset($_GET['nim'])) {
    $nim = trim($_GET['nim']);
    $stmt = $conn->prepare("DELETE FROM nilaimhsw WHERE nim = ?");
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $stmt->close();
}

// Kembali ke index.php, dengan status yang sama dengan yang dibaca index.php
header("Location: index.php?status=deleted");
exit;
?>