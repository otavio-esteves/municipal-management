# Auditoria de Arquitetura

Este documento registra o que foi identificado anteriormente e o que já foi corrigido no código atual.

## 1. Estado atual

O projeto está hoje alinhado com uma Clean Architecture pragmática para o escopo existente:

- `Livewire` foi mantido fino nos fluxos principais;
- `Application` concentra casos de uso e DTOs;
- `Domain` contém enum e exceptions de negócio;
- `Infrastructure` implementa persistência via contratos;
- `Models` Eloquent ficaram restritos a persistência, scopes simples e transições simples de estado.

Também existe proteção automatizada básica contra regressões arquiteturais em:

- `tests/Unit/ArchitectureTest.php`

## 2. Correções realizadas

### 2.1. Fluxo de `ServiceOrder` desacoplado de Eloquent na camada de aplicação

Corrigido.

Situação atual:

- `CreateServiceOrder`, `UpdateServiceOrder`, `GetServiceOrder`, `ListServiceOrders` e `DeleteServiceOrder` dependem de `ServiceOrderRepository`;
- validação de categoria depende de `CategoryRepository`;
- implementações concretas vivem em `app/Infrastructure/Persistence/Eloquent`.

Impacto:

- menor acoplamento da `Application` com Eloquent;
- centralização do escopo por secretaria;
- regras críticas mais testáveis.

### 2.2. Regra “categoria pertence à secretaria” centralizada

Corrigido.

Situação atual:

- a regra foi extraída para `EnsureCategoryBelongsToSecretariat`;
- criação e edição de ODS usam a mesma validação.

### 2.3. Módulos administrativos tirados de persistência direta no Livewire

Corrigido.

Situação anterior:

- `CategoryManager` e `SecretariatManager` persistiam direto com Eloquent.

Situação atual:

- `CategoryManager` usa `SaveCategory`, `GetCategory` e `DeleteCategory`;
- `SecretariatManager` usa `SaveSecretariat`, `GetSecretariat` e `DeleteSecretariat`;
- persistência foi movida para contratos e repositórios Eloquent.

### 2.4. DTOs padronizados e tipados

Corrigido.

Situação atual:

- DTOs de mutação seguem padrão `Create...Data` e `Update...Data`;
- `ServiceOrder` usa `ChecklistItemData` em vez de checklist anônimo trafegando internamente pela `Application`;
- os DTOs são `readonly`.

### 2.5. Tratamento de exceptions padronizado na UI

Corrigido.

Situação atual:

- exceptions de domínio continuam sem dependência de HTTP;
- componentes Livewire usam `InteractsWithFriendlyExceptions` para converter exceptions em mensagens de tela.

### 2.6. Autorização reforçada por rota, policy, Livewire e caso de uso crítico

Corrigido e coberto.

Situação atual:

- rotas usam middleware e `can(...)`;
- components Livewire usam `authorize(...)`;
- ODS continua protegida por escopo de secretaria no caso de uso e no repositório;
- testes cobrem rota, ação Livewire, admin, usuário de secretaria e convidado.

### 2.7. Models revisados

Corrigido no escopo necessário.

Situação atual:

- relacionamentos tipados;
- scopes pequenos;
- `ServiceOrder` mantém enum e transição simples de status;
- factories foram ajustadas para facilitar testes.

## 3. O que continua intencionalmente simples

Nem tudo foi abstraído, por decisão pragmática:

- `ServiceOrder` ainda gera o código e controla a transição simples de status no model;
- `User::isAdmin()` e `User::belongsToSecretariat()` continuam no model por serem regras pequenas e estáveis;
- `ServiceOrderManager` ainda concentra estado de tela e pequenas regras de checklist estritamente de UI.

Esses pontos não foram movidos porque hoje não configuram regra complexa de aplicação.

## 4. Limitações conhecidas que permanecem

As limitações abaixo ainda existem ou não foram atacadas porque não eram necessárias para o estado atual:

- o projeto não usa uma ferramenta especializada de testes de arquitetura; os guards atuais são leves e baseados em scan;
- a decisão de destino pós-login continua distribuída entre fluxo de autenticação e rota `/dashboard`;
- há documentação histórica em `docs/` que serve como referência antiga, não como fonte oficial do estado atual.

## 5. Fonte oficial para evolução

Para evoluir o projeto com segurança, use nesta ordem:

1. `README.md`
2. `docs/architecture-guidelines.md`
3. `tests/Unit/ArchitectureTest.php`
4. testes de feature do módulo alterado

## 6. Validação atual

No estado documentado aqui, a suíte completa passa com:

```bash
./vendor/bin/sail php artisan test
```

Os testes de arquitetura rodam com:

```bash
./vendor/bin/sail php artisan test tests/Unit/ArchitectureTest.php
```
