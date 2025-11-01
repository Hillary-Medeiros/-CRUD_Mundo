<?php
include 'Conexão.php';

$nome_Pais = '';
$populacao_Pais = '';
$idioma_Pais = '';
$id_Continente = '';
$mensagem = '';

$listaContinentes = [];
$res = $mysqli->query("SELECT id_Continente, nome_Continente FROM Continente ORDER BY nome_Continente");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $listaContinentes[] = $row;
    }
    $res->free();
}

// --- 2. TRATAMENTO DO POST (INSERÇÃO) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_Pais = isset($_POST['nome_Pais']) ? trim($_POST['nome_Pais']) : '';
    $populacao_Pais = isset($_POST['populacao_Pais']) ? trim($_POST['populacao_Pais']) : '';
    $idioma_Pais = isset($_POST['idioma_Pais']) ? trim($_POST['idioma_Pais']) : '';
    $id_Continente = isset($_POST['id_Continente']) ? (int)trim($_POST['id_Continente']) : 0; 


    if ($nome_Pais === '' || $populacao_Pais === '' || $idioma_Pais === '' || $id_Continente === 0) {
        $mensagem = "Por favor preencha todos os campos corretamente.";
    } else {
        // INSERT Pais
        $stmt = $mysqli->prepare("INSERT INTO Pais (nome_Pais, populacao_Pais, idioma_Pais, id_Continente) VALUES (?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param('sdsi', $nome_Pais, $populacao_Pais, $idioma_Pais, $id_Continente);
            
            if ($stmt->execute()) {
                $mensagem = "País cadastrado com sucesso. ID gerado: " . $stmt->insert_id;
                $nome_Pais = $populacao_Pais = $idioma_Pais = '';
                $id_Continente = 0;
            } else {
                $mensagem = "Erro ao inserir: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensagem = "Erro na preparação da query: " . $mysqli->error;
        }
    }
}

// Fechar conexão
if (isset($mysqli)) { $mysqli->close(); }
?>