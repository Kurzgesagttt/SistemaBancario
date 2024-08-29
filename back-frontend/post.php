<?php
require_once "config.php";

$nome = $_POST["nome"];
$cpf = $_POST["cpf"];
$senha = $_POST["senha"];
$nivel = $_POST["nivel"];

// Verifica se um arquivo de imagem foi enviado
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    // Le o conteúdo do arquivo
    $foto = file_get_contents($_FILES['foto']['tmp_name']);
} else {
    echo "Nenhum arquivo de imagem enviado.";
    exit; // Se não houver arquivo enviado, encerra o script
}

try {
    //esta linha informa o PDO como se conectar com o BD
    $pdo = new PDO("mysql:host=localhost;dbname=banco", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $pdo->beginTransaction();

    // Inserir usuário na tabela usuarios, incluindo a imagem
    $sql = "INSERT INTO usuarios(nome, cpf, senha, nivel, foto) VALUES (?, ?, ?, ?, ?)";//as "?" são placeholders do metódo bind_param. Que previnem injeções SQL que é um tipo de ataque ao servidor.
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $nome);
    $stmt->bindParam(2, $cpf);
    $stmt->bindParam(3, $senha);
    $stmt->bindParam(4, $nivel);
    $stmt->bindParam(5, $foto, PDO::PARAM_LOB);

    if ($stmt->execute()) {
        // Inserir registro correspondente na tabela Contas
        $sqlConta = "INSERT INTO contas(cpf, saldo, emprestimo) VALUES (?, 0.00, 0.00)";
        $stmtConta = $pdo->prepare($sqlConta);
        $stmtConta->bindParam(1, $cpf);

        if ($stmtConta->execute()) {
            // Se tudo estiver correto, commit a transação
            $pdo->commit();
            echo "Usuário e conta criados com sucesso";
        } else {
            // Rollback para garantir a integridade dos dados caso de algum erro no processo de criação de conta
            $pdo->rollBack();
            echo "Erro ao criar conta: " . $stmtConta->errorInfo()[2];
        }
    } else {
        // Rollback se a inserção na tabela usuarios falhar
        $pdo->rollBack();
        echo "Erro ao gravar dados: " . $stmt->errorInfo()[2];
    }
} catch (Exception $e) {
    // Rollback em caso de qualquer exceção
    $pdo->rollBack();
    echo "Erro: " . $e->getMessage();
}
?>
