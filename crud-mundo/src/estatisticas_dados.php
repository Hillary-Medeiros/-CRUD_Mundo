<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'Conexão.php'; 

header('Content-Type: application/json; charset=utf-8');

$dados = [
    'paises_mais' => [],
    'cidades_mais' => [],
    'paises_menos' => [],
    'cidades_menos' => []
];

try {
    // 1. TOP 3 Países Mais Populosos
    $sql_paises_mais = "SELECT nome_Pais, populacao_Pais FROM pais ORDER BY populacao_Pais DESC LIMIT 3";
    $result_paises_mais = $mysqli->query($sql_paises_mais);
    if ($result_paises_mais) {
        while ($row = $result_paises_mais->fetch_assoc()) {
            // Converte a população para número inteiro ou float antes de adicionar ao array, para o JSON
            $row['populacao_Pais'] = (float) $row['populacao_Pais']; 
            $dados['paises_mais'][] = $row;
        }
        $result_paises_mais->free();
    }

    // 2. TOP 3 Cidades Mais Populosas
    $sql_cidades_mais = "SELECT nome_Cidade, populacao_Cidade FROM cidade ORDER BY populacao_Cidade DESC LIMIT 3";
    $result_cidades_mais = $mysqli->query($sql_cidades_mais);
    if ($result_cidades_mais) {
        while ($row = $result_cidades_mais->fetch_assoc()) {
            $row['populacao_Cidade'] = (int) $row['populacao_Cidade']; 
            $dados['cidades_mais'][] = $row;
        }
        $result_cidades_mais->free();
    }

    // 3. TOP 3 Países Menos Populosos (ORDER BY ASC)
    $sql_paises_menos = "SELECT nome_Pais, populacao_Pais FROM pais ORDER BY populacao_Pais ASC LIMIT 3";
    $result_paises_menos = $mysqli->query($sql_paises_menos);
    if ($result_paises_menos) {
        while ($row = $result_paises_menos->fetch_assoc()) {
             $row['populacao_Pais'] = (float) $row['populacao_Pais']; 
            $dados['paises_menos'][] = $row;
        }
        $result_paises_menos->free();
    }
    
    // 4. TOP 3 Cidades Menos Populosas (ORDER BY ASC)
    $sql_cidades_menos = "SELECT nome_Cidade, populacao_Cidade FROM cidade ORDER BY populacao_Cidade ASC LIMIT 3";
    $result_cidades_menos = $mysqli->query($sql_cidades_menos);
    if ($result_cidades_menos) {
        while ($row = $result_cidades_menos->fetch_assoc()) {
             $row['populacao_Cidade'] = (int) $row['populacao_Cidade']; 
            $dados['cidades_menos'][] = $row;
        }
        $result_cidades_menos->free();
    }

    // Retorna os dados como JSON
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // Em caso de erro, retorna um JSON de erro
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar dados: ' . $e->getMessage()]);
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}
?>