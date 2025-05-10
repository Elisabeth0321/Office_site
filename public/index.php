<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\EntityManager;
use App\Services\MailService;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$mailConfig = __DIR__ . '/../mail_config.ini';

$entityManager = new EntityManager($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
$mailService = new MailService(parse_ini_file($mailConfig));

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router($entityManager, $mailService);
$router->dispatch($_SERVER['REQUEST_URI']);