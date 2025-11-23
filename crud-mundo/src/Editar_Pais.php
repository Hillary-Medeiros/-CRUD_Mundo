<?php
// Editar_Pais.php
require_once 'Conexão.php';
require_once 'IntegracaoApiEDITAR.php'; // Inclui o novo arquivo de integração

// Função para conectar ao banco de dados (adaptada da análise do arquivo)
function conectar() {
    global $mysqli;
    // Verifica se a conexão está ativa
    if ($mysqli->connect_errno) {
        // Não usar die() aqui, apenas retornar null ou lançar exceção
        return null;
    }
    // Retorna o objeto mysqli para uso
    return $mysqli;
}

// Verifica se a requisição é para buscar dados de um país para preencher o formulário
if (isset($_GET['action']) && $_GET['action'] === 'pais' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id_Pais = $_GET['id'];
    $mysqli = conectar();
    if (!$mysqli) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro de conexão com o banco de dados.']);
        exit;
    }
    
    $sql = "SELECT p.*, c.nome_Continente FROM pais p JOIN continente c ON p.id_Continente = c.id_Continente WHERE p.id_Pais = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_Pais);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $pais = $result->fetch_assoc();
        echo json_encode($pais);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'País não encontrado.']);
    }
    exit;
}

// Verifica se a requisição é para buscar a lista de continentes
if (isset($_GET['action']) && $_GET['action'] === 'continentes') {
    header('Content-Type: application/json');
    $mysqli = conectar();
    if (!$mysqli) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro de conexão com o banco de dados.']);
        exit;
    }
    
    $sql = "SELECT id_Continente, nome_Continente FROM continente ORDER BY nome_Continente";
    $result = $mysqli->query($sql);
    
    $continentes = [];
    while ($row = $result->fetch_assoc()) {
        $continentes[] = $row;
    }
    echo json_encode($continentes);
    exit;
}

// Verifica se a requisição é para buscar dados de API (capital, moeda, bandeira)
if (isset($_GET['action']) && $_GET['action'] === 'dados_pais' && isset($_GET['pais'])) {
    // Agora chama a função centralizada que busca e formata os dados da API
    handleApiRequestForEdit('pais', $_GET['pais']);
    exit;
}

// Lógica de edição (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_Pais = $_POST['id_Pais'] ?? null;
    $nome_Pais = $_POST['nome_Pais'] ?? null;
    $populacao_Pais = $_POST['populacao_Pais'] ?? null;
    $idioma_Pais = $_POST['idioma_Pais'] ?? null;
    $id_Continente = $_POST['id_Continente'] ?? null;
    // Campos que virão do JS após a busca na API
    $capital_Pais = $_POST['capital_Pais'] ?? null;
    $moeda_Pais = $_POST['moeda_Pais'] ?? null;
    $bandeira_Pais_url = $_POST['bandeira_Pais'] ?? null;

    if (!$id_Pais || !$nome_Pais || !$populacao_Pais || !$idioma_Pais || !$id_Continente) {
        echo "Erro: Campos obrigatórios (ID, Nome, População, Idioma, Continente) não preenchidos.";
        exit;
    }

    $mysqli = conectar();
    if (!$mysqli) {
        echo "Erro: Falha na conexão com o banco de dados.";
        exit;
    }
    
    // Query de atualização (tabela 'pais', não 'paises')
    $sql = "UPDATE pais SET nome_Pais = ?, populacao_Pais = ?, idioma_Pais = ?, id_Continente = ?, capital_Pais = ?, moeda_Pais = ?, bandeira_Pais_url = ? WHERE id_Pais = ?";
    $stmt = $mysqli->prepare($sql);
    
    // Executa a query
    $stmt->bind_param("sdsisssi", 
        $nome_Pais, 
        $populacao_Pais, 
        $idioma_Pais, 
        $id_Continente, 
        $capital_Pais, 
        $moeda_Pais, 
        $bandeira_Pais_url, 
        $id_Pais
    );
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "País atualizado com sucesso!";
        } else {
            echo "Nenhuma alteração feita ou país não encontrado.";
        }
    } else {
        echo "Erro ao atualizar país: " . $mysqli->error;
    }
    
    $stmt->close();
    $mysqli->close();

} else {
    // Se não for POST e não for uma requisição GET esperada, é um método inválido.
    if (!isset($_GET['action'])) {
        echo "Método de requisição inválido.";
    }
}
?>