<?php

namespace App\Controllers;

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
        $files = $this->adminService->listFiles($relativePath);
        $currentPath = $relativePath;
        include __DIR__ . '/../../public/views/admin/file_manager.php';
    }

    public function uploadAction(): void
    {
        $relativePath = $_GET['path'] ?? '';
        $this->adminService->uploadFile($_FILES, $relativePath);
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
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $relativePath = $_GET['file'] ?? '';
            $fullPath = $this->adminService->getFullPath($relativePath);

            $fileContent = $this->adminService->getFileContent($relativePath);
            include __DIR__ . '/../../public/views/admin/edit_file.php';
        } else {
            try {
                $this->adminService->editFile($_POST['file'], $_POST['content']);
                $_SESSION['success'] = "Файл успешно сохранён";
                header("Location: /admin/files");
                exit;
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: " . $_SERVER['HTTP_REFERER'] ?? '/admin/files');
                exit;
            }
        }
    }

    public function createDirectoryAction(): void
    {
        $this->adminService->createDirectory($_POST['directory']);
        header("Location: /admin/files");
    }
}