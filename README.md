<div align="center">

# Polls Realtime API — Sistema de Enquetes em Tempo Real

[![Laravel 11](https://img.shields.io/badge/Laravel-11-blue?logo=laravel)](https://laravel.com)
[![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777bb3?logo=php)](https://www.php.net)
[![Docker](https://img.shields.io/badge/Docker-✓-2496ED?logo=docker)](https://www.docker.com)
[![WebSockets](https://img.shields.io/badge/WebSockets-Reverb-FF4500)](https://laravel.com/docs/reverb)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-✓-336791?logo=postgresql)](https://www.postgresql.org)
[![Swagger/OpenAPI](https://img.shields.io/badge/Swagger-OpenAPI-85EA2D?logo=swagger)](https://swagger.io)
[![Docs API](https://img.shields.io/badge/Docs%20API-OpenAPI-85EA2D?logo=swagger)](http://localhost:8000/docs/api)

</div>

---

## Demonstração

<!-- TODO: Adicionar GIF/Vídeo demonstrativo do fluxo de votação em tempo real e Reverb -->

<!-- TODO: Adicionar Screenshot da documentação do Swagger UI / Scramble (/docs/api) -->

---

## 📌 Sobre o Projeto

A **Polls Realtime API** é uma API RESTful construída com **Laravel 11** que permite a criação, gerenciamento e votação em enquetes com atualizações em **tempo real** através de **WebSockets (Laravel Reverb)**. Utiliza **PHP Enums** (`PollStatus`) para tipagem forte, um agendador de tarefas que transita automaticamente o status das enquetes e gera documentação **OpenAPI** interativa via **Scramble**.

---

## 📚 Inspiração & Creditos

Este projeto foi desenvolvido como resposta a um **desafio técnico publicado no LinkedIn**. A ideia central (enquetes em tempo real com votação via WebSockets) foi adaptada para a stack Laravel/PHP que domino. 

<!-- TODO: Inserir link para a postagem original no LinkedIn e/ou referência ao recrutador, caso deseje. -->

---

## 🚀 Funcionalidades & Regras de Negócio Implementadas

- **CRUD completo de Enquetes** com validação mínima de **3 opções**.
- **Bloqueio de edição** para enquetes já iniciadas.
- **Votação em tempo real** disparando o evento `OptionVoted` via Laravel Reverb.
- **Cálculo automático** de total de votos e percentuais retornados pelo `PollResource`.
- **Transição automática de status** (`NAO_INICIADO → EM_ANDAMENTO → FINALIZADO`) através do comando Artisan `polls:update-statuses` executado a cada minuto pelo scheduler.
- **Suite de testes** cobrindo os principais cenários de feature usando `RefreshDatabase` e `Event::fake()`.

---

## 🛠️ Arquitetura & Padrões de Projeto (Clean Architecture)

| Camada                | Responsabilidade |
|-----------------------|-------------------|
| **Controllers**       | `PollController` – orquestra requisições HTTP. |
| **Form Requests**     | `StorePollRequest`, `UpdatePollRequest` – validação de entrada. |
| **Services**          | `PollService` – lógica de negócios, coordena repositórios e eventos. |
| **Repositories**      | `PollRepository` – abstração de acesso ao banco. |
| **Resources**         | `PollResource` – formatação da resposta JSON. |
| **Enums**             | `PollStatus` – enum PHP 8.1+ representando os estados da enquete. |
| **Events & WebSockets** | `OptionVoted` + Laravel Reverb – entrega de atualizações em tempo real. |
| **Console Commands / Scheduler** | `UpdatePollStatusesCommand` – atualiza status das enquetes periodicamente. |

---

## 📄 Documentação da API (Swagger / OpenAPI via Scramble)

A documentação interativa é gerada automaticamente pelo **Scramble**. Basta acessar:

```
http://localhost:8000/docs/api
```

A interface permite explorar endpoints, testar requisições e visualizar os schemas de request/response.

---

## ⚙️ Variáveis de Ambiente

O projeto utiliza as variáveis listadas em `.env.example`. As chaves principais são:

| Variable | Description |
|----------|-------------|
| `APP_NAME` | Nome da aplicação. |
| `APP_ENV` | Ambiente (`local`, `production`, …). |
| `APP_KEY` | Chave de criptografia da aplicação (gerada com `php artisan key:generate`). |
| `APP_DEBUG` | Habilita modo debug. |
| `APP_URL` | URL base da aplicação. |
| `DB_CONNECTION` | Driver de banco (sqlite para desenvolvimento, pgsql via Docker). |
| `BROADCAST_CONNECTION` | Driver de broadcast (`reverb`). |
| `REVERB_APP_ID` | Identificador da aplicação Reverb. |
| `REVERB_APP_KEY` | Chave pública Reverb. |
| `REVERB_APP_SECRET` | Segredo Reverb. |
| `REVERB_HOST` | Host do servidor Reverb. |
| `REVERB_PORT` | Porta do servidor Reverb. |
| `VITE_...` | Variáveis expostas ao Vite (frontend) para o Reverb. |

Copie `.env.example` para `.env` e ajuste conforme necessário.

---

## 📂 Estrutura do Repositório

```
.
├─ app
│  ├─ Console
│  │  └─ Commands
│  │     └─ UpdatePollStatusesCommand.php
│  ├─ Enums
│  │  └─ PollStatus.php
│  ├─ Events
│  │  └─ OptionVoted.php
│  ├─ Http
│  │  ├─ Controllers
│  │  │  ├─ Controller.php
│  │  │  └─ PollController.php
│  │  ├─ Requests
│  │  │  ├─ StorePollRequest.php
│  │  │  └─ UpdatePollRequest.php
│  │  └─ Resources
│  │     └─ PollResource.php
│  ├─ Models
│  │  ├─ Option.php
│  │  ├─ Poll.php
│  │  └─ User.php
│  ├─ Providers
│  │  └─ AppServiceProvider.php
│  ├─ Repositories
│  │  └─ PollRepository.php
│  └─ Services
│     └─ PollService.php
├─ config
│  └─ scramble.php
├─ database
│  └─ (migrations, seeders …)
├─ routes
│  └─ api.php
├─ tests
│  └─ Feature
│     └─ PollControllerTest.php
├─ Dockerfile
├─ docker-compose.yml
├─ composer.json
├─ phpunit.xml
└─ README.md
```

---

## 💻 Como Executar o Projeto

> **Observação:** Ao usar Docker Compose a aplicação conecta‑se a um banco PostgreSQL (definido em `docker-compose.yml`). Em um ambiente local sem Docker, a conexão padrão é SQLite, conforme configurado em `.env.example`. Ajuste o arquivo `.env` caso prefira outro driver.


### Opção A: Via Docker Compose (Recomendado)

```bash
# Build e start dos containers
docker compose up -d --build

# Executar migrações e seed (dentro do container `app`)
docker compose exec app php artisan migrate --seed
```

- API disponível em **http://localhost:8000**
- Reverb (WebSockets) em **http://localhost:8080**
- PostgreSQL na porta **5432**

### Opção B: Execução Local sem Docker

```bash
composer install
cp .env.example .env   # ajuste as variáveis de ambiente
php artisan key:generate
php artisan migrate --seed
php artisan serve          # http://127.0.0.1:8000
php artisan reverb:start --host=127.0.0.1 --port=8080
```

---

## 🧪 Executando a Suíte de Testes

```bash
php artisan test
```

A suite contém **~10 testes de feature** cobrindo criação, atualização, votação e regras de negócio de status das enquetes.

---

## 👨💻 Autor

**Cauã Lopes** 

- GitHub: [caualopesrlp](https://github.com/Caualopesrlp)
- LinkedIn: [caualopesrlp](https://linkedin.com/in/caualopesrlp)

---
