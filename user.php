<?php
require_once 'db.php';

session_start();

// 회원가입
function register($username, $password) {
    global $conn;
    
    // 중복 체크
    $username = mysqli_real_escape_string($conn, $username);
    $sql = "SELECT id FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        return false; // 이미 존재하는 아이디
    }
    
    // 비밀번호 해시화 후 저장
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";
    
    return mysqli_query($conn, $sql);
}

// 로그인
function login($username, $password) {
    global $conn;
    
    $username = mysqli_real_escape_string($conn, $username);
    $sql = "SELECT id, username, password FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
    }
    return false;
}

// 로그아웃
function logout() {
    session_destroy();
}

// 로그인 체크
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// 현재 로그인한 사용자 ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// 현재 로그인한 사용자명
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}
?>
