<?php
// Visualizar.php - CORRIGIDO para usar MySQLi

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// O nome do arquivo incluído é 'Conexão.php' (com acento)
include("Conexão.php"); 
include("IntegracaoApi.php"); 

// --- LÓGICA PARA BUSCAR CLIMA (API OpenWeatherMap) ---
if (isset($_GET['action']) && $_GET['action'] === 'clima' && isset($_GET['cidade'])) {
    $cityName = trim($_GET['cidade']);
    
    if (empty($cityName)) {
        http_response_code(400);
        echo json_encode(["erro" => "Nome da cidade não fornecido."]);
        exit;
    }

    $weatherData = getWeatherByCity($cityName);

    if ($weatherData) {
        echo json_encode($weatherData, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(["erro" => "Dados climáticos não encontrados para a cidade: " . $cityName]);
    }
    
    // Fechar conexão antes de sair
    if (isset($mysqli)) { $mysqli->close(); }
    exit;
}

// --- LÓGICA PADRÃO PARA LISTAR CIDADES ---
try {
    // VERIFICA SE O OBJETO MYSQLI ESTÁ DISPONÍVEL
    if (!isset($mysqli) || $mysqli->connect_errno) {
        throw new Exception("Objeto de conexão (mysqli) não disponível ou falha na conexão.");
    }
    
    $sql = "SELECT c.id_Cidade, c.nome_Cidade, c.populacao_Cidade, p.nome_Pais 
            FROM cidade c
            INNER JOIN pais p ON c.id_Pais = p.id_Pais
            ORDER BY c.nome_Cidade ASC";

    // 1. Usa a conexão $mysqli fornecida pelo Conexão.php
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $mysqli->error);
    }
    
    $stmt->execute();
    
    // 2. Obtém o resultado e busca todos como array associativo (requer a extensão php-mysqlnd)
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Erro ao obter resultados: " . $mysqli->error);
    }
    
    // Converte o resultado em um array associativo
    $cidades = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($cidades, JSON_UNESCAPED_UNICODE);
    
    $stmt->close();
    
} catch (Exception $e) {
    // 3. Adiciona o status HTTP 500 para informar o JavaScript sobre a falha
    http_response_code(500); 
    // Retorna a mensagem de erro para o front-end
    echo json_encode(["erro" => "Erro de execução da consulta", "detalhe" => $e->getMessage()]);
}

// Fechar a conexão
if (isset($mysqli)) {
    $mysqli->close();
}
?>