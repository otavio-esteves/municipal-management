##### Fase 1: Fundação e Arquitetura Base
*O objetivo aqui é estabelecer o "esqueleto" do sistema e garantir que os requisitos não funcionais de infraestrutura sejam atendidos desde o dia zero.*

- Setup do Ambiente: Configuração do Laravel 12 com PHP 8.4 e criação do script de setup no composer.json para automação de migrações e chaves.

- Autenticação e Segurança: Implementação do sistema de login e proteção de rotas via middlewares (auth e verified).

- Estrutura de Dados Primária: Criação das migrações para a tabela secretariats com suporte a Soft Deletes.

##### Fase 2: Gestão de Entidades Hierárquicas
*Nesta fase, construímos a hierarquia necessária para que as ordens de serviço existam com contexto.*

- CRUD de Secretarias: Desenvolvimento da interface para cadastro de secretarias (Saúde, Obras, etc.).

- Categorias Vinculadas: Implementação da relação 1:N entre Secretarias e Categorias, garantindo a integridade de dados com exclusão em cascata (Foreign Keys).

- Interface Administrativa: Painel inicial (Dashboard) restrito para gestão dessas entidades.
##### Fase 3: Core Operacional (Ordens de Serviço e Checklists)
*Aqui é onde o valor real do software é entregue, agora com o detalhamento das tarefas por ODS.*

- Motor de Geração de ODS: Implementação da lógica para criação de Ordens de Serviço com títulos, descrições e o gerador de código único (ex: ODS-001).

- Estrutura de Checklists: Criação da tabela ods_checklists vinculada às ODS. Implementação da lógica para que cada ordem possa ter múltiplos itens de verificação.

- Logística de Status e Prazos: Configuração do status padrão como "pendente" e implementação do campo due_date. O status poderá ser influenciado pela conclusão dos itens do checklist.

- Rastreamento e Localização: Inclusão dos campos de localização e urgência conforme documentado.
##### Fase 4: Experiência do Usuário e Reatividade
*Transformaremos a aplicação em uma interface moderna que se comporta como uma SPA (Single Page Application).*

- Interface Reativa: Utilização de ferramentas como Livewire ou Inertia.js para garantir que as atualizações de status, filtros e marcação de itens do checklist ocorram sem recarregar a página.

- Gestão de Checklist In-Place: Interface para adicionar, remover e ordenar itens de checklist dinamicamente dentro da visualização da ODS.

- Filtros Avançados: Busca de ODS por código, secretaria, categoria ou nível de urgência.

- Feedback Visual: Notificações em tempo real para ações de criação e conclusão de tarefas (respeitando o Soft Delete).

- Usar o Vite (npm run build) para compilar o CSS. Isso transformaria aqueles 3MB de script em um arquivo .css de 10kb.
##### Fase 5: Robustez e Preparação para Deploy
*Finalização técnica para garantir que o sistema seja resiliente e fácil de manter.*

- Exclusão Lógica e Cascata: Testes de recuperação de dados e garantia de que, ao deletar uma ODS, seus itens de checklist sigam o comportamento de Soft Delete.

- Refinamento de Performance: Otimização de queries utilizando Eager Loading para evitar o problema de N+1 na hierarquia Secretaria > Categoria > ODS > Checklist.

- Documentação Técnica e Scripts: Finalização do script de setup para garantir que o deploy em ambientes de produção (como Railway ou servidores locais) seja plug-and-play.

##### Setup
Hardware: *i7 - 4º gen, 16 GB - ram (inspiron 5547).*
Software inicial: *Linux; Debian 13; Docker; Laravel, Laravel Sail, Breeze, Livewire.*