# Sistema Foco 7 - Landing Page CMS & Painel Administrativo com Área do Cliente

Sistema integrado de alta performance composto por uma **Landing Page (Single Page)** gerenciável via CMS e um robusto **Painel Administrativo com Área do Cliente**, projetado especificamente para a empresa **Foco 7**, otimizado para ambientes cPanel com LiteSpeed e CloudLinux.

---

## 🚀 Arquitetura e Decisões Técnicas

| Componente | Tecnologia / Padrão Adotado | Justificativa Técnica |
| :--- | :--- | :--- |
| **Framework Backend** | Laravel (PHP 8.2+) | Estrutura moderna, segura, com suporte nativo a Eager Loading e MVC |
| **Banco de Dados** | MySQL 8.0+ / MariaDB | **100% livre de SQLite**, com índices e chaves estrangeiras otimizadas |
| **Frontend Site** | Single Page responsiva | Inspirada na estrutura e distribuição do `sosrastreamento.com` |
| **Identidade Visual** | Paleta Oficial Foco 7 (`#d90000`, `#25d366` e tema claro `#f8fafc`) | Design system limpo, navbar glassmorphism e tipografia Outfit/Roboto |
| **Template do Painel** | AdminLTE 4 com Bootstrap 5 | Interface profissional, suporte nativo Dark/Light mode e Treeview |
| **Notificações** | Toastr (Global) | Notificações dinâmicas e não intrusivas para operações AJAX |
| **Confirmações** | SweetAlert2 (Global) | **Nenhum alert() nativo permitido** no sistema |
| **Editor de Conteúdo** | Summernote | Editor visual WYSIWYG completo em todas as textareas gerenciáveis |
| **Agendamento** | FullCalendar 6 | Gestão completa de instalações, suporte e visitas técnicas |
| **Gateways de Pagamento**| Efí PRO (Gerencianet) & Mercado Pago | Checkout Transparente (PIX, Cartão e Boleto) com Webhooks |
| **Uploads de Arquivos** | FocoDropzone (Arrasta e Solta Global) | **100% dos uploads via drag & drop** com preview imediato, specs de proporção e remoção AJAX |
| **Central de E-mails**  | EmailTemplateService + Páginas Dedicadas | **Rota única obrigatória**, botões fluidos responsivos, simulador multi-dispositivo ao vivo e SMTP exclusivo (`notificacoes@foco7.com.br`) |
| **Central de Cron**     | Agendador Laravel + Crontab cPanel | Linha mestra (`schedule:run`) ativa a cada minuto com **gestão dinâmica de tarefas (CRUD)**, 5 rotinas nativas cadastradas, monitoramento por batimento cardíaco (*Heartbeat*), botões animados com micro-interações e terminal de logs ao vivo |
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
- **Hero Section Dinâmico:** Carrossel de alta conversão com controle de imagem e opacidade do plano de fundo pelo painel CMS (0% a 100%), preview em tempo real, chamada para ação e CTAs duplos.
- **Problema vs Solução:** "O risco não avisa antes de chegar" com cards de riscos e soluções Foco 7.
- **Quem Somos:** Autoridade de mais de uma década no mercado e abrangência no Norte e Nordeste.
- **Planos e Preços:** Cards modernos de alta conversão para Carros, Motos, Frotas e Cargas com checados na cor oficial Foco 7 e checkout transparente integrado.
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
- Summernote WYSIWYG oficial (`v0.8.20-bs5`) com tradução pt-BR, suporte nativo a Dark/Light Mode, toolbar completa e sincronização automática AJAX em todas as textareas gerenciáveis (História da Empresa, Ronda Tática, Aplicativo Mobile, Estética, FAQs, Depoimentos e Banners).
- Hero Carousel com película gradiente cinematográfica escura (`#0b0f19`), controle de opacidade e layout em tela cheia (100% full-bleed).
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

### Módulo 8: Gateways de Pagamento, Carnê Efí, E-mails & Central de Cron (`/admin/gateways`)
- **Organização Modular por Abas:** Guias dedicadas para Mercado Pago, Efí PRO (Gerencianet), Cobrança por Carnê, Templates de E-mail e Central de Cron.
- **Seleção Dinâmica Sandbox vs Produção:** Alternância dinâmica que exibe e armazena chaves e certificados separados para testes e produção com retrocompatibilidade automática.
- **Cobrança por Carnê Efí PRO para Clientes Existentes:** Emissão parcelada de 2x a 24x sob demanda para clientes cadastrados, com geração sequencial de faturas mensais e notificações.
- **Templates de E-mails Próprios:** 5 modelos transacionais customizáveis com Summernote, tags dinâmicas (`{cliente_nome}`, `{valor}`, `{data_vencimento}`) e disparo de e-mail de teste.
- **Central de Cron & Automação:** Comandos Artisan dedicados (`faturas:verificar-vencidas`, `faturas:enviar-lembretes`, `gateways:sincronizar-pagamentos`), monitoramento de agendamento e execução imediata sob demanda via AJAX com SweetAlert2.
- **Checkout Transparente:** Suporte a PIX com cópia e cola / QR Code dinâmico, Boleto Bancário e Cartão de Crédito.
- **Webhooks Oficiais:** Retorno automático de compensação e atualização instantânea de faturas.

### Módulo 9: Estética Automotiva & Detalhamento Premium
- **Landing Page Pública:** Nova seção elegante e de alta conversão apresentando os serviços de estética (Polimento Técnico, Vitrificação Cerâmica 9H, Higienização Interna, Lavagem Detalhada, Cristalização e PPF).
- **Agendamento Online no Site:** Modal inteligente multi-categoria permitindo aos clientes agendarem tanto serviços de estética quanto instalações de rastreamento.
- **Painel Administrativo (`/admin/estetica`):** CRUD completo com gerenciamento de preços, tempos de aplicação, ícones, descrições detalhadas, itens inclusos e ativação/desativação instantânea via AJAX.
- **FullCalendar 6 Integrado:** Identificação visual automática por cores (Grafite Nobre `#1e293b` para estética, Verde `#28a745` para rastreamento) com edição e reagendamento via arrastar e soltar (drag & drop).
- **Área do Cliente:** O cliente pode solicitar serviços de estética diretamente pelo seu painel, com acompanhamento de status em tempo real.

### Módulo 10: Gestão Completa de Clientes & Faturas/Financeiro
- **Base de Clientes (`/admin/clientes`):** Painel administrativo completo para listar, filtrar, cadastrar e editar dados cadastrais, redefinir senhas de acesso e gerenciar status ativo/inativo.
- **Faturas & Mensalidades (`/admin/faturas`):** Controle financeiro com métricas em tempo real (Total Faturado, Pendente, Vencido), emissão de faturas avulsas ou atreladas a planos, e alteração rápida de status para quitação.

### Módulo 11: Edição Avançada do CMS & Textos das Seções
- **Edição Dinâmica via Modal:** Recursos completos para editar banners/slides existentes, perguntas frequentes (FAQs) e depoimentos de clientes.
- **Aba de Textos das Seções:** Controle total sobre os títulos, subtítulos e textos das seções Sobre Nós, Riscos vs Prevenção, Ronda Tática, Recursos do App e Estética Automotiva.

### Módulo 12: Auditoria de Fluxos: CSRF Webhooks, Retenção de Leads e Notificações Automáticas
- Blindagem e validação dos endpoints de webhooks e captura de leads.

### Módulo 13: LGPD, Termos de Privacidade, Segurança Avançada e Recuperação de Senha
- **Painel CMS de Privacidade e LGPD (`/admin/cms`):** Aba dedicada para gerenciar a política de privacidade completa, texto resumido e status do banner flutuante no rodapé público.
- **Página Pública de Privacidade (`/privacidade`):** Minuta jurídica sob a LGPD (Lei Federal nº 13.709/2018), responsiva, com links rápidos e botão nativo para impressão/download.
- **Banner Flutuante LGPD:** Notificação moderna no rodapé com aceite salvo localmente em `localStorage` e botão para leitura das diretrizes.
- **Segurança HTTP Estrita (`SecurityHeaders`):** Middleware global com cabeçalhos `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection: 1; mode=block`, `Referrer-Policy: strict-origin-when-cross-origin` e `Permissions-Policy`.
- **Proteção contra Força Bruta e Invasão:** Rate limiting estrito no login (`throttle:5,1`) e sanitização de requisições.
- **Fluxo Completo de Recuperação de Senha:** Telas modernas "Esqueci minha senha" e "Redefinir senha", envio de token seguro com validade de 60 minutos através do `EmailTemplateService` e redefinição instantânea via AJAX com SweetAlert2.

### Módulo 14: Central Multicanal WhatsApp, Avaliação Interativa de Estrelas & Agendamento com Pagamento Flexível
- **Botão Flutuante do WhatsApp na Paleta Foco 7 (`#d90000`):** Efeito pulsante suave e abertura de pop-up inteligente de suporte multicanal.
- **Box Multicanal Configurável:** 5 canais pré-definidos (Ronda 24h, Comercial, Suporte Técnico, Estética Automotiva e Financeiro) com controle individual de exibição (ligar/desligar), números de telefone e mensagens automáticas personalizadas via CMS.
- **Menu Hambúrguer Estilo App Android:** Abertura deslizante da esquerda para a direita, suporte tátil 44x44px e acionamento infalível no mobile e desktop.
- **Avaliação de Atendimento com Estrelas Dinâmicas:** Efeito visual ao passar o mouse acendendo estrelas parcial e integralmente (1 a 5), seleção ao clique e modal público com envio AJAX e moderação no CMS.
- **Autenticação Obrigatória no Agendamento:** Visitantes anônimos são convidados a realizar login ou cadastro rápido em modal flutuante sem perder nenhum dado preenchido no agendamento.
- **Seleção de Pagamento Flexível:** Opção no agendamento entre "Pagamento Online (PIX / Cartão)" e "Pagar no Local (Instalação / Presencial)", sincronizado com o banco de dados e visível no painel administrativo.

### Módulo 15: Central de Notificações, Web Push no Celular & Alertas por E-mail
- **Fluxo Unificado de Notificações:** Todas as mensagens do formulário de contato, solicitações de agendamento online e novos depoimentos com avaliação em estrelas são automaticamente encaminhados para a tabela `notificacoes`.
- **Alertas por E-mail Transacional:** Disparo imediato com o `EmailTemplateService` e SMTP autenticado para o e-mail do administrador, com tabelas de detalhes e botões de ação direta.
- **Web Push Notifications no Celular e Desktop:** Integração com a API de Notificações do navegador (`Notification API`), permitindo ao administrador autorizar o recebimento de alertas sonoros (sintetizador Web Audio API) e pop-ups nativos no celular mesmo em segundo plano.
- **Sininho Dinâmico na Navbar:** Badge contador de não lidas com atualização em tempo real (*polling* inteligente a cada 25s), lista suspensa rápida e acesso com um clique.
- **Painel Administrativo da Central (`/admin/notificacoes`):** Métricas consolidadas, filtros por categoria (Contatos, Agendamentos, Depoimentos, Sistema), marcação individual e em lote como lida, exclusão e botão de teste imediato de Push & E-mail.

### Módulo 16: Harmonização da Identidade Visual CMS (Cores & Tema) & Alto Contraste de Alertas Warning
- **Harmonização Global da Paleta Oficial Foco 7 (`#d90000`):** Eliminação completa de elementos residuais em azul Bootstrap em todo o sistema. Interruptores (*switches*), checkboxes, botões de rádio, links ativos, abas de navegação (*nav-tabs*), botões primários e ícones agora sincronizam dinamicamente com as cores configuradas no CMS (`cor_primaria`, `cor_secundaria` e `cor_destaque`).
- **Alto Contraste e Legibilidade Perfeita em Alertas Amarelos (`alert-warning`):** Correção de contraste tanto no modo claro (fundo pastel âmbar suave com texto âmbar escuro `#78350f` e títulos em negrito) quanto no modo escuro (fundo âmbar translúcido com texto creme brilhante `#fef3c7`, títulos brancos e ícones dourados `#fbbf24`), eliminando o problema de texto escuro sobre fundo escuro no painel de Gateways e Perfil.
- **Sincronização Dinâmica em Todas as Camadas:** O `AppServiceProvider` injeta variáveis CSS computadas (`--admin-primary`, `--bs-primary`, `--bs-primary-rgb`, `--foco-red`) nas áreas administrativa, do cliente e no site público, garantindo consistência total da marca.

---

## ✅ Status de Implementação dos Módulos

| Módulo | Descrição | Status |
| :--- | :--- | :--- |
| **Módulo 1** | Infraestrutura, `.htaccess` blindado e Instalador Web Wizard (`/instalar`) | ✅ Concluído & Validado |
| **Módulo 2** | Landing Page Single Page de Alta Conversão (`sosrastreamento.com` + Foco 7) | ✅ Concluído & Validado |
| **Módulo 3** | Página de Manutenção Avançada com Countdown, bypass de IP e dispositivo | ✅ Concluído & Validado |
| **Módulo 4** | Painel Administrativo CMS (AdminLTE 4, Dark/Light mode, Toastr, SweetAlert2) | ✅ Concluído & Validado |
| **Módulo 5** | Sistema de Agendamento Interativo (FullCalendar 6 com Drag & Drop e Categorias) | ✅ Concluído & Validado |
| **Módulo 6** | Motor de SEO com Preview do Google, Score em Tempo Real e Sitemap XML | ✅ Concluído & Validado |
| **Módulo 7** | Área do Cliente com Controle de Acesso (ACL) e Emissão de Faturas | ✅ Concluído & Validado |
| **Módulo 8** | Gateways de Pagamento (Mercado Pago e Efí PRO) com Webhooks | ✅ Concluído & Validado |
| **Módulo 9** | Estética Automotiva & Agendamento Multi-categoria (Site, Admin e Cliente) | ✅ Concluído & Validado |
| **Módulo 10**| Gestão Completa de Clientes & Faturas/Financeiro no Admin | ✅ Concluído & Validado |
| **Módulo 11**| Edição Avançada de Banners, FAQs, Depoimentos e Textos de Seções no CMS | ✅ Concluído & Validado |
| **Módulo 12**| Auditoria de Fluxos: CSRF Webhooks, Retenção de Leads e Notificações Automáticas | ✅ Concluído & Validado |
| **Módulo 13**| LGPD, Termos de Privacidade, Segurança Avançada e Recuperação de Senha | ✅ Concluído & Validado |
| **Módulo 14**| WhatsApp Multicanal CMS, Estrelas Dinâmicas, Auth no Agendamento & Pagamento | ✅ Concluído & Validado |
| **Módulo 15**| Central de Notificações, Web Push no Celular e E-mails Transacionais para o Admin | ✅ Concluído & Validado |
| **Módulo 16**| Harmonização da Paleta Oficial CMS (`#d90000`) & Alto Contraste de Alertas Warning | ✅ Concluído & Validado |

---

## 🧪 Testes Automatizados e Garantia de Qualidade

A aplicação conta com uma suíte de testes de integração automatizados (`tests/Feature/`):
- **54 testes executados, 54 aprovados (100% de sucesso), 234 asserções**.
- Cobertura de rotas públicas, fluxo de agendamento online com autenticação obrigatória e tipo de pagamento, formulário de contato persistido como lead e encaminhado à Central de Notificações, alertas push e transacionais por e-mail, webhooks isentos de CSRF, templates de e-mail automatizados, CRUD de estética, clientes e faturas, edição no CMS, sincronização da paleta visual oficial Foco 7, cabeçalhos de segurança HTTP, recuperação de senha, sanitização de acentuação/encoding UTF-8 e isolamento ACL (Administrador vs Cliente).

---

## 🔒 Segurança e Otimização para cPanel
- **Cabeçalhos de Segurança Estritos:** Middleware `SecurityHeaders` injeta proteção contra Clickjacking (`X-Frame-Options: SAMEORIGIN`), MIME-Sniffing (`X-Content-Type-Options: nosniff`) e XSS (`X-XSS-Protection`).
- **Proteção contra Força Bruta:** Limitação de taxa de requisições (*Rate Limiting*) em rotas de login e autenticação sensíveis.
- **Isenção de CSRF Segura:** Rotas `/webhook/*` isentas de CSRF para integração com APIs externas (Mercado Pago e Efí PRO).
- **Bloqueio de Acesso Público:** Diretórios e arquivos sensíveis (`.env`, `.git`, `app/`, `vendor/`, `artisan`) bloqueados no `.htaccess`.
- **Prevenção de N+1 e Sobrecarga:** Todas as consultas com Eager Loading (`with()`) e índices nas tabelas do MySQL.
- **UTF-8 Estrito Sem BOM & Sanitização de Acentos:** 100% dos arquivos do projeto codificados estritamente em UTF-8 sem Byte Order Mark (BOM). Rotina e comando de auditoria automatizada (`php artisan sistema:corrigir-encoding`) que previne e repara quaisquer ocorrências de mojibake nas tabelas do banco de dados MySQL.
- **Timezone:** `America/Sao_Paulo`.


