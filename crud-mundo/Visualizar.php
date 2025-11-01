<?php
// Visualizar.php
// Exibe cidades por padrão e permite alternar para exibir países via botão acima da tabela.

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'Crud_Mundo';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo "Erro de conexão com o banco de dados: " . htmlspecialchars($mysqli->connect_error);
    exit;
}
$mysqli->set_charset('utf8mb4');

$table = isset($_GET['t']) && $_GET['t'] === 'paises' ? 'paises' : 'cidades';

function h($s) { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Visualizar - <?php echo $table === 'paises' ? 'Países' : 'Cidades'; ?></title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; max-width: 900px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        .actions { margin-bottom: 12px; }
        .btn { display:inline-block; padding:8px 12px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px; }
        .btn.secondary { background:#6c757d; }
        .empty { color:#666; }
    </style>
</head>
<body>

<div class="actions">
    <?php if ($table === 'cidades'): ?>
        <a class="btn" href="?t=paises">Mostrar Países</a>
    <?php else: ?>
        <a class="btn" href="?t=cidades">Mostrar Cidades</a>
    <?php endif; ?>
</div>

<?php
if ($table === 'cidades'):
    // Buscar cidades com nome do país
    $sql = "SELECT c.id_Cidade, c.nome_Cidade, c.populacao_Cidade, p.nome_Pais
            FROM Cidade c
            LEFT JOIN Pais p ON c.id_Pais = p.id_Pais
            ORDER BY c.nome_Cidade ASC";
    if ($res = $mysqli->query($sql)):
        if ($res->num_rows === 0) {
            echo '<p class="empty">Nenhuma cidade cadastrada.</p>';
        } else {
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Nome da Cidade</th><th>População</th><th>País</th></tr></thead><tbody>';
            while ($row = $res->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . h($row['id_Cidade']) . '</td>';
                echo '<td>' . h($row['nome_Cidade']) . '</td>';
                echo '<td>' . number_format((int)$row['populacao_Cidade'], 0, ',', '.') . '</td>';
                echo '<td>' . h($row['nome_Pais'] ?? '—') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        $res->free();
    else:
        echo '<p class="empty">Erro ao consultar cidades: ' . h($mysqli->error) . '</p>';
    endif;

else:
    // Buscar países com continente
    $sql = "SELECT p.id_Pais, p.nome_Pais, p.populacao_Pais, p.idioma_Pais, c.nome_Continente
            FROM Pais p
            LEFT JOIN Continente c ON p.id_Continente = c.id_Continente
            ORDER BY p.nome_Pais ASC";
    if ($res = $mysqli->query($sql)):
        if ($res->num_rows === 0) {
            echo '<p class="empty">Nenhum país cadastrado.</p>';
        } else {
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Nome do País</th><th>População</th><th>Idioma</th><th>Continente</th></tr></thead><tbody>';
            while ($row = $res->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . h($row['id_Pais']) . '</td>';
                echo '<td>' . h($row['nome_Pais']) . '</td>';
                echo '<td>' . number_format((float)$row['populacao_Pais'], 0, ',', '.') . '</td>';
                echo '<td>' . h($row['idioma_Pais']) . '</td>';
                echo '<td>' . h($row['nome_Continente'] ?? '—') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        $res->free();
    else:
        echo '<p class="empty">Erro ao consultar países: ' . h($mysqli->error) . '</p>';
    endif;

endif;

$mysqli->close();
?>

</body>
</html>