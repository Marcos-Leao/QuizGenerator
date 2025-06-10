#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Marcos Leão - 03/06/2025

Script para gerar perguntas e respostas para um quiz usando a API Gemini do Google.
Este script recebe um assunto como argumento, faz uma chamada à API Gemini,
e retorna um JSON com 20 perguntas e respostas sobre o assunto.

Uso:
    python3 generate_quiz.py "Assunto do Quiz"

Retorno:
    JSON com perguntas, respostas corretas, respostas erradas e explicações.
"""

import sys
import json
import os
from google import genai
from google.genai import types

from dotenv import load_dotenv
load_dotenv()
api_key = os.getenv('GOOGLE_API_KEY')

client = genai.Client()

def generate_quiz_prompt(subject):
    """Gera o prompt para a API Gemini com base no assunto fornecido."""

    return f"""
    Crie um quiz com 20 perguntas sobre o assunto: {subject}.

    Para cada pergunta, forneça:
    1. A pergunta
    2. A resposta correta
    3. Três respostas erradas plausíveis (não absurdas)
    4. Uma explicação detalhada de por que a resposta correta está correta

    Retorne os dados no seguinte formato JSON, e apenas o JSON sem nenhum texto adicional:

    {{
      "subject": "{subject}",
      "questions": [
        {{
          "question": "Pergunta 1",
          "correct_answer": "Resposta correta para pergunta 1",
          "wrong_answer1": "Primeira resposta errada para pergunta 1",
          "wrong_answer2": "Segunda resposta errada para pergunta 1",
          "wrong_answer3": "Terceira resposta errada para pergunta 1",
          "explanation": "Explicação detalhada de por que a resposta correta está correta"
        }},
        ...
      ]
    }}
    """

def generate_quiz(subject):
    """
    Gera um quiz sobre o assunto fornecido usando a API Gemini.

    Args:
        subject (str): O assunto do quiz.

    Returns:
        dict: Dicionário com as perguntas e respostas do quiz.
    """
    try:

        # Gerar o prompt
        prompt = generate_quiz_prompt(subject)

        response = client.models.generate_content(
            model='gemini-2.0-flash',
            contents=prompt,
            config=types.GenerateContentConfig(

            safety_settings = [
                types.SafetySetting(
                    category=types.HarmCategory.HARM_CATEGORY_HARASSMENT,
                    threshold=types.HarmBlockThreshold.BLOCK_LOW_AND_ABOVE,
                ),
                types.SafetySetting(
                    category=types.HarmCategory.HARM_CATEGORY_HATE_SPEECH,
                    threshold=types.HarmBlockThreshold.BLOCK_LOW_AND_ABOVE,
                ),
                types.SafetySetting(
                    category=types.HarmCategory.HARM_CATEGORY_SEXUALLY_EXPLICIT,
                    threshold=types.HarmBlockThreshold.BLOCK_LOW_AND_ABOVE,
                ),
                types.SafetySetting(
                    category=types.HarmCategory.HARM_CATEGORY_DANGEROUS_CONTENT,
                    threshold=types.HarmBlockThreshold.BLOCK_LOW_AND_ABOVE,
                ),
                types.SafetySetting(
                    category=types.HarmCategory.HARM_CATEGORY_CIVIC_INTEGRITY,
                    threshold=types.HarmBlockThreshold.BLOCK_LOW_AND_ABOVE,
                ),
            ]

            )
        )

        #    model='gemini-2.0-flash',
        #    model='gemini-2.5-flash-preview-05-20',


        # Extrair o texto da resposta
        response_text = response.text

        # Tentar analisar o JSON
        try:
            # Remover possíveis marcadores de código que a API pode incluir
            if "```json" in response_text:
                response_text = response_text.split("```json")[1].split("```")[0].strip()
            elif "```" in response_text:
                response_text = response_text.split("```")[1].split("```")[0].strip()

            quiz_data = json.loads(response_text)

            # Verificar se o JSON tem a estrutura esperada
            if "questions" not in quiz_data or not isinstance(quiz_data["questions"], list):
                raise ValueError("Formato de resposta inválido: 'questions' não encontrado ou não é uma lista")

            # Limitar a 20 perguntas se houver mais
            if len(quiz_data["questions"]) > 20:
                quiz_data["questions"] = quiz_data["questions"][:20]

            return quiz_data

        except json.JSONDecodeError as e:
            return {
                "error": f"Erro ao analisar JSON: {str(e)}",
                "raw_response": response_text
            }

    except Exception as e:
        if "The model is overloaded." in srt(e):
            return {
                "O Gemini está sobrecarregado... Tente outra vez em alguns segundos."
            }
        else:
            return {
                "error": f"Erro ao gerar quiz: {str(e)}"
            }

# Erro: Erro ao gerar quiz: 503 UNAVAILABLE. {'error': {'code': 503, 'message': 'The model is overloaded. Please try again later.', 'status': 'UNAVAILABLE'}}



def main():
    """Função principal que processa os argumentos e gera o quiz."""
    # Verificar se o assunto foi fornecido como argumento
    if len(sys.argv) < 2:
        print(json.dumps({
            "error": "Assunto não fornecido. Use: python3 generate_quiz.py 'Assunto do Quiz'"
        }))
        sys.exit(1)

    # Obter o assunto do argumento da linha de comando
    subject = sys.argv[1]

    # Gerar o quiz
    quiz_data = generate_quiz(subject)

    # Imprimir o JSON resultante
    print(json.dumps(quiz_data, ensure_ascii=False, indent=None))

if __name__ == "__main__":
    main()
