<?php
$phpVersion = phpversion();
$xdebugLoaded = extension_loaded('xdebug') ? 'Enabled (v' . phpversion('xdebug') . ')' : 'Disabled';
$pdoMysqlLoaded = extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled';
$redisLoaded = extension_loaded('redis') ? 'Enabled' : 'Disabled';

$dbStatus = 'Not tested';
try {
    $host = 'mysql';
    $db = getenv('DB_DATABASE') ?: 'laravel';
    $user = getenv('DB_USER') ?: 'laravel_user';
    $pass = getenv('DB_PASSWORD') ?: 'laravel_password';
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3
    ]);
    $dbStatus = 'Connected to MySQL 5.7 successfully!';
} catch (\Throwable $e) {
    $dbStatus = 'DB Connection Notice: ' . $e->getMessage();
}

$redisStatus = 'Not tested';
try {
    $redis = new Redis();
    $redis->connect('redis', 6379, 3);
    $redis->set('test_key', 'Docker Redis OK');
    $val = $redis->get('test_key');
    $redisStatus = 'Connected to Redis successfully! Value: ' . $val;
} catch (\Throwable $e) {
    $redisStatus = 'Redis Notice: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docker PHP 8.4 + Laravel Environment</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .card {
            background: #1e293b;
            border-radius: 12px;
            padding: 32px;
            max-width: 650px;
            width: 100%;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            border: 1px solid #334155;
        }
        h1 { color: #f43f5e; margin-top: 0; font-size: 1.6rem; }
        p { color: #94a3b8; line-height: 1.5; }
        .status-list { list-style: none; padding: 0; margin: 24px 0; }
        .status-item {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            border-bottom: 1px solid #334155;
            font-size: 0.95rem;
        }
        .status-item:last-child { border-bottom: none; }
        .badge {
            background: #065f46;
            color: #34d399;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge.notice {
            background: #451a03;
            color: #fb923c;
        }
        .instructions {
            background: #0f172a;
            border-left: 4px solid #f43f5e;
            padding: 16px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.9rem;
            color: #e2e8f0;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Docker PHP 8.4 + Laravel Environment</h1>
        <p>Tu entorno Docker está listo. Puedes crear o instalar Laravel ejecutando el comando indicado abajo.</p>
        
        <div class="status-list">
            <div class="status-item">
                <span>PHP Version</span>
                <span class="badge"><?= htmlspecialchars($phpVersion) ?></span>
            </div>
            <div class="status-item">
                <span>Xdebug</span>
                <span class="badge"><?= htmlspecialchars($xdebugLoaded) ?></span>
            </div>
            <div class="status-item">
                <span>MySQL Driver (PDO)</span>
                <span class="badge"><?= htmlspecialchars($pdoMysqlLoaded) ?></span>
            </div>
            <div class="status-item">
                <span>Redis Driver</span>
                <span class="badge"><?= htmlspecialchars($redisLoaded) ?></span>
            </div>
            <div class="status-item">
                <span>MySQL Connection</span>
                <span class="badge <?= strpos($dbStatus, 'Connected') !== false ? '' : 'notice' ?>"><?= htmlspecialchars($dbStatus) ?></span>
            </div>
            <div class="status-item">
                <span>Redis Connection</span>
                <span class="badge <?= strpos($redisStatus, 'Connected') !== false ? '' : 'notice' ?>"><?= htmlspecialchars($redisStatus) ?></span>
            </div>
        </div>

        <p><strong>Comando para crear un nuevo proyecto Laravel:</strong></p>
        <div class="instructions">
            docker compose exec app composer create-project laravel/laravel .
        </div>
    </div>
</body>
</html>
