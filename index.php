<?php
// 환경 변수로 DB 연결 정보 설정
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

try {
    // DB에 연결
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 연결 성공 메시지 출력
    echo "Connection successful!<br>";

    // SQL 쿼리 작성: hospital_db 테이블의 모든 데이터를 조회
    $sql = "SELECT * FROM hospital_table"; // hospital_table을 실제 테이블 이름으로 변경하세요.

    // 쿼리 실행
    $stmt = $pdo->query($sql);

    // 결과 출력
    if ($stmt) {
        echo "<h3>Hospital DB Table Contents:</h3><table border='1'><tr><th>ID</th><th>Name</th><th>Specialty</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>" . $row['id'] . "</td><td>" . $row['name'] . "</td><td>" . $row['specialty'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No data found.";
    }

} catch (PDOException $e) {
    // 연결 실패 시 오류 메시지 출력
    echo "DB 연결 실패: " . $e->getMessage();
}
?>
