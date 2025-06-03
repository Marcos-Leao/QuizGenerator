<?php
/**
 * process.php - Processador AJAX para o Quiz Game
 * 
 * Este script recebe o tópico do quiz via POST, chama o script Python
 * para gerar as perguntas e retorna o resultado em formato JSON.
 */

// Definir cabeçalhos para resposta JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar se a requisição é POST e se o tópico foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['topico'])) {
    echo json_encode(['erro' => 'Método inválido ou tópico não fornecido']);
    exit;
}

// Obter e validar o tópico
$topico = trim($_POST['topico']);

if (empty($topico)) {
    echo json_encode(['erro' => 'O tópico não pode estar vazio']);
    exit;
}

// Escapar o tópico para segurança na linha de comando
$topico_escapado = escapeshellarg($topico);

// Definir o caminho completo para o script Python
$python_script = '/home/ubuntu/quiz_generator.py';

// Verificar se o script Python existe
if (!file_exists($python_script)) {
    echo json_encode(['erro' => 'Script Python não encontrado']);
    exit;
}

// Executar o script Python e capturar a saída
$comando = "python3 $python_script $topico_escapado 2>&1";
$resultado = shell_exec($comando);

// Verificar se o resultado é válido
if ($resultado === null) {
    echo json_encode(['erro' => 'Falha ao executar o script Python']);
    exit;
}

// Tentar decodificar o JSON retornado
$quiz_data = json_decode($resultado, true);

// Verificar se a decodificação foi bem-sucedida
if (json_last_error() !== JSON_ERROR_NONE) {
    // Registrar o erro e o resultado para depuração
    error_log("Erro ao decodificar JSON: " . json_last_error_msg());
    error_log("Resultado do script Python: " . $resultado);
    
    echo json_encode([
        'erro' => 'Erro ao processar o resultado do quiz',
        'detalhes' => json_last_error_msg(),
        // Comentado para não expor detalhes técnicos ao usuário final em produção
        // 'resultado_bruto' => $resultado
    ]);
    exit;
}

// Verificar se o resultado é um array (como esperado)
if (!is_array($quiz_data)) {
    echo json_encode(['erro' => 'O formato do resultado não é válido']);
    exit;
}

// Verificar se temos o número esperado de perguntas (20)
if (count($quiz_data) !== 20) {
    echo json_encode([
        'erro' => 'Número incorreto de perguntas geradas',
        'contagem' => count($quiz_data)
    ]);
    exit;
}

// Tudo certo, retornar o JSON das perguntas
echo $resultado;
