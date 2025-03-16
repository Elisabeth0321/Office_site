<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список сотрудников</title>
    <link rel="stylesheet" type="text/css" href="/public/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;

            justify-content: center;
            align-items: center;
        }

        h1 {
            color: #ff69b4;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-add, .btn-edit, .btn-delete {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
        }

        .btn-add {
            background-color: #ff69b4;
        }

        .btn-edit {
            background-color: #87ceeb;
        }

        .btn-delete {
            background-color: #ff4500;
        }

        .employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .employee-table th, .employee-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .employee-table th {
            background-color: #ff69b4;
            color: #fff;
        }

        .employee-table tr:hover {
            background-color: #f5f5f5;
        }

        .message {
            text-align: center;
            color: #6b5b95;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Список сотрудников</h1>
    <a href="/employees/add-form" class="btn-add">Добавить сотрудника</a>

    <table class="employee-table">
        <thead>
        <tr>
            <th>Имя</th>
            <th>Департамент</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($employees)): ?>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?php echo htmlspecialchars($employee->getName()); ?></td>
                    <td><?php echo htmlspecialchars($employee->getDepartment()); ?></td>
                    <td>
                        <a href="/employees/edit-form?id=<?php echo $employee->id; ?>" class="btn-edit">Редактировать</a>
                        <a href="/employees/delete?id=<?php echo $employee->id; ?>" class="btn-delete" onclick="return confirm('Вы уверены?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="message">Сотрудники не найдены.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>