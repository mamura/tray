# Tray – Sales Commission (Laravel + Vue) – Monorepo

Monorepo contendo:
- `api/` – **Laravel** (API de vendedores e vendas, comissão de 8.5%)
- `app/` – **Vue 3 + Vite + TypeScript** (aplicação web)

## Desafio (resumo)
- Cadastrar vendedores e vendas.
- Listar vendedores, vendas e vendas por vendedor.
- Comissão: **8.5%** sobre o valor da venda.
- E-mails diários:
  - Para cada vendedor: quantidade de vendas, total e total de comissão do dia.
  - Para o admin: total de vendas do dia.
- Reenvio manual do e-mail de comissão (admin).

## Estrutura
.
├── api/ # Laravel 11 (a criar)
├── app/ # Vue 3 + TS (a criar)
└── docker/ # Dockerfiles e configs (a criar)


## Requisitos
- Docker e Docker Compose
- Node 20+ (para o frontend)

## Próximos passos
1. Configurar **Docker** (PHP-FPM, Nginx, MySQL, Redis, Mailhog, Queue).
2. Criar projeto **Laravel** em `api/` (migrations, models, seeds, mail, jobs).
3. Criar projeto **Vue** em `app/` (páginas de Sellers/Sales, integração com API).
4. Escrever testes (PHPUnit) e documentar endpoints (coleção Postman/Insomnia).

## Licença
MIT – veja [LICENSE](./LICENSE).
