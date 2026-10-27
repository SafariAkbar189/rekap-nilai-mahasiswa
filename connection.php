<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'db5e';

//koneksi ke database
$conn = mysqli_connect($servername, $username, $password, $dbname);

//validasi koneksi ke database
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>