<?php

session_start();

require_once '../config/database.php';
$complete = false;
if (isset($_POST['newName'])) {

	$newName = $_POST['newName'];
	if (trim($newName) === '') {
		die('Try again');
	}
	try {
		$stmt = $pdo->prepare("UPDATE users SET name=? WHERE id=?");
		$complete = $stmt->execute([trim($newName), $_SESSION['user_id'][0]]);
	} catch (\Exception $e) {
		die($e->getMessage());
	}
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Изменить имя</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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

		.form-container {
			max-width: 500px;
			margin: 50px auto;
			background-color: #fff;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
		}
	</style>
</head>

<body>
	<!-- Шапка -->
	<nav class="navbar navbar-expand-lg navbar-light">
		<a class="navbar-brand" href="/index.php">To-Do List</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
			aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
	</nav>

	<!-- Основной контент -->
	<div class="form-container">
		<h2 class="text-center">Изменить имя</h2>
		<?php if ($complete): ?>
			<h2 class="text-center" style='color: green;'>Имя изменено</h2>
		<?php endif ?>
		<form id="changeNameForm" method='post'>
			<div class="form-group">
				<label for="newName">Новое имя</label>
				<input type="text" class="form-control" id="newName" name='newName' required>
			</div>
			<button type="submit" class="btn btn-primary btn-block">Сохранить</button>
			<button type="button" class="btn btn-secondary btn-danger btn-block"
				onclick="window.location.href='/profile.php'">Назад</button>
		</form>

	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>