<?php
declare(strict_types = 1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\UserService;

class MainController
{
    private TemplateEngine $templateEngine;

    public function __construct()
    {
        $this->templateEngine = new TemplateEngine();
    }

    public function mainPageAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/main.html';
        echo $this->templateEngine->render($templatePath);
    }
}