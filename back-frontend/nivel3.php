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

// Função para vender seguro de vida
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["vender_seguro"])) {
    $cpf = $_POST["cpf"];

    try {
        $pdo->beginTransaction();

        // Verifica se o usuário existe e obtém o saldo atual
        $stmt = $pdo->prepare("SELECT saldo FROM contas WHERE cpf = ?");
        $stmt->execute([$cpf]);
        $conta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($conta) {
            $novoSaldo = $conta["saldo"] - 1000.00;

            if ($novoSaldo >= 0) {
                // Atualiza o saldo do usuário
                $stmt = $pdo->prepare("UPDATE contas SET saldo = ? WHERE cpf = ?");
                $stmt->execute([$novoSaldo, $cpf]);

                $pdo->commit();
                $mensagem = "Seguro de vida vendido com sucesso. R$1000 foram retirados do saldo do usuário.";
            } else {
                $pdo->rollBack();
                $mensagem = "Saldo insuficiente para vender o seguro de vida.";
            }
        } else {
            $pdo->rollBack();
            $mensagem = "Usuário não encontrado.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensagem = "Erro ao vender seguro de vida: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nível 3 - Banco Do Bostil</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo $_SESSION["nome"]; ?></h1>

    <h2>Opções</h2>
    <ul>
        <li><a href="manutencao_clientes.php">Manutenção de Clientes</a></li>
        <li><a href="manutencao_contas.php">Manutenção de Contas</a></li>
    </ul>

    <h2>Vender Seguro de Vida</h2>
    <form method="post">
        <label for="cpf">CPF do Usuário:</label>
        <input type="text" id="cpf" name="cpf" required>
        <button type="submit" name="vender_seguro">Vender Seguro</button>
    </form>

    <?php if (isset($mensagem)): ?>
        <p><?php echo $mensagem; ?></p>
    <?php endif; ?>
</body>
</html>