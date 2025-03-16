<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Автозагрузка классов (Composer)

use App\Core\Router;
use App\Core\EntityManager;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load(); // Загружаем .env

$entityManager = new EntityManager($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

$router = new Router($entityManager);

// Обрабатываем URL
$router->dispatch($_SERVER['REQUEST_URI']);
