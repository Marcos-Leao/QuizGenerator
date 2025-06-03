#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import sys
import os
import json
import google.generativeai as genai

def gerar_perguntas_quiz(topico):
    """Gera perguntas de quiz usando a API Gemini.

    Args:
        topico: O assunto para as perguntas do quiz.

    Returns:
        Uma string JSON contendo a lista de perguntas e respostas,
        ou None em caso de erro.
    """
    try:
        # A chave da API é lida automaticamente da variável de ambiente GOOGLE_API_KEY
        genai.configure(api_key=os.environ['GOOGLE_API_KEY'])

        # Configurações de geração
        generation_config = {
            "temperature": 0.7,
            "top_p": 1,
            "top_k": 1,
            "max_output_tokens": 8192, # Aumentado para garantir espaço para 20 perguntas complexas
        }

        # Configurações de segurança
        safety_settings = [
            {"category": "HARM_CATEGORY_HARASSMENT", "threshold": "BLOCK_MEDIUM_AND_ABOVE"},
            {"category": "HARM_CATEGORY_HATE_SPEECH", "threshold": "BLOCK_MEDIUM_AND_ABOVE"},
            {"category": "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold": "BLOCK_MEDIUM_AND_ABOVE"},
            {"category": "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold": "BLOCK_MEDIUM_AND_ABOVE"},
        ]

        model = genai.GenerativeModel(model_name="gemini-1.0-pro",
                                      generation_config=generation_config,
                                      safety_settings=safety_settings)

        prompt = f"""
        Crie um quiz com exatamente 20 perguntas sobre o tópico '{topico}'.
        Para cada pergunta, forneça:
        1. O texto da pergunta ('pergunta').
        2. A resposta correta ('resposta_certa').
        3. Uma lista com exatamente três respostas incorretas, mas plausíveis ('respostas_erradas').
        4. Uma breve explicação do porquê a resposta correta está certa ('explicacao').

        Formate a saída EXCLUSIVAMENTE como um array JSON válido, onde cada elemento do array é um objeto representando uma pergunta.
        Não inclua nenhuma introdução, explicação textual fora do JSON, ou marcadores de código como ```json ou ```.
        O JSON deve seguir estritamente esta estrutura:

        [
          {{
            "pergunta": "Texto da pergunta 1",
            "resposta_certa": "Resposta correta 1",
            "respostas_erradas": [
              "Resposta incorreta 1.1",
              "Resposta incorreta 1.2",
              "Resposta incorreta 1.3"
            ],
            "explicacao": "Explicação para a pergunta 1"
          }},
          {{
            "pergunta": "Texto da pergunta 2",
            "resposta_certa": "Resposta correta 2",
            "respostas_erradas": [
              "Resposta incorreta 2.1",
              "Resposta incorreta 2.2",
              "Resposta incorreta 2.3"
            ],
            "explicacao": "Explicação para a pergunta 2"
          }},
          ...
        ]

        Certifique-se de que o JSON esteja bem formado e contenha exatamente 20 perguntas.
        """

        response = model.generate_content(prompt)

        # Tenta extrair o JSON da resposta
        try:
            # A API pode retornar texto com markdown, tentamos limpar
            json_text = response.text.strip().lstrip('```json').rstrip('```').strip()
            # Valida se o texto parece ser JSON antes de tentar decodificar
            if not (json_text.startswith('[') and json_text.endswith(']')):
                 # Se não parece JSON, tenta encontrar JSON dentro do texto (caso haja texto extra)
                 start_index = json_text.find('[')
                 end_index = json_text.rfind(']')
                 if start_index != -1 and end_index != -1 and start_index < end_index:
                     json_text = json_text[start_index:end_index+1]
                 else:
                    raise ValueError("A resposta da API não contém um array JSON válido.")

            # Decodifica para garantir que é JSON válido
            quiz_data = json.loads(json_text)

            # Verifica se temos 20 perguntas
            if isinstance(quiz_data, list) and len(quiz_data) == 20:
                 # Recodifica para garantir formatação consistente e retorna
                 return json.dumps(quiz_data, indent=2, ensure_ascii=False)
            else:
                # Tenta gerar novamente se a estrutura não for a esperada
                print("Erro: A estrutura do JSON recebido não corresponde ao esperado (lista de 20 perguntas). Tentando novamente...", file=sys.stderr)
                # Poderia adicionar lógica para tentar novamente aqui, mas por simplicidade, retornamos erro.
                # Para uma segunda tentativa, poderíamos chamar a função recursivamente ou em loop.
                # Por ora, apenas sinalizamos o erro.
                return None # Ou poderia tentar gerar novamente

        except (json.JSONDecodeError, ValueError) as e:
            print(f"Erro ao processar a resposta da API como JSON: {e}", file=sys.stderr)
            print(f"Resposta recebida:\n{response.text}", file=sys.stderr)
            return None
        except AttributeError:
             # Handle cases where response.text might not exist as expected
             print(f"Erro: Resposta da API inesperada ou vazia.", file=sys.stderr)
             # Log the full response if possible and available
             try:
                 print(f"Resposta completa: {response}", file=sys.stderr)
             except Exception:
                 pass # Avoid further errors during error handling
             return None

    except Exception as e:
        print(f"Erro inesperado ao chamar a API Gemini: {e}", file=sys.stderr)
        # Adicionar log mais detalhado se necessário
        import traceback
        traceback.print_exc(file=sys.stderr)
        return None

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Erro: Forneça o tópico como argumento da linha de comando.", file=sys.stderr)
        print("Uso: python3 quiz_generator.py \"Seu Tópico Aqui\"", file=sys.stderr)
        sys.exit(1)

    topico_input = sys.argv[1]
    resultado_json = gerar_perguntas_quiz(topico_input)

    if resultado_json:
        print(resultado_json) # Imprime o JSON para o stdout (capturado pelo PHP)
    else:
        # Imprime uma mensagem de erro JSON para o stdout em caso de falha
        # Isso permite que o PHP saiba que algo deu errado.
        error_output = json.dumps({"erro": "Falha ao gerar o quiz. Verifique os logs do servidor para detalhes."}, ensure_ascii=False)
        print(error_output)
        sys.exit(1) # Sai com status de erro

