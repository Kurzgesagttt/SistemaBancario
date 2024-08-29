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

        $pdo->beginTransaction();

        $query = "SELECT saldo FROM Contas WHERE cpf = :cpf FOR UPDATE";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['cpf' => $cpf]);
        $conta = $stmt->fetch(PDO::FETCH_OBJ);

        if ($conta && $conta->saldo >= $valor) {
            $novoSaldo = $conta->saldo - $valor;
            $updateQuery = "UPDATE Contas SET saldo = :novoSaldo WHERE cpf = :cpf";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute(['novoSaldo' => $novoSaldo, 'cpf' => $cpf]);
            $pdo->commit();
            $mensagem = "Retirada realizada com sucesso!";
        } else {
            $mensagem = "Saldo insuficiente!";
            $pdo->rollBack();
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $mensagem = "Erro ao conectar com o banco de dados.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retirada</title>
</head>
<body>
    <h1>Retirada</h1>
    <form method="post">
        <label for="valor">Valor: </label>
        <input type="number" name="valor" id="valor" required>
        <button type="submit">Retirar</button>
    </form>
    <p><?php echo htmlspecialchars($mensagem); ?></p>
    <a href="nivel1.php">Voltar</a>
</body>
</html>
