<?php
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim = trim($_POST['nim']);
    $namalengkap = trim($_POST['namalengkap']);
    $nilaiakhir = floatval($_POST['nilaiakhir']);

    // Tangkap parameter halaman & filter
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';
    $predikat = isset($_POST['predikat']) ? trim($_POST['predikat']) : '';
    $order = isset($_POST['order']) ? trim($_POST['order']) : 'desc';

    if (empty($nim)) {
        echo "Error: NIM tidak ditemukan.";
        exit();
    }

    // Olah Upload Foto Baru jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $nama_foto = time() . '_' . uniqid() . '.' . $ext;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $nama_foto);

            $stmt = $conn->prepare("UPDATE nilaimhsw SET namalengkap = ?, nilaiakhir = ?, foto = ?, updated_at = NOW() WHERE nim = ?");
            $stmt->bind_param("sdss", $namalengkap, $nilaiakhir, $nama_foto, $nim);
        } else {
            $stmt = $conn->prepare("UPDATE nilaimhsw SET namalengkap = ?, nilaiakhir = ?, updated_at = NOW() WHERE nim = ?");
            $stmt->bind_param("sds", $namalengkap, $nilaiakhir, $nim);
        }
    } else {
        $stmt = $conn->prepare("UPDATE nilaimhsw SET namalengkap = ?, nilaiakhir = ?, updated_at = NOW() WHERE nim = ?");
        $stmt->bind_param("sds", $namalengkap, $nilaiakhir, $nim);
    }

    if ($stmt->execute()) {
        // Redirect kembali dengan membawa nomor halaman & filter aktif
        $redirect_url = "index.php?status=success&page=" . $page;
        if (!empty($search)) { $redirect_url .= "&search=" . urlencode($search); }
        if (!empty($predikat)) { $redirect_url .= "&predikat=" . urlencode($predikat); }
        if (!empty($order)) { $redirect_url .= "&order=" . urlencode($order); }

        header("Location: " . $redirect_url);
    } else {
        echo "Gagal memperbarui data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
}
?>