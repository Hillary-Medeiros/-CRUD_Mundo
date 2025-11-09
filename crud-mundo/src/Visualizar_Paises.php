<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

include("Conexão.php"); 

try {
    if (!isset($mysqli) || $mysqli->connect_errno) {
        throw new Exception("Objeto de conexão (mysqli) não disponível ou falha na conexão.");
    }
    
    $sql = "SELECT p.id_Pais, p.nome_Pais, p.populacao_Pais, p.idioma_Pais, 
                   p.capital_Pais, p.moeda_Pais, p.bandeira_Pais_url, 
                   c.nome_Continente 
            FROM pais p
            INNER JOIN continente c ON p.id_Continente = c.id_Continente
            ORDER BY p.nome_Pais ASC";

    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $mysqli->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Erro ao obter resultados: " . $mysqli->error);
    }
    
    //trasforma em array
    $paises = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($paises, JSON_UNESCAPED_UNICODE);
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500); 
    echo json_encode(["erro" => "Erro de execução da consulta", "detalhe" => $e->getMessage()]);
}

if (isset($mysqli)) {
    $mysqli->close();
}
?>
