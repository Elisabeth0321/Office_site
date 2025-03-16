<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать сотрудника</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            color: #ff6f61;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #6b5b95;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 2px solid #ff6f61;
            border-radius: 10px;
            font-size: 1rem;
            color: #6b5b95;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #6b5b95;
        }

        .btn-edit {
            width: 100%;
            padding: 0.75rem;
            background: #6b5b95;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-edit:hover {
            background: #ff6f61;
        }

        .message {
            text-align: center;
            color: #6b5b95;
            font-weight: bold;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Редактировать сотрудника</h1>
    <form action="/employees/edit" method="POST">
        <?php if (!empty($employee)): ?>
            <input type="hidden" name="id" value="<?php echo $employee->id; ?>">

            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($employee->getName()); ?>" required>

            <label for="salary">Зарплата:</label>
            <input type="number" id="salary" name="salary" value="<?php echo htmlspecialchars($employee->getSalary()); ?>" required>

            <label for="position">Должность:</label>
            <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($employee->getPosition()); ?>" required>

            <label for="department">Департамент:</label>
            <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($employee->getDepartment()); ?>" required>

            <button type="submit" class="btn-edit">Сохранить</button>
        <?php else: ?>
            <div class="message">Сотрудник не найден.</div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>