<?php
require_once 'event.php';

// 로그인 체크
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// 로그아웃 처리
if (isset($_GET['logout'])) {
    logout();
    header("Location: login.php");
    exit;
}

// 현재 년/월 설정
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// 월 범위 조정
if ($month < 1) {
    $month = 12;
    $year--;
} elseif ($month > 12) {
    $month = 1;
    $year++;
}

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        addEvent($_POST['event_date'], $_POST['title'], $_POST['description']);
        header("Location: calendar.php?year=$year&month=$month");
        exit;
    }
    
    if ($_POST['action'] == 'update') {
        updateEvent($_POST['id'], $_POST['title'], $_POST['description']);
        header("Location: calendar.php?year=$year&month=$month");
        exit;
    }
    
    if ($_POST['action'] == 'delete') {
        deleteEvent($_POST['id']);
        header("Location: calendar.php?year=$year&month=$month");
        exit;
    }
}

// 해당 월의 일정 가져오기
$events = getEventsByMonth($year, $month);
$eventsByDate = groupEventsByDate($events);

// 달력 계산
$firstDay = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = date('t', $firstDay);
$startDayOfWeek = date('w', $firstDay);

$prevMonth = $month - 1;
$prevYear = $year;
$nextMonth = $month + 1;
$nextYear = $year;

if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>PHP 달력 - CRUD</title>
    <style>
        body {
            font-family: 맑은 고딕, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .user-info {
            color: #666;
        }
        .user-info a {
            color: #dc3545;
            margin-left: 10px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .nav a {
            padding: 10px 20px;
            background: #4a90d9;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .nav a:hover {
            background: #357abd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            vertical-align: top;
            height: 100px;
            max-height: 100px;
            overflow-y: auto;
            width: 14.28%;
        }
        th {
            background: #4a90d9;
            color: white;
            height: auto;
        }
        td.sunday { color: red; }
        td.saturday { color: blue; }
        td.empty { background: #f9f9f9; }
        td.today { background: #fff3cd; }
        .event {
            background: #e8f4e8;
            margin: 2px 0;
            padding: 2px 4px;
            font-size: 11px;
            border-radius: 3px;
            cursor: pointer;
            text-align: left;
        }
        .event:hover {
            background: #c8e6c9;
        }
        .add-btn {
            font-size: 10px;
            color: #4a90d9;
            cursor: pointer;
        }
        .add-btn:hover {
            text-decoration: underline;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal.show { display: flex; justify-content: center; align-items: center; }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
        }
        .modal-content h3 { margin-top: 0; }
        .modal-content input, .modal-content textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .modal-content button {
            padding: 8px 16px;
            margin-right: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-save { background: #4a90d9; color: white; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-cancel { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span></span>
            <div class="user-info">
                👤 <?= htmlspecialchars(getCurrentUsername()) ?>님
                <a href="?logout=1">로그아웃</a>
            </div>
        </div>
        
        <h1>📅 달력</h1>
        
        <div class="nav">
            <a href="?year=<?=$prevYear?>&month=<?=$prevMonth?>">◀ 이전달</a>
            <h2><?=$year?>년 <?=$month?>월</h2>
            <a href="?year=<?=$nextYear?>&month=<?=$nextMonth?>">다음달 ▶</a>
        </div>
        
        <table>
            <tr>
                <th>일</th>
                <th>월</th>
                <th>화</th>
                <th>수</th>
                <th>목</th>
                <th>금</th>
                <th>토</th>
            </tr>
            <tr>
            <?php
            $dayCount = 0;
            $today = date('Y-m-d');
            
            for ($i = 0; $i < $startDayOfWeek; $i++) {
                echo "<td class='empty'></td>";
                $dayCount++;
            }
            
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = sprintf("%04d-%02d-%02d", $year, $month, $day);
                $classes = [];
                
                if ($dayCount % 7 == 0) $classes[] = 'sunday';
                if ($dayCount % 7 == 6) $classes[] = 'saturday';
                if ($currentDate == $today) $classes[] = 'today';
                
                $classStr = implode(' ', $classes);
                
                echo "<td class='$classStr'>";
                echo "<strong>$day</strong><br>";
                
                if (isset($eventsByDate[$day])) {
                    foreach ($eventsByDate[$day] as $event) {
                        $title = htmlspecialchars($event['title']);
                        $desc = htmlspecialchars($event['description']);
                        echo "<div class='event' onclick='showEdit({$event['id']}, \"$title\", \"$desc\")'>$title</div>";
                    }
                }
                
                echo "<div class='add-btn' onclick='showAdd(\"$currentDate\")'>[+추가]</div>";
                echo "</td>";
                
                $dayCount++;
                if ($dayCount % 7 == 0 && $day < $daysInMonth) {
                    echo "</tr><tr>";
                }
            }
            
            while ($dayCount % 7 != 0) {
                echo "<td class='empty'></td>";
                $dayCount++;
            }
            ?>
            </tr>
        </table>
    </div>
    
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h3>📝 일정 추가</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="event_date" id="addDate">
                
                <label>제목:</label>
                <input type="text" name="title" required>
                
                <label>설명:</label>
                <textarea name="description" rows="3"></textarea>
                
                <button type="submit" class="btn-save">저장</button>
                <button type="button" class="btn-cancel" onclick="closeModal('addModal')">취소</button>
            </form>
        </div>
    </div>
    
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>✏️ 일정 수정</h3>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update" id="editAction">
                <input type="hidden" name="id" id="editId">
                
                <label>제목:</label>
                <input type="text" name="title" id="editTitle" required>
                
                <label>설명:</label>
                <textarea name="description" id="editDesc" rows="3"></textarea>
                
                <button type="submit" class="btn-save">수정</button>
                <button type="button" class="btn-delete" onclick="deleteEvent()">삭제</button>
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">취소</button>
            </form>
        </div>
    </div>
    
    <script>
        function showAdd(date) {
            document.getElementById('addDate').value = date;
            document.getElementById('addModal').classList.add('show');
        }
        
        function showEdit(id, title, desc) {
            document.getElementById('editId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editDesc').value = desc;
            document.getElementById('editAction').value = 'update';
            document.getElementById('editModal').classList.add('show');
        }
        
        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
        }
        
        function deleteEvent() {
            if (confirm('정말 삭제하시겠습니까?')) {
                document.getElementById('editAction').value = 'delete';
                document.getElementById('editForm').submit();
            }
        }
        
        window.onclick = function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('show');
            }
        }
    </script>
</body>
</html>
