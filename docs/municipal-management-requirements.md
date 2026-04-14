##### Visão Geral

O sistema tem como objetivo organizar e rastrear demandas de diferentes secretarias municipais, permitindo a classificação por categorias, definição de urgências, controle de prazos e o detalhamento de etapas através de checklists.
##### Requisitos Funcionais (RF)
*Os requisitos funcionais descrevem as funcionalidades diretas que o sistema oferece ao usuário.*

RF01 - Gestão de Secretarias: O sistema deve permitir o cadastro de secretarias (ex: Saúde, Obras), que servem como a entidade principal para a organização dos dados.

RF02 - Classificação por Categorias: Cada secretaria deve possuir categorias específicas (ex: Iluminação, Limpeza) para classificar as ordens de serviço.

RF03 - Gerenciamento de Ordens de Serviço (ODS):

- O usuário deve ser capaz de criar uma ODS com título, descrição e localização.

- Cada ODS deve gerar um código único de identificação (ODS-001, ODS-002, etc).

- O sistema deve permitir vincular uma ODS a um usuário responsável (requisito para ser atendido no futuro, não agora).

RF04 - Controle de Status e Prioridade: O sistema deve permitir marcar ordens como urgentes e rastrear seu status (pendente, andamento ou concluído), iniciando por padrão como "pendente".

RF05 - Agendamento de Prazos: Deve ser possível atribuir uma data de vencimento (due_date) para a conclusão da demanda.

RF06 - Autenticação de Usuários: O acesso ao painel principal (dashboard) e ao perfil do usuário é restrito a usuários autenticados e com e-mail verificado.

RF07 - Exclusão Lógica (Soft Delete): As ordens de serviço não devem ser removidas permanentemente do banco de dados ao serem deletadas, permitindo recuperação posterior.

RF08 - Checklists de Verificação: - Cada ODS deve permitir a criação de múltiplos itens de checklist para detalhar as etapas da execução.

- O sistema deve permitir marcar/desmarcar cada item individualmente como concluído.

- A ODS deve exibir o progresso percentual de conclusão baseado nos itens do checklist.
##### Requisitos Não Funcionais (RNF)

*Os requisitos não funcionais especificam critérios de operação e tecnologias que garantem a qualidade do software.*

RNF01 - Stack Tecnológica Moderna: O sistema deve ser executado no PHP 8.4 e Laravel 12, aproveitando as melhorias de performance e segurança das versões mais recentes.

RNF02 - Interface Reativa: Interface dinâmica e sem recarregamentos de página (SPA-like), especialmente na interação com os itens de checklist.

RNF03 - Integridade de Dados: O banco de dados deve utilizar restrições de chave estrangeira com exclusão em cascata para garantir que categorias, ordens de serviço e itens de checklist sejam tratados corretamente caso uma secretaria ou ODS seja removida.

RNF04 - Segurança de Acesso: Proteção de rotas via middlewares nativos do Laravel (auth e verified) para garantir que apenas pessoas autorizadas acessem os dados municipais.

RNF05 - Facilidade de Deploy: O projeto deve incluir um script de setup no composer.json que automatiza a instalação de dependências, geração de chaves e migrações.

---
##### Estrutura de Dados Resumida

A arquitetura do banco de dados segue uma hierarquia de quatro níveis principais:

**1 - Secretariats (Pai):** Entidade de nível superior.

**2 - Categories (Filho):** Pertencem a uma secretaria e categorizam o serviço.

**3 - Service Orders (Neto):** A demanda em si, vinculada a uma secretaria e, opcionalmente, a uma categoria e um usuário.

**4 - Checklist Items (Bisneto):** Itens operacionais vinculados a uma Ordem de Serviço específica.