<?php
// Конфигурация подключения к базе данных
$dbPath = __DIR__ . '/../data/clinic.db';
$dsn = 'sqlite:' . $dbPath;

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Инициализация базы данных
    $pdo->exec("CREATE TABLE IF NOT EXISTS doctors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        surname TEXT NOT NULL,
        name TEXT NOT NULL,
        specialization TEXT NOT NULL
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS schedules (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        doctor_id INTEGER NOT NULL,
        day_of_week TEXT NOT NULL,
        start_time TEXT NOT NULL,
        end_time TEXT NOT NULL,
        FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS services (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        doctor_id INTEGER NOT NULL,
        service_name TEXT NOT NULL,
        date TEXT NOT NULL,
        cost REAL NOT NULL,
        FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
    )");
    
    // Добавляем тестовые данные, если таблица пуста
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM doctors");
    $count = $stmt->fetch()['count'];
    if ($count == 0) {
        $pdo->exec("INSERT INTO doctors (surname, name, specialization) VALUES
            ('Иванов', 'Иван', 'Терапевт'),
            ('Петрова', 'Мария', 'Хирург'),
            ('Сидоров', 'Алексей', 'Кардиолог')");
    }
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}
?>

