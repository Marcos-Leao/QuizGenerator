<?php
/**
 * Arquivo separado para processamento do formulário
 * Este arquivo é incluído no index.php e contém apenas a lógica de processamento
 * Marcos Leão - 02/06/2025
 */

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se o assunto foi enviado
    if (isset($_POST['subject']) && !empty($_POST['subject'])) {
        $subject = escapeshellarg($_POST['subject']);
        
        // Chamar o script Python e passar o assunto como argumento
        $command = "python3 " . __DIR__ . "/generate_quiz.py " . $subject;
        $output = shell_exec($command);
        
        // Verificar se o output é válido
        if ($output === null) {
            echo '<script>
                document.getElementById("error-message").textContent = "Erro ao executar o script Python. Verifique se o script existe e tem permissões adequadas.";
                document.getElementById("error-message").style.display = "block";
                document.getElementById("loading").style.display = "none";
            </script>';
        } else {
            // Decodificar o JSON retornado pelo Python
            $quiz_data = json_decode($output, true);
            
            // Verificar se o JSON é válido
            if ($quiz_data === null && json_last_error() !== JSON_ERROR_NONE) {
                echo '<script>
                    document.getElementById("error-message").textContent = "Erro ao processar os dados do quiz. Resposta inválida.";
                    document.getElementById("error-message").style.display = "block";
                    document.getElementById("loading").style.display = "none";
                </script>';
            } else {
                // Verificar se há erro no retorno do Python
                if (isset($quiz_data['error'])) {
                    echo '<script>
                        document.getElementById("error-message").textContent = "Erro: ' . addslashes($quiz_data['error']) . '";
                        document.getElementById("error-message").style.display = "block";
                        document.getElementById("loading").style.display = "none";
                    </script>';
                } else {
                    // Armazenar os dados do quiz em uma variável JavaScript para uso posterior
                    //echo '<script>var quizData = ' . $output . ';</script>';
                    
                    //echo '<script>
                    //    document.getElementById("loading").style.display = "none";
                    //    document.getElementById("results").style.display = "block";
                    //    document.getElementById("toggle-view").style.display = "flex";
                    //</script>';
                                        
                    //echo '<div class="info">----DEBUG-------------------------</div>';
                    //echo "<pre>" . $output . "</pre>";
                    //echo '<div class="info">-------------------------------------</div>';

                    // Retornar o JSON de saída
                    echo $output;
                }
            }
        }
    }
}
?>
