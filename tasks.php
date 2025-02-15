<?php

require_once 'config/database.php';
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute($_SESSION['user_id']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/0f8151a53c.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            color: #fff !important;
        }

        .card {
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .status-dropdown {
            margin-left: 10px;
        }

        .status-dropdown .dropdown-menu {
            min-width: 120px;
        }

        .status-text {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .status-text[data-status="Активная"] {
            background-color: #d4edda;
            color: #155724;
        }

        .status-text[data-status="Приостановлена"] {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-text[data-status="Завершена"] {
            background-color: #ffcccb;
            color: #d9534f;

        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.php">To-Do List</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href='profile.php'>
                        <i class="fa-regular fa-user" style='padding-right: 10px;'></i>
                        <?= $user['name']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="actions/logout.php">Выйти</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Добавить новое задание</h5>
                    </div>
                    <div class="card-body">
                        <form id="taskForm">
                            <div class="form-group">
                                <label for="taskTitle">Заголовок</label>
                                <input type="text" class="form-control" id="taskTitle" placeholder="Введите заголовок">
                            </div>
                            <div class="form-group">
                                <label for="taskDescription">Описание</label>
                                <textarea class="form-control" id="taskDescription" rows="3"
                                    placeholder="Введите описание" style='resize: none;'></textarea>
                            </div>
                            <div class="form-group">
                                <label for="taskStatus">Статус</label>
                                <select class="form-control" id="taskStatus">
                                    <option value="Активная">Активная</option>
                                    <option value="Приостановлена">Приостановлена</option>
                                    <option value="Завершена">Завершена</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" id="addTaskBtn">Добавить задание</button>
                            <button type="button" class="btn btn-success" id="saveChangesBtn"
                                style="display: none;">Сохранить изменения</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Мои задания</h5>
                    </div>
                    <div class="card-body">
                        <ul id="taskList" class="list-group">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/tasks.js"></script>
    <script>
        // Скрипт для сохранения задач пользователя

        const userId = <?php echo json_encode($_SESSION['user_id']) ?>

        function saveTasksToLocalStorage() {
            const tasks = []
            document.querySelectorAll('#taskList .list-group-item').forEach(taskItem => {
                const title = taskItem.querySelector('h5').textContent
                const description = taskItem.querySelector('p').textContent
                const status = taskItem.querySelector('.status-text').getAttribute('data-status')
                tasks.push({ title, description, status })
            })

            localStorage.setItem(`tasks_user_${userId}`, JSON.stringify(tasks))
        }


        function loadTasksFromLocalStorage() {
            const tasks = JSON.parse(localStorage.getItem(`tasks_user_${userId}`)) || []
            tasks.forEach(task => {
                const taskItem = document.createElement('li')
                taskItem.className = 'list-group-item'
                taskItem.innerHTML = `
            <h5>${task.title}</h5>
            <p>${task.description}</p>
            <div class="status-dropdown">
                <span class="status-text" data-status="${task.status}">${task.status}</span>
                <button class="btn btn-sm btn-secondary float-right">Редактировать</button>
                <button class="btn btn-sm btn-danger float-right mr-2">Удалить</button>
            </div>
        `
                document.getElementById('taskList').appendChild(taskItem)
            })
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (userId) {
                loadTasksFromLocalStorage()
            } else {
                console.error('ID пользователя не найден. Задачи не будут загружены.')
            }
        })
    </script>
</body>

</html>