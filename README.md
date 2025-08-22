# Sales App — Laravel API + Vue Frontend

Sistema de cadastro de **vendas** e cálculo de **comissões (8.5%)** com:

* **API** (Laravel 12, PHP 8.3, MySQL 8, Redis, MailHog, Queues, Scheduler, Cache com tags e invalidação por Observer)
* **Frontend** (Vue 3 + Vite + TypeScript, Tailwind, Pinia, Toaster global)
* **Docker** para todo o ambiente de desenvolvimento

> Comissão padrão: **8.5%** (configurável via `COMMISSION_RATE` no `.env`)

---

## Sumário

* [Stack & Requisitos](#stack--requisitos)
* [Estrutura do Repositório (Monorepo)](#estrutura-do-repositório-monorepo)
* [Subindo o Ambiente (Docker)](#subindo-o-ambiente-docker)
* [Configuração da API (Laravel)](#configuração-da-api-laravel)
* [Banco de Dados (Migrations/Factories/Seeders)](#banco-de-dados-migrationsfactoriesseeders)
* [API — Endpoints](#api--endpoints)
* [Validações, Resources, Services](#validações-resources-services)
* [Comissão](#comissão)
* [Cache & Invalidação](#cache--invalidação)
* [E-mails, Filas & Agendamentos](#e-mails-filas--agendamentos)
* [Testes & Cobertura](#testes--cobertura)
* [Frontend (Vue 3 + TS + Vite)](#frontend-vue-3--ts--vite)
* [Arquivo de Testes REST (VS Code)](#arquivo-de-testes-rest-vs-code)
* [Troubleshooting](#troubleshooting)
* [Futuros Melhoramentos](#futuros-melhoramentos)
* [Licença](#licença)

---

## Stack & Requisitos

**Backend**

* PHP **8.3** (FPM, Alpine)
* Laravel **12**
* MySQL **8.4**
* Redis **7**
* MailHog (SMTP dev + UI)
* Nginx (proxy para Laravel)
* Queue worker e Scheduler dedicados

**Frontend**

* Vue **3**
* Vite
* TypeScript
* TailwindCSS
* Pinia

**Ferramentas**

* Docker & Docker Compose
* PHPUnit + PCOV (coverage)
* VS Code REST Client (opcional)

---

## Estrutura do Repositório (Monorepo)

```
tray/
├── api/                    # Laravel API (Laravel 12)
├── app/                    # Frontend (Vue 3 + Vite + TS)
├── docker/
│   ├── php/                # Dockerfile PHP-FPM
│   ├── nginx/              # nginx conf
│   └── node/               # Dockerfile Node (dev server)
├── docker-compose.yml
├── README.md
├── .editorconfig
├── .gitignore
├── LICENSE (MIT)
└── ...
```

---

## Subindo o Ambiente (Docker)

1. **Copie o `.env` da API** (dentro de `api/`):

```bash
cd api
cp .env.example .env
```

2. **Ajuste variáveis** no `.env` (valores compatíveis com o Compose):

```env
APP_NAME="Sales App"
APP_ENV=local
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=app
DB_USERNAME=app
DB_PASSWORD=secret

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
REDIS_HOST=redis
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_FROM_ADDRESS="no-reply@tray.test"
MAIL_FROM_NAME="${APP_NAME}"

COMMISSION_RATE=0.085
ADMIN_EMAIL=admin@example.test
```

3. **Suba os serviços** (na raiz do projeto):

```bash
cd ..
docker compose up -d --build
```

Serviços e portas:

* **API via Nginx**: [http://localhost:8080](http://localhost:8080) (API em `/api/v1`)
* **Frontend (Vite)**: [http://localhost:5173](http://localhost:5173)
* **MailHog**: [http://localhost:8025](http://localhost:8025) (SMTP porta 1025)
* **MySQL**: porta **3307** do host → 3306 do container
* **Redis**: 6379

4. **Instale a API** (dentro do container `app`):

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

> Containers **queue** (worker) e **scheduler** sobem automaticamente.

---

## Configuração da API (Laravel)

* **Rotas** sob `/api/v1`
* **Controllers**: `App\Http\Controllers\Api\...`
* **Form Requests**: `App\Http\Requests\{Sellers,Sales}\...`
* **Resources (Transformers)**: `App\Http\Resources\{Sellers,Sales}\...`
* **Services**:

  * `CommissionService` — cálculo da comissão
  * `SalesQuery` — agregações (totais por dia/seller)
  * `DailySalesReportService` — relatórios do dia + cache
* **Jobs** e **Mailables** para envio de e-mails
* **Observers** (`SaleObserver`) para invalidação de cache
* **Cache** com **tags** quando suportado (Redis); fallback para chaves simples

---

## Banco de Dados (Migrations/Factories/Seeders)

* `sellers` (id, name, email, timestamps)
* `sales` (id, seller\_id, amount, sold\_at, timestamps)

Executar:

```bash
docker compose exec app php artisan migrate --seed
```

---

## API — Endpoints

Base: `http://localhost:8080/api/v1`

### Sellers

* `GET /sellers` — lista paginada (`page`, `per_page`)
* `GET /sellers/{id}` — detalhes
* `POST /sellers` — cria (`name`, `email`)
* `PUT /sellers/{id}` — atualiza (`name`, `email`)
* *(DELETE opcional)*

### Sales

* `GET /sales` — lista paginada + filtros:

  * `seller_id` (opcional)
  * `date_from=YYYY-MM-DD`
  * `date_to=YYYY-MM-DD`
* `GET /sales/{id}` — detalhes
* `POST /sales` — cria (`seller_id`, `amount`, `sold_at`)
* `PUT /sales/{id}` — atualiza (`seller_id?`, `amount?`, `sold_at?`)
* *(DELETE opcional)*

### Reports / E-mails

* `GET /reports/daily/admin?date=YYYY-MM-DD` — resumo total do dia
* `GET /reports/daily/sellers?date=YYYY-MM-DD` — resumos por vendedor (somente quem vendeu)
* `GET /reports/daily/sellers/{sellerId}?date=YYYY-MM-DD` — resumo de um vendedor
* `POST /sellers/{sellerId}/commission/resend` — reenviar e-mail do dia:

  ```json
  { "date": "YYYY-MM-DD" }
  ```

---

## Validações, Resources, Services

* **Form Requests** garantem campos obrigatórios/formatos.
* **Resources** padronizam o payload (snake\_case) e incluem campos calculados (ex.: `commission`).
* **Services**:

  * `CommissionService::forTotalAmount(float)` → comissão total do período.
  * `SalesQuery` → agrega `count` e `sum` por data/seller.
  * `DailySalesReportService` → empacota dados de relatórios; aplica **cache** (tags/chaves).

---

## Comissão

Taxa definida no `.env`:

```env
COMMISSION_RATE=0.085
```

Usada por `CommissionService`. Pode ser alterada sem mudar o código.

---

## Cache & Invalidação

* **Helpers**

  * `App\Support\CacheKeys` — padrões de chave (`reports:admin:{date}`, etc.)
  * `App\Support\CacheTtl` — TTL dinâmico por data (hoje: minutos; passado: horas)

* **Tags** (se o driver suportar; ex.: Redis):

  * Ex.: `['reports', "date:{$date}", "seller:{$id}"]` → permite **flush por grupo**

* **Invalidação automática**

  * `SaleObserver` invalida caches de:

    * admin do dia
    * seller do dia
    * lista de sellers do dia
  * Em `updated`, se mudou `seller_id` ou `sold_at`, invalida **pares antigo e novo**.

---

## E-mails, Filas & Agendamentos

* **Mailables**

  * `SellerDailyCommissionMail`
  * `AdminDailySalesMail`

* **Jobs (Redis)**

  * `DispatchSellerDailyCommissionsJob` → enfileira mails individuais de vendedores
  * `SendAdminDailySalesMailJob` → enfileira mail do admin

* **Scheduler** (cron) no container `scheduler`:

  * Executa a cada minuto:

    * `daily:dispatch-seller-mails`
    * `daily:admin-mail`
  * Manual:

    ```bash
    docker compose exec app php artisan schedule:list
    docker compose exec app php artisan schedule:run --verbose
    ```

* **MailHog**

  * UI: [http://localhost:8025](http://localhost:8025)
  * SMTP: `mailhog:1025` (no Docker)

* **Queue worker**

  * Container `queue` roda `php artisan queue:work`
  * Ver falhas:

    ```bash
    docker compose exec app php artisan queue:failed
    ```

---

## Testes & Cobertura

* **Testes**:

  ```bash
  docker compose exec app php artisan test
  ```

* **Coverage (PCOV)**:

  * PCOV está instalado e **desativado por padrão**.
  * Habilite no comando:

    ```bash
    docker compose exec app php -d pcov.enabled=1 -d pcov.directory=/var/www \
      vendor/bin/phpunit --coverage-html coverage
    # ou
    docker compose exec app php -d pcov.enabled=1 -d pcov.directory=/var/www \
      artisan test --coverage
    ```
  * Saída HTML em `api/coverage/`.

---

## Frontend (Vue 3 + TS + Vite)

* **Estrutura** (arquitetura em camadas):

```
app/src/
├── app/                 # shell (App.vue, layouts, router, providers)
├── shared/              # infra: api, ui, utils, config, styles, types
├── entities/            # tipos do domínio (Seller, Sale, User…)
├── features/            # api + model + store + ui por feature
├── pages/               # telas roteadas (ex.: sellers/ListView.vue, sales/ListView.vue)
└── widgets/             # opcional
```

* **API Client** (`axios`) com interceptors (erros 422 → `validation`)

* **Toaster global** (Pinia) para feedback (criar/editar/reenviar)

* **Paginação com URL-sync** (`?page=&per_page=`), filtros de **seller** e **período**

* **Sales**

  * Listagem com filtros e paginação
  * Criar/editar (`SaleForm.vue`)

* **Sellers**

  * Listagem, criar e atualizar
  * Reenviar e-mail de comissão do dia

* **Dev server (Docker)**:

  * [http://localhost:5173](http://localhost:5173)
  * `VITE_API_URL=/api` → Nginx proxy para a API em `http://localhost:8080`

> Se rodar o frontend **fora** do Docker, configure um **proxy** no `vite.config.ts` de `/api` para `http://localhost:8080`.

---

## Arquivo de Testes REST (VS Code)

Há um `api.http` com exemplos de chamadas para:

* `Sellers` (create/list/show/update)
* `Sales` (create/list/show/update + filtros)
* `Reports` (admin/sellers)
* `Commission resend`

> Caso use trechos `> {% ... %}` para capturar IDs, comente-os se seu VS Code não executar scripts pós-requisição.

---

## Troubleshooting

* **Página em branco no `/`** → confira se o *layout* possui `<RouterView/>` e a **ordem** das rotas (Dashboard primeiro).
* **Frontend `ECONNREFUSED 127.0.0.1:80`** → ajuste/proxy do Vite; usando `VITE_API_URL=/api` resolve no cenário Docker.
* **Sem e-mails** → verifique:

  * `MAIL_*` no `.env`
  * containers `queue` e `scheduler` (`docker compose ps`)
  * `php artisan queue:failed`
  * MailHog em [http://localhost:8025](http://localhost:8025)
* **Jobs não disparam** → `php artisan schedule:list` e `schedule:run --verbose` no container `app`.
* **Cache** não invalida após alterar venda → veja se `SaleObserver` está registrado e se o driver de cache suporta **tags** (Redis).

---

## Futuros Melhoramentos

* **Autenticação JWT** (API) + guarda no Frontend
* **Autorização** (roles)
* **Testes E2E** no frontend (Cypress/Playwright)
* **Relatórios CSV/PDF**
* **DELETE** para sellers/sales (com soft delete)
* **Observabilidade** (Sentry/LogRocket)

---

## Licença

**MIT** — veja `LICENSE`.

---

## Comandos Úteis (Resumo)

```bash
# Subir tudo
docker compose up -d --build

# Instalar API
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed

# Testes
docker compose exec app php artisan test

# Coverage (PCOV)
docker compose exec app php -d pcov.enabled=1 -d pcov.directory=/var/www vendor/bin/phpunit --coverage-html coverage

# Schedule (manual)
docker compose exec app php artisan schedule:list
docker compose exec app php artisan schedule:run --verbose

# Fila
docker compose exec app php artisan queue:failed

# Endpoints principais
# API:      http://localhost:8080/api/v1
# Frontend: http://localhost:5173
# MailHog:  http://localhost:8025
```
