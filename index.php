<?php
// index.php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizIA - Quiz Inteligente</title>
    <style>
        :root {
            --primary-color: #ff99bb;
            --secondary-color: #00cc99;
            --primary-dark: #ff6699;
            --secondary-dark: #00aa77;
            --background-light: #f9f9f9;
            --text-dark: #333;
            --text-light: #fff;
            --correct-color: #cceedd;
            --wrong-color: #f8d7da;
            --border-radius: 12px;
            --box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            /*background: linear-gradient(135deg, #f9f9f9, #e6f7ff);*/
            min-height: 70vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            /*align-items: center;*/
            color: var(--text-dark);
        }

        .container {
            width: 100%;
            max-width: 900px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-dark);
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .logo-placeholder {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            background-image: url("QuizIA-LOGO-100x100.png");
            border-radius: 12%;
            display: flex;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 5px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }

        .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .screen {
            padding: 18px;
            display: none;
        }

        .active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Formulário Inicial */
        #initial-screen {
            text-align: center;
        }

        #initial-screen h2 {
            margin-bottom: 25px;
            color: var(--secondary-dark);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }

        input[type="text"] {
            width: 100%;
            padding: 14px;
            border: 2px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        input[type="text"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 153, 187, 0.3);
        }

        .btn {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            width: 220px;
            padding: 14px 28px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 8px rgba(255, 153, 187, 0.3);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(255, 153, 187, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
            box-shadow: 0 4px 8px rgba(0, 204, 153, 0.3);
        }

        .btn-secondary:hover {
            box-shadow: 0 6px 12px rgba(0, 204, 153, 0.4);
        }

        /* Spinner */
        .spinner-container {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 153, 187, 0.3);
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Tela de Histórico */
        .history-container {
            max-height: 400px;
            overflow-y: auto;
            margin: 25px 0;
            border: 1px solid #eee;
            border-radius: var(--border-radius);
        }

        .history-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }

        .history-item:hover {
            background-color: #f9f9f9;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-info {
            flex: 1;
        }

        .history-date {
            font-weight: bold;
            color: var(--secondary-dark);
        }

        .history-subject {
            font-weight: bold;
            color: var(--primary-dark);
        }

        .history-stats {
            display: flex;
            gap: 15px;
        }

        .stat {
            text-align: center;
            min-width: 80px;
        }

        .stat-value {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #777;
        }

        /* Tela do Jogo */
        .game-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .score-container {
            display: flex;
            gap: 20px;
        }

        .score-box {
            background: white;
            padding: 10px 15px;
            border-radius: var(--border-radius);
            min-width: 100px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .score-label {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 5px;
        }

        .score-value {
            font-weight: bold;
            font-size: 1.4rem;
        }

        .timer-container {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 100px;
        }

        .timer-value {
            font-weight: bold;
            font-size: 1.8rem;
        }

        .question-container {
            /*margin-bottom: 30px;*/
            padding: 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .question-text {
            font-size: 1.4rem;
            margin-bottom: 25px;
            line-height: 1.5;
            color: var(--text-dark);
        }

        .answers-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 18px;
        }

        @media (max-width: 768px) {
            .answers-container {
                grid-template-columns: 1fr;
            }
        }

        .answer-option {
            padding: 18px;
            background: #f5f5f5;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.1rem;
            text-align: center;
        }

        .answer-option:hover {
            background: #e9ecef;
            transform: translateY(-3px);
        }

        .answer-option.selected {
            border-color: var(--primary-color);
            background: rgba(255, 153, 187, 0.1);
        }

        .answer-option.correct {
            background: var(--correct-color);
            border-color: #28a745;
        }

        .answer-option.wrong {
            background: var(--wrong-color);
            border-color: #dc3545;
        }

        .explanation-container {
            padding: 20px;
            background: #e9f7ef;
            border-radius: var(--border-radius);
            margin-top: 20px;
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .explanation-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--secondary-dark);
        }

        .explanation-text {
            line-height: 1.6;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
        }

        .progress-container {
            margin: 6px 0;
        }

        .progress-bar {
            height: 12px;
            background: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            width: 0%;
            transition: width 0.5s ease;
        }

        .progress-text {
            text-align: center;
            margin-top: 6px;
            font-weight: 500;
            color: #777;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #777;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
        }
        /* Para header flex */
        .FlexContainer {
           display: flex;
           flex-wrap: wrap;
           justify-content: space-around;
           align-items: center;
           align-content: center;
           overflow: auto;
           flex-direction: row;
        }

        .FlexContainer div {
            margin: 1px;
        }
    </style>

</head>
<body>
    <div class="container">
        <header>
        <section class='FlexContainer'>
            <div class="logo-placeholder"></div>
            <div><h1>QuizIA</h1></div>
        </section>
            <div class="subtitle">Aprenda brincando! Brinque aprendendo!</div>
        </header>

        <!-- Tela Inicial -->
        <div id="initial-screen" class="screen active">
            <h2>Crie seu quiz personalizado</h2>
            <form id="quiz-form">
                <div class="form-group">
                    <label for="subject">Sobre qual assunto você quer o quiz?</label>
                    <input type="text" id="subject" name="subject" placeholder="Ex: França, Física, História do Brasil..." required>
                </div>
                <button type="submit" class="btn">Gerar Quiz</button>
            </form>

            <div class="spinner-container" id="loading-spinner">
                <div class="spinner"></div>
                <span style="color:#00aa77; font-size:26px; font-weight:bold;">
                <p>Gerando seu quiz com inteligência artificial...</p>
                <p>Isso pode levar alguns segundos</p>
                </span>
            </div>
        </div>

        <!-- Tela de Histórico -->
        <div id="history-screen" class="screen">
            <h2>Histórico de Quizzes</h2>
            <div class="history-container" id="history-list">
                <!-- Histórico será preenchido por JavaScript -->
            </div>
            <div class="controls">
                <button id="start-quiz-btn" class="btn">Iniciar Quiz</button>
                <button id="new-quiz-btn" class="btn btn-secondary">Criar Novo Quiz</button>
            </div>
        </div>

        <!-- Tela do Jogo -->
        <div id="game-screen" class="screen">
            <div class="game-header">
                <div class="score-container">
                    <div class="score-box">
                        <div class="score-label">Pontuação</div>
                        <div class="score-value" id="score">0</div>
                    </div>
                    <div class="score-box">
                        <div class="score-label">Bônus</div>
                        <div class="score-value" id="bonus">0</div>
                    </div>
                    <div class="score-box">
                        <div class="score-label">Certas</div>
                        <div class="score-value" id="correct-count">0</div>
                    </div>
                    <div class="score-box">
                        <div class="score-label">Erradas</div>
                        <div class="score-value" id="wrong-count">0</div>
                    </div>
                </div>
                <div class="timer-container">
                    <div class="score-label">Tempo</div>
                    <div class="timer-value" id="timer">60</div>
                </div>
            </div>

            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <div class="progress-text" id="progress-text">Pergunta 1 de 20</div>
            </div>

            <div class="question-container">
                <div class="question-text" id="question-text"></div>

                <div class="answers-container" id="answers-container"></div>

                <div class="explanation-container" id="explanation-container">
                    <div class="explanation-title">Explicação:</div>
                    <div class="explanation-text" id="explanation-text"></div>
                </div>

                <div class="controls">
                    <button id="next-question-btn" class="btn" style="display: none;">Próxima Pergunta</button>
                </div>
            </div>
        </div>

        <footer>
            QuizIA &copy; <?php echo date('Y'); ?> - Desenvolvido por Marcos Leão
        </footer>
    </div>

    <script>
        // Dados do quiz (serão preenchidos pela resposta do servidor)
        let quizData = null;

        // Variáveis de estado do jogo
        let currentQuestionIndex = 0;
        let score = 0;
        let bonus = 0.0;
        let correctCount = 0;
        let wrongCount = 0;
        let timeLeft = 60;
        let timer;
        let gameActive = false;
        let selectedAnswer = null;

        // Elementos DOM
        const initialScreen = document.getElementById('initial-screen');
        const historyScreen = document.getElementById('history-screen');
        const gameScreen = document.getElementById('game-screen');
        const loadingSpinner = document.getElementById('loading-spinner');
        const quizForm = document.getElementById('quiz-form');
        const historyList = document.getElementById('history-list');
        const startQuizBtn = document.getElementById('start-quiz-btn');
        const newQuizBtn = document.getElementById('new-quiz-btn');
        const scoreElement = document.getElementById('score');
        const bonusElement = document.getElementById('bonus');
        const correctCountElement = document.getElementById('correct-count');
        const wrongCountElement = document.getElementById('wrong-count');
        const timerElement = document.getElementById('timer');
        const questionText = document.getElementById('question-text');
        const answersContainer = document.getElementById('answers-container');
        const explanationContainer = document.getElementById('explanation-container');
        const explanationText = document.getElementById('explanation-text');
        const nextQuestionBtn = document.getElementById('next-question-btn');
        const progressFill = document.getElementById('progress-fill');
        const progressText = document.getElementById('progress-text');

        // Função para mostrar uma tela específica
        function showScreen(screen) {
            initialScreen.classList.remove('active');
            historyScreen.classList.remove('active');
            gameScreen.classList.remove('active');
            loadingSpinner.style.display = 'none';

            screen.classList.add('active');
        }

        // Função para carregar e exibir o histórico
        function loadHistory() {
            const historyItem = localStorage.getItem('quizHistory');
            let history = [];

            if (historyItem) {
                try {
                    history = JSON.parse(historyItem);
                } catch (e) {
                    console.error('Erro ao parsear o histórico:', e);
                }
            }

            historyList.innerHTML = '';

            if (history.length === 0) {
                historyList.innerHTML = '<p class="no-history">Nenhum quiz realizado ainda</p>';
                return;
            }

            // Limita ao máximo de 10 itens
            const recentHistory = history.slice(0, 10);

            recentHistory.forEach(quiz => {
                const historyItem = document.createElement('div');
                historyItem.className = 'history-item';

                const date = new Date(quiz.date);
                const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();

                historyItem.innerHTML = `
                    <div class="history-info">
                        <div class="history-date">${formattedDate}</div>
                        <div class="history-subject">${quiz.subject}</div>
                    </div>
                    <div class="history-stats">
                        <div class="stat">
                            <div class="stat-value">${quiz.score}</div>
                            <div class="stat-label">Pontos</div>
                        </div>
                        <div class="stat">
                            <div class="stat-value">${quiz.bonus}</div>
                            <div class="stat-label">Bônus</div>
                        </div>
                        <div class="stat">
                            <div class="stat-value">${quiz.correct}</div>
                            <div class="stat-label">Certas</div>
                        </div>
                        <div class="stat">
                            <div class="stat-value">${quiz.wrong}</div>
                            <div class="stat-label">Erradas</div>
                        </div>
                    </div>
                `;

                historyList.appendChild(historyItem);
            });

            // Se quizData é nulo, não exibe botão de iniciar quiz
            if (quizData == null) {
                startQuizBtn.style.display = 'none';
            } else {
                startQuizBtn.style.display = 'block';
            }

        }

        // Função para salvar os resultados no histórico
        function saveToHistory() {
            const historyItem = localStorage.getItem('quizHistory');
            let history = [];

            if (historyItem) {
                try {
                    history = JSON.parse(historyItem);
                } catch (e) {
                    console.error('Erro ao parsear o histórico:', e);
                }
            }

            const quizResult = {
                date: new Date().toISOString(),
                subject: quizData.subject,
                score: score,
                bonus: Math.ceil(bonus),
                correct: correctCount,
                wrong: wrongCount
            };

            // Adiciona no início do array para manter os mais recentes primeiro
            history.unshift(quizResult);

            // Mantém apenas os 10 mais recentes
            if (history.length > 10) {
                history.pop();
            }

            localStorage.setItem('quizHistory', JSON.stringify(history));
        }

        // Função para iniciar o temporizador
        function startTimer() {
            clearInterval(timer);
            timeLeft = 60;
            timerElement.textContent = timeLeft;

            timer = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    handleAnswer(null); // Tempo esgotado
                }
            }, 1000);
        }

        // Função para embaralhar as respostas
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        // Função para mostrar uma pergunta
        function showQuestion() {
            if (currentQuestionIndex >= quizData.questions.length) {
                endGame();
                return;
            }

            const question = quizData.questions[currentQuestionIndex];
            questionText.textContent = question.question;

            // Cria as opções de resposta
            answersContainer.innerHTML = '';

            // Cria um array com todas as respostas e embaralha
            const answers = [
                { text: question.correct_answer, correct: true },
                { text: question.wrong_answer1, correct: false },
                { text: question.wrong_answer2, correct: false },
                { text: question.wrong_answer3, correct: false }
            ];

            const shuffledAnswers = shuffleArray(answers);

            // Adiciona as respostas ao DOM
            shuffledAnswers.forEach(answer => {
                const answerElement = document.createElement('div');
                answerElement.className = 'answer-option';
                answerElement.textContent = answer.text;
                answerElement.dataset.correct = answer.correct;

                answerElement.addEventListener('click', () => {
                    if (!gameActive) return;
                    handleAnswer(answerElement);
                });

                answersContainer.appendChild(answerElement);
            });

            // Atualiza o progresso
            const progress = ((currentQuestionIndex + 1) / quizData.questions.length) * 100;
            progressFill.style.width = `${progress}%`;
            progressText.textContent = `Pergunta ${currentQuestionIndex + 1} de ${quizData.questions.length}`;

            // Esconde a explicação e o botão de próxima pergunta
            explanationContainer.style.display = 'none';
            nextQuestionBtn.style.display = 'none';

            // Reinicia o jogo para a próxima pergunta
            gameActive = true;
            selectedAnswer = null;

            // Inicia o temporizador
            startTimer();
        }

        // Função para lidar com a resposta
        function handleAnswer(answerElement) {
            if (!gameActive) return;

            gameActive = false;
            clearInterval(timer);

            // Verifica se o jogador respondeu
            if (answerElement) {
                selectedAnswer = answerElement;
                answerElement.classList.add('selected');

                // Verifica se a resposta está correta
                const isCorrect = answerElement.dataset.correct === 'true';

                if (isCorrect) {
                    score += 5;
                    correctCount++;
                    // Calcula o bônus baseado no tempo restante
                    const questionBonus = timeLeft / 60;
                    bonus += questionBonus;
                } else {
                    wrongCount++;
                }

                // Atualiza o placar
                scoreElement.textContent = score;
                bonusElement.textContent = Math.ceil(bonus);
                correctCountElement.textContent = correctCount;
                wrongCountElement.textContent = wrongCount;

                // Mostra as respostas corretas e incorretas
                const allAnswers = answersContainer.querySelectorAll('.answer-option');
                allAnswers.forEach(ans => {
                    if (ans.dataset.correct === 'true') {
                        ans.classList.add('correct');
                    } else {
                        ans.classList.add('wrong');
                    }
                });
            } else {
                // Tempo esgotado
                wrongCount++;
                wrongCountElement.textContent = wrongCount;

                const allAnswers = answersContainer.querySelectorAll('.answer-option');
                allAnswers.forEach(ans => {
                    if (ans.dataset.correct === 'true') {
                        ans.classList.add('correct');
                    } else {
                        ans.classList.add('wrong');
                    }
                });
            }

            // Mostra a explicação
            explanationText.textContent = quizData.questions[currentQuestionIndex].explanation;
            explanationContainer.style.display = 'block';

            // Mostra o botão de próxima pergunta
            nextQuestionBtn.style.display = 'block';
        }

        // Função para avançar para a próxima pergunta
        function nextQuestion() {
            currentQuestionIndex++;
            showQuestion();
        }

        // Função para finalizar o jogo
        function endGame() {
            // Salva os resultados no histórico
            saveToHistory();

            // Atualiza a tela de histórico
            loadHistory();

            // Mostra a tela de histórico
            showScreen(historyScreen);
        }

        // Função para iniciar o jogo
        function startGame() {
            // Reinicia as variáveis do jogo
            currentQuestionIndex = 0;
            score = 0;
            bonus = 0;
            correctCount = 0;
            wrongCount = 0;

            // Atualiza os elementos do placar
            scoreElement.textContent = score;
            bonusElement.textContent = Math.ceil(bonus);
            correctCountElement.textContent = correctCount;
            wrongCountElement.textContent = wrongCount;

            // Mostra a tela do jogo
            showScreen(gameScreen);

            // Mostra a primeira pergunta
            showQuestion();
        }

        // Event Listeners
        quizForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const subject = document.getElementById('subject').value;

            // Validação do assunto
            if (subject.trim().length < 3) {
                alert('Por favor, digite um assunto válido (mínimo 3 caracteres)');
                return;
            }

            // Mostra o spinner
            loadingSpinner.style.display = 'flex';

            try {
                // Envia os dados para o process_quiz.php
                const response = await fetch('process_quiz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `subject=${encodeURIComponent(subject)}`
                });

                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor: ' + response.status);
                }

                // Recebe os dados do quiz
                quizData = await response.json();

                // Valida a estrutura dos dados
                if (!quizData || !quizData.questions || quizData.questions.length === 0) {
                    throw new Error('Dados do quiz inválidos');
                }

                // Esconde o spinner
                loadingSpinner.style.display = 'none';

                // Mostra a tela de histórico
                showScreen(historyScreen);
                loadHistory();

            } catch (error) {
                console.error('Erro:', error);
                loadingSpinner.style.display = 'none';
                alert('Ocorreu um erro ao gerar o quiz. Por favor, tente novamente.');
            }
        });

        startQuizBtn.addEventListener('click', startGame);
        newQuizBtn.addEventListener('click', () => showScreen(initialScreen));
        nextQuestionBtn.addEventListener('click', nextQuestion);

        // Carrega o histórico quando a página é carregada
        window.addEventListener('DOMContentLoaded', () => {
            loadHistory();

            // Se já tivermos um quiz no histórico, mostra a tela de histórico
            if (localStorage.getItem('quizHistory')) {
                showScreen(historyScreen);
            }
        });
    </script>
</body>
</html>