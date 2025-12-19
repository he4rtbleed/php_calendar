<?php
// 데이터베이스 연결 설정
$host = '127.0.0.1';
$username = 'root';
$password = 'tiger';
$dbname = 'calendar_db';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error() . "<br>먼저 db_setup.sql을 실행해주세요.");
}

mysqli_set_charset($conn, "utf8");
?>
