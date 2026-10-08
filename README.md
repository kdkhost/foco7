# Sistema Foco 7 - Landing Page CMS & Painel Administrativo com Área do Cliente

Sistema integrado de alta performance composto por uma **Landing Page (Single Page)** gerenciável via CMS e um robusto **Painel Administrativo com Área do Cliente**, projetado especificamente para a empresa **Foco 7**, otimizado para ambientes cPanel com LiteSpeed e CloudLinux.

---

## 🚀 Arquitetura e Decisões Técnicas

| Componente | Tecnologia / Padrão Adotado | Justificativa Técnica |
| :--- | :--- | :--- |
| **Framework Backend** | Laravel (PHP 8.2+) | Estrutura moderna, segura, com suporte nativo a Eager Loading e MVC |
| **Banco de Dados** | MySQL 8.0+ / MariaDB | **100% livre de SQLite**, com índices e chaves estrangeiras otimizadas |
| **Frontend Site** | Single Page responsiva | Inspirada na estrutura e distribuição do `sosrastreamento.com` |
| **Identidade Visual** | Paleta Foco 7 (`#d90000`, tons escuros e neutros) | Extraída do portal institucional `foco7.com.br` |
| **Template do Painel** | AdminLTE 4 com Bootstrap 5 | Interface profissional, suporte nativo Dark/Light mode e Treeview |
| **Notificações** | Toastr (Global) | Notificações dinâmicas e não intrusivas para operações AJAX |
| **Confirmações** | SweetAlert2 (Global) | **Nenhum alert() nativo permitido** no sistema |
| **Editor de Conteúdo** | Summernote | Editor visual WYSIWYG completo em todas as textareas gerenciáveis |
| **Agendamento** | FullCalendar 6 | Gestão completa de instalações, suporte e visitas técnicas |
| **Gateways de Pagamento**| Efí PRO (Gerencianet) & Mercado Pago | Checkout Transparente (PIX, Cartão e Boleto) com Webhooks |
| **Ambiente de Hospedagem**| cPanel / LiteSpeed (`public_html`) | `.htaccess` raiz blindando arquivos sensíveis e roteando para `public/` |
| **Fuso Horário & Locale**| `America/Sao_Paulo` / `pt_BR` | Formatação estrita de Moeda (R$), CPF/CNPJ, CEP, Datas e Horas |

---

## 📁 Estrutura de Diretórios do Projeto (Diretamente em `public_html`)

```text
public_html/
├── .htaccess                      # Roteamento seguro e blindagem de segurança para cPanel
├── .env.example                   # Exemplo de configurações de ambiente (MySQL, Gateways)
├── artisan                        # CLI do Laravel para tarefas agendadas e manutenção
├── composer.json                  # Dependências PHP do projeto
├── package.json                   # Dependências Node.js/Vite e plugins frontend
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/             # Gestão de Conteúdo, SEO, Manutenção, Planos, Agendamentos
│   │   │   ├── Cliente/           # Área do Cliente (Faturas, Serviços, Agendamentos)
│   │   │   ├── Site/              # Landing Page pública, Formulários de Contato e Vendas
│   │   │   ├── Pagamento/         # Gateways Efí PRO e Mercado Pago + Webhooks
│   │   │   └── Instalador/        # Wizard Web amigável de instalação
│   │   ├── Middleware/
│   │   │   ├── ModoManutencao.php # Bloqueio avançado com bypass por IP e Dispositivo
│   │   │   └── ControleAcesso.php # ACL (Administrador vs Cliente)
│   │   └── Requests/              # Validações com máscaras brasileiras
│   ├── Models/                    # Models com relacionamentos e Eager Loading
│   └── Services/                  # Camada de serviços (SEO, Efí, Mercado Pago, Upload)
├── bootstrap/                     # Inicialização do framework
├── config/                        # Configurações do sistema (app, database, gateways, seo)
├── database/
│   ├── migrations/                # Estrutura do banco de dados MySQL
│   └── seeders/                   # Dados iniciais (Admin, Configurações padrão, Planos)
├── public/                        # Diretório público do Laravel
│   ├── .htaccess                  # Regras internas do Laravel
│   ├── index.php                  # Front Controller do Laravel
│   ├── css/                       # Estilos compilados e customizados do AdminLTE e Site
│   ├── js/
│   │   ├── global-ajax.js         # Interceptor global AJAX com Toastr e SweetAlert2
│   │   ├── global-mascaras.js     # Máscaras brasileiras (CPF/CNPJ, Moeda, Telefone, CEP)
│   │   ├── global-dropzone.js     # Upload drag & drop com validação de proporções e preview
│   │   └── seo-analyzer.js        # Analisador de pontuação SEO em tempo real
│   ├── img/                       # Logomarca oficial, banners, ícones
│   └── uploads/                   # Mídias gerenciáveis via CMS
├── resources/
│   └── views/
│       ├── site/                  # Landing page com WhatsApp flutuante e Voltar ao Topo
│       │   ├── index.blade.php
│       │   └── manutencao.blade.php
│       ├── admin/                 # Painel Administrativo AdminLTE 4
│       │   ├── layouts/
│       │   ├── dashboard.blade.php
│       │   ├── perfil/            # Gestão de perfil, avatar e alteração segura de senha
│       │   ├── configuracoes/     # Configurações institucionais e Servidor SMTP com teste AJAX
│       │   ├── gateways/          # Mercado Pago, Efí PRO e Regras Comerciais/Financeiras
│       │   ├── cms/               # Gestão de logos, banners, carrossel com foco-dropzone
│       │   ├── seo/               # Gestão de meta tags com SEO Score em tempo real
│       │   ├── agendamentos/      # FullCalendar 6
│       │   └── manutencao/        # Configuração do modo de manutenção
│       ├── cliente/               # Área do Cliente
│       │   ├── dashboard.blade.php
│       │   └── faturas.blade.php
│       ├── errors/                # Páginas de erro personalizadas (404, 500, 403, 419, 503)
│       └── instalador/            # Assistente web de configuração e banco MySQL
├── routes/
│   └── web.php                    # Rotas públicas, painel admin, cliente e instalador
└── storage/                       # Diretório de logs, sessões, certificados .pem e uploads
```

---

## 🛠️ Passo a Passo de Desenvolvimento dos Módulos

### Módulo 1: Infraestrutura e Instalador Web Amigável
- Configuração do `.htaccess` blindado para execução direta no `public_html`.
- Desenvolvimento do **Instalador Web Wizard** (`/instalar`):
  - Checagem automática dos requisitos do PHP (8.2+) e extensões (PDO MySQL, cURL, GD, Mbstring).
  - Formulário para inserção das credenciais do MySQL criadas no cPanel.
  - Execução automática de migrations e seeders.
  - Cadastro do Administrador master e bloqueio do instalador com arquivo de trava (`installed.lock`).

### Módulo 2: Landing Page Única (Inspirada no SOS Rastreamento)
- **Topo / Header Fixo:** Telefone, WhatsApp, Horário e botão de acesso à Área do Cliente.
- **Hero Section Dinâmico:** Carrossel de alta conversão, chamada para ação e CTAs duplos.
- **Problema vs Solução:** "O risco não avisa antes de chegar" com cards de riscos e soluções Foco 7.
- **Quem Somos:** Autoridade de mais de uma década no mercado e abrangência no Norte e Nordeste.
- **Planos e Preços:** Tabela comparativa e cards modernos para Carros, Motos, Frotas e Cargas.
- **Ronda Tática Foco 7 & Central 24h:** Fluxograma em 4 etapas ilustrando a velocidade de resposta.
- **Recursos do App e Plataforma:** Visualização interativa das funcionalidades de rastreamento.
- **6 Diferenciais:** Pilares de confiança da empresa.
- **Depoimentos & FAQ:** Prova social e perguntas frequentes com acordeão interativo.
- **Rodapé Institucional:** Links âncora, dados da empresa e selos de segurança.

### Módulo 3: Página de Manutenção Avançada
- Ativação/Desativação com 1 clique pelo CMS.
- Liberação automática por IP e por token/identificação de dispositivo.
- Temporizador em contagem regressiva dinâmico.
- Background personalizável com efeito blur e overlay de alta legibilidade.
- Links diretos para canais de atendimento e redes sociais.

### Módulo 4: Painel Administrativo CMS (AdminLTE 4)
- Adaptação visual completa do AdminLTE 4 à paleta oficial Foco 7.
- Alternância nativa de Modo Escuro (Dark) e Claro (Light) com persistência no navegador.
- Navbar fixa e Sidebar com marcação dinâmica da rota ativa.
- Notificações globais com Toastr e exclusões/ações críticas confirmadas via SweetAlert2.
- Summernote integrado em todas as textareas gerenciáveis.
- Gerenciador visual de conteúdo (Logo, Banners, Carrossel, Textos, FAQ, Telefones, Cores).

### Módulo 5: Sistema de Agendamento (FullCalendar 6)
- Calendário interativo para agendamento de instalações, vistorias e suporte.
- Suporte a filtros por status (Pendente, Confirmado, Concluído, Cancelado).
- Notificações de confirmação e integração com dados de clientes.

### Módulo 6: Módulo de SEO com Preview de Pontuação em Tempo Real
- Gestão de Meta Title, Meta Description, Meta Keywords e Tags Open Graph.
- **SEO Score Indicator:** Cálculo em tempo real com barra de progresso e dicas de otimização (comprimento de títulos, densidade de palavras-chave, legibilidade).
- Geração automática de Sitemap XML e Robots.txt.

### Módulo 7: Área do Cliente e Controle de Acesso (ACL)
- Autenticação unificada com separação de perfis: `Admin` e `Cliente`.
- Visualização de contratos ativos, veículos cadastrados e histórico financeiro.
- Emissão de 2ª via de faturas (PIX / Boleto / Cartão).

### Módulo 8: Gateways de Pagamento (Efí PRO & Mercado Pago)
- Checkout Transparente sem redirecionamento para fora do site.
- Suporte a PIX com cópia e cola e QR Code dinâmico, Boleto Bancário e Cartão de Crédito.
- Webhooks dinâmicos com logs e validação de segurança para retorno automático de status.

---

## ✅ Status de Implementação dos Módulos

| Módulo | Descrição | Status |
| :--- | :--- | :--- |
| **Módulo 1** | Infraestrutura, `.htaccess` blindado e Instalador Web Wizard (`/instalar`) | ✅ Concluído & Validado |
| **Módulo 2** | Landing Page Single Page de Alta Conversão (`sosrastreamento.com` + Foco 7) | ✅ Concluído & Validado |
| **Módulo 3** | Página de Manutenção Avançada com Countdown, bypass de IP e dispositivo | ✅ Concluído & Validado |
| **Módulo 4** | Painel Administrativo CMS (AdminLTE 4, Dark/Light mode, Toastr, SweetAlert2) | ✅ Concluído & Validado |
| **Módulo 5** | Sistema de Agendamento Interativo (FullCalendar 6 com Drag & Drop) | ✅ Concluído & Validado |
| **Módulo 6** | Motor de SEO com Preview do Google, Score em Tempo Real e Sitemap XML | ✅ Concluído & Validado |
| **Módulo 7** | Área do Cliente com Controle de Acesso (ACL) e Emissão de Faturas | ✅ Concluído & Validado |
| **Módulo 8** | Gateways de Pagamento (Mercado Pago e Efí PRO) com Webhooks | ✅ Concluído & Validado |

---

## 🧪 Testes Automatizados e Garantia de Qualidade

A aplicação conta com uma suíte de testes de integração automatizados (`tests/Feature/Foco7SystemTest.php` e `ExampleTest.php`):
- **11 testes executados, 11 aprovados (100% de sucesso), 25 asserções**.
- Verificação de carregamento de rotas, isolamento de perfis ACL (Administrador vs Cliente), validação do modo de manutenção com bypass por token e submissões AJAX de formulários.

---

## 🔒 Segurança e Otimização para cPanel
- **Bloqueio de Acesso Público:** Diretórios e arquivos sensíveis (`.env`, `.git`, `app/`, `vendor/`, `artisan`) bloqueados no `.htaccess`.
- **Prevenção de N+1 e Sobrecarga:** Todas as consultas com Eager Loading (`with()`) e índices nas tabelas do MySQL.
- **UTF-8 Sem BOM:** Arquivos salvos rigorosamente em UTF-8 sem Byte Order Mark.
- **Timezone:** `America/Sao_Paulo`.

