#!/usr/bin/env python3
import csv
import re
import os

def escape_sql_string(s):
    return s.replace("'", "''")

def process_movies():
    with open('movies.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            title = row['title']
            year_match = re.search(r'\((\d{4})\)$', title)
            year = year_match.group(1) if year_match else 'NULL'
            clean_title = re.sub(r'\s*\(\d{4}\)$', '', title)
            
            yield (
                row['movieId'],
                escape_sql_string(clean_title),
                year,
                escape_sql_string(row['genres'])
            )

def process_ratings():
    with open('ratings.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            yield (
                row['userId'],
                row['movieId'],
                row['rating'],
                row['timestamp']
            )

def process_tags():
    with open('tags.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            yield (
                row['userId'],
                row['movieId'],
                escape_sql_string(row['tag']),
                row['timestamp']
            )

def process_users():
    with open('users.txt', 'r', encoding='utf-8') as f:
        for line in f:
            fields = line.strip().split('|')
            if len(fields) == 6:
                yield (
                    fields[0],
                    escape_sql_string(fields[1]),
                    escape_sql_string(fields[2]),
                    escape_sql_string(fields[3]),
                    escape_sql_string(fields[4]),
                    escape_sql_string(fields[5])
                )


def generate_sql():
    with open('db_init.sql', 'w', encoding='utf-8') as f:
        f.write("DROP TABLE IF EXISTS movies;\n")
        f.write("DROP TABLE IF EXISTS ratings;\n")
        f.write("DROP TABLE IF EXISTS tags;\n")
        f.write("DROP TABLE IF EXISTS users;\n\n")

        f.write("""
CREATE TABLE movies (
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    year INTEGER,
    genres TEXT
);

CREATE TABLE ratings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    movie_id INTEGER NOT NULL,
    rating REAL NOT NULL,
    timestamp INTEGER NOT NULL
);

CREATE TABLE tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    movie_id INTEGER NOT NULL,
    tag TEXT NOT NULL,
    timestamp INTEGER NOT NULL
);

CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    gender TEXT NOT NULL,
    register_date TEXT NOT NULL,
    occupation TEXT NOT NULL
);

\n""")

        f.write("INSERT INTO movies (id, title, year, genres) VALUES\n")
        movies_data = list(process_movies())
        for i, (mid, title, year, genres) in enumerate(movies_data):
            f.write(f"({mid}, '{title}', {year}, '{genres}')")
            f.write(",\n" if i < len(movies_data)-1 else ";\n\n")

        f.write("INSERT INTO ratings (user_id, movie_id, rating, timestamp) VALUES\n")
        ratings_data = list(process_ratings())
        for i, (uid, mid, rating, ts) in enumerate(ratings_data):
            f.write(f"({uid}, {mid}, {rating}, {ts})")
            f.write(",\n" if i < len(ratings_data)-1 else ";\n\n")

        f.write("INSERT INTO tags (user_id, movie_id, tag, timestamp) VALUES\n")
        tags_data = list(process_tags())
        for i, (uid, mid, tag, ts) in enumerate(tags_data):
            f.write(f"({uid}, {mid}, '{tag}', {ts})")
            f.write(",\n" if i < len(tags_data)-1 else ";\n\n")

        f.write("INSERT INTO users (id, name, email, gender, register_date, occupation) VALUES\n")
        users_data = list(process_users())
        for i, (uid, name, email, gender, reg_date, occupation) in enumerate(users_data):
            f.write(f"({uid}, '{name}', '{email}', '{gender}', '{reg_date}', '{occupation}')")
            f.write(",\n" if i < len(users_data)-1 else ";\n")

if __name__ == '__main__':
    generate_sql()
