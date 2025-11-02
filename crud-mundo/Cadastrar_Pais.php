<?php 
include 'Conexão.php';
include 'api_integration.php';

// Buscar na api o nm do pais
if (isset($_GET['action']) && $_GET['action'] === 'dados_pais' && isset($_GET['pais'])) {
    header('Content-Type: application/json');
    $paisName = trim($_GET['pais']);
    
    if (empty($paisName)) {
        echo json_encode(["erro" => "Nome do país não fornecido."]);
        exit;
    }

    $countryData = getCountryData($paisName);

    if ($countryData) {
        echo json_encode($countryData, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["erro" => "País não encontrado na API Rest Countries."]);
    }
    exit;
}

// puxar continentes do bd
if (isset($_GET['action']) && $_GET['action'] === 'continentes') {
    include 'Conexão.php';
    $res = $mysqli->query("SELECT id_Continente, nome_Continente FROM Continente ORDER BY nome_Continente");
    $continentes = [];
    while ($row = $res->fetch_assoc()) {
        $continentes[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($continentes);
    exit;
}

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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_Pais = isset($_POST['nome_Pais']) ? trim($_POST['nome_Pais']) : '';
    $populacao_Pais = isset($_POST['populacao_Pais']) ? trim($_POST['populacao_Pais']) : '';
    $idioma_Pais = isset($_POST['idioma_Pais']) ? trim($_POST['idioma_Pais']) : '';
    $id_Continente = isset($_POST['id_Continente']) ? (int)trim($_POST['id_Continente']) : 0; 

    // campos da api
    $capital_Pais = '';
    $moeda_Pais = '';
    $bandeira_Pais_url = '';

    $countryData = getCountryData($nome_Pais);

    if ($countryData) {
        $capital_Pais = $countryData['capital'];
        $moeda_Pais = $countryData['currency_name'] . ' (' . $countryData['currency_code'] . ')';
        $bandeira_Pais_url = $countryData['flag_url'];
    } else {
        // não permitir se a aoi falhar, mudar isso
        $mensagem = "⚠️ Não foi possível obter dados complementares do país pela API Rest Countries.";
    } 

    if ($nome_Pais === '' || $populacao_Pais === '' || $idioma_Pais === '' || $id_Continente === 0) {
        $mensagem = "❌ Por favor, preencha todos os campos corretamente.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO Pais (nome_Pais, populacao_Pais, idioma_Pais, id_Continente, capital_Pais, moeda_Pais, bandeira_Pais_url) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param('sdsisss', $nome_Pais, $populacao_Pais, $idioma_Pais, $id_Continente, $capital_Pais, $moeda_Pais, $bandeira_Pais_url);
            
            if ($stmt->execute()) {
                echo "<script>alert('✅ País cadastrado com sucesso!\\nID: " . $stmt->insert_id . "\\nCapital: " . $capital_Pais . "\\nMoeda: " . $moeda_Pais . "'); window.location.href='Cadastrar_Pais.html';</script>";
                exit;
            } else {
                echo "<script>alert('❌ Erro ao inserir: " . addslashes($stmt->error) . "'); history.back();</script>";
                exit;
            }
            $stmt->close();
        } else {
            echo "<script>alert('❌ Erro ao preparar a query: " . addslashes($mysqli->error) . "'); history.back();</script>";
            exit;
        }
    }
}

if (isset($mysqli)) { $mysqli->close(); }
?>
