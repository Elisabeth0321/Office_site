<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Файловый менеджер</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/admin/file_manager.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<h1>Файловый менеджер</h1>

<div class="nav-buttons">
    <a href="/admin" class="nav-button" title="В корневую папку">
        <i class="fas fa-home"></i>
    </a>

    <?php if ($currentPath && $currentPath !== 'public'): ?>
        <a href="?path=<?= urlencode(dirname($currentPath)) ?>" class="nav-button" title="Назад">
            <i class="fas fa-arrow-left"></i>
        </a>
    <?php endif; ?>

    <span class="current-path"><?= htmlspecialchars($currentPath) ?></span>
</div>

<form method="post" action="?route=upload_file&path=<?= urlencode($currentPath) ?>" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Загрузить</button>
</form>

<form method="post" action="/admin/files/mkdir" style="margin-top: 15px;">
    <label>
        <input type="text" name="directory" placeholder="Название директории" required>
    </label>
    <button type="submit">Создать папку</button>
</form>

<ul class="file-list">
    <?php foreach ($files as $file): ?>
        <?php $isDir = is_dir($this->adminService->getFullPath(($currentPath ? $currentPath . '/' : '') . $file)); ?>
        <li class="<?= $isDir ? 'dir-item' : 'file-item' ?>">
            <div class="file-name">
                <?= $isDir ? '📁' : '📄' ?>

                <?php if ($isDir): ?>
                    <a href="?path=<?= urlencode(($currentPath ? $currentPath . '/' : '') . $file) ?>">
                        <?= htmlspecialchars($file) ?>
                    </a>
                <?php else: ?>
                    <a href="/admin/files/edit?file=<?= urlencode(($currentPath ? $currentPath . '/' : '') . $file) ?>">
                        <?= htmlspecialchars($file) ?>
                    </a>
                <?php endif; ?>
            </div>

            <span class="actions">
        <?php if (!$isDir): ?>
            <a href="/admin/files/download?file=<?= urlencode(($currentPath ? $currentPath . '/' : '') . $file) ?>">Скачать</a>
        <?php endif; ?>
        <form method="post" action="/admin/files/delete" style="display:inline;">
            <input type="hidden" name="file" value="<?= htmlspecialchars(($currentPath ? $currentPath . '/' : '') . $file) ?>">
            <button type="submit" onclick="return confirm('<?= $isDir ? 'Удалить директорию?' : 'Удалить файл?' ?>')">Удалить</button>
        </form>
    </span>
        </li>

    <?php endforeach; ?>
</ul>
</body>
</html>