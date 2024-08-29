<?php
session_start();

// Verifica se o usuário está logado e tem nível 2
if (!isset($_SESSION["nivel"]) || $_SESSION["nivel"] != 2) {
    header("Location: login.html");
    exit;
}

// Função para buscar as informações do usuário pelo CPF
function buscarUsuarioPorCpf($pdo, $cpf) {
    $query = "SELECT u.nome, c.saldo, u.foto FROM usuarios u JOIN contas c ON u.cpf = c.cpf WHERE u.cpf = :cpf";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['cpf' => $cpf]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

$erro = "";
$resultado = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpfConsulta = $_POST["cpfConsulta"];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=banco", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Buscar as informações do usuário
        $resultado = buscarUsuarioPorCpf($pdo, $cpfConsulta);

        if (!$resultado) {
            $erro = "Usuário não encontrado.";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao conectar com o banco de dados: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nível 2 - Banco Do Bostil</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION["nome"]); ?></h1>
    <h2>Consulta de Usuário</h2>
    
    <form action="nivel2.php" method="post">
        <label for="cpfConsulta">CPF do Usuário:</label>
        <input type="text" name="cpfConsulta" id="cpfConsulta" required>
        <button type="submit">Consultar</button>
    </form>
    
    <?php
    if ($erro) {
        echo "<p style='color:red;'>$erro</p>";
    }
    
    if ($resultado) {
        
        echo "<h3>Resultado da Consulta:</h3>";
        if ($resultado->foto) {
            $fotoBase64 = base64_encode($resultado->foto);
            echo "<img src='data:image/jpeg;base64,$fotoBase64' alt='Foto do usuário' style='max-width: 150px; height: auto;'/>";
        } else {
            echo "<p>Foto não disponível.</p>";
        }
        echo "<p>Nome: " . htmlspecialchars($resultado->nome) . "</p>";
        echo "<p>Saldo: R$ " . number_format($resultado->saldo, 2, ',', '.') . "</p>";
        
    }
    ?>
</body>
</html>
