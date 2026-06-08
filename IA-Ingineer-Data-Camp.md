# IA Engineer Data Camp

Ce chapitre vous permet de comprendre comment envoyer des requêtes à l'API OpenAI, une étape essentielle pour exploiter la puissance de l'IA dans vos projets. Vous allez apprendre à communiquer efficacement avec l'API pour obtenir des réponses pertinentes.

## Comment faire des requêtes à l'API OpenAI

Il existe plusieurs façon de faire des requêtes à l'API OpenAI. Voici les principales:

### Syntaxe avec Python

```python

from openai import OpenAI

client = OpenAI(
  api_key="[ENCRYPTION_KEY]"
)

response = client.chat.completions.create(
  model="gpt-4.1", 

  messages=[
    {"role": "system", "content": "You are a helpful assistant."},
    {"role": "user", "content": "Who won the world series in 2020?"},
  ],
)


print(response.choices[0].message.content) # Affiche la réponse

```

### Résumer et éditer un texte

Ce chapitre vous prépare à transformer et résumer du texte, une compétence essentielle pour extraire rapidement des informations clés et gagner en efficacité dans la gestion de grandes quantités de données.

**Jetons** : Unités de texte qui assistent l'IA dans la compréhension et l'interprétation des textes. En général, 1000 jetons équivalent à environ 750 mots.

Voici un exemple de code pour résumer et éditer un texte à un certains nombre de mots:

```python
client = OpenAI(api_key="<OPENAI_API_TOKEN>")

# Use an f-string to format the prompt
prompt = f"""Summarize the following text into two concise bullet points:
{finance_text}"""


response = client.chat.completions.create(
  model="gpt-4.1", 

  messages=[
    {"role": "system", "content": "You are a helpful assistant."},
    {"role": "user", "content": prompt}
    ],
  max_completion_tokens= 400,
)

print(response.choices[0].message.content)

```

### Générer du texte

Ce chapitre vous aidera à comprendre comment générer et affiner du texte avec l'IA, une compétence essentielle pour créer du contenu personnalisé ou automatisé. Vous allez découvrir comment utiliser la génération de texte pour améliorer vos projets et applications.


