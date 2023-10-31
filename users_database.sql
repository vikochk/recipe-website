-- Создать базу данных "users", если она не существует
DO $$ 
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_database WHERE datname = 'users') THEN
    CREATE DATABASE users;
  END IF;
END $$;

-- Переключиться на базу данных "users"
SET search_path TO users;

-- Создать таблицу "users" в схеме "public", если она не существует
CREATE TABLE IF NOT EXISTS public.users (
    user_id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    name VARCHAR(100) NOT NULL
);

SELECT * FROM public.users

