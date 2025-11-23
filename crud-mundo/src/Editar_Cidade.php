<?php
// Editar_Cidade.php
require_once 'Conexão.php';
require_once 'IntegracaoApiEDITAR.php'; // Inclui o novo arquivo de integração

// Função para conectar ao banco de dados (adaptada da análise do arquivo)
function conectar() {
    global $mysqli;
    // Verifica se a conexão está ativa
    if ($mysqli->connect_errno) {
        die("Falha na conexão com o banco: " . $mysqli->connect_error);
    }
    // Retorna o objeto mysqli para uso
    return $mysqli;
}

// Verifica se a requisição é para buscar dados de uma cidade para preencher o formulário
if (isset($_GET['action']) && $_GET['action'] === 'cidade' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id_Cidade = $_GET['id'];
    $mysqli = conectar();
    
    $sql = "SELECT * FROM cidade WHERE id_Cidade = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_Cidade);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $cidade = $result->fetch_assoc();
        echo json_encode($cidade);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Cidade não encontrada.']);
    }
    exit;
}

// Verifica se a requisição é para buscar a lista de países
if (isset($_GET['action']) && $_GET['action'] === 'paises') {
    header('Content-Type: application/json');
    $mysqli = conectar();
    
    $sql = "SELECT id_Pais, nome_Pais FROM pais ORDER BY nome_Pais";
    $result = $mysqli->query($sql);
    
    $paises = [];
    while ($row = $result->fetch_assoc()) {
        $paises[] = $row;
    }
    echo json_encode($paises);
    exit;
}

// Verifica se a requisição é para buscar dados de API (clima)
if (isset($_GET['action']) && $_GET['action'] === 'dados_cidade' && isset($_GET['cidade'])) {
    // Agora chama a função centralizada que busca e formata os dados da API
    handleApiRequestForEdit('cidade', $_GET['cidade']);
    exit;
}

// Lógica de edição (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_Cidade = $_POST['id_Cidade'] ?? null;
    $nome_Cidade = $_POST['nome_Cidade'] ?? null;
    $populacao_Cidade = $_POST['populacao_Cidade'] ?? null;
    $id_Pais = $_POST['id_Pais'] ?? null;
    // Campo que virá do JS após a busca na API (não persistido, mas verificado no front)
    // $clima_Cidade = $_POST['clima_Cidade'] ?? null;

    if (!$id_Cidade || !$nome_Cidade || !$populacao_Cidade || !$id_Pais) {
        echo "Erro: Todos os campos obrigatórios não preenchidos.";
        exit;
    }

    $mysqli = conectar();
    
    // Query de atualização (tabela 'cidade', não incluindo o campo clima_Cidade)
    $sql = "UPDATE cidade SET nome_Cidade = ?, populacao_Cidade = ?, id_Pais = ? WHERE id_Cidade = ?";
    $stmt = $mysqli->prepare($sql);
    
    // Executa a query
    $stmt->bind_param("siii", $nome_Cidade, $populacao_Cidade, $id_Pais, $id_Cidade);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Cidade atualizada com sucesso!";
        } else {
            echo "Nenhuma alteração feita ou cidade não encontrada.";
        }
    } else {
        echo "Erro ao atualizar cidade: " . $mysqli->error;
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