<?php
require_once 'config.php';

$doctor_id = $_GET['doctor_id'] ?? null;

if (!$doctor_id) {
    header('Location: index.php');
    exit;
}

// Получение информации о враче
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch();

if (!$doctor) {
    header('Location: index.php');
    exit;
}

// Обработка удаления записи графика
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ? AND doctor_id = ?");
    $stmt->execute([$_GET['id'], $doctor_id]);
    header("Location: schedule.php?doctor_id=$doctor_id");
    exit;
}

// Получение расписания врача
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE doctor_id = ? ORDER BY 
    CASE day_of_week 
        WHEN 'Понедельник' THEN 1
        WHEN 'Вторник' THEN 2
        WHEN 'Среда' THEN 3
        WHEN 'Четверг' THEN 4
        WHEN 'Пятница' THEN 5
        WHEN 'Суббота' THEN 6
        WHEN 'Воскресенье' THEN 7
        ELSE 8
    END, start_time");
$stmt->execute([$doctor_id]);
$schedules = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>График работы - <?= htmlspecialchars($doctor['surname']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
        }
        .doctor-info {
            background-color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #FF9800;
            color: white;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-edit {
            background-color: #2196F3;
            color: white;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-back {
            background-color: #757575;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .add-btn {
            margin-top: 20px;
            text-align: center;
        }
        .add-btn .btn {
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>График работы врача</h1>
    
    <div class="doctor-info">
        <p><strong>Врач:</strong> <?= htmlspecialchars($doctor['surname']) ?> <?= htmlspecialchars($doctor['name']) ?> (<?= htmlspecialchars($doctor['specialization']) ?>)</p>
    </div>
    
    <?php if (empty($schedules)): ?>
        <p>График работы не установлен.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>День недели</th>
                    <th>Время начала</th>
                    <th>Время окончания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?= htmlspecialchars($schedule['id']) ?></td>
                        <td><?= htmlspecialchars($schedule['day_of_week']) ?></td>
                        <td><?= htmlspecialchars($schedule['start_time']) ?></td>
                        <td><?= htmlspecialchars($schedule['end_time']) ?></td>
                        <td>
                            <div class="actions">
                                <a href="schedule_edit.php?id=<?= $schedule['id'] ?>&doctor_id=<?= $doctor_id ?>" class="btn btn-edit">Редактировать</a>
                                <a href="schedule_delete.php?id=<?= $schedule['id'] ?>&doctor_id=<?= $doctor_id ?>" class="btn btn-delete">Удалить</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <div class="add-btn">
        <a href="schedule_add.php?doctor_id=<?= $doctor_id ?>" class="btn btn-primary">Добавить запись в график</a>
        <a href="index.php" class="btn btn-back">Назад к списку врачей</a>
    </div>
</body>
</html>

