<?php

namespace App\Core;

class TemplateEngine {
    public function render(string $templatePath, array $data = []): string {
        if (!file_exists($templatePath)) {
            throw new \Exception("Ошибка: Шаблон не найден - {$templatePath}");
        }

        $templateContent = file_get_contents($templatePath);

        // Заменяем кастомные теги на PHP-код
        $templateContent = $this->parseCustomSyntax($templateContent);

        // Делаем переменные доступными в шаблоне
        extract($data);

        // Буферизация вывода
        ob_start();
        eval('?>' . $templateContent);
        return ob_get_clean();
    }

    private function parseCustomSyntax(string $template): string {
        $replacements = [
            '/\{\{if (.+?)\}\}/' => '<?php if ($1): ?>',
            '/\{\{else\}\}/' => '<?php else: ?>',
            '/\{\{endif\}\}/' => '<?php endif; ?>',
            '/\{\{foreach (.+?) as (.+?)\}\}/' => '<?php foreach ($1 as $2): ?>',
            '/\{\{endforeach\}\}/' => '<?php endforeach; ?>',
            '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_\->\[\]\'"\(\)]*)\s*\}\}/' => '<?= htmlspecialchars($$1, ENT_QUOTES, "UTF-8") ?>'
        ];
        return preg_replace(array_keys($replacements), array_values($replacements), $template);
    }
}