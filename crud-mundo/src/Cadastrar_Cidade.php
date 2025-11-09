<?php
include 'Conexão.php';

//Para puxar os paises cadastrados e depois enviar pelo js
if (isset($_GET['action']) && $_GET['action'] === 'paises') {
    header('Content-Type: application/json');
    $sql = "SELECT id_Pais, nome_Pais FROM Pais ORDER BY nome_Pais";
    $result = $mysqli->query($sql); // <-- CORRIGIDO: $conn -> $mysqli

    $paises = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $paises[] = $row;
        }
        $result->free();
    }

    echo json_encode($paises);
    exit;
}

$nome_Cidade = '';
$populacao_Cidade = '';
$id_Pais = '';
$mensagem = '';

$listaPaises = [];
$res = $mysqli->query("SELECT id_Pais, nome_Pais FROM Pais ORDER BY nome_Pais");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $listaPaises[] = $row;
    }
    $res->free();
}

// INSERT Cidade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_Cidade = isset($_POST['nome_Cidade']) ? trim($_POST['nome_Cidade']) : '';
    $populacao_Cidade = isset($_POST['populacao_Cidade']) ? trim($_POST['populacao_Cidade']) : '';
    $id_Pais = isset($_POST['id_Pais']) ? (int)trim($_POST['id_Pais']) : 0;

    if ($nome_Cidade === '' || $populacao_Cidade === '' || $id_Pais === 0) {
        $mensagem = "Por favor preencha todos os campos corretamente.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO Cidade (nome_Cidade, populacao_Cidade, id_Pais) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param('sii', $nome_Cidade, $populacao_Cidade, $id_Pais);
            if ($stmt->execute()) {
                $mensagem = "Cidade cadastrada com sucesso. ID gerado: " . $stmt->insert_id;
                $nome_Cidade = '';
                $populacao_Cidade = '';
                $id_Pais = '';
            } else {
                $mensagem = "Erro ao inserir: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensagem = "Erro na preparação da query: " . $mysqli->error;
        }
    }
}

if (isset($mysqli)) { $mysqli->close(); }

?>
