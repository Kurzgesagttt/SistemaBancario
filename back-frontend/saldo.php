<?php
session_start();

if (!isset($_SESSION["nivel"]) || $_SESSION["nivel"] != 1) {
    header("Location: login.html");
    exit;
}

$cpf = $_SESSION["cpf"];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT saldo FROM Contas WHERE cpf = :cpf";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['cpf' => $cpf]);
    $conta = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$conta) {
        echo "Conta não encontrada.";
        exit;
    }
} catch (PDOException $e) {
    echo "Erro ao conectar com o banco de dados.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Saldo</title>
</head>
<body>
    <h1>Saldo</h1>
    <p>Seu saldo é: <?php echo htmlspecialchars($conta->saldo); ?></p>
    <a href="nivel1.php">Voltar</a>
</body>
</html>
