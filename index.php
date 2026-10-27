<?php
session_start();
include "connection.php";
include "convertnilai.php";

// 1. PENGATURAN PAGE LIMIT BERBASIS SESSION
if (isset($_GET['limit']) && in_array((int)$_GET['limit'], [5, 10, 15])) {
    $_SESSION['limit'] = (int)$_GET['limit'];
}
$limit = isset($_SESSION['limit']) ? $_SESSION['limit'] : 10;

// 2. PENGATURAN FILTER, SORTING, DAN PAGINASI
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$predikat_filter = isset($_GET['predikat']) ? trim($_GET['predikat']) : '';
$sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'asc' ? 'asc' : 'desc';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$offset = ($page - 1) * $limit;

// 3. QUERY SEARCH DAN FILTER PREDIKAT
$count_sql = "SELECT * FROM nilaimhsw WHERE 1=1";
$params = [];
$types = "";

if ($search != '') {
    $count_sql .= " AND (nim LIKE ? OR namalengkap LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($params)) {
    $stmt_count = $conn->prepare($count_sql);
    $stmt_count->bind_param($types, ...$params);
    $stmt_count->execute();
    $res_count = $stmt_count->get_result();
} else {
    $res_count = $conn->query($count_sql);
}

$all_filtered_rows = [];
if ($res_count && $res_count->num_rows > 0) {
    while ($r = $res_count->fetch_assoc()) {
        $p = getPredikat($r['nilaiakhir']);
        if ($predikat_filter != '' && $p != $predikat_filter) {
            continue;
        }
        $all_filtered_rows[] = $r;
    }
}

// 4. SORTING DATA BERDASARKAN NILAI AKHIR (ASC / DESC)
usort($all_filtered_rows, function($a, $b) use ($sort_order) {
    if ($a['nilaiakhir'] == $b['nilaiakhir']) return 0;
    return ($sort_order == 'asc') 
        ? ($a['nilaiakhir'] < $b['nilaiakhir'] ? -1 : 1) 
        : ($a['nilaiakhir'] > $b['nilaiakhir'] ? -1 : 1);
});

// Perhitungan pagination
$total_records = count($all_filtered_rows);
$total_pages = ceil($total_records / $limit);
if ($total_pages < 1) { $total_pages = 1; }
if ($page > $total_pages) { $page = $total_pages; $offset = ($page - 1) * $limit; }

$page_data = array_slice($all_filtered_rows, $offset, $limit);
?>
<!doctype html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai Mahasiswa</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #1e1e1e;
            color: #ffffff;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .card-custom {
            background-color: #2a2a2a;
            border: none;
            border-radius: 8px;
        }
        .table-custom {
            --bs-table-bg: transparent;
            --bs-table-color: #ffffff;
            width: 100%;
            margin-bottom: 0 !important;
        }
        .table-custom th {
            color: #ffffff;
            font-weight: 600;
            border-bottom: 1px solid #3d3d3d;
            padding-top: 12px;
            padding-bottom: 12px;
            white-space: nowrap;
        }
        .table-custom td {
            color: #ffffff;
            border-bottom: 1px solid #3d3d3d;
            padding-top: 12px;
            padding-bottom: 12px;
            vertical-align: middle;
            white-space: nowrap;
        }
        .btn-action-edit {
            color: #ffffff;
            border: 1px solid #5a5a5a;
            background: transparent;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .btn-action-edit:hover {
            background-color: #3d3d3d;
            color: #ffffff;
        }
        .btn-action-delete {
            color: #dc3545;
            border: 1px solid #5a5a5a;
            background: transparent;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .btn-action-delete:hover {
            background-color: #dc3545;
            color: #ffffff;
        }
        .form-control-custom, .form-select-custom {
            background-color: #333333 !important;
            border: 1px solid #444444 !important;
            color: #ffffff !important;
        }
        .form-control-custom::placeholder {
            color: #aaaaaa;
        }
        .search-input-group {
            border: 1px solid #444444;
            border-radius: 6px;
            background-color: #333333;
            overflow: hidden;
        }
        .search-input-group .input-group-text {
            background-color: transparent;
            border: none;
            color: #aaaaaa;
        }
        .search-input-group .form-control {
            border: none !important;
            background-color: transparent !important;
        }
        .breadcrumb-link {
            color: #9ea5ad;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .breadcrumb-link:hover {
            color: #ffffff;
        }
        .breadcrumb-active {
            color: #ffffff;
            font-weight: 500;
        }
        .alert-duplicate-custom {
            background-color: #2d2d2d;
            border: 1px solid #555555;
            color: #f8f9fa;
        }
        .pagination .page-link {
            background-color: #333333;
            border-color: #444444;
            color: #cccccc;
        }
        .pagination .page-link:hover {
            background-color: #444444;
            color: #ffffff;
            border-color: #555555;
        }
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #ffffff;
        }
        .pagination .page-item.disabled .page-link {
            background-color: #222222;
            border-color: #333333;
            color: #666666;
        }

        .modal-confirm-dark .modal-dialog {
            margin-top: 50px;
        }
        .modal-confirm-dark .modal-content {
            background-color: #252526;
            border: 1px solid #3c3c3c;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
        }
        .modal-confirm-dark .modal-header {
            border-bottom: 1px solid #333333;
            padding: 12px 16px;
        }
        .modal-confirm-dark .modal-body {
            padding: 20px 16px;
            color: #cccccc;
            font-size: 0.95rem;
        }
        .modal-confirm-dark .modal-footer {
            border-top: 1px solid #333333;
            padding: 10px 16px;
            background-color: #1e1e1e;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
    </style>
</head>
<body class="p-4">

    <div class="container-fluid">
        <!-- Header Utama & Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0 text-white">Rekap Nilai Mahasiswa</h4>
            <div class="fs-6">
                <span class="breadcrumb-active">Beranda</span>
                <span class="text-white-50 mx-1">/</span>
                <a href="index.php" class="breadcrumb-link">Nilai</a>
            </div>
        </div>

        <!-- Alert Notifikasi Status -->
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data berhasil disimpan/diperbarui!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'deleted'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Data berhasil dihapus!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'error_duplicate'): ?>
                <div class="alert alert-duplicate-custom alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2 text-warning"></i><strong>Gagal menyimpan:</strong> NIM tersebut sudah terdaftar di sistem.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Card Container -->
        <div class="card card-custom p-3 mb-4">
            <!-- Filter Bar -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div class="fw-bold text-white fs-6">
                    Direktori Nilai
                </div>
                
                <form action="index.php" method="GET" class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                    <input type="hidden" name="order" value="<?= $sort_order ?>">
                    
                    <div class="input-group input-group-sm search-input-group" style="width: 220px;">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" class="form-control form-control-custom" placeholder="Cari Berdasarkan Nama" value="<?= htmlspecialchars($search) ?>">
                    </div>

                    <!-- Dropdown Filter Predikat -->
                    <select name="predikat" class="form-select form-select-sm form-select-custom rounded" style="width: 160px;" onchange="this.form.submit()">
                        <option value="">Semua Predikat</option>
                        <option value="Sangat Memuaskan" <?= $predikat_filter == 'Sangat Memuaskan' ? 'selected' : '' ?>>Sangat Memuaskan</option>
                        <option value="Memuaskan" <?= $predikat_filter == 'Memuaskan' ? 'selected' : '' ?>>Memuaskan</option>
                        <option value="Cukup" <?= $predikat_filter == 'Cukup' ? 'selected' : '' ?>>Cukup</option>
                        <option value="Kurang" <?= $predikat_filter == 'Kurang' ? 'selected' : '' ?>>Kurang</option>
                        <option value="Sangat Kurang" <?= $predikat_filter == 'Sangat Kurang' ? 'selected' : '' ?>>Sangat Kurang</option>
                    </select>

                    <button type="button" class="btn btn-primary btn-sm rounded px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Data Nilai
                    </button>
                </form>
            </div>

            <!-- Area Tabel Data -->
            <div class="table-responsive">
                <table class="table table-custom align-middle text-center">
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th class="text-start">Nama Lengkap</th>
                            <th>
                                <a href="index.php?page=1&search=<?= urlencode($search) ?>&predikat=<?= urlencode($predikat_filter) ?>&order=<?= $sort_order == 'asc' ? 'desc' : 'asc' ?>" 
                                   class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                    Nilai Akhir
                                    <?php if ($sort_order == 'asc'): ?>
                                        <i class="bi bi-caret-up-fill text-white-50 fs-7"></i>
                                    <?php else: ?>
                                        <i class="bi bi-caret-down-fill text-white-50 fs-7"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Nilai Huruf</th>
                            <th>Predikat</th>
                            <th>Tgl Dibuat</th>
                            <th>Tgl Diubah</th>
                            <th style="min-width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($page_data) > 0): ?>
                            <?php foreach ($page_data as $row): 
                                $nilai_huruf = getNilaiHuruf($row['nilaiakhir']);
                                $predikat = getPredikat($row['nilaiakhir']);
                            ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($row['nim']) ?></td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center justify-content-start gap-2">
                                            <?php if (!empty($row['foto']) && file_exists('uploads/' . $row['foto'])): ?>
                                                <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" 
                                                     alt="Foto Profil" 
                                                     class="rounded-circle shadow-sm" 
                                                     width="28" 
                                                     height="28" 
                                                     style="object-fit: cover;">
                                            <?php else: ?>
                                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['namalengkap']) ?>&background=0d6efd&color=fff&size=32" 
                                                     alt="Foto Profil" 
                                                     class="rounded-circle shadow-sm" 
                                                     width="28" 
                                                     height="28">
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($row['namalengkap']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= number_format($row['nilaiakhir'], 2) ?></td>
                                    <td class="text-center"><?= $nilai_huruf ?></td>
                                    <td class="text-center"><?= $predikat ?></td>
                                    <td class="text-center"><?= $row['created_at'] ?></td>
                                    <td class="text-center"><?= $row['updated_at'] ?></td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button class="btn-action-edit btn-edit" 
                                                    data-nim="<?= htmlspecialchars($row['nim']) ?>" 
                                                    data-nama="<?= htmlspecialchars($row['namalengkap']) ?>" 
                                                    data-nilai="<?= htmlspecialchars($row['nilaiakhir']) ?>" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEdit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            
                                            <button type="button" 
                                                    class="btn-action-delete btn-delete" 
                                                    data-nim="<?= htmlspecialchars($row['nim']) ?>" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalConfirmDelete">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center py-4 text-white-50">Data tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer Paginasi & Page Limit -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 border-top border-secondary-subtle gap-2">
                <div class="text-white-50 small">
                    <?php 
                    $start_num = $total_records > 0 ? $offset + 1 : 0;
                    $end_num = min($offset + $limit, $total_records);
                    ?>
                    Menampilkan <?= $start_num ?> - <?= $end_num ?> dari <?= $total_records ?> data
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <label for="pageLimit" class="text-white-50 small m-0 fw-medium" style="white-space: nowrap;">Page Limit:</label>
                        <select id="pageLimit" class="form-select form-select-sm form-select-custom rounded" style="width: 70px;" onchange="changePageLimit(this.value)">
                            <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="15" <?= $limit == 15 ? 'selected' : '' ?>>15</option>
                        </select>
                    </div>

                    <nav>
                        <ul class="pagination pagination-sm m-0">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="index.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&predikat=<?= urlencode($predikat_filter) ?>&order=<?= $sort_order ?>">&laquo;</a>
                            </li>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&predikat=<?= urlencode($predikat_filter) ?>&order=<?= $sort_order ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="index.php?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&predikat=<?= urlencode($predikat_filter) ?>&order=<?= $sort_order ?>">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL 1: Tambah Data -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light border-secondary">
                <div class="modal-header border-secondary">
                    <div>
                        <h5 class="modal-title mb-0">Tambah Data Nilai</h5>
                        <small class="text-white-50 fs-7">(* Field bertanda bintang wajib diisi)</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="createnilai.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">NIM <span class="text-danger">*</span></label>
                            <input type="text" name="nim" class="form-control form-control-custom" placeholder="Masukkan NIM (contoh: 3202316001)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="namalengkap" class="form-control form-control-custom" placeholder="Masukkan Nama Lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nilai Akhir <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="nilaiakhir" min="0" max="100" class="form-control form-control-custom" placeholder="Masukkan Nilai (0 - 100)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Profil (Opsional)</label>
                            <input type="file" name="foto" class="form-control form-control-custom" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Edit Data -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light border-secondary">
                <div class="modal-header border-secondary">
                    <div>
                        <h5 class="modal-title mb-0">Edit Data Nilai</h5>
                        <small class="text-white-50 fs-7">(* Field bertanda bintang wajib diisi)</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="updatenilai.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="nim" id="edit_nim">
                        <input type="hidden" name="page" value="<?= $page ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <input type="hidden" name="predikat" value="<?= htmlspecialchars($predikat_filter) ?>">
                        <input type="hidden" name="order" value="<?= htmlspecialchars($sort_order) ?>">

                        <div class="mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" id="edit_nim_show" class="form-control form-control-custom" placeholder="NIM Mahasiswa" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="namalengkap" id="edit_nama" class="form-control form-control-custom" placeholder="Masukkan Nama Lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nilai Akhir <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="nilaiakhir" id="edit_nilai" min="0" max="100" class="form-control form-control-custom" placeholder="Masukkan Nilai (0 - 100)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ganti Foto Profil (Opsional)</label>
                            <input type="file" name="foto" class="form-control form-control-custom" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: Modal Konfirmasi Hapus -->
    <div class="modal fade modal-confirm-dark" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-white fw-bold m-0">Konfirmasi</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <form action="deletenilai.php" method="GET" class="m-0">
                        <input type="hidden" name="nim" id="delete_target_nim">
                        <button type="submit" class="btn btn-danger btn-sm px-3">OK</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_nim_show').value = this.getAttribute('data-nim');
                document.getElementById('edit_nim').value = this.getAttribute('data-nim');
                document.getElementById('edit_nama').value = this.getAttribute('data-nama');
                document.getElementById('edit_nilai').value = this.getAttribute('data-nilai');
            });
        });

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const nim = this.getAttribute('data-nim');
                document.getElementById('delete_target_nim').value = nim;
            });
        });

        function changePageLimit(limitVal) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('limit', limitVal);
            urlParams.set('page', 1);
            window.location.search = urlParams.toString();
        }
    </script>
</body>
</html>