<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Game - Gerador de Perguntas</title>
    <style>
        :root {
            --primary-color: #4a6baf;
            --secondary-color: #6c8cd5;
            --accent-color: #ff6b6b;
            --background-color: #f5f7fa;
            --text-color: #333;
            --light-text: #666;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 2.5rem;
        }

        .subtitle {
            color: var(--light-text);
            font-size: 1.2rem;
        }

        .quiz-form {
            background-color: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        input[type="text"]:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(108, 140, 213, 0.25);
        }

        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid var(--primary-color);
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .quiz-container {
            display: none;
            background-color: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .question {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .question h2 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .answers {
            margin-bottom: 15px;
        }

        .answer {
            padding: 10px 15px;
            background-color: #f9f9f9;
            border-radius: var(--border-radius);
            margin-bottom: 8px;
            transition: var(--transition);
        }

        .answer.correct {
            background-color: #d4edda;
            color: #155724;
            font-weight: 600;
        }

        .explanation {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--accent-color);
            font-style: italic;
            color: var(--light-text);
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: none;
        }

        .toggle-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .toggle-btn {
            background-color: #eee;
            color: var(--light-text);
            border: none;
            padding: 8px 15px;
            margin: 0 5px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .toggle-btn.active {
            background-color: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .quiz-form, .quiz-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Quiz Game</h1>
            <p class="subtitle">Gere perguntas sobre qualquer assunto com IA</p>
        </header>

        <div class="error-message" id="errorMessage"></div>

        <div class="quiz-form">
            <form id="quizForm" method="post">
                <div class="form-group">
                    <label for="topico">Sobre qual assunto você quer jogar?</label>
                    <input type="text" id="topico" name="topico" placeholder="Ex: História do Brasil, Astronomia, Inteligência Artificial..." required>
                </div>
                <button type="submit" id="submitBtn">Gerar Perguntas</button>
            </form>
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Gerando perguntas... Isso pode levar alguns segundos.</p>
        </div>

        <div class="toggle-container" id="toggleContainer" style="display: none;">
            <button class="toggle-btn active" id="phpBtn">Versão PHP</button>
            <button class="toggle-btn" id="jsBtn">Versão JavaScript</button>
        </div>

        <div class="quiz-container" id="phpQuizContainer">
            <!-- Conteúdo gerado pelo PHP será inserido aqui -->
        </div>

        <div class="quiz-container" id="jsQuizContainer" style="display: none;">
            <!-- Conteúdo gerado pelo JavaScript será inserido aqui -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quizForm = document.getElementById('quizForm');
            const loading = document.getElementById('loading');
            const errorMessage = document.getElementById('errorMessage');
            const toggleContainer = document.getElementById('toggleContainer');
            const phpBtn = document.getElementById('phpBtn');
            const jsBtn = document.getElementById('jsBtn');
            const phpQuizContainer = document.getElementById('phpQuizContainer');
            const jsQuizContainer = document.getElementById('jsQuizContainer');
            const submitBtn = document.getElementById('submitBtn');

            // Função para alternar entre as versões PHP e JS
            phpBtn.addEventListener('click', function() {
                phpBtn.classList.add('active');
                jsBtn.classList.remove('active');
                phpQuizContainer.style.display = 'block';
                jsQuizContainer.style.display = 'none';
            });

            jsBtn.addEventListener('click', function() {
                jsBtn.classList.add('active');
                phpBtn.classList.remove('active');
                jsQuizContainer.style.display = 'block';
                phpQuizContainer.style.display = 'none';
            });

            // Função para renderizar o quiz com JavaScript
            function renderQuizJS(quizData) {
                jsQuizContainer.innerHTML = '';
                
                quizData.forEach((question, index) => {
                    const questionElement = document.createElement('div');
                    questionElement.className = 'question';
                    
                    const questionTitle = document.createElement('h2');
                    questionTitle.textContent = `${index + 1}. ${question.pergunta}`;
                    
                    const answersContainer = document.createElement('div');
                    answersContainer.className = 'answers';
                    
                    // Resposta correta
                    const correctAnswer = document.createElement('div');
                    correctAnswer.className = 'answer correct';
                    correctAnswer.textContent = question.resposta_certa;
                    answersContainer.appendChild(correctAnswer);
                    
                    // Respostas erradas
                    question.respostas_erradas.forEach(wrongAnswer => {
                        const wrongAnswerElement = document.createElement('div');
                        wrongAnswerElement.className = 'answer';
                        wrongAnswerElement.textContent = wrongAnswer;
                        answersContainer.appendChild(wrongAnswerElement);
                    });
                    
                    // Explicação
                    const explanationElement = document.createElement('div');
                    explanationElement.className = 'explanation';
                    explanationElement.textContent = question.explicacao;
                    
                    // Montar a questão completa
                    questionElement.appendChild(questionTitle);
                    questionElement.appendChild(answersContainer);
                    questionElement.appendChild(explanationElement);
                    
                    // Adicionar ao container
                    jsQuizContainer.appendChild(questionElement);
                });
                
                jsQuizContainer.style.display = 'block';
                toggleContainer.style.display = 'flex';
            }

            // Manipulador de envio do formulário
            quizForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const topico = document.getElementById('topico').value.trim();
                
                if (!topico) {
                    errorMessage.textContent = 'Por favor, informe um assunto para o quiz.';
                    errorMessage.style.display = 'block';
                    return;
                }
                
                // Esconder mensagens de erro anteriores
                errorMessage.style.display = 'none';
                
                // Mostrar loading e desabilitar botão
                loading.style.display = 'block';
                submitBtn.disabled = true;
                
                // Enviar formulário via AJAX
                const formData = new FormData();
                formData.append('topico', topico);
                formData.append('ajax', '1'); // Indicador para o PHP que é uma requisição AJAX
                
                fetch('process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na requisição: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.erro) {
                        throw new Error(data.erro);
                    }
                    
                    // Renderizar o quiz com JavaScript
                    renderQuizJS(data);
                    
                    // Recarregar a página para mostrar a versão PHP (sem AJAX)
                    // Isso é feito apenas para demonstrar as duas versões
                    const reloadForm = document.createElement('form');
                    reloadForm.method = 'POST';
                    reloadForm.action = window.location.href;
                    
                    const topicoInput = document.createElement('input');
                    topicoInput.type = 'hidden';
                    topicoInput.name = 'topico';
                    topicoInput.value = topico;
                    
                    reloadForm.appendChild(topicoInput);
                    document.body.appendChild(reloadForm);
                    reloadForm.submit();
                })
                .catch(error => {
                    console.error('Erro:', error);
                    errorMessage.textContent = 'Erro ao gerar o quiz: ' + error.message;
                    errorMessage.style.display = 'block';
                    loading.style.display = 'none';
                    submitBtn.disabled = false;
                });
            });
        });
    </script>

    <?php
    // Processamento do formulário (versão não-AJAX)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topico']) && !isset($_POST['ajax'])) {
        $topico = trim($_POST['topico']);
        
        if (!empty($topico)) {
            // Escapar o tópico para segurança na linha de comando
            $topico_escapado = escapeshellarg($topico);
            
            // Executar o script Python
            $comando = "python3 /home/ubuntu/quiz_generator.py $topico_escapado";
            $resultado = shell_exec($comando);
            
            // Verificar se o resultado é válido
            $quiz_data = json_decode($resultado, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($quiz_data)) {
                // Exibir o quiz (versão PHP)
                echo '<script>
                    document.getElementById("loading").style.display = "none";
                    document.getElementById("submitBtn").disabled = false;
                    document.getElementById("phpQuizContainer").style.display = "block";
                    document.getElementById("toggleContainer").style.display = "flex";
                </script>';
                
                echo '<div id="phpQuizContent">';
                foreach ($quiz_data as $index => $pergunta) {
                    echo '<div class="question">';
                    echo '<h2>' . ($index + 1) . '. ' . htmlspecialchars($pergunta['pergunta']) . '</h2>';
                    echo '<div class="answers">';
                    echo '<div class="answer correct">' . htmlspecialchars($pergunta['resposta_certa']) . '</div>';
                    
                    foreach ($pergunta['respostas_erradas'] as $resposta_errada) {
                        echo '<div class="answer">' . htmlspecialchars($resposta_errada) . '</div>';
                    }
                    
                    echo '</div>';
                    echo '<div class="explanation">' . htmlspecialchars($pergunta['explicacao']) . '</div>';
                    echo '</div>';
                }
                echo '</div>';
                
                // Armazenar os dados do quiz para uso pelo JavaScript
                echo '<script>
                    const quizData = ' . $resultado . ';
                    if (typeof renderQuizJS === "function") {
                        renderQuizJS(quizData);
                    }
                </script>';
            } else {
                // Exibir mensagem de erro
                echo '<script>
                    document.getElementById("loading").style.display = "none";
                    document.getElementById("submitBtn").disabled = false;
                    document.getElementById("errorMessage").textContent = "Erro ao processar o resultado do quiz. Verifique os logs do servidor.";
                    document.getElementById("errorMessage").style.display = "block";
                </script>';
            }
        }
    }
    ?>
</body>
</html>
