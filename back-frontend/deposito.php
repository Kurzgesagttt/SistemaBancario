<?php
session_start();

if (!isset($_SESSION["nivel"]) || $_SESSION["nivel"] != 1) {
    header("Location: login.html");
    exit;
}

$cpf = $_SESSION["cpf"];
$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor = $_POST["valor"];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=banco", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "UPDATE Contas SET saldo = saldo + :valor WHERE cpf = :cpf";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['valor' => $valor, 'cpf' => $cpf]);

        $mensagem = "Depósito realizado com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro ao conectar com o banco de dados.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depósito</title>
</head>
<body>
    <h1>Depósito</h1>
    <form method="post">
        <label for="valor">Valor: </label>
        <input type="number" name="valor" id="valor" required>
        <button type="submit">Depositar</button>
    </form>
    <p><?php echo htmlspecialchars($mensagem); ?></p>
