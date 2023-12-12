-- Создать базу данных "users", если она не существует
DO $$ 
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_database WHERE datname = 'users') THEN
    CREATE DATABASE users;
  END IF;
END $$;

-- Переключиться на базу данных "users"
SET search_path TO users;

-- Пользователь зарегистрированный
CREATE TABLE IF NOT EXISTS public.users (
    user_id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    name VARCHAR(100) NOT NULL
);

/* Кто запускал этот файлик, раскомментируйте DROP TABLE в all_ingredients.sql
-- Ингредиенты, которые можно добавить
CREATE TABLE IF NOT EXISTS public.ingredients (
    ingredient_id SERIAL PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    category VARCHAR(100)
);
*/
-- Связка между users и холодильников
CREATE TABLE IF NOT EXISTS public.user_fridges (
    fridge_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES public.users(user_id)
);

-- Ингредиенты конкретного холодильника
CREATE TABLE IF NOT EXISTS public.fridge_ingredients (
    fridge_ingredient_id SERIAL PRIMARY KEY,
    fridge_id INT REFERENCES public.user_fridges(fridge_id),
    ingredient_id INT REFERENCES public.ingredients(ingredient_id),
    quantity INT
);


SELECT * FROM public.fridge_ingredients

