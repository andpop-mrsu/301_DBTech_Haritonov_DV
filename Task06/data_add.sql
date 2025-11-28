INSERT INTO users (name, email, gender, register_date, occupation_id)
VALUES 
('Cеничев Александр', 'sanek.senichev@example.com', 'male', date('now'), 
    (SELECT id FROM occupations WHERE name = 'student')),
('Фомин Сергей', 'serega.fomin@example.com', 'female', date('now'), 
    (SELECT id FROM occupations WHERE name = 'student')),
('Харитонов Данил', 'danil.haritonov@example.com', 'male', date('now'), 
    (SELECT id FROM occupations WHERE name = 'student')),
('Хопов Николай', 'kolya.hopov@example.com', 'male', date('now'), 
    (SELECT id FROM occupations WHERE name = 'student')),
('Хрипченко Юлия', 'juli.hripchenko@example.com', 'female', date('now'), 
    (SELECT id FROM occupations WHERE name = 'student'));


INSERT INTO movies (title, year)
VALUES 
('Inception', 2010),
('Душа', 2020),
('Интерстеллар', 2014);


INSERT INTO movies_genres (movie_id, genre_id)
VALUES 
-- Inception: Sci-Fi, Action, Thriller
((SELECT id FROM movies WHERE title = 'Inception'), 
 (SELECT id FROM genres WHERE name = 'Sci-Fi')),
((SELECT id FROM movies WHERE title = 'Inception'), 
 (SELECT id FROM genres WHERE name = 'Action')),
((SELECT id FROM movies WHERE title = 'Inception'), 
 (SELECT id FROM genres WHERE name = 'Thriller')),

-- Душа: Animation, Comedy, Drama
((SELECT id FROM movies WHERE title = 'Душа'), 
 (SELECT id FROM genres WHERE name = 'Animation')),
((SELECT id FROM movies WHERE title = 'Душа'), 
 (SELECT id FROM genres WHERE name = 'Comedy')),
((SELECT id FROM movies WHERE title = 'Душа'), 
 (SELECT id FROM genres WHERE name = 'Drama')),

-- Интерстеллар: Sci-Fi, Drama, Adventure
((SELECT id FROM movies WHERE title = 'Интерстеллар'), 
 (SELECT id FROM genres WHERE name = 'Sci-Fi')),
((SELECT id FROM movies WHERE title = 'Интерстеллар'), 
 (SELECT id FROM genres WHERE name = 'Drama')),
((SELECT id FROM movies WHERE title = 'Интерстеллар'), 
 (SELECT id FROM genres WHERE name = 'Adventure'));

-- 4. Добавление отзывов
INSERT INTO ratings (user_id, movie_id, rating, timestamp)
VALUES 
((SELECT id FROM users WHERE email = 'danil.haritonov@example.com'), 
 (SELECT id FROM movies WHERE title = 'Inception'), 4.8, strftime('%s', 'now')),
((SELECT id FROM users WHERE email = 'danil.haritonov@example.com'), 
 (SELECT id FROM movies WHERE title = 'Душа'), 4.7, strftime('%s', 'now')),
((SELECT id FROM users WHERE email = 'danil.haritonov@example.com'), 
 (SELECT id FROM movies WHERE title = 'Интерстеллар'), 4.9, strftime('%s', 'now'));

-- 5. Добавление тегов
INSERT INTO tags (user_id, movie_id, tag, timestamp)
VALUES 
((SELECT id FROM users WHERE email = 'danil.haritonov@example.com'), 
 (SELECT id FROM movies WHERE title = 'Inception'), 'Умопомрачительный сюжет с многослойными снами', strftime('%s', 'now')),
((SELECT id FROM users WHERE email = 'danil.haritonov@example.com'), 
 (SELECT id FROM movies WHERE title = 'Душа'), 'Трогательная история о смысле жизни', strftime('%s', 'now')),
((SELECT id FROM users WHERE email = 'danil.haritonov@example.com'), 
 (SELECT id FROM movies WHERE title = 'Интерстеллар'), 'Эпическое космическое путешествие с глубоким смыслом', strftime('%s', 'now'));