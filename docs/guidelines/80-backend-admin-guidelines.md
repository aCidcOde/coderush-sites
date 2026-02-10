# 80 – Backend Admin (`/backend`)

Este documento define as regras para criação e evolução do **painel administrativo** do Emergency, acessado sob o prefixo `/backend`.

Ele deve ser lido em conjunto com:

- `00-master-guideline.md` – visão geral do produto.
- `10-certidoes-catalogo.md` – catálogo de certidões (`certificate_types`).
- `20-pedidos-wizard.md` – pedidos e wizard.
- Demais arquivos de guideline já existentes.

---

## 1. Objetivo do Backend

- Disponibilizar um **painel administrativo** separado da área do cliente, em `/backend`.
- Reutilizar a mesma tabela de usuários (`users`) para autenticação.
- Permitir que um mesmo usuário tenha acesso:
  - ao **frontend** (fluxo normal de pedidos); e
  - ao **backend** (painel admin), **quando autorizado**.
- Primeira versão do backend deve expor:
  1. **Dashboard admin** simples.
  2. **Listagem de usuários** (somente leitura na V1).
  3. **CRUD do catálogo de certidões** (`certificate_types`).

---

## 2. Convenções gerais do backend

1. **Prefixo de rotas**
   - Todo o backend deve viver sob o prefixo:
     - `/backend/*`
   - Exceção: rotas globais de logout já providas pelo Fortify podem ser reutilizadas.

2. **Reaproveitamento de usuários**
   - A tabela `users` é **única** para toda a aplicação.
   - Não criar uma tabela separada para admins.
   - Um usuário pode ser:
     - **Somente cliente** (acessa apenas o frontend).
     - **Cliente + admin** (acessa frontend e backend).

3. **Flag de admin**
   - Adicionar um campo booleano em `users`:
     - `is_admin` – `boolean`, default `false`.
   - A lógica de autorização do backend deve se basear inicialmente nesse campo.
   - Futuras evoluções de permissão mais granular (roles, policies mais específicas) podem ser criadas sem quebrar essa base.

4. **Stack de UI**
   - Backend também usa:
     - Blade + Tailwind v4.
     - Livewire 3 + Volt.
     - Flux UI (componentes livres).
   - Antes de criar componentes novos, reutilizar layouts/componentes existentes sempre que possível.

---

## 3. Autenticação & autorização do backend

### 3.1 Guard e Fortify

- Utilizar **o mesmo guard `web`** para frontend e backend.
- O backend terá **uma tela de login própria**, mas o processo de autenticação continua sendo o mesmo (Fortify + guard `web`).
- Não criar, nesta fase, um segundo guard separado (como `admin`); a separação será puramente por **autorização** (`is_admin`) e prefixo de rotas.

### 3.2 Login separado do admin

- Rota de login do backend:
  - `GET /backend/login` – exibe tela de login administrativa.
  - Essa tela deve:
    - utilizar a mesma ação de login do Fortify (POST de e-mail/senha para a rota padrão de login);
    - ao autenticar com sucesso **e** o usuário sendo `is_admin = true`, redirecionar para `backend.dashboard`;
    - se o usuário não for admin, exibir mensagem adequada ou redirecionar para o frontend (fica a critério da implementação, mas deve ser consistente).

- O formulário de `/backend/login` deve:
  - usar o layout de autenticação padrão como base;
  - aplicar ajustes visuais (ver seção 5 – Layout) para deixar claro que é o **login do painel administrativo**.

### 3.3 Middleware / Gate de acesso ao backend

- Criar um **Gate** para acesso ao backend (em `AuthServiceProvider`):
  - nome sugerido: `access-backend`.
  - regra base: `User::is_admin === true`.

- Rotas do backend devem usar, **no mínimo**, o seguinte middleware:
  - `auth`
  - `verified` (se já estiver ativo no frontend)
  - `can:access-backend`

Exemplo conceitual de agrupamento de rotas (guia, não código definitivo):

```php
Route::prefix('backend')
    ->name('backend.')
    ->middleware(['auth', 'verified', 'can:access-backend'])
    ->group(function () {
        // rotas admin aqui
    });
```

---

## 4. URLs, nomes de rotas e navegação

### 4.1 Rotas principais do backend

- **Dashboard admin**
  - `GET /backend`  
    - Nome: `backend.dashboard`  
    - Função: visão geral de métricas/resumos administrativos (podemos começar simples: contagem de usuários, pedidos recentes, etc.).

- **Login admin**
  - `GET /backend/login`  
    - Nome: `backend.login`  
    - Função: exibir tela de login do admin.
  - O POST do login continua sendo o fornecido pelo Fortify (rota padrão `login`).

### 4.2 Módulo Usuários (V1 – somente leitura)

- `GET /backend/users`  
  - Nome: `backend.users.index`  
  - Função: listar usuários cadastrados.

- `GET /backend/users/{user}` (opcional na V1)  
  - Nome: `backend.users.show`  
  - Função: detalhar um usuário (dados básicos e, futuramente, pedidos associados).

> **Importante:**  
> Na primeira versão, **não** implementar criação/edição/remoção de usuários via backend. A página é focada em **consulta**.  
> Quando CRUD for implementado:
> - usar Form Requests para validação;
> - respeitar qualquer regra de segurança (não permitir trocas de e-mail sem verificação, etc.).

### 4.3 Módulo Catálogo de Certidões (`certificate_types`)

- `GET /backend/certificate-types`  
  - Nome: `backend.certificate-types.index`  
  - Função: listar todas as certidões do catálogo.

- `GET /backend/certificate-types/create`  
  - Nome: `backend.certificate-types.create`  
  - Função: exibir formulário para criar nova certidão no catálogo.

- `POST /backend/certificate-types`  
  - Nome: `backend.certificate-types.store`  
  - Função: persistir nova certidão.

- `GET /backend/certificate-types/{certificate_type}/edit`  
  - Nome: `backend.certificate-types.edit`  
  - Função: exibir formulário de edição.

- `PUT/PATCH /backend/certificate-types/{certificate_type}`  
  - Nome: `backend.certificate-types.update`  
  - Função: atualizar dados da certidão.

- (Opcional) `DELETE /backend/certificate-types/{certificate_type}`  
  - Nome: `backend.certificate-types.destroy`  
  - Função: desativar/apagar certidão.  
  - **Regra recomendada:** ao invés de apagar, preferir marcar `is_active = false`. A exclusão física só deve ocorrer se tivermos certeza de que não há referências em pedidos.

---

## 5. Layout e UX do painel admin

### 5.1 Diferenciação visual do admin

- O painel admin deve ser **claramente distinto** da área do cliente, mas ainda parecer parte do mesmo produto.
- Sugestões (a serem aplicadas no layout do backend):
  - Barra superior com cor de fundo diferente (por exemplo, tom mais escuro).
  - Inserir um rótulo / badge “Admin” próximo ao logo ou no título da página.
  - Menu lateral ou superior com seções administrativas (Usuários, Catálogo de certidões, Pedidos, etc.).
  - Título da página inicial: “Painel Administrativo – Emergency”.

### 5.2 Layout base

- Reutilizar o layout existente do dashboard como referência (container, tipografia, espaçamentos).
- Criar um **layout específico para o backend**, por exemplo:
  - `components/layouts/backend` (seguindo o padrão já existente de components/layouts).
- Esse layout deve:
  - incluir a navegação/admin menu;
  - exibir o usuário logado (nome e e-mail);
  - oferecer link claro de “Voltar para o site” ou “Ver painel do cliente”, se isso fizer sentido na UX.

### 5.3 Login admin

- O login de `/backend/login` deve:
  - ser baseado no layout de autenticação existente;
  - alterar título/subtítulo para algo como:
    - **Título:** “Acesso ao painel administrativo”
    - **Descrição:** “Use suas credenciais de administrador para entrar.”
  - destacar visualmente que é a área restrita de admin (cor da header, badge “Admin”, etc.).

---

## 6. Módulo Usuários – detalhes de comportamento (V1)

1. **Listagem**
   - Tabela com colunas mínimas:
     - ID
     - Nome
     - E-mail
     - Data de criação (`created_at`)
     - `is_admin` (sim/não, ícone ou badge).
   - Ordenação padrão:
     - `created_at` desc (mais recentes primeiro).
   - Paginação padrão (ex.: 20 por página).

2. **Busca / filtro (opcional, mas desejável)**
   - Campo de busca por nome ou e-mail.
   - Implementar com Livewire/Volt para busca reativa.

3. **Detalhe de usuário (quando implementado)**
   - Mostrar dados básicos:
     - nome, e-mail, data de cadastro;
     - flag `is_admin`;
   - Em futuras versões, pode incluir:
     - relação de pedidos;
     - saldo em carteira, etc.

4. **Edição futura**
   - Quando houver edição de usuário:
     - Proteger alteração de `is_admin` (somente admins já admins podem promover/outros admins);
     - Registrar auditoria, se necessário, para mudanças sensíveis.

---

## 7. Módulo Catálogo de Certidões – detalhes de comportamento

Regras abaixo complementam o documento `10-certidoes-catalogo.md`.

1. **Campos do formulário**
   - Usar os campos definidos na modelagem:
     - `code` (string, único)
     - `name` (string)
     - `provider` (string)
     - `description` (text, opcional)
     - `base_price` (decimal)
     - `is_active` (boolean)
   - Validar:
     - `code` obrigatório, único.
     - `name`, `provider`, `base_price` obrigatórios.
     - `base_price >= 0`.

2. **Listagem**
   - Tabela com colunas:
     - código (`code`)
     - nome (`name`)
     - órgão (`provider`)
     - preço base (`base_price`)
     - status (`is_active` – ativo/inativo)
   - Permitir filtrar por:
     - texto (nome/código);
     - status (ativo/inativo).

3. **Criação**
   - Ao criar uma nova certidão:
     - gravar `is_active = true` por padrão;
     - garantir unicidade de `code`.

4. **Edição**
   - `name`, `provider`, `description`, `base_price` e `is_active` são editáveis.
   - Política sugerida para `code`:
     - Se **ainda não houver** pedidos associados àquele `certificate_type`, o `code` pode ser editado.
     - Se **já houver** pedidos, preferir **bloquear edição de `code`** (somente leitura) para evitar inconsistências com sistemas externos ou integrações.

5. **Desativação vs exclusão**
   - Padrão: **não excluir fisicamente** certidões em uso.
   - Ao invés de `DELETE`, usar:
     - `is_active = false` para ocultar do catálogo no frontend e no wizard de novos pedidos.
   - Exclusão física só deve ser feita se tivermos certeza de que não há pedidos ou dependências.

---

## 8. Livewire / Volt e Flux UI no backend

- Páginas com interação (busca, filtros, paginação reativa) devem usar **Volt** + **Livewire 3**.
- Exemplos de uso no backend:
  - `backend.users.index` – componente Volt reativo para listagem/pesquisa de usuários.
  - `backend.certificate-types.index` – listagem com filtros reativos.
  - `backend.certificate-types.form` – formulário de criação/edição usando componentes Flux UI (`flux:input`, `flux:button`, etc.).

- Boas práticas:
  - Centralizar regras de validação em Form Requests ou, quando a convenção do projeto permitir, dentro do componente Volt, mas sempre com validação explícita.
  - Utilizar `wire:model.live` quando precisar de atualização em tempo real.
  - Reutilizar componentes Flux UI já existentes antes de criar novos.

---

## 9. Testes

- Toda funcionalidade nova de backend deve vir acompanhada de **tests PHPUnit**.
- Cobertura mínima desejada na V1:
  - Testes de acesso:
    - usuários não autenticados **não** acessam nenhuma rota `/backend/*` (redirecionar para login).
    - usuários autenticados, mas com `is_admin = false`, **não** acessam o backend.
    - usuários com `is_admin = true` acessam normalmente.
  - Testes de listagem:
    - `backend.users.index` exibe usuários esperados.
    - `backend.certificate-types.index` exibe itens do catálogo.
  - Testes de CRUD de `certificate_types`:
    - criação com dados válidos funciona;
    - validação de campos obrigatórios;
    - atualização de campos permitidos;
    - regra de desativação (`is_active`) funcionando.

- Usar `php artisan make:test --phpunit` para criar novos testes, seguindo a convenção de diretórios existente (por exemplo, `tests/Feature/Backend/UsersTest.php`, etc., se for compatível com a estrutura atual).

---

## 10. Evoluções futuras

Estas regras não precisam ser implementadas na V1, mas o backend deve ser pensado para comportá-las sem reestruturações grandes:

- Permissões mais granulares:
  - papéis diferentes (ex.: operador, financeiro, super admin);
  - policies específicas para cada módulo (users, orders, payments, etc.).
- Dashboard admin mais completo:
  - métricas de pedidos por período;
  - volume financeiro (pagamentos, saldo em carteira).
- Integração maior com o sistema legado, quando necessário, via módulo específico dentro do backend.

---

**Resumo:**  
O backend em `/backend` é um painel administrativo baseado no mesmo `User` da aplicação, com login visualmente separado, autorização via `is_admin` + `can:access-backend`, e primeiro escopo focado em **listagem de usuários** e **CRUD do catálogo de certidões**. Todas as implementações devem respeitar as convenções de Laravel 12, Fortify, Livewire/Volt, Flux UI e Tailwind v4 definidas nas guidelines principais do projeto.
