<?php
require_once '../config/database.php';
session_start();

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
	header('Location: tasks.php');
	exit();
}

if (isset($_POST['email'])) {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	$stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
	$stmt->execute([$email]);
	$candidate = $stmt->fetch(PDO::FETCH_ASSOC);
	$isNotValidPass = false;

	if (!$password === $_POST['confirm_password']) {
		$isNotValidPass = true;
	} else if ($candidate) {
	} else {
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		try {
			$stmt = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password_hash`) VALUES (?, ?, ?)");
			$stmt->execute([$name, $email, $password_hash]);
			header('Location: ../index.php');
		} catch (\Exception $e) {
			die($e->getMessage());
		}
	}
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Регистрация</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
	<style>
		body {
			background-color: #f8f9fa;
		}

		.register-container {
			max-width: 400px;
			margin: 100px auto;
			padding: 30px;
			background-color: #ffffff;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
		}
	</style>
</head>

<body>

	<div class="register-container">
		<h2 class="text-center">Регистрация</h2>
		<form method="post">
			<div class="form-group">
				<label for="name">Имя</label>
				<input type="text" class="form-control" name="name" id="fullname" placeholder="Введите ваше полное имя"
					required>
			</div>
			<div class="form-group">
				<label for="email">Электронная почта</label>
				<input type="email" class="form-control" name="email" id="email" placeholder="Введите вашу почту"
					required>
			</div>
			<div class="form-group">
				<label for="password">Пароль</label>
				<input type="password" class="form-control" name="password" id="password"
					placeholder="Введите ваш пароль" required>
			</div>
			<div class="form-group">
				<label for="confirm_password">Подтверждение пароля</label>
				<input type="password" class="form-control" name="confirm_password" id="confirm_password"
					placeholder="Подтвердите ваш пароль" required>
			</div>
			<?php if (isset($_POST['email'])): ?>
				<?php if ($isNotValidPass): ?>
					<div style="color: red;">Вы ввели разные пароли</div>
				<?php elseif ($candidate): ?>
					<div style="color: red;">Пользователь с такое почтой уже существует</div>
				<?php endif; ?>
			<?php endif; ?>
			<button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
			</m>
			<div class="text-center mt-3">
				<a href="../index.php">Уже есть аккаунт? Войти</a>
			</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>