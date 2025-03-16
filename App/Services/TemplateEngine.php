<?php

namespace App\Services;

class TemplateEngine {
    public function render(string $templatePath, array $data = []): string {
        if (!file_exists($templatePath)) {
            throw new \Exception("Ошибка: Шаблон не найден - {$templatePath}");
        }

        // Делаем переменные доступными в шаблоне
        extract($data);

        // Буферизация вывода
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}
