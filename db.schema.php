<?php

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$connection->query(
    'CREATE TABLE users (
        uuid
        TEXT NOT NULL
        CONSTRAINT uuid_primary_key PRIMARY KEY,
        username
        TEXT NOT NULL
        CONSTRAINT username_unique_key UNIQUE,
        first_name TEXT NOT NULL,
        last_name TEXT NOT NULL
    )'
);

$connection->query(
    'CREATE TABLE posts (
        uuid
        TEXT NOT NULL
        CONSTRAINT uuid_primary_key PRIMARY KEY,
        author_uuid
        TEXT NOT NULL
        CONSTRAINT author_unique_key,
        title TEXT NOT NULL,
        text TEXT NOT NULL
    )'
);

$connection->query(
    'CREATE TABLE comment (
        uuid
        TEXT NOT NULL
        CONSTRAINT uuid_primary_key PRIMARY KEY,
        post_uuid
        TEXT NOT NULL
        CONSTRAINT post_unique_key,
        author_uuid
        TEXT NOT NULL
        CONSTRAINT author_unique_key,
        text TEXT NOT NULL
    )'
);