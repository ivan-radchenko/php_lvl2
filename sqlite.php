<?php

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$connection->exec("DROP TABLE IF EXISTS users");

$connection->exec(
    "CREATE TABLE users (
    uuid TEXT NOT NULL
    CONSTRAINT uuid_primary_key PRIMARY KEY,
    username TEXT NOT NULL
    CONSTRAINT username_unique_key UNIQUE,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL
    );"
);

$connection->exec("DROP TABLE IF EXISTS posts");

$connection->exec(
    "CREATE TABLE posts (
    uuid TEXT NOT NULL
    CONSTRAINT uuid_primary_key PRIMARY KEY,
    author_uuid TEXT NOT NULL,
    title TEXT NOT NULL,
    text TEXT NOT NULL
    );"
);

$connection->exec("DROP TABLE IF EXISTS comments");

$connection->exec(
    "CREATE TABLE comments (
    uuid TEXT NOT NULL
    CONSTRAINT uuid_primary_key PRIMARY KEY,
    post_uuid TEXT NOT NULL,
    author_uuid TEXT NOT NULL,
    text TEXT NOT NULL
    );"
);

//creating likes table

$connection->exec("DROP TABLE IF EXISTS likes");

$connection->exec(
    "CREATE TABLE likes (
    uuid TEXT NOT NULL
    CONSTRAINT uuid_primary_key PRIMARY KEY,
    user_uuid TEXT NOT NULL,
    post_uuid TEXT NOT NULL
    );"
);
/* 
$connection->exec(
    "INSERT INTO users (first_name, last_name) VALUES ('Ivan', 'Nikitin')"
); */