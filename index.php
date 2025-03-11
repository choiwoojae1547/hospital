<?php
// 환경 변수로 DB 연결 정보 설정
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

// Redis 클라이언트 생성
$redis = new Redis();
$redisHost = getenv('REDIS_HOST');
$redisPort = getenv('REDIS_PORT');

// DB 연결
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection to DB successful!<br>";

    // Redis 서버에 연결
    $redis->connect($redisHost, $redisPort);

    // Redis 연결 확인
    if ($redis->ping() == "+PONG") {
        echo "Successfully connected to Redis!<br>";
    } else {
        echo "Failed to connect to Redis.<br>";
    }

    // SQL 쿼리 작성: reservations 테이블에서 데이터 조회
    $sql = "SELECT * FROM reservations";
    $stmt = $pdo->query($sql);

    // Redis 캐시에서 예약 리스트 확인
    $redisKey = "reservation_list";
    $cachedReservations = $redis->get($redisKey);

    if ($cachedReservations) {
        echo "Using cached data from Redis.<br>";
        // 캐시된 예약 데이터 출력
        $reservations = json_decode($cachedReservations, true);
    } else {
        echo "No cached data found, fetching from DB.<br>";
        $reservations = [];
        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // 예약 데이터를 배열에 저장
                $reservations[] = $row;
            }
            // Redis에 예약 데이터를 캐시 (캐시 시간은 1시간으로 설정)
            $redis->set($redisKey, json_encode($reservations), 3600); 
        }
    }

    // 예약 데이터 출력
    echo "<h3>Hospital Reservation List:</h3><table border='1'><tr><th>ID</th><th>Name</th><th>Specialty</th></tr>";
    foreach ($reservations as $reservation) {
        echo "<tr><td>" . $reservation['id'] . "</td><td>" . $reservation['name'] . "</td><td>" . $reservation['specialty'] . "</td></tr>";
    }
    echo "</table>";

} catch (PDOException $e) {
    echo "DB 연결 실패: " . $e->getMessage();
}
?>
