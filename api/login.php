<?php
require_once './config.php';
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$app->post('/login', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Invalid input']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    // Connect to the database
    $db = new db();
    $dbConnection = $db->connect();

    if (!$dbConnection) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Database connection failed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }


 // Check if the user exists
 $stmt = $dbConnection->prepare("SELECT * FROM users WHERE Email = ?");
 $stmt->execute([$email]);
 $user = $stmt->fetch(PDO::FETCH_ASSOC);

 if ($user && password_verify($password, $user['cPassword'])) {
    $key = '85ldofi';
    $payload = [
        'iss' => 'localhost', // Issuer
        'aud' => 'localhost', // Audience
        'iat' => time(), // Issued at
        'nbf' => time(), // Not before
        'exp' => time() + (60 * 60), // Expiration time
        'data' => [
            'userId' => $user['user_ID'],
            'role' => $user['cRole']
        ]
    ];

    $jwtToken = JWT::encode($payload, $key, 'HS256');
    $response->getBody()->write(json_encode(['success' => true, 'token' => $jwtToken ]));

    // $response->getBody()->write(json_encode(['success' => true, 'role' => $user['cRole'],'userId' => $user['user_ID'] ])); // Ensure userId is included in response
     return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
 } else {
     $response->getBody()->write(json_encode(['success' => false, 'message' => 'Wrong Email or Password']));
     return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
 }
});

// $app->run();

?>
