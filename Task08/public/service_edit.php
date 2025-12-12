<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;
$doctor_id = $_GET['doctor_id'] ?? null;

if (!$id || !$doctor_id) {
    header('Location: index.php');
    exit;
}

// Получение информации об услуге
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND doctor_id = ?");
$stmt->execute([$id, $doctor_id]);
$service = $stmt->fetch();

if (!$service) {
    header('Location: index.php');
    exit;
}

// Получение информации о враче
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch();

$errors = [];
$service_name = $service['service_name'];
$date = $service['date'];
$cost = $service['cost'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = trim($_POST['service_name'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $cost = trim($_POST['cost'] ?? '');
    
    if (empty($service_name)) {
        $errors[] = 'Название услуги обязательно для заполнения';
    }
    if (empty($date)) {
        $errors[] = 'Дата обязательна для заполнения';
    }
    if (empty($cost) || !is_numeric($cost) || $cost <= 0) {
        $errors[] = 'Стоимость должна быть положительным числом';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE services SET service_name = ?, date = ?, cost = ? WHERE id = ?");
        $stmt->execute([$service_name, $date, $cost, $id]);
        header("Location: services.php?doctor_id=$doctor_id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать услугу</title>
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
        .doctor-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .error {
            color: #f44336;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 4px;
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
        .btn-primary {
            background-color: #4CAF50;
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
        <h1>Редактировать услугу</h1>
        
        <div class="doctor-info">
            <p><strong>Врач:</strong> <?= htmlspecialchars($doctor['surname']) ?> <?= htmlspecialchars($doctor['name']) ?></p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="service_name">Название услуги *</label>
                <input type="text" id="service_name" name="service_name" value="<?= htmlspecialchars($service_name) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="date">Дата *</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="cost">Стоимость (руб.) *</label>
                <input type="number" id="cost" name="cost" value="<?= htmlspecialchars($cost) ?>" step="0.01" min="0.01" required>
            </div>
            
            <div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="services.php?doctor_id=<?= $doctor_id ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>

