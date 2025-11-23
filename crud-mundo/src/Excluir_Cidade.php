<?php
// Excluir_Pais.php - CORRIGIDO PARA USAR MYSQLI

// Isso inclui o arquivo Conexão.php, criando o objeto $mysqli
require_once 'Conexão.php';

if (isset($_GET['id_Cidade'])) {
    // 1. Acessa a variável de conexão criada em Conexão.php
    global $mysqli; 
    
    $id_Cidade = $_GET['id_Cidade'];

    try {
        // A linha $pdo = conectar(); foi removida
        
        // Query de exclusão
        $sql = "DELETE FROM cidade WHERE id_Cidade = ?";
        
        // 2. Usa o método prepare do objeto $mysqli
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            // 3. Faz o bind do parâmetro: "i" para integer (inteiro)
            $stmt->bind_param("i", $id_Cidade);
            
            // 4. Executa a query
            $success = $stmt->execute();

            // 5. Verifica o sucesso da execução e o número de linhas afetadas
            if ($success && $mysqli->affected_rows > 0) {
                header("Location: Visualizar.html");
                exit;
            } else {
                // Se $success for true mas affected_rows for 0, o ID não existe
                echo "Erro ao excluir cidade ou cidade não encontrada.";
            }
            
            $stmt->close(); // Fecha o statement
            
        } else {
             // Trata erro de preparação da query
            echo "Erro na preparação da query: " . $mysqli->error;
        }

    } catch (Exception $e) {
        // Esta parte do catch é menos comum em mysqli, mas mantida para segurança
        echo "Erro no servidor: " . $e->getMessage();
    }
    
} else {
    echo "ID da cidade não fornecido.";
}

// Fechamento da conexão no final do script
if (isset($mysqli)) {
    $mysqli->close();
}
?>