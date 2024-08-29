<?php
session_start();

if (!isset($_SESSION["nivel"]) || $_SESSION["nivel"] != 3) {
    header("Location: login.html");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar com o banco de dados: " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = $_POST["id"];
    $stmt = $pdo->prepare("DELETE FROM contas WHERE id = ?");
    $stmt->execute([$id]);
}

$contas = $pdo->query("SELECT * FROM contas")->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção de Contas</title>
</head>
<body>
    <h1>Manutenção de Contas</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>CPF</th>
            <th>Saldo</th>
            <th>Empréstimo</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($contas as $conta): ?>
        <tr>
            <form method="post">
                <td><?php echo $conta->id; ?></td>
                <td><?php echo $conta->cpf; ?></td>
                <td><?php echo $conta->saldo; ?></td>
                <td><?php echo $conta->emprestimo; ?></td>
                <td>
                    <input type="hidden" name="id" value="<?php echo $conta->id; ?>">
                    <button type="submit" name="delete">Excluir</button>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
