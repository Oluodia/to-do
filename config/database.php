<?php


$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'todoproj';

try {
	$pdo = new PDO("mysql:host=$host; dbname=$dbname", $user, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die('Connected failed: ' . $e->getMessage());
}