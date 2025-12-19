<?php
require_once 'user.php';

$error = '';
$success = '';

// 이미 로그인 상태면 달력으로 이동
if (isLoggedIn()) {
    header("Location: calendar.php");
    exit;
}

// 회원가입 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    
    if (strlen($username) < 4) {
        $error = '아이디는 4자 이상이어야 합니다.';
    } elseif (strlen($password) < 4) {
        $error = '비밀번호는 4자 이상이어야 합니다.';
    } elseif ($password !== $password2) {
        $error = '비밀번호가 일치하지 않습니다.';
    } elseif (register($username, $password)) {
        $success = '회원가입이 완료되었습니다! 로그인해주세요.';
    } else {
        $error = '이미 존재하는 아이디입니다.';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
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
        .register-box {
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
        .success {
            color: green;
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
    <div class="register-box">
        <h2>📅 회원가입</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="아이디 (4자 이상)" required>
            <input type="password" name="password" placeholder="비밀번호 (4자 이상)" required>
            <input type="password" name="password2" placeholder="비밀번호 확인" required>
            <button type="submit">회원가입</button>
        </form>
        
        <div class="link">
            이미 계정이 있으신가요? <a href="login.php">로그인</a>
        </div>
    </div>
</body>
</html>

