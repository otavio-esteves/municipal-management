# Diretrizes Arquiteturais

Este documento descreve o que existe hoje no projeto e como evoluir sem quebrar a arquitetura atual.

## 1. Camadas existentes

### Domain

Local:

- `app/Domain`

Conteúdo atual:

- `ServiceOrderStatus`
- exceptions de negócio de `ServiceOrder`, `Category` e `Secretariat`

Regras:

- não depende de `Livewire`;
- não depende de `Illuminate\Http`;
- não conhece rotas, policies, models Eloquent ou views.

### Application

Local:

- `app/Application`

Conteúdo atual:

- casos de uso;
- DTOs de create/update;
- results de listagem;
- contratos de persistência.

Exemplos atuais:

- `CreateServiceOrder`
- `UpdateServiceOrder`
- `GetServiceOrder`
- `ListServiceOrders`
- `SaveCategory`
- `SaveSecretariat`

Regras:

- depende de `Domain` e de contratos;
- não renderiza resposta;
- não contém código de `Livewire` ou `Blade`.

### Infrastructure

Local:

- `app/Infrastructure`

Conteúdo atual:

- implementações Eloquent dos contratos da `Application`.

Exemplos:

- `EloquentServiceOrderRepository`
- `EloquentCategoryRepository`
- `EloquentSecretariatRepository`

Regras:

- implementa persistência;
- não concentra regra de negócio de tela.

### Interface/UI

Local:

- `app/Livewire`
- `resources/views`
- `routes/web.php`

Responsabilidades:

- estado da tela;
- validação simples;
- autorização;
- chamada de use case;
- mensagens amigáveis;
- renderização.

Regras:

- não deve reimplementar regra de negócio já existente em `Application` ou `Domain`;
- não deve voltar a executar queries Eloquent diretas nos componentes críticos quando já existe caso de uso equivalente.

### Persistence

Local:

- `app/Models`
- `database/factories`
- `database/migrations`

Responsabilidades:

- relacionamentos;
- casts;
- scopes simples;
- transições simples de estado;
- suporte a testes.

Regras:

- não deve concentrar regra complexa de aplicação.

## 2. Dependências permitidas

Direção prática:

- `Domain <- Application <- Infrastructure / Interface / Persistence`

Regras atuais:

- `Domain` não depende de `Livewire` nem de `Illuminate\Http`;
- `Application` usa contratos para persistência nos fluxos críticos;
- `Infrastructure` implementa os contratos da `Application`;
- `Livewire` chama casos de uso;
- `Models` Eloquent ficam restritos a persistência e comportamento simples.

## 3. Convenções de casos de uso

Padrão:

- nome verbal e explícito;
- método público `handle(...)`.

Exemplos atuais:

- `CreateServiceOrder`
- `UpdateServiceOrder`
- `DeleteServiceOrder`
- `GetServiceOrder`
- `ListServiceOrders`
- `SaveCategory`
- `DeleteCategory`
- `SaveSecretariat`

Evitar:

- `Helper`
- `Manager`
- `ProcessData`
- casos de uso que misturam múltiplas intenções sem necessidade.

## 4. Convenções de DTOs

Os DTOs ficam em `app/Application/**/Data`.

Padrões já adotados no código:

- `Create...Data`
- `Update...Data`
- `...ListResult`

Exemplos reais:

- `CreateServiceOrderData`
- `UpdateServiceOrderData`
- `CreateCategoryData`
- `UpdateCategoryData`
- `CreateSecretariatData`
- `UpdateSecretariatData`
- `ChecklistItemData`
- `ServiceOrderListResult`

Regras:

- usar `readonly` quando possível;
- usar tipos explícitos;
- evitar arrays soltos na fronteira entre `Livewire` e `Application`;
- permitir normalização simples dentro do DTO;
- não mover regra de negócio para dentro do DTO.

## 5. Exceptions de domínio

As exceptions de negócio ficam em `app/Domain/**/Exceptions`.

Exemplos atuais:

- `InvalidServiceOrderCategory`
- `InvalidServiceOrderStatusTransition`
- `ServiceOrderNotFound`
- `CategorySlugAlreadyExists`
- `CategoryNotFound`
- `SecretariatNameAlreadyExists`
- `SecretariatNotFound`

Regras:

- nome semântico;
- mensagem curta e útil;
- sem dependência de HTTP;
- tratadas pela UI para exibição amigável.

No Livewire, o projeto hoje usa o trait:

- `App\Livewire\Concerns\InteractsWithFriendlyExceptions`

## 6. Fluxo real de ServiceOrder

### Entrada

- rota protegida por `auth`, `verified` e `can('view', 'secretariat')`;
- `ServiceOrderManager` valida acesso adicional com policy.

### Listagem

- `ServiceOrderManager` chama `ListServiceOrders`;
- `ListServiceOrders` depende de `ServiceOrderRepository`;
- `EloquentServiceOrderRepository` aplica busca, paginação e resumo por secretaria.

### Criação

- `ServiceOrderManager` cria `CreateServiceOrderData`;
- `CreateServiceOrder` valida categoria com `EnsureCategoryBelongsToSecretariat`;
- repositório persiste ODS e checklist.

### Edição

- `GetServiceOrder` carrega sempre por secretaria;
- `UpdateServiceOrderData::fromServiceOrder(...)` preenche a tela;
- `UpdateServiceOrder` revalida a categoria e atualiza o registro.

### Exclusão

- `DeleteServiceOrder` depende de `GetServiceOrder` para garantir escopo por secretaria.

### Status

- `ServiceOrder` usa o enum `ServiceOrderStatus`;
- transição simples fica no model com `changeStatus(...)`;
- cobertura existe em `tests/Feature/ServiceOrders/ServiceOrderDomainTest.php`.

## 7. Policies e autorização

Policies existentes:

- `CategoryPolicy`
- `SecretariatPolicy`
- `ServiceOrderPolicy`

Regras atuais:

- rotas protegidas usam `can(...)` quando aplicável;
- componentes Livewire usam `authorize(...)`;
- autorização de acesso não depende só de botão escondido;
- casos de uso críticos de ODS ainda validam coerência de secretaria/categoria/registro.

## 8. Como criar uma nova feature

Passo a passo recomendado:

1. identificar a intenção da feature;
2. definir a policy se houver recurso protegido;
3. criar DTOs de entrada/saída em `Application`;
4. criar o caso de uso;
5. criar contrato de persistência se o fluxo for relevante;
6. implementar repositório em `Infrastructure`;
7. registrar binding em `AppServiceProvider`;
8. chamar o caso de uso do `Livewire`;
9. criar testes de feature;
10. atualizar documentação se a estrutura do projeto mudar.

## 9. Como criar um novo caso de uso

Checklist:

1. criar nome verbal e explícito;
2. definir `handle(...)`;
3. decidir quais DTOs entram e saem;
4. depender de contratos quando houver persistência crítica;
5. manter autorização na borda e validação de coerência no caso de uso quando necessário;
6. adicionar teste de feature cobrindo o comportamento.

## 10. Como criar testes

### Feature

Use para:

- fluxos HTTP;
- Livewire;
- autorização;
- integração com banco;
- regras críticas do sistema.

### Unit / Architecture

Use para:

- guards arquiteturais leves.

Arquivo atual:

- `tests/Unit/ArchitectureTest.php`

Coberturas atuais:

- `Domain` sem `Livewire`;
- `Domain` sem `Illuminate\Http`;
- `Domain` sem `Illuminate\Routing`, `App\Models` e dependência explícita de Eloquent;
- `Application` sem `Livewire`, `Blade`, controllers HTTP e requests HTTP;
- ausência de queries Eloquent diretas nos Livewire críticos;
- ausência de instanciação direta de models críticos nos Livewire protegidos;
- existência de coverage para use cases críticos;
- existência de coverage para criação, atualização, autorização, regras de secretaria, regras de categoria e transição de status;
- existência de policies para entidades protegidas.

Limitações:

- esses testes usam scan simples de arquivo e presença de fragments;
- eles não provam dependências indiretas resolvidas em tempo de execução;
- eles não substituem revisão arquitetural;
- eles protegem regressões óbvias e intencionais, não um grafo completo de arquitetura.

## 11. Como rodar o projeto e os testes

### Projeto

```bash
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail php artisan key:generate
./vendor/bin/sail php artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

### Testes

```bash
./vendor/bin/sail php artisan test
./vendor/bin/sail php artisan test tests/Unit/ArchitectureTest.php
./vendor/bin/sail php artisan test tests/Feature/ServiceOrders/ServiceOrderDomainTest.php
```
