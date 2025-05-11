<?php
declare(strict_types = 1);

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\AdminService;
use Throwable;

class AdminController
{
    private AdminService $adminService;
    private TemplateEngine $templateEngine;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
        $this->templateEngine = new TemplateEngine();
    }

    public function indexAction(): void
    {
        try {
            $relativePath = $_GET['path'] ?? '';
            $templatePath = __DIR__ . '/../../public/views/admin/file_manager.html';
            $viewData = $this->adminService->getFileListViewData($relativePath);
            echo $this->templateEngine->render($templatePath, $viewData);
        } catch (Throwable $e) {
            error_log("Ошибка в indexAction (AdminController): " . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при загрузке файлов.";
        }
    }

    public function uploadAction(): void
    {
        try {
            $relativePath = $_GET['path'] ?? '';
            $this->adminService->uploadFile($_FILES, $relativePath);
        } catch (Throwable $e) {
            error_log("Ошибка в uploadAction (AdminController): " . $e->getMessage());
            $_SESSION['error'] = "Не удалось загрузить файл.";
        }

        header("Location: /admin/files?path=" . urlencode($relativePath));
        exit();
    }

    public function createDirectoryAction(): void
    {
        try {
            $relativePath = $_GET['path'] ?? '';
            $this->adminService->createDirectory($_POST['directory'], $relativePath);
        } catch (Throwable $e) {
            error_log("Ошибка в createDirectoryAction (AdminController): " . $e->getMessage());
            $_SESSION['error'] = "Не удалось создать каталог.";
        }

        header("Location: /admin/files?path=" . urlencode($relativePath));
        exit();
    }

    public function downloadAction(): void
    {
        try {
            $this->adminService->downloadFile($_GET['file']);
        } catch (Throwable $e) {
            error_log("Ошибка в downloadAction (AdminController): " . $e->getMessage());
            http_response_code(500);
            echo "Ошибка при скачивании файла.";
        }
    }

    public function deleteAction(): void
    {
        try {
            $this->adminService->deleteFile($_POST['file']);
        } catch (Throwable $e) {
            error_log("Ошибка в deleteAction (AdminController): " . $e->getMessage());
            $_SESSION['error'] = "Не удалось удалить файл.";
        }

        header("Location: /admin/files");
        exit();
    }

    public function editAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/admin/edit_file.html';

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $relativePath = $_GET['file'] ?? '';
                $viewData = $this->adminService->getEditFileViewData($relativePath);
                echo $this->templateEngine->render($templatePath, $viewData);
            } catch (Throwable $e) {
                error_log("Ошибка в editAction GET (AdminController): " . $e->getMessage());
                http_response_code(500);
                echo "Ошибка при загрузке файла для редактирования.";
            }
        } else {
            try {
                $this->adminService->editFile($_POST['file'], $_POST['content']);
                $_SESSION['success'] = "Файл успешно сохранён";
            } catch (Throwable $e) {
                error_log("Ошибка в editAction POST (AdminController): " . $e->getMessage());
                $_SESSION['error'] = "Не удалось сохранить файл.";
            }

            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/admin/files'));
            exit();
        }
    }
}
