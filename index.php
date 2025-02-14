<?php

require_once 'config/database.php';

session_start();
error_reporting(0);


if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
	header('Location: tasks.php');
	exit();
}

$email = $_POST['email'];
$password = $_POST['password'];
$is_valid = true;

if (!isset($_SESSION['user_id'])) {
	$_SESSION['user_id'] = [];
}


if (isset($_POST['email'])) {
	$stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
	$stmt->execute([$email]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	$verifyPass = password_verify($password, $user['password_hash']);

	if ($user && $verifyPass) {
		array_push($_SESSION['user_id'], $user['id']);
		header('Location: tasks.php');
		exit();
	} else {
		$is_valid = false;
	}
}


?>

<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
	<style>
		body {
			background-color: #f8f9fa;
		}

		.login-container {
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

	<div class="login-container">
		<h2 class="text-center">Войти</h2>
		<form method="post">
			<div class="form-group">
				<label for="email">Электронная почта</label>
				<input type="email" class="form-control" name="email" id=" email" placeholder="Введите вашу почту"
					required>
			</div>
			<div class="form-group">
				<label for="password">Пароль</label>
				<input type="password" class="form-control" name="password" id="password"
					placeholder="Введите ваш пароль" required>
			</div>
			<?php if (!$is_valid): ?>
				<div style="color: red;">Неправильный логин или пароль</div>
			<?php endif; ?>
			<button type="submit" class="btn btn-primary btn-block">Войти</button>
		</form>
		<div class="text-center mt-3">
			<a href="actions/register.php">Регистрация</a>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>