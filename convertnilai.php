<?php
function getNilaiHuruf($nilai) {
    if ($nilai >= 80) return 'A';
    if ($nilai >= 70) return 'B';
    if ($nilai >= 60) return 'C';
    if ($nilai >= 50) return 'D';
    return 'E';
}

function getPredikat($nilai) {
    if ($nilai >= 80) return 'Sangat Memuaskan';
    if ($nilai >= 70) return 'Memuaskan';
    if ($nilai >= 60) return 'Cukup';
    if ($nilai >= 50) return 'Kurang';
    return 'Sangat Kurang';
}
?>