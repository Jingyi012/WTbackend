<?php
require_once './config.php';  // Include your database configuration and JWT setup
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$app->get('/profile', function (Request $request, Response $response, $args) {
    $headers = $request->getHeader('Authorization');
    $token = str_replace('Bearer ', '', $headers[0]);

    try {
        $key = '85ldofi';
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $userId = $decoded->data->userId;

        $db = new db();
        $dbConnection = $db->connect();

        if (!$dbConnection) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Database connection failed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $stmt = $dbConnection->prepare("SELECT Username, Email, cRole FROM users WHERE user_ID = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $response->getBody()->write(json_encode(['success' => true, 'data' => $user]));
        } else {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'User not found']));
        }

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Invalid token']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
});

$app->post('/profile/update', function (Request $request, Response $response, $args) {
    $headers = $request->getHeader('Authorization');
    $token = str_replace('Bearer ', '', $headers[0]);

    try {
        $key = '85ldofi';
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $userId = $decoded->data->userId;

        $data = $request->getParsedBody();
        $username = $data['Username'] ?? '';
        $email = $data['Email'] ?? '';

        $db = new db();
        $dbConnection = $db->connect();

        if (!$dbConnection) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Database connection failed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $stmt = $dbConnection->prepare("UPDATE users SET Username = ?, Email = ? WHERE user_ID = ?");
        $stmt->execute([$username, $email, $userId]);

        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Profile updated successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Invalid token']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
});