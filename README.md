# 🚀 CRM de Orcamentos em PHP

Sistema completo de CRM desenvolvido em PHP puro para gestao de clientes, produtos/servicos e geracao de orcamentos em PDF, com compartilhamento via WhatsApp e dashboard comercial com metricas de vendas.

Projeto pensado para hospedagem simples, incluindo estrutura compativel com Hostinger, banco MySQL e modo offline local para demonstracao e testes.

## ✨ Features

- 📋 Cadastro completo de clientes com historico de orcamentos
- 📦 Cadastro de produtos e servicos com preco de venda e custo interno
- 🧾 Criacao de orcamentos com multiplos itens e total automatico
- 📄 Geracao de PDF com `dompdf`
- 📲 Compartilhamento direto por WhatsApp com link do PDF
- ⚙️ Area de configuracoes da empresa com engrenagem no topo
- 🏢 Edicao de nome, documento, telefone, email, endereco e logo da empresa
- 📱 Diretorio de WhatsApp com todos os numeros cadastrados por cliente
- 📊 Dashboard com faturamento, lucro estimado, conversao, clientes e top clientes
- 📈 Graficos locais em JavaScript puro, sem dependencia externa para analytics
- 🔐 Login com sessao PHP
- 📁 Estrutura pronta para upload em `public_html`

## 🛠️ Tecnologias Utilizadas

- PHP
- MySQL
- HTML
- Tailwind CSS
- JavaScript
- dompdf

## 📁 Estrutura do Projeto

- `public_html/`: raiz da aplicacao para Hostinger
- `public_html/assets/`: CSS, JavaScript e imagens
- `public_html/controllers/`: controladores da aplicacao
- `public_html/models/`: regras de negocio e acesso a dados
- `public_html/views/`: telas do sistema
- `public_html/pdf/`: PDFs gerados
- `public_html/uploads/company/`: logos enviadas pela empresa
- `public_html/vendor/dompdf/`: biblioteca embarcada para PDF
- `database.sql`: script completo do banco
- `start-offline.ps1`: inicializacao do ambiente local
- `stop-offline.ps1`: encerramento do ambiente local

## 🧩 Modulos do Sistema

### 📊 Dashboard

- Indicadores de orcamentos criados, aprovados, enviados e recusados
- Receita aprovada
- Lucro estimado com base no custo dos itens
- Ticket medio
- Taxa de conversao
- Graficos de vendas e crescimento de clientes
- Ranking de melhores clientes

### 👥 Clientes

- Nome
- Email
- Telefone
- Empresa
- Observacoes
- Pesquisa por nome
- Historico de orcamentos por cliente

### 📦 Produtos e Servicos

- Nome
- Descricao
- Preco de venda
- Custo interno
- Margem estimada por item

### 🧾 Orcamentos

- Seleciona cliente
- Adiciona multiplos itens
- Calcula total automaticamente
- Permite observacoes adicionais
- Controle de status: Enviado, Aprovado e Recusado
- Gera PDF automaticamente
- Exibe botao de envio por WhatsApp

### ⚙️ Configuracoes da Empresa

- Nome da empresa
- Documento
- Telefone
- Email
- Endereco
- Upload de foto/logo
- Preview visual dos dados da empresa

### 📱 Diretorio de WhatsApp

- Lista nome do cliente com numero cadastrado
- Permite pesquisa por nome ou telefone
- Link direto para abrir conversa no WhatsApp
- Acesso rapido ao historico do cliente

## ⚙️ Instalacao Rapida

1. Importe o arquivo `database.sql` no MySQL.
2. Edite `public_html/config.php` com os dados do banco, URL do dominio e configuracoes basicas.
3. Envie o conteudo da pasta `public_html` para a pasta `public_html` da sua hospedagem.
4. Acesse o sistema pelo navegador.

## 💻 Uso Offline Local

1. Execute:

1. Execute:

```powershell
powershell -ExecutionPolicy Bypass -File .\start-offline.ps1
```

2. Abra:

```text
http://127.0.0.1:8000
```

3. Para encerrar:

```powershell
powershell -ExecutionPolicy Bypass -File .\stop-offline.ps1
```

## 🔐 Login Padrao

- Usuario: `admin`
- Senha: `admin123`

## 🎲 Banco de Dados

O projeto utiliza as tabelas:

- `users`
- `clients`
- `products`
- `quotes`
- `quote_items`
- `settings`

Tambem inclui suporte a:

- `cost_price` em produtos
- `cost_price` em itens do orcamento
- configuracoes persistentes da empresa via tabela `settings`

## 📌 Observacoes

- Os PDFs sao salvos em `public_html/pdf/orcamento-[id].pdf`
- O botao do WhatsApp usa `wa.me` com o link publico do PDF
- A biblioteca `dompdf` ja esta embarcada no projeto
- O sistema aplica bootstrap automatico de algumas estruturas do banco em tempo de execucao

## 🎯 Objetivo do Projeto

Entregar um CRM de orcamentos bonito, funcional e facil de hospedar, permitindo controlar clientes, produtos, propostas, documentos PDF e acompanhamento comercial em um unico painel.
