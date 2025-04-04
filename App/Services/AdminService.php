<?php

namespace App\Services;

class AdminService
{
    private string $baseDir;
    private array $allowedExtensions = ['css', 'html', 'js', 'png', 'jpeg', 'jpg'];

    public function __construct()
    {
        $this->baseDir = realpath(__DIR__ . '/../../public') . '/';
        if (!is_dir($this->baseDir)) {
            throw new \RuntimeException("Базовая директория не существует: " . $this->baseDir);
        }
    }

    public function listFiles(string $relativePath = ''): array
    {
        $path = $this->sanitizePath($relativePath);
        $items = scandir($path);

        if ($items === false) {
            return [];
        }

        $filteredItems = [];
        foreach (array_diff($items, ['.', '..']) as $item) {
            if (stripos($item, 'admin') !== false) {
                continue;
            }

            $fullPath = $path . $item;
            if (is_dir($fullPath)) {
                $filteredItems[] = $item;
            } elseif ($this->isAllowedExtension($item)) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }

    public function uploadFile(array $file, string $relativePath = ''): void
    {
        if (!isset($file['file']) || $file['file']['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("Ошибка загрузки файла");
        }

        if (!$this->isAllowedExtension($file['file']['name'])) {
            throw new \Exception("File type not allowed");
        }

        $targetDir = $this->sanitizePath($relativePath);
        $destination = $relativePath . basename($file['file']['name']);

        if (!move_uploaded_file($file['file']['tmp_name'], $destination)) {
            throw new \Exception("Ошибка при сохранении файла");
        }
    }

    public function downloadFile(string $relativePath): void
    {
        $filename = basename($relativePath);
        if (!$this->isAllowedExtension($filename) && !is_dir($this->sanitizePath($relativePath))) {
            throw new \Exception("File type not allowed");
        }

        if (file_exists($relativePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($relativePath).'"');
        readfile($relativePath);
        exit;
        } else {
            throw new \Exception("Файл не найден");
        }
    }

    public function createDirectory(string $relativePath): void
    {
        $dirPath = $this->sanitizePath($relativePath);

        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        chmod($relativePath, 0755);
    }

    private function deleteDirectory(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                $this->deleteFile($path);
            }
        }

        if (!rmdir($dir)) {
            throw new \Exception("Failed to remove directory: " . $dir);
        }
    }

    public function deleteFile(string $relativePath): void
    {
        $filename = basename($relativePath);

        if (!is_dir($relativePath) && !$this->isAllowedExtension($filename)) {
            throw new \Exception("File type not allowed");
        }

        if (!file_exists($relativePath)) {
            throw new \Exception("File or directory not found: " . $filename);
        }

        if (is_dir($relativePath)) {
            $this->deleteDirectory($relativePath);
        } else {
            if (!unlink($relativePath)) {
                throw new \Exception("Failed to delete file: " . $filename);
            }
        }
    }

    public function editFile(string $relativePath, string $content): void
    {
        if (file_exists($relativePath) && is_writable($relativePath)) {
            file_put_contents($relativePath, $content);
        } else {
            throw new \Exception("Файл недоступен для записи");
        }
    }

    public function getFileContent(string $relativePath): string
    {
        if (is_dir($relativePath)) {
            throw new \Exception("Это директория, а не файл");
        }

        if (!file_exists($relativePath)) {
            throw new \Exception("Файл не найден");
        }

        if (!is_readable($relativePath)) {
            throw new \Exception("Нет прав на чтение файла");
        }

        $content = file_get_contents($relativePath);
        if ($content === false) {
            throw new \Exception("Ошибка чтения файла");
        }

        return file_get_contents($relativePath);
    }

    public function getFullPath(string $relativePath): string
    {
        $fullPath = $this->baseDir . ltrim($relativePath, '/');
        $realPath = realpath($fullPath);

        if ($realPath === false || !file_exists($realPath)) {
            throw new \Exception("Файл или директория не существует: " . $relativePath);
        }

        if (strpos($realPath, $this->baseDir) !== 0) {
            throw new \Exception("Доступ запрещён: " . $relativePath);
        }

        return $realPath;
    }

    private function sanitizePath(string $relativePath): string
    {
        $fullPath = $this->baseDir . ltrim($relativePath, '/');
        $path = realpath($fullPath) ?: $fullPath;

        $baseDir = rtrim($this->baseDir, '/') . '/';
        $path = rtrim($path, '/') . '/';

        if (strpos($path, $baseDir) !== 0) {
            throw new \Exception("Access denied: {$relativePath} is not inside public directory");
        }

        return $path;
    }

    private function isAllowedExtension(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $this->allowedExtensions) && ($filename !== 'admin');
    }
}