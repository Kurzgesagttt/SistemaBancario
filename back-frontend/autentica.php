<?php
session_start();

$autenticador = $_POST["cpfzinho"];
$senha = $_POST["senha"];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=banco", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Não consegui conexão com o BD: " . $e->getMessage();
    header("Location: erro.html");
    exit;
}

$query = "SELECT * FROM usuarios WHERE cpf = :autenticador";
$stmt = $pdo->prepare($query);
$stmt->execute(['autenticador' => $autenticador]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Verifica se a senha fornecida corresponde à senha armazenada no banco de dados
    if ($senha === $usuario['senha']) {
        $_SESSION["nivel"] = $usuario['nivel'];
        $_SESSION["nome"] = $usuario['nome'];
        $_SESSION["cpf"] = $usuario['cpf'];

        switch($usuario['nivel']) {
            case 1:
                header("Location: nivel1.php");
                exit;
            case 2:
                header("Location: nivel2.php");
                exit;
            case 3:
                header("Location: nivel3.php");
                exit;
            default:
                echo "<strong>Nível de acesso inválido</strong>";
                exit;
        }
    } else {
        echo "<strong>Erro na senha</strong>";
    }
} else {
    echo "<strong>Usuário não encontrado</strong>";
}
exit;
?>
