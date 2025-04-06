<?php

namespace App\Controllers;

use App\Core\TemplateEngine;
use App\Services\AdminService;

class AdminController
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function indexAction(): void
    {
        $relativePath = $_GET['path'] ?? '';
        $templatePath = __DIR__ . '/../../public/views/admin/file_manager.html';
        $templateEngine = new TemplateEngine();

        $viewData = $this->adminService->getFileListViewData($relativePath);

        echo $templateEngine->render($templatePath, $viewData);
    }

    public function uploadAction(): void
    {
        $relativePath = $_GET['path'] ?? '';
        $this->adminService->uploadFile($_FILES, $relativePath);
        header("Location: /admin/files?path=" . urlencode($relativePath));
    }

    public function createDirectoryAction(): void
    {
        $relativePath = $_GET['path'] ?? '';
        $this->adminService->createDirectory($_POST['directory'], $relativePath);
        header("Location: /admin/files?path=" . urlencode($relativePath));
    }

    public function downloadAction(): void
    {
        $this->adminService->downloadFile($_GET['file']);
    }

    public function deleteAction(): void
    {
        $this->adminService->deleteFile($_POST['file']);
        header("Location: /admin/files");
    }

    public function editAction(): void
    {
        $templatePath = __DIR__ . '/../../public/views/admin/edit_file.html';
        $templateEngine = new TemplateEngine();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $relativePath = $_GET['file'] ?? '';
            $viewData = $this->adminService->getEditFileViewData($relativePath);
            echo $templateEngine->render($templatePath, $viewData);
        } else {
            try {
                $this->adminService->editFile($_POST['file'], $_POST['content']);
                $_SESSION['success'] = "Файл успешно сохранён";
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/admin/files'));
            exit;
        }
    }
}