# Teste Técnico – Desenvolvedor PHP Laravel

## Objetivo

Desenvolver uma aplicação backend responsável pelo processamento, transformação e sincronização de dados de produtos e preços, utilizando Views SQL para padronização das informações e disponibilizando os dados por meio de uma API REST.

---

## Requisitos Técnicos

Tecnologias obrigatórias:

* PHP 8.0+
* Laravel 11.0+
* SQLite
* Docker
* Docker Compose

---

## Restrições Obrigatórias

O projeto deve:

* Rodar integralmente via Docker.
* Possuir arquivo `docker-compose.yml`.
* Expor exclusivamente endpoints de API REST.
* Conter testes automatizados.
* Incluir instruções de execução no `README.md`.
* Documentar os endpoints disponíveis.

O projeto não deve:

* Exigir instalação de dependências na máquina host além do Docker.
* Conter qualquer tipo de interface web.

---

## Modelagem de Banco de Dados

### Tabelas de Origem

Devem ser criadas duas tabelas base:

* `produtos_base`
* `precos_base`

O script de criação das tabelas base encontra-se na raiz do projeto.

### Tabelas de Destino

Devem ser criadas duas tabelas para armazenamento dos dados processados:

* `produto_insercao`
* `preco_insercao`

Considere modelagem adequada, chaves e índices quando necessário.

---

## Processamento com Views SQL

A transformação dos dados deve ser realizada obrigatoriamente por meio de Views SQL.

Devem ser criadas:

* Uma View para produtos.
* Uma View para preços.

As Views devem contemplar:

* Normalização dos dados.
* Processamento apenas de registros ativos.

---

## Processo de Sincronização

A sincronização deve:

* Consumir os dados a partir das Views.
* Inserir, atualizar ou remover registros nas tabelas de destino.
* Evitar duplicidade.
* Evitar operações desnecessárias.

---

## API REST

A aplicação deve disponibilizar os seguintes endpoints:

### Sincronizar Produtos

POST /api/sincronizar/produtos

Executa o processo de transformação e sincronização dos dados de `produtos_base` para `produto_insercao`.

---

### Sincronizar Preços

POST /api/sincronizar/precos

Executa o processo de transformação e sincronização dos dados de `precos_base` para `preco_insercao`.

---

### Listar Produtos Sincronizados (Paginado)

GET /api/produtos-precos

Deve retornar os produtos processados com seus respectivos preços de forma paginada.
A paginação deve aceitar parâmetros de controle via query string.

---

## Como executar o projeto?

Certifique-se de ter Docker e Docker Compose instalados.

**1. Clonar o repositório**

```bash
git clone https://github.com/rafaellabueno/teste-backend-php-rafaella.git
cd teste-backend-php-rafaella
```

**2. Subir os containers**

```bash
docker compose up -d --build
```

**3. (Opcional) Popular dados de exemplo**

```bash
docker compose exec app php artisan db:seed
```

**4. Executar os testes**

```bash
docker compose exec app php artisan test
```

## 📖 Documentação da API

### 🔹 Sincronizar Produtos

Processa os dados da tabela `produtos_base` por meio da View SQL normalizada e popula a tabela `produto_insercao`.

- **Método:** `POST`
- **Endpoint:** `/api/sincronizar/produtos`
- **Resposta:** `200 OK`

```bash
curl -X POST http://localhost:8000/api/sincronizar/produtos
```

### 🔹 Sincronizar Preços

Processa os dados de preços garantindo:

- Normalização financeira
- Vinculação correta aos produtos existentes
- Integridade relacional

- **Método:** `POST`
- **Endpoint:** `/api/sincronizar/precos`
- **Resposta:** `200 OK`

```bash
curl -X POST http://localhost:8000/api/sincronizar/precos
```

### 🔹 Listar Produtos e Preços

Retorna a listagem paginada de produtos com seus respectivos preços relacionados.

- **Método:** `GET`
- **Endpoint:** `/api/produtos-precos`
- **Parâmetro opcional:**
    - `per_page` (default: 10)

```bash
curl -X GET "http://localhost:8000/api/produtos-precos?per_page=5"
```

**Exemplo de resposta**

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 11,
      "codigo": "PRD001",
      "nome": "Teclado Mecânico RGB",
      "precos": [
        { "valor": 499.9, "status": "ativo" }
      ]
    }
  ],
  "total": 9
}
```