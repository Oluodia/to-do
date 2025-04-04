<?php

require_once 'config/database.php';
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

try{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$_SESSION['user_id'][0]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (\Exception $e) {
    die($e->getMessage());
}

if(isset($_POST['newName'])) {
    $newName = $_POST['newName'];

	if (trim($newName) === '') {
		die('Try again');
	}

	try {
		$stmt = $pdo->prepare("UPDATE users SET name=? WHERE id=?");
		$complete = $stmt->execute([trim($newName), $_SESSION['user_id'][0]]);

        header('Location: profile.php');
	} catch (\Exception $e) {
		die($e->getMessage());
	}

} else if(isset($_POST['newEmail'])) {
    try {
		$stmt = $pdo->prepare("UPDATE users SET email=? WHERE id=?");
		$complete = $stmt->execute([$_POST['newEmail'], $_SESSION['user_id'][0]]);

        header('Location: profile.php');
	} catch (\Exception $e) {
		die($e->getMessage());
	}
} else if(isset($_POST['currentPassword'])) {
    $newPassword = $_POST['newPassword'];

    if(password_verify($_POST['currentPassword'], $user['password_hash'])) {
        if($newPassword === $_POST['confirmNewPassword']) {
            try {
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?");
                $complete = $stmt->execute([$newPasswordHash, $_SESSION['user_id'][0]]);
                header('Location: profile.php');
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        } else {
            die('Passwords must match');
        }
    } else {
        die('Wrong current password');
    }
}




?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #fff !important;
        }
        .profile-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .user-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        .user-email {
            font-size: 18px;
            color: #555;
            text-align: center;
            margin-bottom: 20px;
        }
        .buttons-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 300px;
            margin: 0 auto;
        }
        .btn-custom {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            text-align: left;
        }
    </style>
</head>
<body>
    <!-- Шапка -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.php">To-Do List</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>

    <!-- Основной контент -->
    <div class="profile-container">
        <!-- Заголовок с именем пользователя -->
        <div class="user-name" id="userName"><?= $user['name'] ?></div>
        <div class="user-email" id="userEmail">Ваша почта: <?= $user['email'] ?></div>

        <!-- Контейнер с кнопками -->
        <div class="buttons-container">
            <button id="changeNameBtn" class="btn btn-info btn-custom">Изменить имя</button>
            <button id="changeEmailBtn" class="btn btn-primary btn-custom">Поменять почту</button>
            <button id="changePasswordBtn" class="btn btn-warning btn-custom">Поменять пароль</button>
            <button id="logoutBtn" class="btn btn-danger btn-custom">Выйти из аккаунта</button>
        </div>
    </div>

    <!-- Модальное окно для смены имени -->
    <div class="modal fade" id="changeNameModal" tabindex="-1" aria-labelledby="changeNameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeNameModalLabel">Изменить имя</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeNameForm" method='post'>
                        <div class="form-group">
                            <label for="newName">Новое имя</label>
                            <input type="text" class="form-control" id="newName" name='newName' required>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для смены почты -->
    <div class="modal fade" id="changeEmailModal" tabindex="-1" aria-labelledby="changeEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeEmailModalLabel">Поменять почту</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeEmailForm" method='post'>
                        <div class="form-group">
                            <label for="newEmail">Новая почта</label>
                            <input type="email" class="form-control" id="newEmail" name='newEmail' required>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для смены пароля -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Поменять пароль</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm" method='post'>
                        <div class="form-group">
                            <label for="currentPassword">Текущий пароль</label>
                            <input type="password" class="form-control" id="currentPassword" name='currentPassword' required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">Новый пароль</label>
                            <input type="password" class="form-control" id="newPassword" name='newPassword' required>
                        </div>
                        <div class="form-group">
                            <label for="confirmNewPassword">Подтвердите новый пароль</label>
                            <input type="password" class="form-control" id="confirmNewPassword" name='confirmNewPassword' required>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Открытие модального окна для смены имени
        document.getElementById('changeNameBtn').addEventListener('click', function() {
            $('#changeNameModal').modal('show');
        });

        // Открытие модального окна для смены почты
        document.getElementById('changeEmailBtn').addEventListener('click', function() {
            $('#changeEmailModal').modal('show');
        });

        // Открытие модального окна для смены пароля
        document.getElementById('changePasswordBtn').addEventListener('click', function() {
            $('#changePasswordModal').modal('show');
        });

        // Обработка выхода
        document.getElementById('logoutBtn').addEventListener('click', function() {
            window.location.href = 'actions/logout.php'; // Перенаправление на страницу выхода
        });
    </script>
</body>
</html>