<?php
require_once 'config.php';

// Получение списка врачей, отсортированных по фамилии
$stmt = $pdo->query("SELECT * FROM doctors ORDER BY surname");
$doctors = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Клиника - Список врачей</title>
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
            text-align: center;
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
            background-color: #4CAF50;
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
        .btn-schedule {
            background-color: #FF9800;
            color: white;
        }
        .btn-services {
            background-color: #9C27B0;
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
    <h1>Список врачей клиники</h1>
    
    <?php if (empty($doctors)): ?>
        <p>Врачи не найдены.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Специализация</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?= htmlspecialchars($doctor['id']) ?></td>
                        <td><?= htmlspecialchars($doctor['surname']) ?></td>
                        <td><?= htmlspecialchars($doctor['name']) ?></td>
                        <td><?= htmlspecialchars($doctor['specialization']) ?></td>
                        <td>
                            <div class="actions">
                                <a href="doctor_edit.php?id=<?= $doctor['id'] ?>" class="btn btn-edit">Редактировать</a>
                                <a href="doctor_delete.php?id=<?= $doctor['id'] ?>" class="btn btn-delete">Удалить</a>
                                <a href="schedule.php?doctor_id=<?= $doctor['id'] ?>" class="btn btn-schedule">График</a>
                                <a href="services.php?doctor_id=<?= $doctor['id'] ?>" class="btn btn-services">Оказанные услуги</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <div class="add-btn">
        <a href="doctor_add.php" class="btn btn-primary">Добавить врача</a>
    </div>
</body>
</html>

