<?php
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim = trim($_POST['nim']);
    $namalengkap = trim($_POST['namalengkap']);
    $nilaiakhir = floatval($_POST['nilaiakhir']);

    // 1. Cek Duplikasi NIM
    $check_stmt = $conn->prepare("SELECT nim FROM nilaimhsw WHERE nim = ?");
    $check_stmt->bind_param("s", $nim);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        header("Location: index.php?status=error_duplicate");
        exit();
    }
    $check_stmt->close();

    // 2. Olah Upload Foto
    $nama_foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $nama_foto = time() . '_' . uniqid() . '.' . $ext;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $nama_foto);
        }
    }

    // 3. Insert Data Baru
    $stmt = $conn->prepare("INSERT INTO nilaimhsw (nim, namalengkap, nilaiakhir, foto, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssds", $nim, $namalengkap, $nilaiakhir, $nama_foto);

    if ($stmt->execute()) {
        header("Location: index.php?status=success");
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
}
?>