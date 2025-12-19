<?php
require_once 'user.php';

// 일정 추가 (Create)
function addEvent($date, $title, $description) {
    global $conn;
    $userId = getCurrentUserId();
    $date = mysqli_real_escape_string($conn, $date);
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    
    $sql = "INSERT INTO events (user_id, event_date, title, description) VALUES ($userId, '$date', '$title', '$description')";
    return mysqli_query($conn, $sql);
}

// 일정 조회 (Read) - 현재 로그인한 사용자의 특정 월 일정
function getEventsByMonth($year, $month) {
    global $conn;
    $userId = getCurrentUserId();
    $startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
    $endDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-31";
    
    $sql = "SELECT * FROM events WHERE user_id = $userId AND event_date BETWEEN '$startDate' AND '$endDate' ORDER BY event_date";
    $result = mysqli_query($conn, $sql);
    
    $events = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
    return $events;
}

// 일정 수정 (Update)
function updateEvent($id, $title, $description) {
    global $conn;
    $userId = getCurrentUserId();
    $id = (int)$id;
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    
    $sql = "UPDATE events SET title = '$title', description = '$description' WHERE id = $id AND user_id = $userId";
    return mysqli_query($conn, $sql);
}

// 일정 삭제 (Delete)
function deleteEvent($id) {
    global $conn;
    $userId = getCurrentUserId();
    $id = (int)$id;
    
    $sql = "DELETE FROM events WHERE id = $id AND user_id = $userId";
    return mysqli_query($conn, $sql);
}

// 날짜별로 일정 정리
function groupEventsByDate($events) {
    $eventsByDate = [];
    foreach ($events as $event) {
        $day = (int)date('j', strtotime($event['event_date']));
        $eventsByDate[$day][] = $event;
    }
    return $eventsByDate;
}
?>
