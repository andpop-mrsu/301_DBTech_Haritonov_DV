<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;
$doctor_id = $_GET['doctor_id'] ?? null;

if (!$id || !$doctor_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT s.*, d.surname, d.name FROM services s 
                      JOIN doctors d ON s.doctor_id = d.id 
                      WHERE s.id = ? AND s.doctor_id = ?");
$stmt->execute([$id, $doctor_id]);
$service = $stmt->fetch();

if (!$service) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: services.php?doctor_id=$doctor_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить услугу</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .warning {
            color: #f44336;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #ffebee;
            border-radius: 4px;
            border-left: 4px solid #f44336;
        }
        .service-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            display: inline-block;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
        }
        .btn-secondary {
            background-color: #757575;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Удалить услугу</h1>
        
        <div class="warning">
            <strong>Внимание!</strong> Вы уверены, что хотите удалить эту услугу? Это действие нельзя отменить.
        </div>
        
        <div class="service-info">
            <p><strong>Врач:</strong> <?= htmlspecialchars($service['surname']) ?> <?= htmlspecialchars($service['name']) ?></p>
            <p><strong>Название услуги:</strong> <?= htmlspecialchars($service['service_name']) ?></p>
            <p><strong>Дата:</strong> <?= htmlspecialchars($service['date']) ?></p>
            <p><strong>Стоимость:</strong> <?= number_format($service['cost'], 2, '.', ' ') ?> руб.</p>
        </div>
        
        <form method="POST">
            <button type="submit" name="confirm" value="1" class="btn btn-danger">Да, удалить</button>
            <a href="services.php?doctor_id=<?= $doctor_id ?>" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</body>
</html>

