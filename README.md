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
| **Template do Painel** | AdminLTE 4 com Bootstrap 5 | Interface profissional oficial, suporte nativo Dark/Light mode e Treeview |
| **Design System & Temas**| Padrão Oficial AdminLTE 4 & CMS | Superfícies 100% nativas do padrão oficial Dark/Light do AdminLTE 4 (Bootstrap 5.3), aplicando a paleta configurada pelo administrativo exclusivamente nas marcações e detalhes de identidade (sidebar ativa, botões, abas, badges, switches e foco) |
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
- **Planos e Preços:** Cards modernos de alta conversão para Carros, Motos, Frotas e Cargas com checados na cor oficial Foco 7, botões inferiores em linha única protegidos contra quebra (`white-space: nowrap`), texto de botão configurável por plano e checkout transparente integrado.
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
- **Gestão de Planos & Preços (`/admin/planos`):** CRUD completo com seleção intuitiva de ícones via `<select>` dinâmico com preview ao vivo (dispensando digitação manual de classes), personalização individual do texto do botão de contratação direto no plano, mensalidades, taxas de adesão, badges de destaque e listagem de recursos.

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

### Módulo 17: Checkout Transparente Efí Bank (Gerencianet), Emissão de Carnê em PDF & Repasse de Taxas
- **Isolamento Absoluto do Mercado Pago:** O gateway Mercado Pago foi integralmente preservado em sua estrutura original sem qualquer interferência ou quebra de fluxo.
- **Efí Bank Totalmente Funcional:** Suporte nativo à API oficial Efí Bank (Gerencianet) para PIX instantâneo com mTLS (certificado `.pem`) e fallback BACEN EMVCo com CRC16 válido, Boletos registrados com linha digitável e Cartão de crédito transparente.
- **Emissão de Carnê de Mensalidades em PDF (A4):** Geração de carnês profissionais parcelados via `barryvdh/laravel-dompdf`, incluindo capa institucional, resumo geral de parcelas, canhotos destacáveis do sacado e ficha de compensação com QR Code Pix em cada lâmina.
- **Repasse de Taxas Operacionais & Tarifas Bancárias:** Parametrização dinâmica no painel administrativo (`/admin/gateways`) para repassar custos ao cliente de forma transparente. Cálculo em tempo real no checkout via AJAX (`/checkout/calcular-taxas`) discriminando subtotal do plano, custos operacionais e desconto à vista.
### Módulo 18: Sidebar Treeview Responsivo, Rolagem Fluida Mobile/Tablet & Central de Notificações Dedicada
- **Sidebar com Treeview & Agrupamento Lógico de Submenus:** Reestruturação visual completa da barra lateral com organização hierárquica por categorias (Visão Geral, Atendimentos & Serviços, Financeiro & Assinantes, Gestão de Conteúdo CMS, Comunicação & Mensagens e Sistema & Operações), rotação fluida da seta indicadora e expansão automática da seção da página ativa.
- **Rolagem Vertical Suave em Dispositivos Móveis e Tablets:** Ajuste estrito de CSS com altura adaptativa viewport (`100dvh`), inércia touch nativa (`-webkit-overflow-scrolling: touch; touch-action: pan-y; overscroll-behavior: contain`), espaçamento inferior de segurança (`padding-bottom: 90px`) e botão de fechamento rápido ("X") na barra superior para telas menores que 992px.
- **Central de Notificações com Marcação Automática no Clique & Decremento Visual:** Interceptador AJAX no dropdown do sino da navbar e na página de listagem dedicada (`/admin/notificacoes`) que marca imediatamente como lida, remove o destaque visual e decrementa o contador sem recarregar a tela, além da rota segura `GET /admin/notificacoes/{id}/abrir` com suporte a cliques diretos.
- **Desvinculação Estrita entre Formulário de Contato e Calendário de Serviços:** O formulário de contato do site público foi desconectado do banco de agendamentos e direcionado exclusivamente para a Central de Notificações. O FullCalendar 6 (`/admin/agendamentos`) foi blindado para exibir única e exclusivamente agendamentos reais de serviços de rastreamento e estética automotiva.

### Módulo 19: Central Administrativa Dedicada de Depoimentos & Moderação e Blindagem Anti-Spam com reCAPTCHA v3 Dinâmico
- **Página Administrativa Dedicada de Depoimentos (`/admin/depoimentos`):** Área exclusiva para gerenciar avaliações com cards de métricas em tempo real (Total Geral, Pendentes de Moderação, Aprovados/Publicados e Média de Estrelas), listagem com paginação e busca, modal interativo com drag & drop de foto do cliente, e exclusões confirmadas exclusivamente via SweetAlert2 (**zero `alert()` nativo**).
- **Moderação em 1 Clique (Toggle AJAX Rápido):** O administrador aprova ou suspende depoimentos instantaneamente com feedback visual em tempo real e atualização automática dos contadores.
- **Retenção para Moderação:** Depoimentos enviados por clientes na Landing Page entram compulsoriamente como inativos (`ativo = false`) e geram notificações direcionadas com link direto para a moderação.
- **Google reCAPTCHA v3 no Formulário de Contato:** Validação transparente e sem fricção de pontuação humana (*score*), ativável e parametrizável nas Configurações do Sistema (`recaptcha_site_key`, `recaptcha_secret_key` e `recaptcha_ativo`).
- **Paleta de Cores Dinâmica Sincronizada com o CMS:** O selo de segurança do formulário e o badge flutuante `.grecaptcha-badge` do Google herdam automaticamente as variáveis CSS `--foco-red`, `--bs-primary` e `--bs-primary-rgb`. Caso o administrador altere a cor primária no painel, o reCAPTCHA acompanha imediatamente a nova identidade visual.
- **Blindagem em Camadas Anti-Spam & Anti-Injeção:** 
  1. *Honeypot invisível (`_foco_hp_website`)*: Desarma crawlers e spambots silenciosamente;
  2. *Time-Trap (`_foco_form_time`)*: Bloqueia disparos instantâneos de robôs submetidos em frações de segundo;
  3. *Motor Anti-Injeção*: Expressões regulares ativas contra SQL Injection e injeções de script XSS;
  4. *Sanitização Completa*: Limpeza e remoção de tags de risco antes do registro na Central de Notificações.

### Módulo 24: Modal Interativo de Leitura de Notificações com Marcação Automática & Ação Rápida Sem Leitura
- **Abertura em Modal Interativo:** Ao clicar na linha da notificação em `/admin/notificacoes` ou no botão de ação 'Ler', abre um modal responsivo exibindo cabeçalho estilizado, corpo completo da mensagem, data/hora formatada, tabela com metadados estruturados (origem, hora, telefone, dispositivo, etc.) e link para o módulo de destino.
- **Marcação Automática como Lida na Leitura:** Ao abrir o modal, os dados completos são carregados via AJAX (`GET /admin/notificacoes/{id}/obter`), marcando a notificação automaticamente como lida no backend e sincronizando os contadores em tempo real.
- **Ação Rápida 'Marcar como Lida' Sem Leitura:** Preserva o botão direto 'Marcar como Lida' na listagem com controle de propagação (`event.stopPropagation()`), permitindo ao usuário marcar diretamente sem precisar abrir o modal.
- **Transição Suave no Fechamento:** Na aba de filtro 'Não Lidas', o item é mantido em tela durante a leitura e é removido suavemente com fadeOut apenas ao fechar o modal (`hidden.bs.modal`).

### Módulo 25: Notificações Push via OneSignal com Imagem, Small Boxes AdminLTE 4 & Logo Compacta com Favicon
- **Web Push via OneSignal com Imagem:** Implementação da camada `OneSignalService` com integração à API REST v1 oficial do OneSignal (`/api/v1/notifications`), disparando notificações instantâneas com imagem de banner (`chrome_web_image` e `big_picture`), ícone oficial da Foco 7 e links de redirecionamento. Painel administrativo em `/admin/notificacoes` com gestão de App ID, REST API Key, switch de ativação, preview em tempo real da imagem do push e botão de inscrição de dispositivo via OneSignal SDK v16.
- **Padronização Oficial AdminLTE 4 (`small-box`):** Substituição dos cards da Central de Notificações pelos componentes oficiais `small-box` do AdminLTE 4 (`text-bg-danger`, `text-bg-warning`, `text-bg-primary`, `text-bg-success`), garantindo tipografia legível, ícones translúcidos no background e barra inferior de ação rápida para filtragem.
### Módulo 28: Área do Cliente Completa: Cancelamento de Agendamentos, Gestão Cadastral, Alteração Segura de Senha & Sidebar Responsiva
- **Cancelamento de Agendamentos pelo Assinante:** O cliente pode cancelar seus próprios agendamentos pendentes ou confirmados diretamente na sua Dashboard com confirmação visual via SweetAlert2 (**zero `alert()` nativo**). O cancelamento libera instantaneamente o horário no FullCalendar 6 e gera uma notificação prioritária automática na Central de Notificações do Administrador.
- **Gestão Cadastral & Endereço Completo:** Nova área `/cliente/perfil` permitindo ao cliente manter atualizados seu Nome, WhatsApp/Telefone, CPF/CNPJ, CEP com preenchimento automático via ViaCEP e endereço completo de instalação/cobrança.
- **Alteração Segura de Senha com Olho de Visualização:** Aba dedicada de segurança com validação estrita da senha atual via `Hash::check()`, campos de nova senha e confirmação com botões visuais para alternar visibilidade (olho aberto/fechado) e feedback imediato via AJAX.
- **Upload de Avatar com FocoDropzone:** Suporte a upload por arrastar e soltar (drag & drop), validação de dimensões 1:1, preview em tempo real e opção de remoção imediata.
- **Alternância Dinâmica de Logo/Favicon na Sidebar da Área do Cliente:** Unificação total com o painel administrativo: a logo completa horizontal alterna automaticamente para o favicon oficial quadrado quando a sidebar é compactada e restaura a logo completa com expansão fluida ao passar o mouse por cima (hover).

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
| **Módulo 17**| Checkout Transparente Efí Bank, Emissão de Carnê em PDF e Repasse de Taxas | ✅ Concluído & Validado |
| **Módulo 18**| Sidebar Treeview Responsivo, Rolagem Mobile/Tablet e Notificações Dedicadas | ✅ Concluído & Validado |
| **Módulo 19**| Página de Depoimentos & Moderação e reCAPTCHA v3 Dinâmico Anti-Spam | ✅ Concluído & Validado |
| **Módulo 20**| Carrossel Touch & Drag de Depoimentos (1 por tela no mobile, avatares circulares 1:1) | ✅ Concluído & Validado |
| **Módulo 21**| Padronização Profissional Dark/Light Mode (Contraste dinâmico, fim do borrão vermelho) | ✅ Concluído & Validado |
| **Módulo 22**| Gestão Universal de Ícones de Planos (FontAwesome 6, Flaticon UIcons, Bootstrap Icons & SVG Livre com Preview ao Vivo) | ✅ Concluído & Validado |
| **Módulo 23**| Limpeza Automática da Central de Notificações & Reset Universal de Formulários | ✅ Concluído & Validado |
| **Módulo 24**| Modal de Leitura Detalhada de Notificações com Marcação Automática e Ação Rápida Direta | ✅ Concluído & Validado |
| **Módulo 25**| Push OneSignal com Imagem, Small Boxes AdminLTE 4 & Alternância Dinâmica de Logo/Favicon na Sidebar (Compactada e no Hover) | ✅ Concluído & Validado |
| **Módulo 26**| Dashboard Reformulada com KPIs Animados, Contador de Visitas & Analítico Completo (Chart.js), Cancelamento de Agendamentos e Antecipação de Parcelas Financeiras | ✅ Concluído & Validado |
| **Módulo 27**| Auditoria Contábil de Faturas (Bloqueio Direto & Estorno Manual com Recálculo da Receita), Logo Adaptável em Auth, Visualização de Senha e Proteção contra Força Bruta | ✅ Concluído & Validado |
| **Módulo 28**| Área do Cliente Completa: Cancelamento de Agendamentos Online com Notificação Automática, Gestão Cadastral Completa, Alteração Segura de Senha com Botões de Olho, Avatar com Drag & Drop e Alternância Dinâmica de Logo/Favicon na Sidebar (Compactada e no Hover) | ✅ Concluído & Validado |
| **Módulo 29**| Blindagem Geográfica para as Américas (Prevenção de Ataques, Scanners e Injeções de Países Europeus/Asiáticos com Painel de Controle, Whitelist e Simulador AJAX) & Seção de Empresas Parceiras em Carrossel Infinito Touch & Drag (Gerenciamento CMS, Logos Vetorizadas Oficiais e Suporte aos Modos Claro/Escuro) | ✅ Concluído & Validado |
| **Módulo 30**| Unidades, Filiais & Mapa Interativo do Brasil com Rotas e Presença Nacional (CRUD AJAX no Admin, Leaflet.js, Busca em Tempo Real, Traçado de Rotas Google Maps/Waze, Ocultação Inteligente e Zero Alert) | ✅ Concluído & Validado |

---

## 🌎 Módulo 29: Bloqueio Geográfico (Américas) & Carrossel Infinito de Parceiros

### 1. Bloqueio Geográfico para as Américas (Geo-Restrição)
- **Objetivo:** Blindar o sistema contra tráfego malicioso, bots automatizados, scanners de vulnerabilidades e tentativas de injeção provenientes de servidores fora do continente americano (Europa, Ásia, África e Oceania).
- **Cobertura Territorial:** Abrange todas as nações da América do Sul (Brasil, Argentina, Chile, Colômbia, etc.), América do Norte (Estados Unidos, Canadá, México), América Central e Caribe.
- **Detecção Multicamadas de Alta Precisão:**
  - Identificação de cabeçalhos de borda Cloudflare (`CF-IPCountry`, `X-Country-Code`).
  - Consulta georreferenciada de IP com cache de 24 horas por endereço.
  - Reconhecimento automático de IPs locais (`127.0.0.1`, `::1`) e faixas privadas (`192.168.*`, `10.*`).
  - Isenção automática de rotas críticas: Webhooks de pagamento bancário (`/webhook/*`), instalador (`/instalar*`) e bypass autenticado.
- **Painel Administrativo (`/admin/configuracoes`):**
  - Botão de ativação/desativação instantânea do Geo-bloqueio.
  - Configuração de lista branca de IPs permitidos (*Whitelist* com suporte a curingas).
  - Simulador interativo em tempo real via AJAX: permite digitar qualquer IP ou código de país e obter diagnóstico imediato de aprovação/bloqueio com badge colorido.
- **Tela de Bloqueio Amigável (HTTP 403 Forbidden):**
  - Página estilizada com o padrão visual da Foco 7 (`resources/views/errors/geoblock.blade.php`), informando com clareza o motivo da restrição geográfica, exibindo o IP e o país detectado do visitante, com botão de contato humanizado via WhatsApp para desvio de falsos positivos.

### 2. Seção de Empresas Parceiras no Carrossel Infinito (Landing Page & CMS)
- **Apresentação na Landing Page:**
  - Carrossel infinito contínuo construído com **Swiper 11** com as mesmas regras do carrossel de depoimentos.
  - Suporte completo a **Touch & Drag** (mouse no computador e arrasto fluido no celular/tablet).
  - Autoplay suave com desaceleração e pausa automática ao passar o ponteiro do mouse (*Pause on Mouse Enter*).
  - Responsividade total: 2 logotipos em smartphones, 3 a 4 em tablets e 5 a 6 em monitores widescreen.
  - Botões de navegação estilizados e paginação sutil em bullets com a paleta oficial da Foco 7.
  - Logotipos vetorizados de alta definição (SVGs oficiais homologados: Cobli, Sascar, Porto Seguro, Mapfre, Tokio Marine, Teltonika, Suntech, Queclink).
  - Adaptação dinâmica para **Modo Escuro (Dark Mode)** e **Modo Claro (Light Mode)**.
- **Painel Administrativo Dedicado (`/admin/parceiros`):**
  - Small Boxes com KPIs em tempo real (Total de marcas, ativas no carrossel e inativas).
  - Tabela responsiva com logo, nome, categoria/descrição, ordem de exibição, link externo seguro (`target="_blank" rel="noopener noreferrer"`) e status ativo.
  - Alternância de ativação em 1 clique via AJAX com confirmação por SweetAlert2/Toastr.
  - Upload intuitivo de logomarca via **FocoDropzone** com drag & drop e visualização imediata da imagem.
  - Edição dos títulos e subtítulos da seção diretamente na aba de seções do CMS (`/admin/cms`).

---

## 💳 Módulo 30: Modo Produção Real & Régua de Cobrança Automatizada (Pré e Pós-Vencimento)

### 1. Transição Total para Modo Produção Real (Zero Resíduos de Teste)
- **Eliminação de Mockups & Payloads Fictícios:**
  - Remoção de qualquer chave PIX demo ou CRC mockado (`FOCO7PIXDEMO`, `6304ABCD`, UUIDs arbitrários).
  - Emissão de QR Code PIX e Copia e Cola 100% no padrão oficial EMVCo do Banco Central do Brasil com cálculo dinâmico de CRC-16 CCITT válido e chave oficial cadastrada (`EfiPaymentService::gerarPayloadPixBacen`).
  - Integração nativa de Produção / Sandbox no Mercado Pago via API oficial (`/v1/payments`) com cabeçalho de idempotência e fallback dinâmico BACEN em caso de ausência de credencial de API.
  - Sanitização de faturas seed e dados demonstrativos com payloads reais.
  - Modal de pagamento do cliente (`/cliente/faturas`) atualizada para exibir o demonstrativo de encargos discriminado (multa contratual, juros diários de mora e total atualizado).

### 2. Régua de Cobrança Automatizada em 7 Etapas (Pré e Pós-Vencimento)
- **Geração Antecipada de Mensalidades Recorrentes:**
  - Emissão automática de novas faturas mensais com antecedência de **X dias** (configurável no painel administrativo, padrão de 10 a 15 dias) para clientes com planos ativos sem fatura futura em aberto.
  - Prevenção de duplicidade: garante uma única fatura mensal por ciclo de competência.
- **Esteira Pré-Vencimento (Preventiva):**
  - **1º Lembrete Preventivo (D - X dias, padrão 10 dias):** Fatura disponibilizada para pagamento e aviso de vencimento próximo.
  - **2º Lembrete Preventivo (D - Y dias, padrão 5 dias):** Notificação de vencimento se aproximando com código PIX rápido.
  - **3º Lembrete Preventivo (D - Z dias / Véspera, padrão 1 dia):** Alerta de que a mensalidade vence amanhã.
  - **Cobrança no Vencimento (D - 0 / Vence Hoje):** Aviso no próprio dia informando que a fatura vence hoje e prevenindo a incidência de encargos.
- **Esteira Pós-Vencimento com Encargos Legais:**
  - **1º Aviso de Atraso (D + A dias, padrão 1 dia de atraso):** Comunicação de vencimento expirado com aplicação de multa moratória legal de 2% (Art. 52 § 1º do Código de Defesa do Consumidor).
  - **2º Aviso de Atraso (D + B dias, padrão 5 dias de atraso):** Encargos recalculados diariamente com juros de mora legais (1% ao mês / 0,033% ao dia) e link direto para quitação.
  - **3º Aviso de Atraso (D + C dias, padrão 10 dias de atraso):** Notificação severa com aviso prévio de suspensão de sinal do rastreador veicular e negativação de crédito.

### 3. Templates de E-mail Oficiais Registrados
- Templates responsivos encapsulados no layout corporativo da Foco 7:
  - `cobranca_lembrete_1` (1º Lembrete Preventivo - Fatura Disponível)
  - `cobranca_lembrete_2` (2º Lembrete Preventivo - Vencimento Próximo)
  - `cobranca_lembrete_3` (3º Lembrete Preventivo - Vence Amanhã)
  - `cobranca_vencimento_hoje` (Cobrança no Dia do Vencimento - Vence Hoje)
  - `cobranca_atraso_1` (1º Aviso de Atraso - Fatura Vencida com Multa Inicial)
  - `cobranca_atraso_2` (2º Aviso de Atraso - Juros de Mora Diários)
  - `cobranca_atraso_3` (3º Aviso de Atraso - Alerta de Suspensão de Sinal e Bloqueio)

### 4. Painel Administrativo (`/admin/gateways` - Aba "Régua de Cobrança & Mensalidades")
- Chave geral liga/desliga da régua de cobrança.
- Inputs configuráveis para dias de antecedência de emissão de mensalidade.
- Switches e inputs de dias para cada uma das 7 etapas da régua.
- Parametrização financeira de Multa (%) e Juros de Mora Mensal (%).
- Botão interativo **"Processar Régua Agora"** via AJAX com SweetAlert2 para execução imediata e feedback de métricas em tempo real.
- Comando Artisan integrado à central de tarefas Cron: `php artisan faturas:processar-regua`.

---

## 🛠️ Módulo 31: Modo de Manutenção Avançado & Wallpaper Drag & Drop (FocoDropzone)

### 1. Persistência de Dados & Resolução de Limpeza de Campos
- **Formulário com `.keep-data`:** Utilização da classe oficial `.keep-data` no formulário de manutenção (`/admin/manutencao`), prevenindo a limpeza universal de inputs pós-sucesso do `global-ajax.js` e mantendo títulos, mensagens e datas intactos na interface.
- **Sincronização Direta de Status:** Interruptor liga/desliga (`manutencao_ativa`) inserido diretamente no formulário, permitindo salvar ou alternar a ativação do modo de manutenção via submissão tradicional de formulário ou botão de 1 clique no cabeçalho com SweetAlert2.

### 2. Wallpaper Panorâmico via Drag & Drop (FocoDropzone)
- **Upload Inteligente:** Suporte completo ao componente `FocoDropzone` com arrastar e soltar (drag & drop), seletor de arquivos, validação de tipos (JPG, PNG, WEBP), limite de tamanho (5MB) e preview em tempo real com dimensões detectadas via JavaScript.
- **Armazenamento e Sanitização:** Upload direcionado para `public/uploads/manutencao/` com remoção preventiva de wallpapers customizados anteriores e opção de restauração rápida para a imagem padrão do sistema.
- **Wallpaper Padrão de Alta Resolução (`/img/manutencao-bg.jpg`):** Imagem oficial com ambientação de segurança veicular, mapa de telemetria GPS e iluminação neon.

### 3. Experiência de Visitantes vs Administradores
- **Página de Manutenção Estilizada (`site.manutencao`):** Background panorâmico dinâmico com desfoque e overlay escuro (`backdrop-filter`), contagem regressiva em tempo real com blocos monoespaçados, integração com botão de emergência WhatsApp e canais de plantão 24h.
- **Barra de Alerta Fixa no Topo da Landing Page:** Exibição de banner vermelho no topo da página inicial para administradores e IPs autorizados avisando que a manutenção está ativa para o público geral, com atalhos diretos para visualizar a página como visitante.
- **Simulador e Pré-Visualização:** Rota exclusiva `/admin/manutencao/preview` e suporte a parâmetro de consulta `?preview_manutencao=1` para conferência do layout sem necessidade de logout.

---

## 📍 Módulo 30: Unidades, Filiais & Mapa Interativo OpenStreetMap (100% Gratuito / Sem Custos de API)

### 1. Painel Administrativo de Filiais (`/admin/filiais`)
- **Gestão Completa (CRUD AJAX):** Cadastro, edição, ordenação e exclusão de unidades de atendimento com formulário modal interativo e zero recarregamento de página.
- **Mini-Mapa Interativo OpenStreetMap no Modal:** Visualização com Leaflet.js permitindo arrastar o marcador ou clicar diretamente em qualquer ponto do mapa para definir Latitude e Longitude instantaneamente sem qualquer custo de API.
- **Geocodificação Automática GPS (Nominatim OpenStreetMap):** Botão "Preencher Coordenadas pelo Endereço" que consulta via AJAX a API gratuita do OpenStreetMap Nominatim e preenche automaticamente Latitude e Longitude, reposicionando o marcador no mini-mapa com animação `flyTo`.
- **Upload de Fachada/Foto:** Integração com o componente `FocoDropzone` com proporção 16:9, preview em tempo real e remoção instantânea.
- **Toggle Rápido de Status & Destaque (Matriz):** Ativação/desativação em 1 clique e marcação de unidade Matriz com feedback via Toastr e SweetAlert2 (**zero `alert()` nativo**).

### 2. Mapa do Brasil Interativo na Landing Page (`#unidades`)
- **Regras Idênticas à Seção de Parceiros:** Se não houver filiais ativas no banco de dados, a seção completa, o item do menu desktop ("Onde Estamos"), o link do menu drawer Android e o link do rodapé são 100% ocultados automaticamente.
- **OpenStreetMap Oficial com Leaflet.js (Zero Custo / Livre de Google Maps):** Renderização direta com tiles do OpenStreetMap (`tile.openstreetmap.org`), sem necessidade de faturamento, sem chave paga de API e sem cobranças por visualizações.
- **Marcador Customizado com Efeito Pulso:** Pin estilizado com gradiente oficial da Foco 7 (`#d90000`) e animação de radar pulsante.
- **Popups Informativos e Traçado de Rotas:** Ao clicar no marcador, o popup exibe foto da fachada, nome da filial, badge de Matriz/UF, endereço completo, horário de atendimento, telefone, botão de contato WhatsApp e botões para traçar rota no **OpenStreetMap Routing** (`https://www.openstreetmap.org/directions`) e em navegadores móveis (Waze/GPS).
- **Lista Lateral com Busca em Tempo Real:** Campo de pesquisa instantânea que filtra filiais por cidade, estado ou nome, contador dinâmico de unidades visíveis, botão para resetar a visão geral do Brasil e animação suave `flyTo` ao clicar no card de qualquer filial.

---

## 🧪 Testes Automatizados e Garantia de Qualidade

A aplicação conta com uma suíte de testes de integração automatizados (`tests/Feature/`):
- **113 testes executados, 113 aprovados (100% de sucesso), 526 asserções**.
- Cobertura completa: Unidades, filiais e mapa interativo do Brasil (Leaflet.js com geocodificação, traçado de rotas Google Maps/Waze, ocultação condicional quando não há unidades ativas, CRUD e toggle AJAX), régua de cobrança pré e pós-vencimento com cálculo de multa (2%) e juros de mora diários, geração antecipada de mensalidades recorrentes, prevenção de duplicidade de disparos, endpoint AJAX de execução imediata da régua, persistência de parâmetros no painel admin, modal de faturas do cliente sem resíduos de teste, rotas públicas, moderação de depoimentos, bloqueio geográfico de países fora das Américas, liberação de países americanos, bypass por whitelist de IP, rotas de webhook isentas de bloqueio geográfico, CRUD e toggle de parceiros via AJAX, carrossel infinito de parceiros, moderação de depoimentos com toggle AJAX e exclusão SweetAlert2, anti-spam honeypot, bloqueio de injeções SQL/XSS, validação Google reCAPTCHA v3, checkout transparente Efí (Pix, Boleto, Cartão e Carnê em PDF), cálculo de taxas repassadas, fluxo de agendamento online, cancelamento de agendamentos com liberação de horário no calendário, formulário de contato, notificações Web Push e transacionais por e-mail, abertura e marcação de lida de notificações, contador de visitas com respeito à LGPD (hash SHA-256) e analítico de tráfego, antecipação individual e em lote de faturas com desconto, bloqueio de alteração direta de faturas quitadas com estorno manual e recálculo da receita contábil, proteção contra força bruta no login/cadastro/redefinição com Rate Limiting duplo e Honeypots, webhooks isentos de CSRF, templates de e-mail, CRUD de estética, clientes e faturas, edição no CMS, sincronização de paleta visual oficial Foco 7, cabeçalhos de segurança HTTP e isolamento ACL.

---

## 🔒 Segurança e Otimização para cPanel
- **Blindagem Geográfica:** Restrição regional para as Américas gerenciável pelo painel administrativo com proteção ativa contra vetores de ataque transcontinentais.
- **Cabeçalhos de Segurança Estritos:** Middleware `SecurityHeaders` injeta proteção contra Clickjacking (`X-Frame-Options: SAMEORIGIN`), MIME-Sniffing (`X-Content-Type-Options: nosniff`) e XSS (`X-XSS-Protection`).
- **Proteção contra Força Bruta:** Limitação de taxa de requisições (*Rate Limiting*) em rotas de login e autenticação sensíveis.
- **Isenção de CSRF Segura:** Rotas `/webhook/*` isentas de CSRF para integração com APIs externas (Mercado Pago e Efí PRO).
- **Bloqueio de Acesso Público:** Diretórios e arquivos sensíveis (`.env`, `.git`, `app/`, `vendor/`, `artisan`) bloqueados no `.htaccess`.
- **Prevenção de N+1 e Sobrecarga:** Todas as consultas com Eager Loading (`with()`) e índices nas tabelas do MySQL.
- **UTF-8 Estrito Sem BOM & Sanitização de Acentos:** 100% dos arquivos do projeto codificados estritamente em UTF-8 sem Byte Order Mark (BOM). Rotina e comando de auditoria automatizada (`php artisan sistema:corrigir-encoding`) que previne e repara quaisquer ocorrências de mojibake nas tabelas do banco de dados MySQL.
- **Timezone:** `America/Sao_Paulo`.


