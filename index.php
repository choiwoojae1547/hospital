<?php
include 'config/config.php'; // DB 연결 설정 파일

// DB 연결 예외 처리
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // DB 연결이 성공했으면 메시지 출력
    echo "Connection successful!";
} catch (PDOException $e) {
    // 연결 실패 시 오류 메시지 출력
    echo "DB 연결 실패: " . $e->getMessage();
}
?>
