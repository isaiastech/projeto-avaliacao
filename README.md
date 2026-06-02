# Sistema de Avaliação de Desempenho

## Sobre o Projeto

O Sistema de Avaliação de Desempenho é uma aplicação web desenvolvida em PHP para gerenciamento de avaliações mensais de colaboradores.

O sistema permite que avaliadores realizem avaliações de colaboradores através de questionários personalizados, gerando rankings, relatórios e indicadores de desempenho.

---

## Funcionalidades

### Autenticação

* Login de usuários
* Controle de sessão
* Logout seguro
* Controle de acesso por nível

### Níveis de Acesso

#### Administrador

* Gerenciar usuários
* Gerenciar questões
* Visualizar relatórios
* Visualizar ranking geral
* Acompanhar progresso das avaliações
* Gerenciar avaliadores

#### Gerente

* Visualizar relatórios
* Visualizar ranking
* Acompanhar avaliações

#### Avaliador

* Avaliar colaboradores
* Visualizar status das avaliações realizadas
* Consultar avaliações pendentes

#### Usuário

* Acesso restrito
* Recebimento de avaliações

---

## Regras de Avaliação

### Avaliador

Pode avaliar:

* Usuários (`user`)
* Outros avaliadores (`avaliador`)

Não pode avaliar:

* Gerentes (`gerente`)
* Administradores (`admin`)
* A si próprio

### Gerente e Administrador

Podem visualizar relatórios e rankings.

---

## Funcionalidades de Avaliação

* Avaliação mensal
* Bloqueio de avaliações duplicadas no mesmo mês
* Validação de todas as questões obrigatórias
* Validação das notas
* Impedimento de autoavaliação
* Controle de usuários ativos

---

## Relatórios

### Ranking Mensal

Exibe:

* Posição dos colaboradores
* Média das avaliações
* Classificação automática

Classificações:

| Média       | Classificação |
| ----------- | ------------- |
| 4.50 a 5.00 | Excelente     |
| 3.50 a 4.49 | Bom           |
| 2.50 a 3.49 | Regular       |
| 0.00 a 2.49 | A Melhorar    |

### Relatório Individual

Permite visualizar:

* Média geral
* Notas por questão
* Histórico mensal

### Controle de Avaliadores

Exibe:

* Total de avaliadores
* Quantos concluíram as avaliações
* Quantos estão pendentes
* Lista de avaliadores concluídos
* Lista de avaliadores pendentes
* Barra de progresso

---

## Estrutura do Banco de Dados

### usuarios

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255),
    email VARCHAR(255),
    senha VARCHAR(255),
    nivel ENUM(
        'admin',
        'gerente',
        'avaliador',
        'user'
    ),
    ativo TINYINT(1) DEFAULT 1
);
```

### questoes

```sql
CREATE TABLE questoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questoes TEXT NOT NULL
);
```

### avaliacoes

```sql
CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    avaliador_id INT NOT NULL,
    avaliado_id INT NOT NULL,
    mes INT NOT NULL,
    ano INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### avaliacao_notas

```sql
CREATE TABLE avaliacao_notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    avaliacao_id INT NOT NULL,
    questao_id INT NOT NULL,
    nota INT NOT NULL
);
```

---

## Tecnologias Utilizadas

### Backend

* PHP 8+
* Composer
* MySQL 8

### Frontend

* HTML5
* CSS3
* Bootstrap 4 e Bootstrap 5
* JavaScript
* jQuery

### Banco de Dados

* MySQL

---

## Instalação

### Clonar Repositório

```bash
git clone https://github.com/seu-usuario/sistema-avaliacao.git
```

### Instalar Dependências

```bash
composer install
```

### Configurar Banco

Criar banco:

```sql
CREATE DATABASE sistema_avaliacao;
```

Importar estrutura SQL.

### Configurar Ambiente

Criar arquivo:

```text
config/.env
```

Exemplo:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=sistema_avaliacao
DB_USER=root
DB_PASS=senha
```

---

## Executar Projeto

Servidor Apache:

```text
http://localhost
```

ou utilizando servidor embutido:

```bash
php -S localhost:8000
```

---

## Segurança Implementada

* Sessões autenticadas
* Controle de permissões
* Prepared Statements
* Validação de formulários
* Proteção contra avaliações duplicadas
* Validação de usuários ativos
* Controle de níveis de acesso
* Hash de senhas com password_hash()

---

## Funcionalidades Futuras

* Perfil do usuário
* Alteração de senha
* Alteração de e-mail
* Histórico completo de avaliações
* Exportação para Excel
* Exportação para PDF
* Dashboard com gráficos
* Notificações por e-mail
* API REST
* Integração com Supabase

---

## Autor

**Isaias Batista**

Sistema desenvolvido para gestão e acompanhamento de avaliações de desempenho corporativo.
