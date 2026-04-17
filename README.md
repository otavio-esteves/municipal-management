# Municipal Management

Aplicação Laravel 12 + Livewire 3 para gestão municipal de secretarias, categorias e ordens de serviço.

## Stack e requisitos

- PHP `^8.2`
- Laravel `^12.0`
- Livewire `^3.6`
- Volt `^1.7`
- Node.js para build frontend com Vite
- Banco compatível com Laravel 12
- Docker + Sail para ambiente local recomendado

Observação:
- O projeto hoje é desenvolvido e testado preferencialmente com `Sail`.
- Alguns documentos antigos em `docs/` mencionam PHP 8.4. O requisito real do código atual está em [composer.json](/home/esteves/Projects/laravel-server/html/composer.json:1): PHP `^8.2`.

## Como executar

### Com Sail

```bash
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail php artisan key:generate
./vendor/bin/sail php artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

### Sem Sail

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

## Comandos principais

```bash
./vendor/bin/sail php artisan test
./vendor/bin/sail php artisan route:list
./vendor/bin/sail php artisan migrate
./vendor/bin/sail composer dump-autoload
./vendor/bin/sail npm run dev
./vendor/bin/sail npm run build
```

Atalhos definidos em `composer.json`:

```bash
composer setup
composer dev
composer test
```

## Fluxo da aplicação

### Perfis de acesso

- `admin`: usuário com `secretariat_id = null`
- `usuário de secretaria`: usuário vinculado a uma `secretariat`

### Fluxo principal

1. Usuário autenticado e com e-mail verificado acessa o sistema.
2. Se estiver vinculado a uma secretaria, o login e o dashboard o direcionam para `/secretarias/{secretariat}/ods`.
3. Admin acessa áreas administrativas para gerir secretarias e categorias.
4. Usuário de secretaria opera apenas as ordens de serviço da própria secretaria.

## Regras de autorização

As regras estão explícitas em `Policies` e não dependem apenas de navegação ou redirecionamento.

- `SecretariatPolicy`
  - admin pode listar, criar, editar e excluir secretarias
  - usuário de secretaria só pode visualizar a própria secretaria quando necessário
- `CategoryPolicy`
  - gestão administrativa restrita a admin
- `ServiceOrderPolicy`
  - admin pode acessar qualquer painel de ODS e manipular qualquer ordem
  - usuário de secretaria só acessa e manipula ODS da própria secretaria

Pontos de enforcement:

- rotas em [routes/web.php](/home/esteves/Projects/laravel-server/html/routes/web.php:1)
- componentes Livewire administrativos e de ODS
- casos de uso de `ServiceOrder`, que validam coerência de secretaria/categoria

## Fluxo de ServiceOrder

O fluxo de ODS foi separado em camada de aplicação e domínio.

### Criação

- entrada capturada no Livewire
- transformação para `ServiceOrderData`
- execução de `CreateServiceOrder`
- validação de categoria pertencente à mesma secretaria
- persistência com código final gerado a partir do `id`
- status inicial definido no domínio como `pending`

### Edição

- carregamento por secretaria com `GetServiceOrder`
- atualização por `UpdateServiceOrder`
- edição comum não reseta `status`

### Listagem

- listagem via `ListServiceOrders`
- busca por `code`, `title` e `location`
- paginação
- resumo agregado para total, urgentes e concluídas

### Status

- `status` usa enum de domínio: `pending`, `in_progress`, `completed`
- transições ficam no modelo `ServiceOrder`

## Estrutura de pastas adotada

```text
app/
  Application/
    Categories/
    Secretariats/
    ServiceOrders/
  Domain/
    ServiceOrders/
  Livewire/
    Admin/
    Secretariat/
    Forms/
  Models/
  Policies/
  Providers/

database/
  factories/
  migrations/
  seeders/

resources/
  views/
    livewire/
    layouts/

tests/
  Feature/
    Authorization/
    Listings/
    ServiceOrders/
```

## Convenções para futuras features

- regras de domínio ficam em `app/Domain`
- casos de uso ficam em `app/Application`
- componentes Livewire devem ser finos:
  - capturam input
  - autorizam
  - chamam casos de uso
  - lidam com mensagens e renderização
- consultas reutilizáveis ficam em:
  - scopes de model quando forem simples
  - classes de aplicação quando envolverem listagem/filtro com mais contexto
- autorização nova deve nascer com `Policy` ou `Gate`
- comportamento crítico deve vir acompanhado de testes de feature ou integração

## Testes

A suíte atual cobre principalmente:

- autorização entre admin e secretaria
- isolamento de ODS por secretaria
- validação de categoria por secretaria
- domínio de `ServiceOrder`:
  - geração de código
  - preservação de status
  - transições de status
- listagens com filtro e paginação

Para rodar:

```bash
./vendor/bin/sail php artisan test
```

## Documentação adicional

- [Arquitetura](./docs/architecture.md)
- [Plano original](./docs/municipal-management-code-plan.md)
- [Requisitos originais](./docs/municipal-management-requirements.md)
