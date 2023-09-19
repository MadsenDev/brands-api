<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

include 'db.php';

function validateApiKey($key, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM api_keys WHERE api_key = ?");
    $stmt->execute([$key]);
    return $stmt->fetch();
}

function rateLimit($apiKeyId, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM rate_limiting WHERE api_key_id = ?");
    $stmt->execute([$apiKeyId]);
    $data = $stmt->fetch();

    $current_time = new DateTime();
    if ($data) {
        $reset_time = new DateTime($data['reset_time']);
        $time_diff = $current_time->getTimestamp() - $reset_time->getTimestamp();
        
        if ($time_diff > 60) {
            $stmt = $pdo->prepare("UPDATE rate_limiting SET request_count = 1, reset_time = NOW() WHERE api_key_id = ?");
            $stmt->execute([$apiKeyId]);
        } elseif ($data['request_count'] >= 60) {
            return false;
        } else {
            $stmt = $pdo->prepare("UPDATE rate_limiting SET request_count = request_count + 1 WHERE api_key_id = ?");
            $stmt->execute([$apiKeyId]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO rate_limiting (api_key_id, request_count, reset_time) VALUES (?, 1, NOW())");
        $stmt->execute([$apiKeyId]);
    }
    return true;
}

$apiKey = $_SERVER['HTTP_API_KEY'] ?? null;
$apiKeyData = validateApiKey($apiKey, $pdo);

if (!$apiKeyData) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid API Key']);
    exit();
}

if (!rateLimit($apiKeyData['id'], $pdo)) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded']);
    exit();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
            $stmt->execute([$_GET['id']]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM brands");
            $stmt->execute();
        }
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO brands (name, logo, website) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['logo'], $data['website']]);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("UPDATE brands SET name = ?, logo = ?, website = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['logo'], $data['website'], $_GET['id']]);
        echo json_encode(['updated' => $stmt->rowCount()]);
        break;

    case 'DELETE':
        $stmt = $pdo->prepare("DELETE FROM brands WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(['deleted' => $stmt->rowCount()]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
?>