<?php
require_once 'user.php';

$error = '';

// 이미 로그인 상태면 달력으로 이동
if (isLoggedIn()) {
    header("Location: calendar.php");
    exit;
}

// 로그인 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (login($username, $password)) {
        header("Location: calendar.php");
        exit;
    } else {
        $error = '아이디 또는 비밀번호가 틀렸습니다.';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>로그인</title>
    <style>
        body {
            font-family: 맑은 고딕, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 350px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #4a90d9;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background: #357abd;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .link {
            text-align: center;
            margin-top: 20px;
        }
        .link a {
            color: #4a90d9;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>📅 달력 로그인</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="아이디" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <button type="submit">로그인</button>
        </form>
        
        <div class="link">
            계정이 없으신가요? <a href="register.php">회원가입</a>
        </div>
    </div>
</body>
</html>

