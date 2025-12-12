<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;
$doctor_id = $_GET['doctor_id'] ?? null;

if (!$id || !$doctor_id) {
    header('Location: index.php');
    exit;
}

// Получение информации о записи графика
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ? AND doctor_id = ?");
$stmt->execute([$id, $doctor_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    header('Location: index.php');
    exit;
}

// Получение информации о враче
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch();

$errors = [];
$day_of_week = $schedule['day_of_week'];
$start_time = $schedule['start_time'];
$end_time = $schedule['end_time'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day_of_week = trim($_POST['day_of_week'] ?? '');
    $start_time = trim($_POST['start_time'] ?? '');
    $end_time = trim($_POST['end_time'] ?? '');
    
    if (empty($day_of_week)) {
        $errors[] = 'День недели обязателен для заполнения';
    }
    if (empty($start_time)) {
        $errors[] = 'Время начала обязательно для заполнения';
    }
    if (empty($end_time)) {
        $errors[] = 'Время окончания обязательно для заполнения';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE schedules SET day_of_week = ?, start_time = ?, end_time = ? WHERE id = ?");
        $stmt->execute([$day_of_week, $start_time, $end_time, $id]);
        header("Location: schedule.php?doctor_id=$doctor_id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать запись графика</title>
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
        select {
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
        <h1>Редактировать запись графика</h1>
        
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
                <label for="day_of_week">День недели *</label>
                <select id="day_of_week" name="day_of_week" required>
                    <option value="">Выберите день</option>
                    <option value="Понедельник" <?= $day_of_week === 'Понедельник' ? 'selected' : '' ?>>Понедельник</option>
                    <option value="Вторник" <?= $day_of_week === 'Вторник' ? 'selected' : '' ?>>Вторник</option>
                    <option value="Среда" <?= $day_of_week === 'Среда' ? 'selected' : '' ?>>Среда</option>
                    <option value="Четверг" <?= $day_of_week === 'Четверг' ? 'selected' : '' ?>>Четверг</option>
                    <option value="Пятница" <?= $day_of_week === 'Пятница' ? 'selected' : '' ?>>Пятница</option>
                    <option value="Суббота" <?= $day_of_week === 'Суббота' ? 'selected' : '' ?>>Суббота</option>
                    <option value="Воскресенье" <?= $day_of_week === 'Воскресенье' ? 'selected' : '' ?>>Воскресенье</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="start_time">Время начала * (формат: ЧЧ:ММ)</label>
                <input type="text" id="start_time" name="start_time" value="<?= htmlspecialchars($start_time) ?>" placeholder="09:00" required>
            </div>
            
            <div class="form-group">
                <label for="end_time">Время окончания * (формат: ЧЧ:ММ)</label>
                <input type="text" id="end_time" name="end_time" value="<?= htmlspecialchars($end_time) ?>" placeholder="18:00" required>
            </div>
            
            <div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="schedule.php?doctor_id=<?= $doctor_id ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>

