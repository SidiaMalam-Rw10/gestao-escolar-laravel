# 📚 Sistema de Gestão Escolar - Laravel

Sistema moderno de gestão escolar desenvolvido com Laravel 11, seguindo padrão MVC com design responsivo.

## ✨ Características Principais

### 🏗️ Arquitetura
- ✅ **Laravel 11** - Framework PHP moderno
- ✅ **Arquitetura MVC** - Separação clara de responsabilidades
- ✅ **Migrations** - Controle de versão do banco de dados
- ✅ **Eloquent ORM** - Relacionamentos entre entidades
- ✅ **Autenticação unificada** - Um único formulário de login para todos os usuários

### 👥 Sistema de Usuários Unificado
- ✅ **Tabela única `users`** com coluna `role` (função)
- ✅ Funções disponíveis:
  - **Admin** - Acesso total ao sistema
  - **Diretor** - Gestão acadêmica
  - **Financeiro** - Gestão de pagamentos e finanças
  - **Professor** - Gestão de turmas e notas
  - **Aluno** - Consulta de notas, horários e pagamentos

### 🎨 Design Moderno
- ✅ **Tailwind CSS** - Framework CSS utility-first
- ✅ **Alpine.js** - Interatividade leve
- ✅ **Font Awesome** - Ícones modernos
- ✅ **Responsivo** - Funciona em desktop, tablet e mobile
- ✅ **Dark Mode** - Modo escuro opcional
- ✅ **Sidebar dinâmico** - Menu lateral colapsável

### 📊 Funcionalidades por Função

#### Admin / Diretor
- Dashboard com estatísticas gerais
- Gestão de alunos, professores e turmas
- Visualização de pagamentos pendentes
- Sistema de mensagens e avisos

#### Financeiro
- Dashboard financeiro
- Registro de pagamentos
- Relatórios de receitas e despesas
- Gestão de folha salarial

#### Professor
- Visualização de horários
- Registro de presenças
- Lançamento de notas
- Turmas sob responsabilidade

#### Aluno
- Visualização de notas por trimestre
- Consulta de horários
- Histórico de pagamentos
- Avisos e notificações

## 🚀 Instalação

### Requisitos
- PHP 8.2 ou superior
- Composer
- MySQL 5.7 ou superior
- Node.js e NPM (opcional, para compilar assets)

### Passos

1. **Clone ou navegue até o projeto:**
```bash
cd gestao-escolar-laravel
```

2. **Instale as dependências:**
```bash
composer install
```

3. **Configure o arquivo .env:**
O arquivo já está configurado, mas verifique as credenciais do banco:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestao_escolar_laravel
DB_USERNAME=root
DB_PASSWORD=
```

4. **Gere a chave da aplicação:**
```bash
php artisan key:generate
```

5. **Crie o banco de dados:**
Crie um banco chamado `gestao_escolar_laravel` no MySQL.

6. **Execute as migrations e seeders:**
```bash
php artisan migrate:fresh --seed --seeder=InitialDataSeeder
```

7. **Inicie o servidor de desenvolvimento:**
```bash
php artisan serve
```

8. **Acesse o sistema:**
Abra o navegador e acesse: `http://localhost:8000`

## 🔐 Credenciais de Acesso

O sistema vem com usuários de teste já criados:

| Função | Usuário | Senha |
|--------|---------|-------|
| **Admin** | admin | admin123 |
| **Diretor** | diretor | diretor123 |
| **Financeiro** | financeiro | financeiro123 |
| **Professor** | prof.carlos | professor123 |
| **Aluno** | pedro.almeida | aluno123 |

## 📁 Estrutura do Banco de Dados

### Tabelas Principais

- **users** - Tabela unificada de usuários (Admin, Diretor, Financeiro, Professor, Aluno)
- **encarregados** - Responsáveis pelos alunos
- **turmas** - Classes escolares
- **notas** - Avaliações dos alunos
- **pagamentos** - Pagamentos mensais
- **presencas** - Controle de frequência
- **horarios** - Grade horária das turmas
- **avisos** - Sistema de notificações
- **contatos** - Mensagens do formulário de contato

## 🗂️ Estrutura de Diretórios

```
gestao-escolar-laravel/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       └── Admin/
│   │           └── AlunoController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Turma.php
│   │   ├── Nota.php
│   │   ├── Pagamento.php
│   │   └── ...
│   └── Providers/
│       └── AppServiceProvider.php (Gates de autorização)
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_09_04_104032_create_encarregados_table.php
│   │   ├── 2026_09_04_104046_create_turmas_table.php
│   │   └── ...
│   └── seeders/
│       └── InitialDataSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── auth/
│       │   └── login.blade.php
│       └── dashboard.blade.php
└── routes/
    └── web.php
```

## 🔧 Comandos Úteis

```bash
# Recriar banco de dados com dados de teste
php artisan migrate:fresh --seed --seeder=InitialDataSeeder

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Criar novo controller
php artisan make:controller NomeController

# Criar novo model
php artisan make:model NomeModel

# Criar nova migration
php artisan make:migration create_nome_table
```

## 📝 Próximos Passos

Para expandir o sistema, você pode:

1. **Implementar CRUD completo** para alunos, professores e turmas
2. **Sistema de notas** - Lançamento e consulta de notas
3. **Gestão de pagamentos** - Registro e geração de recibos
4. **Relatórios** - Gerar relatórios em PDF
5. **Sistema de avisos** - Notificações em tempo real
6. **Upload de documentos** - Gestão de arquivos

## 🐛 Troubleshooting

### Erro: "Base table or view not found"
Execute: `php artisan migrate:fresh`

### Erro: "SQLSTATE[HY000] [1045] Access denied"
Verifique as credenciais do banco no arquivo `.env`

### Página em branco após login
Execute: `php artisan cache:clear && php artisan config:clear`

## 📄 Licença

Este projeto foi desenvolvido como sistema educacional de gestão escolar.

## 👨‍💻 Suporte

Para suporte ou dúvidas, consulte a documentação do Laravel: https://laravel.com/docs

---

**Desenvolvido com ❤️ usando Laravel 11**
