<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование файла</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/admin/edit_file.css">
</head>
<body>
<h1>Редактирование: <?= htmlspecialchars(basename($_GET['file'])) ?></h1>
<form method="post" action="/admin/files/edit">
    <input type="hidden" name="file" value="<?= htmlspecialchars($_GET['file']) ?>">
    <label for="editor">Содержимое файла:</label>
    <textarea id="editor" name="content"><?= htmlspecialchars($fileContent) ?></textarea>
    <div class="form-actions">
        <button type="submit">Сохранить</button>
        <a href="/admin/files">Отмена</a>
    </div>
</form>
</body>
</html>