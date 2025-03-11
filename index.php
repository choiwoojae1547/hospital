<?php
// 환경 변수로 DB 연결 정보 설정
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

// Redis 연결 설정
$redis_host = getenv('REDIS_HOST');
$redis = new Redis();
$redis->connect($redis_host, 6379); // Redis 포트는 기본 6379

try {
    // DB에 연결
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 연결 성공 메시지 출력
    echo "Connection successful to MySQL DB!<br>";

    // SQL 쿼리 작성: hospital_db 테이블의 모든 데이터를 조회
    $sql = "SELECT * FROM reservations"; // reservations 테이블에서 모든 데이터를 조회

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
        echo "No data found in DB.";
    }

    // 대기 리스트 추가 기능 (POST 요청으로 처리)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_name'])) {
        $user_name = $_POST['user_name'];
        $timestamp = time();  // 현재 타임스탬프

        // 1. Redis에 대기 리스트 추가 (Sorted Set 사용)
        $redis->zAdd('waitlist', $timestamp, $user_name); // Sorted set에 사용자 추가

        // 2. MariaDB에 대기 리스트 추가
        $stmt = $pdo->prepare("INSERT INTO reservations (user_name, created_at) VALUES (?, ?)");
        $stmt->bind_param("si", $user_name, $timestamp);
        $stmt->execute();
        $stmt->close();

        echo "<p>$user_name has been added to the waitlist.</p>";
    }

    // 대기 리스트 조회 기능 (GET 요청으로 처리)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $waitlist = $redis->zRange('waitlist', 0, -1, true);  // Sorted set에서 대기 리스트 조회

        echo "<h3>Current Waitlist:</h3>";
        echo "<ul>";
        foreach ($waitlist as $name => $timestamp) {
            echo "<li>$name - " . date("Y-m-d H:i:s", $timestamp) . "</li>";  // 타임스탬프를 읽기 좋은 형식으로 변환
        }
        echo "</ul>";
    }

} catch (PDOException $e) {
    // 연결 실패 시 오류 메시지 출력
    echo "DB 연결 실패: " . $e->getMessage();
} catch (RedisException $e) {
    // Redis 연결 실패 시 오류 메시지 출력
    echo "Redis 연결 실패: " . $e->getMessage();
}
?>
