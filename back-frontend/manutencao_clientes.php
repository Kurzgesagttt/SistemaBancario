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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete"])) {
        $id = $_POST["id"];
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST["update"])) {
        $id = $_POST["id"];
        $nome = $_POST["nome"];
        $cpf = $_POST["cpf"];
        $senha = $_POST["senha"];
        $nivel = $_POST["nivel"];
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, cpf = ?, senha = ?, nivel = ? WHERE id = ?");
        $stmt->execute([$nome, $cpf, $senha, $nivel, $id]);
    }
}

$usuarios = $pdo->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção de Clientes</title>
</head>
<body>
    <h1>Manutenção de Clientes</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>Senha</th>
            <th>Nível</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <form method="post">
                <td><?php echo $usuario->id; ?></td>
                <td><input type="text" name="nome" value="<?php echo $usuario->nome; ?>"></td>
                <td><input type="text" name="cpf" value="<?php echo $usuario->cpf; ?>"></td>
                <td><input type="password" name="senha" value="<?php echo $usuario->senha; ?>"></td>
                <td><input type="number" name="nivel" value="<?php echo $usuario->nivel; ?>"></td>
                <td>
                    <input type="hidden" name="id" value="<?php echo $usuario->id; ?>">
                    <button type="submit" name="update">Atualizar</button>
                    <button type="submit" name="delete">Excluir</button>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
