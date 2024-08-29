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

    // Consulta para obter a foto da tabela 'usuarios'
    $query = "SELECT foto FROM usuarios WHERE cpf = :cpf";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['cpf' => $cpf]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "Usuário não encontrado.";
        exit;
    }

    // Converta os dados da imagem em uma string base64
    $imagemBase64 = base64_encode($usuario['foto']);
    $caminhoFoto = 'data:image/jpeg;base64,' . $imagemBase64;

    // Consulta para obter saldo e empréstimo da tabela 'contas'
    $query = "SELECT saldo, emprestimo FROM contas WHERE cpf = :cpf";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['cpf' => $cpf]);
    $conta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$conta) {
        echo "Conta não encontrada.";
        exit;
    }

    $saldo = $conta['saldo'];
    $emprestimo = $conta['emprestimo'];
} catch (PDOException $e) {
    echo "Erro ao conectar com o banco de dados: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nível 1 - Banco Do Bostil</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION["nome"]); ?></h1>
    <!-- Exibe a foto do usuário -->
    <img src="<?php echo $caminhoFoto; ?>" alt="Foto do usuário" width="150px">

    <h2>Dados da Conta</h2>
    <p>Saldo: <?php echo number_format($saldo, 2, ',', '.'); ?></p>
    <p>Empréstimo: <?php echo number_format($emprestimo, 2, ',', '.'); ?></p>

    <h2>Menu</h2>
    <ul>
        <li><a href="consultar_saldo.php">Consultar Saldo</a></li>
        <li><a href="retirada.php">Retirada</a></li>
        <li><a href="deposito.php">Depósito</a></li>
        <li><a href="contrair_emprestimo.php">Contrair Empréstimo</a></li>
    </ul>
</body>
</html>
