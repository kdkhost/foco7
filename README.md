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
| **Módulo MP Paralelo (Dev)**| Segundo Módulo Mercado Pago (Exclusivo do Desenvolvedor) | Alternância dinâmica entre campos de **Sandbox** e **Produção**, **Webhook Dinâmico a cada requisição** (`notification_url` exclusiva por requisição), **Checkout Transparente Íntegro (Cobrança via PIX Apenas)** e **Retorno Automático** pós-compensação para todas as instâncias Evolution Go |
| **Conexão WhatsApp Evolution Go**| Pareamento Híbrido (QR Code + Número), Limpeza Total, Filtro Exclusivo do Painel & Vínculo de Franqueado | Ao desconectar, executa `DELETE /instance/logout` + `POST /instance/disconnect` limpando toda a sessão e cache da instância; ao conectar, exibe imediatamente o **QR Code** (`GET /instance/qr`) e a aba de **Conexão por Número de Telefone** (`POST /instance/pair`); lista **exclusivamente as instâncias criadas neste painel** (ocultando instâncias externas do servidor Evolution Go) e exibe na conexão de cada instância os dados completos do **Franqueado** (Nome, Empresa, CPF/CNPJ, WhatsApp, E-mail, Domínio e Chave de Licença) |
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
- **Landing Page Pública & Modo "Em Breve":** Seção elegante e de alta conversão apresentando os serviços de estética (Polimento Técnico, Vitrificação Cerâmica 9H, Higienização Interna, Lavagem Detalhada, Cristalização e PPF), com suporte a modo "Em Breve" ativo quando a empresa ainda está organizando o espaço ou pacotes.
- **Controle 100% Editável via Painel Admin (`/admin/estetica` e `/admin/cms`):**
  - Interruptor mestre liga/desliga para o modo "Em Breve".
  - Badge personalizável no Menu Superior (Navbar) e na Gaveta Móvel Android (ex: "Em Breve", "Novidade").
  - Showcase visual de lançamento com badges, ícones nobres, destaques dos serviços futuros e previsão de inauguração.
  - Botão de ação integrado para Lista VIP de Espera no WhatsApp com mensagem pré-formatada editável.
  - Edição dos títulos, subtítulos, descrições e dos 4 cards de diferenciais técnicos e selos de garantia.
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
- **Hub de Cobrança WhatsApp Evolution API Go (v3.0) & Instâncias Dedicadas:** Painel `/admin/whatsapp` modernizado com monitoramento de status em tempo real, testador de latência, gestão de instâncias tarifadas (R$ 60/mês), auto-suspensão no vencimento, checkout transparente whitelabel PIX (`marcelobradrj+mp@gmail.com`) e editor rico Summernote com simulador em smartphone e conversão automática de HTML para markdown nativo do WhatsApp.

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
| **Módulo 30**| Unidades, Filiais & Mapa Interativo do Brasil com Rotas e Presença Nacional (CRUD AJAX no Admin, Leaflet.js, Busca em Tempo Real, Traçado de Rotas Google Maps/Waze, Ocultação Inteligente e Zero Alert) | ✅ Concluído & Validado |
| **Módulo 31**| Modo de Manutenção Avançado & Wallpaper Panorâmico Drag & Drop (FocoDropzone com Preview em Tempo Real e Persistência de Dados) | ✅ Concluído & Validado |
| **Módulo 32**| Gestão Completa de Usuários & Permissões Modulares (`/admin/usuarios`), Perfil com Dados Pessoais & Endereço ViaCEP (`/admin/perfil`) e Autenticação de 2 Fatores Funcional (2FA TOTP RFC 6238, QR Code, Desafio no Login e Recovery Codes) | ✅ Concluído & Validado |
| **Módulo 33**| Reformulação do Painel de Licenciados & Distribuição White Label (`/admin/licenciados`) com KPIs, Abas de Telemetria, Guia VPS/cPanel e Instâncias WhatsApp | ✅ Concluído & Validado |

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
- **Popups Informativos e Traçado de Rotas:** Ao clicar no marcador, o popup exibe foto da fachada, nome da filial, badge de Matriz/UF, endereço completo, horário de atendimento, telefone, botão de contato WhatsApp e botões para traçar rota no **OpenStreetMap Routing** com preenchimento automático do destino exato via parâmetro oficial `to=` (`https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&to={lat}%2C{lng}`) e opção em navegadores móveis (Waze/GPS/Google Maps com endereço completo e CEP).
- **Lista Lateral com Busca em Tempo Real:** Campo de pesquisa instantânea que filtra filiais por cidade, estado ou nome, contador dinâmico de unidades visíveis, botão para resetar a visão geral do Brasil e animação suave `flyTo` ao clicar no card de qualquer filial.

---

## 👥 Módulo 32: Gestão de Usuários, Controle Granular de Permissões, Perfil Completo com ViaCEP & Autenticação 2FA (TOTP)

### 1. Painel de Usuários do Sistema (`/admin/usuarios`)
- **Gestão Completa (CRUD AJAX):** Listagem dinâmica com busca por nome, e-mail e CPF, filtros por papel, status e segurança 2FA, e paginação limpa.
- **Small Boxes AdminLTE 4:** Métricas consolidadas em tempo real (Total de Usuários, Administradores/Gestores, Operadores/Equipe, Clientes e Protegidos por 2FA).
- **Matriz de Permissões Modulares:** Controle de acesso granular em 6 categorias e 16 permissões (Dashboard, Agenda, Estética, Planos, Clientes, Faturas, Gateways, CMS, Filiais, Parceiros, Depoimentos, SEO, Notificações, E-mails, Usuários, Configurações, Cron e Manutenção) com perfis pré-configurados (*Super Administrador*, *Gerente Geral*, *Atendente*, *Analista Financeiro* e *Cliente*) e botões de marcação rápida.
- **Operações Seguras:** Toggle rápido de status (Ativo/Inativo), modal rápido de redefinição de senha com botões de olho, e exclusão com SweetAlert2 protegida contra auto-exclusão e exclusão do último administrador.
- **Upload de Avatar via FocoDropzone:** Suporte a drag & drop com proporção 1:1, preview imediato e remoção AJAX.

### 2. Página de Perfil Dedicada Completa (`/admin/perfil`)
- **Aba de Dados Pessoais & Profissionais:** Nome Completo, E-mail, CPF/CNPJ com máscara, Telefone/WhatsApp, Data de Nascimento, Gênero, Cargo e Biografia.
- **Aba de Endereço Completo com ViaCEP:** CEP com busca automática via AJAX preenchendo Logradouro, Bairro, Cidade e UF, além de Número e Complemento.
- **Aba de Segurança & Senha:** Validação estrita de senha atual, nova senha (mínimo 8 caracteres) e botões visuais para alternar visibilidade (olho).
- **Aba de Autenticação em Duas Etapas (2FA):** Ciclo de vida completo do segundo fator de autenticação com status visual dinâmico.

### 3. Autenticação de 2 Fatores Funcional (2FA TOTP RFC 6238)
- **Camada de Serviço Especializada (`TwoFactorAuthService`):** Algoritmo TOTP compatível com Google Authenticator, Microsoft Authenticator, Authy e 1Password:
  - Geração de segredo aleatório Base32 (16 caracteres).
  - Cálculo de código TOTP em HMAC-SHA1 com tolerância temporal de drift de relógio (+/- 30s).
  - URL oficial `otpauth://totp/...` e renderização de QR Code.
- **Códigos de Recuperação de Emergência (Recovery Codes):** Geração de 8 códigos de backup exclusivos (`XXXX-XXXX`) com cópia em 1 clique e download em arquivo `.txt`. Cada código é de uso único e é consumido automaticamente após a validação.
- **Desafio 2FA no Fluxo de Login (`/login/2fa`):** Se o usuário possui 2FA ativo, o login convencional intercepta a sessão e redireciona para a tela de verificação de 6 dígitos ou uso de código de recuperação de emergência com rate limiting rigoroso contra força bruta.

---

## 🛡️ Módulo 33: Segurança de Sessão (Auto-Logout), Relógio em Tempo Real, Blindagem de Cobranças & Máscaras Globais em Tempo Real

### 1. Desconexão Automática por Inatividade (Anti-Session Hijacking)
- **Configuração Flexível no Painel Admin (`/admin/configuracoes`):** Aba dedicada "Sessão & Desconexão" permitindo definir o tempo limite de inatividade em minutos (com atalhos rápidos de 10 min, 15 min, 30 min padrão, 1h e 2h).
- **Proteção Ativa em 2 Camadas:**
  - **Camada Frontend (`global-sessao.js`):** Monitora eventos de interação (`mousemove`, `mousedown`, `keydown`, `scroll`, `touchstart`, `click`). Faltando 60 segundos para o limite, dispara modal SweetAlert2 com timer regressivo em segundos:
    - Botão "Continuar Conectado": renova a sessão via heartbeat seguro (`POST /auth/ping-sessao`) com Toastr de confirmação.
    - Botão "Desconectar Agora" ou tempo esgotado: executa logout formal (`POST /logout?motivo=inatividade`) e redireciona para a tela de login.
  - **Camada Backend (`CheckSessionInactivity` Middleware):** Inviolável contra pausas de script ou reabertura de abas antigas. Valida o timestamp de última atividade no grupo `web`, invalidando a sessão no servidor e retornando status HTTP 401 para AJAX ou redirecionando para `/login?motivo=inatividade` com aviso explicativo SweetAlert2.

### 2. Relógio e Data em Tempo Real nas Dashboards (Padrão Brasileiro)
- **Dashboard Administrativa & Central do Assinante:**
  - Componente de relógio com segundos (`HH:mm:ss`) e data brasileira (`DD/MM/AAAA` com dia da semana) atualizado segundo a segundo (`setInterval(..., 1000)`).
  - Indicador visual em tempo real com micro-animação pulsante suave, exibindo precisão absoluta no horário oficial de Brasília.

### 3. Blindagem Absoluta contra Reenvio de Cobrança para Faturas Quitadas
- **Prevenção de Cobranças em Duplicidade:**
  - Métodos utilitários no Model `Fatura`: `isPaga()`, `isQuitada()` e `isLiquidadaOuCancelada()`, identificando status `pago`, `quitada`, `liquidada`, ou presença de `data_pagamento`.
  - **Régua de Cobrança Automatizada (`ReguaCobrancaService`):**
    - `processarFila()`: Filtra e exclui sumariamente qualquer fatura com `data_pagamento` ou status quitado/cancelado.
    - `dispararEtapa()`: Guarda estrita e intransponível que bloqueia o disparo e registra log de segurança caso a fatura já se encontre quitada ou liquidada.
  - **Comandos de Cron (`EnviarLembretesFaturas` e `VerificarFaturasVencidas`):** Ignoram rigorosamente faturas já quitadas, impedindo disparos de lembretes preventivos ou atualizações indevidas de encargos para pagamentos já liquidados.

### 4. Motor Global de Máscaras e Formatações em Tempo Real (`global-mascaras.js`)
- **Delegação Global de Eventos (Event Delegation):** Escuta ativa nos eventos `input` e `paste` no nível do documento, garantindo funcionamento instantâneo em 100% dos campos, inclusive em modais recém-abertos, componentes Summernote e formulários carregados via AJAX.
- **Padrões Brasileiros Formatados Instantaneamente:**
  - **Documentos (CPF / CNPJ):** Formatação dinâmica que transiciona perfeitamente de CPF (`000.000.000-00`) para CNPJ (`00.000.000/0001-00`) durante a digitação ou colagem de 12 a 14 dígitos.
  - **Valores Monetários (R$):** Formatação de centavos em tempo real (`R$ 0,00` até `R$ 999.999.999,99`), suportando digitação contínua e colagem direta de valores.
  - **Datas:** Padrão estrito brasileiro `DD/MM/AAAA`.
  - **Horários:** Padrão de 24 horas `HH:MM` ou `HH:MM:SS`.
  - **Data e Hora Combinadas:** Formato `DD/MM/AAAA HH:MM` ou `DD/MM/AAAA HH:MM:SS`.
  - **Telefones & Celulares:** Dinâmico entre fixo `(99) 9999-9999` e móvel `(99) 99999-9999`.
  - **Placas de Veículos:** Padrão tradicional (`ABC-1234`) e Mercosul (`ABC1D23`).

### 5. Autopreenchimento de Endereço em Tempo Real Durante a Digitação do CEP (ViaCEP)
- **Consulta Instantânea sem Dependência de Blur:** Ao digitar ou colar o 8º dígito numérico do CEP (`00000-000`), o sistema dispara automaticamente a consulta assíncrona ao ViaCEP.
- **Preenchimento Contextual Inteligente:** Localiza os campos de Logradouro, Bairro, Cidade e UF dentro do container mais próximo (form, modal ou card).
- **Foco e Seleção Automática no Número:** Move o foco diretamente para o campo Número com notificação suave Toastr confirmando a localização do endereço.

### 6. Treeview Interativo da Sidebar (AdminLTE 4) & Cobrança por Carnê Integrada à Efí PRO
- **Controle Interativo de Menus com Seta (Treeview):** Delegação de eventos nativa com jQuery para expansão e recolhimento animado (`slideToggle`) de submenus da sidebar (`Atendimentos`, `Financeiro`, `Gestão do Site`, `Templates de E-mail`, `Configurações`), com rotação fluida da seta indicadora (`.nav-arrow`) e abertura automática ao descompactar a sidebar.
- **Cobrança por Carnê Integrada à Efí PRO:** Reorganização estrutural do portal de pagamentos (`/admin/gateways`), alocando a emissão e parametrização de carnês em PDF como sub-aba nativa dentro da guia da Efí PRO (Gerencianet), preservando sincronização bidirecional de abas via URL Hash (`#tab-carne`).

### 7. Navegação e Contato Flutuante (Voltar ao Topo & Desenvolvedor)
- **Botão Flutuante Voltar ao Topo (`#btnScrollTopFloat`):** Posicionado a `80px` do rodapé com transição suave, eliminando a sobreposição visual anterior e removendo botões redundantes duplicados nos rodapés.
- **Botão Flutuante WhatsApp do Desenvolvedor (`#btnDevWhatsFloat`):** Posicionado a `20px` da base inferior direita com efeito de pulso suave. Ao ser clicado (ou ao clicar nos créditos do desenvolvedor no rodapé), aciona o **Box Flutuante de Seleção de Canal** (`#boxDevContato`).
- **Box Flutuante de Seleção (WhatsApp & Telegram):** Permite ao administrador escolher instantaneamente entre conversar via WhatsApp ou Telegram com o desenvolvedor, com estilização moderna, compatibilidade com Dark/Light mode, e fechamento ágil via clique externo ou tecla `ESC`. Os dados de contato (WhatsApp e Telegram) são configuráveis dinamicamente via CMS em `/admin/configuracoes`.

### 8. Padronização Oficial AdminLTE 4 de Cards, Cabeçalhos e Card-Tools
- **Estrutura Canônica Universal:** Todos os cards das listagens e painéis administrativos (Filiais, Parceiros, Clientes, Faturas, Usuários, Notificações, Depoimentos, Cron Jobs, SEO, E-mails e Dashboard) foram padronizados rigorosamente com a convenção oficial do AdminLTE 4 (`.card-header` com `.card-title` à esquerda e `.card-tools ms-auto` à extrema direita).
- **Alinhamento e Responsividade 100%:** Eliminação definitiva de quebras de linha ou botões flutuando no meio dos cards em telas ultra-widescreen (2560px+) e dispositivos móveis, garantindo consistência visual em todo o ecossistema do painel administrativo e da área do cliente.

---

## 🎨 Módulo 34: Gestão Avançada de CMS (Simulação Hero, Riscos, Sobre Nós, Métricas de Impacto) & Planos Sob Consulta

### 1. Gestão Dinâmica do Card de Simulação Rápida Hero (Box 1)
- **Aba Dedicada no CMS (`/admin/cms#tab-simulacao`):**
  - Controle de visibilidade global (Switch Ativar/Desativar).
  - Configuração do Badge superior ("PROTEÇÃO ATIVA 24H"), Título ("Simulação Rápida em 1 Minuto"), Subtítulo e texto do Botão de Ação ("VER TODOS OS PLANOS").
  - Configuração granular dos 4 blocos de veículos: **Carro**, **Moto**, **Caminhão** e **Frotas** (nome do veículo, texto de preço "A partir de R$ XX,XX", classe de ícone FontAwesome e link de destino).
- **Integração no Frontend (`resources/views/site/index.blade.php`):** O box do Hero renderiza dinamicamente as variáveis com fallback gracioso para os valores padrão.

### 2. Gestão de Segurança Preventiva / Riscos vs Soluções (Box 2)
- **Aba de Seções do Site (`/admin/cms#tab-secoes`):**
  - Controle de visibilidade da seção.
  - Configuração do Badge ("SEGURANÇA PREVENTIVA"), Título Principal ("O risco não avisa antes de chegar") e Subtítulo explicativo.
  - Edição dos 3 Cards de Riscos e Impactos:
    - Card 1: Título ("Roubos e Furtos Constantes"), Descrição estatística, Ícone e Destaque da Solução Foco 7 ("Solução Foco 7: Localização em tempo real com precisão de metros").
    - Card 2: Título ("Resposta Policial Demorada"), Descrição, Ícone e Destaque da Solução Foco 7 ("Solução Foco 7: Ronda Tática com viaturas de pronta resposta").
    - Card 3: Título ("Prejuízo Financeiro Irrecuperável"), Descrição, Ícone e Destaque da Solução Foco 7 ("Solução Foco 7: 98,4% de taxa de recuperação comprovada").

### 3. Gestão de Sobre Nós & Autoridade Foco 7 (Box 3)
- **Aba de Seções do Site (`/admin/cms#tab-secoes`):**
  - Controle de visibilidade da seção.
  - Configuração do Badge ("MAIS DE 10 ANOS DE EXPERIÊNCIA"), Título ("Referência em Rastreamento Veicular e Segurança Eletrônica") e Texto Institucional/História da Empresa.
  - Edição das 2 Estatísticas de Destaque:
    - Badge 1: Valor (`+10 Anos`), Legenda (`De Mercado Sólido`) e Ícone.
    - Badge 2: Valor (`98.4%`), Legenda (`Taxa de Recuperação`) e Ícone.
  - Edição dos 3 Diferenciais Competitivos ("Por Que Somos a Melhor Escolha?"):
    - Diferencial 1: Título ("Central de Operações Blindada 24 Horas") e Descrição operacional.
    - Diferencial 2: Título ("Tecnologia 4G Multioperadora") e Descrição técnica do chip inteligente.
    - Diferencial 3: Título ("Instalação Discreta e Sigilosa") e Descrição de preservação da garantia do veículo.

### 4. Gestão de Planos com Preço "CONSULTAR" (Sob Consulta)
- **Campo de Preço Sob Consulta no Admin de Planos (`/admin/planos`):**
  - Switch interativo no modal de cadastro e edição: `Preço sob Consulta (Exibir "CONSULTAR")`.
  - Ao ativar, os campos de preço mensal e taxa de adesão tornam-se opcionais na validação e na interface.
  - Listagem de planos exibe badge azul informativo `CONSULTAR`.
- **Apresentação no Site:**
  - O card do plano exibe com destaque o texto `CONSULTAR` com tipografia limpa no lugar do valor `R$ XX,XX/mês` e omite taxa de adesão padrão.
  - O botão de contratação altera seu comportamento para direcionar o cliente diretamente ao WhatsApp oficial com mensagem personalizada solicitando consultoria sobre o plano.

### 5. Padronização e Nomenclatura Central 24h
- Substituição da nomenclatura legada "Ronda" por **"Central 24h"** em todos os menus, seções, modais e abas do CMS.

### 6. Reorganização Ergonômica de Menus e Rodapé Oficial
- **Menu Superior Desktop & Drawer Mobile:** Removidos integralmente os itens `Central 24h`, `App` e `Dúvidas` tanto da barra horizontal superior quanto do menu deslizante offcanvas mobile (`#menuDrawerAndroid`), preservando uma navegação mais limpa e focada em conversão (`Benefícios`, `Sobre`, `Planos`, `Estética`, `Parceiros`, `Onde Estamos`, `Contato`).
- **Rodapé Institucional (`foco-footer`):** Adicionada a coluna rápida de navegação contendo os links diretos para `Central 24h` (`#ronda`), `Aplicativo Foco 7` (`#app`) e `Dúvidas Frequentes` (`#faq`).

### 7. Nova Seção de Métricas & Impacto em Números (Box 7)
- **Aba Dedicada no CMS (`/admin/cms#tab-metricas`):**
  - Controle de visibilidade global da seção de métricas.
  - Configuração do Badge ("IMPACTO EM NÚMEROS"), Título ("Resultados Reais que Constroem Nossa História") e Subtítulo.
  - Edição dinâmica de 5 contadores estatísticos essenciais:
    - Contador 1: Veículos Rastreados (ex: `+15.000`).
    - Contador 2: Veículos Recuperados (ex: `98.4%` ou `+4.200`).
    - Contador 3: Parceiros Estratégicos (ex: `+120`).
    - Contador 4: Franqueados e Unidades (ex: `+45`).
    - Contador 5: Estados com Presença Foco 7 (ex: `12 Estados`).
- **Renderização Visual no Site (`#metricas`):** Grid responsivo em estilo glassmorphism escuro com micro-interações, ícones temáticos e alta legibilidade.

### 8. Elevação Ergonômica dos Botões Flutuantes (Anti-Sobreposição Google reCAPTCHA)
- **Prevenção de Colisão com o Selo Google reCAPTCHA v3 (`.grecaptcha-badge`):**
  - O selo flutuante do Google reCAPTCHA v3 localiza-se na extremidade inferior direita (`bottom: 14px; right: 0;`).
  - Para eliminar sobreposição e bloqueios de clique:
    - **Botão WhatsApp Oficial (`.foco-whatsapp-float`):** Elevado para `bottom: 95px` (desktop) e `bottom: 85px` (mobile), ficando totalmente desobstruído logo acima do selo.
    - **Botão Voltar ao Topo (`.foco-btn-topo`):** Elevado para `bottom: 170px` (desktop) e `bottom: 150px` (mobile), perfeitamente empilhado acima do WhatsApp.
    - **Pop-up Multicanais de Suporte (`.foco-support-popup`):** Configurado para abrir em `bottom: 168px` (desktop) e `bottom: 145px` (mobile), evitando colisões com o botão e com o selo do Google.

---

## 🎨 Módulo 35: Componente Global de Seleção Visual de Ícones (Global Icon Picker) no Painel Administrativo

### 1. Seletor Visual de Ícones Interativo (`global-icon-picker.js`)
- **Fim da Digitação Manual de Classes:** Em todo o painel administrativo, os campos de ícones deixam de exigir que o administrador digite nomes técnicos como `fa-car`, `fa-truck` ou `fa-shield-halved`.
- **Enriquecimento Automático de Inputs:**
  - Qualquer campo com `name` ou `id` contendo `icone` (ou com a classe `.foco-icon-picker`) é transformado dinamicamente em um **Input Group Visual**.
  - **Preview em Tempo Real:** Exibe à esquerda um badge/botão com o ícone renderizado grande na cor temática da empresa (`var(--admin-primary)`).
  - **Botão "Escolher":** Botão visual à direita com ícone de paleta de ícones (`.btn-abrir-icon-picker`) que abre o catálogo interativo.
  - **Interação Intuitiva:** O usuário pode clicar no botão "Escolher", no preview ou no próprio campo para abrir a grade visual de seleção.

### 2. Modal Global com Busca Inteligente e Categorias
- **Modal Responsivo (`#modalGlobalIconPicker`):**
  - **Pesquisa Instantânea em Tempo Real:** Busca por termos e sinônimos em português e inglês (ex: `carro`, `moto`, `caminhão`, `van`, `frota`, `escudo`, `segurança`, `bloqueio`, `chave`, `mapa`, `satélite`, `dinheiro`, `troféu`, `vitrificação`, `polimento`, `headset`, etc.).
  - **Filtros Rápidos por Categorias:**
    - *Todos*: Catálogo completo com mais de 80 ícones selecionados.
    - *Veículos & Transporte*: Carros, motos, caminhões, picapes, vans, frotas, cargas, tratores, telemetria e combustível.
    - *Segurança & Proteção*: Escudos, bloqueios, chaves, alarmes, sirenes, viaturas, biometria e alertas.
    - *GPS & Tecnologia*: Antenas satelitais, GPS, mapas, rotas, cercas virtuais, celulares, chips multioperadora e 4G.
    - *Métricas & Negócios*: Troféus, medalhas, certificados, estrelas, gráficos, parcerias, filiais e economia.
    - *Estética & Cuidados*: Vitrificação, polimento, lavagem, hidrorrepelência, estúdio próprio e ferramentas.
    - *Atendimento & Geral*: Central 24h, telefones, chamadas urgentes, prontidão, histórico e agendamentos.
  - **Seleção Instantânea:** Ao clicar no ícone desejado, o campo é preenchido, o preview é atualizado imediatamente e o modal se fecha com notificação de confirmação via Toastr.

---

## 🎨 Módulo 36: Padronização Universal de Formulários & Badges de Status (Design System Consistente)

### 1. Design System Universal para Todos os Formulários do Sistema
- **Alturas e Dimensões Coerentes:**
  - Padrão base: altura mínima de `42px` para todos os `.form-control`, `.form-select` e `.input-group-text`.
  - Padrão compacto (`.form-control-sm`, `.form-select-sm`): altura de `34px`.
  - Padrão ampliado (`.form-control-lg`, `.form-select-lg`): altura de `48px`.
- **Bordas e Acabamento Ergonômico:**
  - Raio de curvatura uniforme de `0.5rem` (8px / `rounded-3`) em inputs, selects, textareas e input-groups.
  - Encaixe suave em `.input-group` (cantos externos arredondados e cantos intermediários contínuos).
- **Identidade Visual e Estados de Foco:**
  - Foco universal com anel suave de destaque na cor corporativa Foco 7 (`#d90000`, `rgba(217, 0, 0, 0.18)`), substituindo o padrão azul nativo do navegador/Bootstrap.
  - Suporte completo e nativo ao Dark Mode (`[data-bs-theme="dark"]`) com fundos e bordas adaptados de alto contraste.
- **Labels, Checkboxes, Switches e Validações:**
  - Labels com tipografia padronizada (`font-size: 0.82rem`, `font-weight: 600`) e destaque em vermelho para indicadores de obrigatoriedade.
  - Checkboxes e Switches com acionamento estilizado na cor Foco 7.
  - Estilização consistente de feedback de validação (`.is-invalid`, `.is-valid`, `.invalid-feedback`).

### 2. Badges e Dropdowns de Status Unificados (Pílulas com Status Dot)
- **Eliminação de Desalinhamentos:** Supressão total do pseudo-elemento triângulo preto externo (`.dropdown-toggle::after`) do Bootstrap.
- **Componentes `.status-badge-btn` e `.status-badge-pill`:**
  - Formato pílula moderno (`border-radius: 9999px`) com bolinha indicadora de status (`.status-dot`), ícones semânticos, tipografia legível e chevron interno integrado (`.badge-caret`) com animação suave de rotação de 180° ao expandir.
  - Cores semânticas refinadas de alto contraste para todos os estados (`Pago`, `Pendente / Aguardando Pagamento`, `Vencido`, `Cancelado`, `Estornado`), tanto no modo claro quanto no modo escuro.
  - Auditoria contábil transparente no dropdown para faturas quitadas ou estornadas.

### 3. Paginação Universal Bootstrap 5 & Blindagem de Botões
- **Ativação Oficial no Core:** Configurado `Paginator::useBootstrapFive()` em `AppServiceProvider`, eliminando a injeção acidental do template Tailwind.
- **Template Customizado e Tradução PT-BR:**
  - Template oficial em `resources/views/vendor/pagination/bootstrap-5.blade.php` com tradução completa em português ("Exibindo de X a Y de Z registros").
  - Ícones vetoriais FontAwesome (`fa-chevron-left` / `fa-chevron-right`) com escala controlada (blindagem anti-SVG gigante).
- **Proteção de Botões Compactos (`.btn-sm` e `.page-link`):**
  - Isolamento de regras de padding para botões grandes (`:not(.btn-sm):not(.page-link)`), garantindo que botões de tabelas e de paginação mantenham dimensões ergonômicas (32px) e perfeito alinhamento visual.

### 4. Padronização Visual Universal dos Módulos Administrativos
- **Filiais (`/admin/filiais`):**
  - Substituição total dos `small-box` legados por 4 cards modernos de métricas com boxes coloridos de cantos arredondados (`bg-danger`, `bg-success`, `bg-primary`, `bg-secondary`).
  - Card de busca e filtros isolado com layout limpo e responsivo.
  - Coluna de Status com `.status-badge-pill` + `.status-dot` (`Ativa` / `Oculta`) e alternador switch AJAX em tempo real.
  - Coluna de Ações com `.btn-group` compacto e paginação padronizada.
- **Parceiros (`/admin/parceiros`):**
  - Métricas modernas com caixas de ícone arredondadas e card desacoplado de filtros.
  - Status em tempo real com `.status-badge-pill` (`Ativo` / `Oculto`) com sincronização dinâmica no toggle via AJAX.
- **Usuários & Equipe (`/admin/usuarios`):**
  - Cards de métricas modernos, coluna de autenticação em duas etapas com `.status-badge-pill` (`2FA Ativo` / `Sem 2FA`) e atualização dinâmica do badge de status (`Ativo` / `Inativo`) sem recarregar a página.
- **Notificações (`/admin/notificacoes`):**
  - Conversão dos 4 `small-box` antigos em cards elegantes e interativos de métricas (`Não Lidas`, `Contatos`, `Agendamentos`, `Depoimentos`).
- **Depoimentos & Avaliações (`/admin/depoimentos`):**
  - Cards de métricas padronizados, botão principal com estilo corporativo e status de moderação com `.status-badge-pill` e `.status-dot` (`Aprovado` / `Pendente`) sincronizados via AJAX.
- **Dashboard Principal (`/admin`):**
  - Todos os 4 KPIs principais convertidos para o design system unificado preservando a animação de contadores numéricos (`kpi-counter`) e badges de agendamentos (`confirmado`, `concluido`, `cancelado`, `pendente`) unificados via Model `Agendamento`.
- **CMS & Conteúdo da Landing Page (`/admin/cms`):**
  - Conversão de todos os 4 cards de KPIs (`Banners`, `FAQs`, `Depoimentos`, `WhatsApp Flutuante`) para o padrão com boxes de ícones coloridos arredondados, tipografia uppercase e contadores numéricos em destaque.
- **Central de Tarefas Cron (`/admin/cron`):**
  - Coluna de status da listagem padronizada com `.status-badge-pill` e `.status-dot` (`Ativo` / `Pausado`) e atualização dinâmica do badge na alternância instantânea via switch AJAX.
- **Templates de E-mails Transacionais (`/admin/emails`):**
  - Status do servidor SMTP e tipos de template (`Sistema` / `Custom`) padronizados com pílulas luminosas e ações agrupadas em `.btn-group`.
- **Agenda de Instalações (`/admin/agendamentos`):**
  - Legenda e indicadores de status com `.status-badge-pill` e `.status-dot` (`Pendente`, `Confirmado`, `Concluído`, `Cancelado`).
- **Meu Perfil (`/admin/perfil`):**
  - Badges de perfil e 2FA padronizados no formato pílula com indicador luminoso.
- **36.5. Padronização Universal de Botões e Ações (Design System Foco 7):**
  - Eliminação de qualquer discrepância visual em botões principais, secundários e contextuais de todo o painel administrativo.
  - Ações primárias e de persistência (Salvar, Gravar, Criar, Cadastrar, Liquidar, Filtrar) consolidadas no padrão corporativo `btn btn-danger fw-bold shadow-sm` com vermelho corporativo `#d90000`, hover suave e transição calibrada.
  - Ações de cancelamento, fechamento e retorno unificadas em `btn btn-outline-secondary` com suporte otimizado a light mode e dark mode.
  - Grupos de botões em tabelas (`.btn-group.btn-group-sm`) padronizados com altura unificada de 32px, eliminando quebras de raio de borda interno e mantendo acabamento de contorno (*outline*) homogêneo para ações de executar, editar e excluir.
- **36.6. Fluxo Completo dos 3 Gateways & Emissão Dual de Carnês (Próprio vs Efí PRO):**
  - **Três Provedores/Módulos Integrados:**
    1. *Mercado Pago (`mercadopago`):* PIX dinâmico com QR Code / Copia e Cola, Cartão de Crédito e Boleto Bancário. Webhook dedicado (`/webhook/mercadopago`) com assinatura criptográfica HMAC SHA-256 e sincronizador em segundo plano.
    2. *Efí PRO / Gerencianet (`efi`):* PIX Open Finance com autenticação mTLS via certificado `.p12`, Boleto Bancário com código de barras FEBRABAN e Carnês bancários oficiais registrados diretamente na API `/v1/carnet` da Efí Bank.
    3. *Carnê Próprio Foco 7 (`proprio`):* Emissão interna de carnês de mensalidades vinculados ao cliente sem taxas bancárias por boleto da Efí, gerando parcelas automáticas com QR Code Pix direto, linha digitável e PDF próprio para impressão e envio ao cliente via WhatsApp ou e-mail.
  - **Decisão Interativa de Emissão (Carnê Próprio vs Efí PRO):**
    - Disponível tanto na aba de Gateways (`/admin/gateways#tab-carne`) quanto na Gestão Financeira de Faturas (`/admin/faturas`).
    - Formulário com seletor visual e rádio buttons com descrições detalhadas entre "Carnê Próprio Foco 7" (sem tarifas bancárias) e "Carnê Bancário via Efí PRO" (registrado na API Efí com boletos bancários).
    - Confirmação inteligente via SweetAlert2 perguntando ao administrador qual modalidade deseja emitir, com opção de alternar instantaneamente com um clique antes do disparo da requisição AJAX.
  - **Sincronização & Webhook Unificado de Carnês:**
    - Tratamento de notificações v1 (`/v1/notification/:token`) no webhook `/webhook/efi` para conciliação automática de carnês e boletos emitidos via Efí PRO.
    - Comando de sincronização diária (`php artisan gateways:sincronizar-pagamentos`) consultando faturas pendentes da Efí via endpoint `/v1/carnet/{id}` e baixando parcelas quitadas.
---

## ⚡ Módulo 37: Checkout 100% Transparente (Sem Venda Fora do Site), Webhooks Dinâmicos por Requisição & API RESTful v1 Escalável

### 1. Checkout 100% Transparente & Experiência Nativa (Zero Redirecionamento Externo)
- **Venda Exclusiva no Site Foco 7:** O sistema não utiliza soluções de redirecionamento (como Checkout Pro ou páginas externas de instituições financeiras).
- **Processamento Inline Multigateway:**
  - **PIX Instantâneo:** Geração em milissegundos com QR Code renderizado no modal do site e código Copia e Cola com botão de cópia com 1 clique.
  - **Cartão de Crédito Transparente:** Entrada de dados e tokenização direta no navegador. Suporte integral tanto na Efí PRO quanto no Mercado Pago, retornando aprovação imediata ou status de processamento na própria tela.
  - **Boleto Bancário:** Retorno imediato da linha digitável copiada e visualizador do boleto embutido.
  - **Carnê em PDF:** Emissão sequencial de lâminas (Carnê Próprio Foco 7 ou Oficial Efí PRO) com visualizador e download direto em PDF.

### 2. Webhooks Dinâmicos por Requisição
- **Rastreamento Individualizado:** A cada emissão de cobrança (Pix, Boleto, Cartão ou Carnê), o sistema injeta dinamicamente o parâmetro `notification_url` com identificadores únicos da fatura (`fatura_codigo`, `carne_codigo`) e timestamp de requisição (`t=...`).
- **Conciliação e Baixa Imediata:** Os controladores de webhook (`/webhook/mercadopago` e `/webhook/efi`) leem os parâmetros dinâmicos da URL para localizar a fatura instantaneamente e disparar os e-mails transacionais de confirmação e recibo ao cliente.

### 3. API RESTful v1 Escalável para Integrações & Aplicativos Móveis
- **Arquitetura Oficial de Rotas (`routes/api.php`):** Registrada oficialmente no ciclo de inicialização do Laravel 13 (`bootstrap/app.php`) sob o prefixo `/api/v1/`.
- **Endpoints Públicos de Consulta:**
  - `GET /api/v1/ping`: Health check e diagnóstico de serviços com timestamp, timezone e versão.
  - `GET /api/v1/planos`: Catálogo de planos com preços, benefícios, taxas de adesão e destaques.
  - `GET /api/v1/filiais`: Listagem de unidades e filiais para mapas com coordenadas GPS e WhatsApp.
  - `GET /api/v1/faturas/{codigo}`: Consulta detalhada de fatura por código ou ID com QR Code Pix, linha digitável e link de carnê.
- **Endpoints de Checkout Transparente na API:**
  - `POST /api/v1/checkout/calcular-taxas`: Simulação de valores com repasse de taxas e descontos em tempo real.
  - `POST /api/v1/checkout/gerar`: Processamento de checkout seguro via API REST com geração de faturas e retorno dos payloads para consumo nativo por aplicativos móveis ou plataformas parceiras.
- **Endpoints Administrativos Protegidos (`ApiAuthKey`):**
  - Autenticação via cabeçalhos `Authorization: Bearer <token>` ou `X-API-Key: <token>`.
  - `GET /api/v1/admin/metricas`: KPIs de faturamento, faturas quitadas/pendentes e receita mensal.
  - `GET /api/v1/admin/clientes`: Listagem paginada de clientes com busca e filtros.
  - `POST /api/v1/admin/carnes/emitir`: Emissão programática de carnê escolhendo explicitamente entre `tipo_carne: "proprio"` ou `"efi"`.
  - `POST /api/v1/admin/faturas/{id}/estornar`: Estorno auditado de fatura quitada com justificativa.

- **Créditos & Suporte Técnico do Desenvolvedor (Área Restrita com Tarja, Avatar Oficial & Criptografia no Banco):**
  - Contato oficial do desenvolvedor Marcelo Brad configurado com WhatsApp direto `+55 (21) 98132-5441` (`5521981325441`), Telegram `@marcelobrad` e blindagem no core impedindo sobrescrita acidental com dados da empresa cliente.
  - **Criptografia AES-256 & Proteção Anti-Adulteração no Banco de Dados:**
    - Todos os dados sensíveis de autoria e contato (`desenvolvedor_nome`, `desenvolvedor_whatsapp`, `desenvolvedor_telegram`, `desenvolvedor_link`, `desenvolvedor_avatar`, `dev_master_password`) são **criptografados no banco de dados** via AES-256 e assinados com HMAC de integridade.
    - Caso alguém acesse o MySQL diretamente (phpMyAdmin, cPanel ou SQL injection) e tente forjar ou alterar os valores em texto puro ou com chaves inválidas, a descriptografia falha e o sistema **rejeita a adulteração**, restaurando e exibindo automaticamente os dados oficiais do desenvolvedor Marcelo Brad.
  - **Avatar Oficial do Desenvolvedor:**
    - Exibição da foto avatar de perfil no cabeçalho do box flutuante de atendimento direto (`#boxDevContato`) com indicador online verde de alta precisão.
    - No painel administrativo (`/admin/configuracoes`), conta com pré-visualização circular, upload direto de imagem e efeito de tarja blur quando bloqueado.
  - **Proteção Visual & Blindagem por Senha Master:**
    - No painel administrativo (`/admin/configuracoes`), os campos de autoria e contato do desenvolvedor ficam **tarjados por padrão** (`filter: blur(6px)` e máscara de caracteres `••••••••••••`), desabilitados para leitura e edição.
    - Desbloqueio seguro e interativo via **SweetAlert2** solicitando a **Senha Master do Desenvolvedor** (`Brad@2026`, o próprio WhatsApp `21981325441` ou chave personalizada via `.env`).
    - O backend bloqueia categoricamente qualquer tentativa de salvar ou sobrescrever os dados de autoria caso a requisição não possua a autenticação válida do desenvolvedor.

- **Acesso Supervisionado (Impersonation) no Painel do Cliente com Retorno Contextual:**
  - **Início da Supervisão:** O administrador pode acessar o painel de qualquer cliente ativo diretamente pela listagem de clientes (`/admin/clientes`) ou pelo modal de edição, com botão dedicado `<i class="fas fa-user-secret"></i>`.
  - **Badge de Supervisão Integrado na Navbar:** Durante todo o período em que estiver navegando no painel do cliente, um badge translúcido de alta visibilidade (`.navbar-supervisao-badge`) fica integrado de forma centralizada e harmônica na barra superior de navegação com o alerta "MODO SUPERVISIONADO" e identificação do cliente, sem quebrar o layout nem cobrir o cabeçalho do AdminLTE 4.
  - **Botão Flutuante Não-Intrusivo com Arraste Livre:** O botão flutuante **"Voltar ao Administrador"** (`.btn-flutuante-voltar-admin`) foi posicionado na lateral direita (`bottom: 74px; right: 24px`), perfeitamente empilhado acima do botão de WhatsApp e abaixo do botão de voltar ao topo, sem colidir com a sidebar esquerda nem cobrir menus. O botão possui suporte a arraste livre (drag & drop) com persistência de posição na sessão, além de acesso rápido via dropdown de perfil.
  - **Função Voltar ao Administrador:** O administrador pode clicar a qualquer momento em **"Voltar ao Administrador"** para encerrar a supervisão; a sessão original é restaurada e o usuário é redirecionado **exatamente para a mesma página anterior** de onde iniciou a supervisão.

---

## 📱 Módulo 38: Cobranças Automatizadas via WhatsApp & Evolution API Go (v3.0) com Criptografia AES-256 no Banco

### 1. Painel de Controle Dedicado (`/admin/whatsapp`)
- **Gestão de Conexão em Tempo Real:**
  - Status em tempo real com badges visuais (Conectado, Desconectado, Conectando, QR Code Pendente).
  - Indicador de segurança com selo **Criptografia AES-256 Ativa** no banco de dados.
  - Ações de 1 clique: Conectar, Obter QR Code, Desconectar, Forçar Reconexão e Logout formal da sessão do WhatsApp.
  - **Modal de Pareamento com QR Code Dinâmico:** Exibição do QR Code gerado pela Evolution API com atualização automática a cada 5 segundos e auto-fechamento com SweetAlert2 assim que a conexão for confirmada.
  - **Criação de Instâncias via Admin:** Modal para criação de novas instâncias do WhatsApp diretamente pela `globalApikey`, permitindo definir nome, token e opções avançadas (rejeição de chamadas, ignorar mensagens de grupos, always online, sincronização de histórico e leitura automática de status).

### 2. Criptografia AES-256 no Banco de Dados MySQL
- **Blindagem Contra Vazamento de Servidor ou SQL Dump:**
  - Todas as credenciais sensíveis da Evolution API (`evolution_api_url`, `evolution_global_apikey`, `evolution_apikey`, `evolution_instance`, `evolution_pix_key`, `evolution_webhook_secret`) são encriptadas automaticamente no banco de dados através da classe `CmsSetting` utilizando o algoritmo `AES-256-CBC` do Laravel (`Crypt::encryptString`).
  - Nenhum token, chave mestra ou credencial do WhatsApp fica armazenado em texto puro. Mesmo em caso de dump do MySQL, acesso indevido ao phpMyAdmin ou backup interceptado, os dados permanecem cifrados (`eyJpdiI6...`).

### 3. Suporte Integral a Todos os Formatos da Evolution API Go (v3.0)
- **Botões Interativos (`send/interactive` com tipo `button`):**
  - Botão de URL segura para pagamento online no checkout transparente do site.
  - Botão de ação nativa de cópia do código PIX (`cta_copy`), permitindo ao cliente copiar a chave PIX com 1 único toque.
- **Botão Nativo PIX (`send/pix`):**
  - Payload específico da versão Go v3.0 com `keyType`, `pixKey` e valor formatado para transferência instantânea.
- **Botão Review and Pay (`send/button` com `review_and_pay`):**
  - Fluxo nativo do WhatsApp com cabeçalho, corpo explicativo e rodapé institucional.
- **Texto Puro Formatado (`send/text`):**
  - Mensagens ricas com tipografia WhatsApp (`*negrito*`, `_itálico_`), emojis temáticos e quebra de linha.
- **Anexo de Mídia / Carnê em PDF (`send/media`):**
  - Envio automático de faturas e carnês em PDF gerados no servidor com legenda informativa.

### 4. Integração Nativa com a Régua de Cobrança (`ReguaCobrancaService`)
- **Automação Completa nas 7 Etapas:**
  - Disparos preventivos pré-vencimento: 1º Lembrete (ex: 5 dias antes), 2º Lembrete (ex: 3 dias antes) e 3º Lembrete (ex: 1 dia antes).
  - Cobrança no dia do vencimento (D-0).
  - Avisos pós-vencimento: 1º Aviso (D+1), 2º Aviso com juros e multa (D+5) e 3º Aviso com alerta de suspensão (D+10).
- **Controle Granular por Canal:** Opção de habilitar/desabilitar disparos por WhatsApp de forma global ou individual para cada etapa da régua, independente do e-mail.
- **Blindagem de Faturas Quitadas:** Nenhuma mensagem é enviada para faturas pagas, canceladas ou liquidadas.

### 5. Cobrança Manual de Faturas com 1 Clique (`/admin/faturas`)
- Botão "Cobrar via WhatsApp" presente na listagem de faturas pendentes e vencidas.
- Modal de confirmação inteligente com SweetAlert2 exibindo o nome do cliente, código da fatura e telefone de destino.
- Processamento assíncrono via AJAX com retorno imediato via Toastr e SweetAlert2 (**zero `alert()` nativo**).

### 6. Notificação Instantânea de Pagamento & Webhook Oficial
- Envio automático de mensagem de confirmação e agradecimento assim que a fatura é compensada via Mercado Pago ou Efí PRO.
- Endpoint seguro `/webhook/evolution` para recepção de eventos da Evolution API Go (conexão, QR Code e status de mensagens).

---

## 🚀 Módulo 39: Programa de Licenciados FOCO 7 & Distribuição de Software (White Label, Servidor Próprio cPanel/VPS/Cloud & Validação Remota)

### 1. Visão Geral do Modelo de Negócio White Label
- **Empreendedorismo e Franquia de Software:** Concessão de licenças independentes para empresários e parceiros montarem sua própria empresa de rastreamento veicular utilizando o ecossistema completo da FOCO 7.
- **Liberdade de Infraestrutura (Servidor Próprio):** O licenciado pode hospedar o sistema no servidor de sua preferência: VPS Linux (Ubuntu 22.04 LTS / 24.04 LTS), Hospedagem Compartilhada cPanel, Cloud (AWS, DigitalOcean, Hetzner, Linode, Contabo), Servidor Dedicado ou containers Docker.
- **Marca 100% Personalizada (White Label):** Logotipo, nome fantasia, CNPJ, paleta de cores e domínio/subdomínio próprios. Seus clientes utilizam o sistema sem qualquer menção à Foco 7.
- **Faturamento Direto e Sem Royalties:** Recebimento de mensalidades e taxas de adesão via Pix, Boleto e Cartão de Crédito direto na conta bancária do licenciado (Mercado Pago ou Efí Bank), sem taxas ocultas ou cobrança por veículo rastreado.

### 2. Ciclo de Vida da Licença & Ativação Automática Pós-Pagamento
- **Chave de Licença Criptográfica Única:** Formato padronizado `FOCO7-XXXX-XXXX-XXXX-XXXX` com verificação de entropia e integridade.
- **Ativação Instantânea via Webhook de Pagamento:**
  - O processamento pós-pagamento em `WebhookController` (Mercado Pago, Efí Pix e Carnê) e a quitação manual em `FaturasController` ativam a licença imediatamente ao identificar a compensação da fatura.
  - O perfil do usuário é automaticamente promovido para o papel `licenciado`.
  - Disparo automático de e-mail transacional e mensagem no WhatsApp oficial via Evolution API Go informando a liberação da licença, chave de ativação e link direto para download do pacote.

### 3. Mecanismo de Validação Remota & Auto-Binding de Domínio (API REST v1)
- **Endpoint Público Seguro (`POST /api/v1/licenca/validar`):**
  - Recebe a chave de licença, domínio do servidor chamador e IP remoto.
  - Valida rigorosamente a existência da chave, status (`ativa`), limite de expiração e correspondência do domínio e IP autorizados.
- **Auto-Binding no Primeiro Acesso:**
  - Se a licença for emitida com o domínio autorizado em branco e o parâmetro `licenciamento_permitir_auto_binding` estiver ativo no CMS, o primeiro check-in realizado pelo servidor do parceiro vincula o domínio chamador de forma automática e segura, eliminando etapas manuais de configuração.
- **Telemetria e Auditoria:**
  - Armazenamento em tempo real do timestamp da última validação (`ultimo_checkin_em`), IP do servidor chamador (`ultimo_checkin_ip`) e metadados técnicos do servidor remoto.

### 4. Instalador Web Gráfico Integrado (`/instalar`)
- **Assistente Interativo de Configuração de Servidor:** O código distribuído no arquivo `.zip` já possui um instalador web gráfico completo (`InstaladorController` e views em `resources/views/instalador/`).
- **Verificações Automatizadas:**
  - Teste de extensões essenciais do PHP (BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO MySQL, Tokenizer, XML).
  - Teste e validação de permissões de escrita nos diretórios `storage/`, `bootstrap/cache/` e `.env`.
  - Teste de conexão ativa com o banco de dados MySQL fornecido pelo usuário.
  - Execução automatizada de migrações e seeders do sistema.
  - Criação do primeiro usuário Administrador Master com os dados personalizados da empresa do parceiro.

### 5. Empacotamento Dinâmico (.ZIP), Script VPS e Guia Markdown
- **Serviço Centralizado (`LicenciamentoService`):**
  - `gerarPacoteZipParaDownload(Licenca $licenca)`: Empacota dinamicamente todo o código-fonte limpo da aplicação, excluindo arquivos sensíveis do ambiente de desenvolvimento/produção (`.env`, `.git`, `.idea`, caches, logs e arquivos de lock), injetando automaticamente o arquivo `license.key` com a chave do licenciado, o script `install-vps.sh` e o guia `GUIA_INSTALACAO_SERVIDOR.md`. Incrementa automaticamente o contador de downloads da licença.
  - `gerarScriptInstalacaoVps(Licenca $licenca)`: Gera sob demanda um script em Shell Bash (`install-vps.sh`) sob medida para Ubuntu 22.04/24.04 LTS, automatizando a instalação de Nginx, PHP 8.3/8.5 FPM, Composer, MariaDB, criação de vhost e configuração de permissões.
  - `gerarGuiaInstalacaoMarkdown(Licenca $licenca)`: Documentação técnica completa em Markdown para instalação em cPanel e VPS.

### 6. Painel de Controle Administrativo (`/admin/licenciados`)
- **Gestão Completa de Licenças:** Listagem paginada com busca por chave, nome da empresa, cliente, e-mail ou domínio, e filtro por status (Ativa, Pendente, Suspensa, Bloqueada e Expirada).
- **Métricas em Tempo Real (AdminLTE 4):** Total de Licenças, Licenças Ativas, Pendentes de Setup e Total de Downloads Realizados.
- **Operações Administrativas Avançadas:**
  - Modal de Criação / Edição de Licença (vinculação com cliente existente ou cadastro rápido de novo titular, definição de versão, limites de veículos/clientes e validade).
  - Modal de Configurações do Programa (visibilidade no site, badge, títulos, taxa de adesão, mensalidade, versão de distribuição, WhatsApp de atendimento e auto-binding).
  - Download direto do pacote `.zip` e do script `install-vps.sh`.
  - Regeneração instantânea de chave de licença em caso de comprometimento da anterior.
  - Alternância rápida de status (Ativar, Suspender e Bloquear) e exclusão segura com SweetAlert2.
  - **100% de conformidade com AJAX, SweetAlert2 e Toastr globais (zero `alert()` nativo)**.

### 7. Portal do Licenciado na Central do Assinante (`/cliente/licenca`)
- **Interface Exclusiva do Licenciado:**
  - Card da Chave de Licença com tipografia monospace de alta visibilidade e botão de cópia rápida para o clipboard com feedback Toastr.
  - Formulário AJAX para o próprio licenciado vincular ou atualizar o domínio e IP de seu servidor.
  - Botões de alta conversão para download imediato do Pacote Completo do Sistema (.ZIP), do script de instalação VPS (`install-vps.sh`) e do manual técnico em Markdown.
  - Central de Guias Passo a Passo em abas interativas do Bootstrap 5:
    - *Aba 1: Hospedagem Compartilhada / cPanel* (Criação de MySQL, upload do ZIP, extração e execução do instalador gráfico em `/instalar`).
    - *Aba 2: VPS Linux Ubuntu 22.04 / 24.04* (Execução via SSH, permissões do Nginx e configuração do Cron Job do agendador de tarefas).
    - *Aba 3: Configuração de Apontamento DNS* (Registros do tipo A para subdomínio e domínio raiz).
    - *Aba 4: Suporte Técnico ao Licenciado* (Canal direto no WhatsApp exclusivo com mensagem personalizada).
- **Showcase Comercial para Clientes Não-Licenciados:** Apresentação de alto padrão visual destacando os benefícios do modelo White Label, precificação de adesão e botão direto para contratação imediata via WhatsApp.

### 8. Landing Page Oficial (Seção `#licenciado`)
- Seção institucional `#licenciado` configurável via CMS com os 4 pilares de valor da franquia de software (Servidor Próprio, 100% White Label, Lucro 100% Seu e Instalador Web Fácil).
- Card de oferta com exibição dinâmica do valor da adesão, benefícios inclusos e botões de ação integrados ao WhatsApp comercial e ao portal do licenciado.
- Links dedicados adicionados na Navbar principal do desktop e no Drawer Android deslizante com selo comemorativo.

---

### Módulo 14: WhatsApp Cobranças & Evolution API Go (v3.0) Centralizado com Tarifação Recorrente PIX
- **Integração Oficial com Cluster Evolution API Go (v3.0) & MCP Server:** Centralização oficial da comunicação e mensageria no servidor `https://api.kdkhost.com.br` com documentação OpenAPI/LLM em `/docs/llms.txt` e MCP Server `evolution-go`.
- **Suporte a Múltiplas Instâncias & Tarifação Recorrente PIX:**
  - Instância primária (`foco7`) inclusa para a empresa.
  - Instâncias adicionais para a empresa ou licenciados provisionadas no cluster central a **R$ 60,00 / mês (recorrente)** por instância, garantindo total governança, controle de spam e alta entregabilidade.
  - **Checkout Transparente PIX:** Cobrança recorrente automatizada direcionada para a conta Mercado Pago / Chave PIX `marcelobradrj+mp@gmail.com`, gerando QR Code dinâmico e código Copia e Cola no padrão oficial BACEN (EMV BR Code).
  - **Whitelabeling Estrito no Portal do Licenciado:** O licenciado visualiza o checkout institucional como taxa de infraestrutura e servidor Foco 7, sem exposição de nomes pessoais ou dados do gestor master.
  - **Suspensão Automática por Vencimento:** Rotina automatizada no agendador Cron diário que identifica instâncias com mensalidade vencida, altera seu status para `suspensa` e corta a conexão na Evolution API. Ao efetuar o pagamento via PIX, o sistema detecta a baixa em tempo real e reativa a instância instantaneamente com prorrogação de +30 dias.
- **Painel Administrativo `/admin/whatsapp` Reformulado e Modernizado:**
  - Indicador de status pulsante em tempo real e medidor de latência (ping em ms).
  - Grade de KPIs no topo: Servidor, Instâncias no Cluster (Ativas/Suspensas), Receita Recorrente Mensal e Total de Disparos.
  - Aba completa de Gestão de Instâncias & Cobrança PIX com tabela de vencimentos, botão de cobrança PIX imediata, confirmação manual, suspensão e reativação.
  - Simulador de Smartphone realista com mockup interativo de WhatsApp exibindo em tempo real a renderização da mensagem conforme edição de templates.
- **WhatsApp Dedicado no Portal do Licenciado (`/cliente/licenca`):**
  - Seção integrada para o franqueado/licenciado ativar e parear sua própria instância de WhatsApp com apenas 1 clique via AJAX.
  - Exibição da data de renovação e botão de pagamento PIX transparente com polling a cada 3 segundos.
  - Geração automática de credenciais `.env` (`EVOLUTION_API_URL`, `EVOLUTION_INSTANCE`, `EVOLUTION_APIKEY`) para colar no servidor próprio (cPanel/VPS).
  - Consulta de status de conexão em tempo real e leitor de QR Code integrado via SweetAlert2.
- **Régua Automatizada de Cobranças:** Cobranças automatizadas por etapas (Lembretes prévios 10, 5 e 1 dia antes; Aviso no dia do vencimento; 1º, 2º e 3º avisos de atraso com juros, multa e risco de suspensão; nova fatura gerada e confirmação de pagamento).
- **Formato PIX & Botões Interativos:** Suporte a botões interativos nativos (URL e Copiar Chave PIX) e formato `/send/pix` com geração de QR Code Copia e Cola na mensagem do WhatsApp.
- **Blindagem Estrita dos Parâmetros do Servidor por Senha Particular Master:**
  - URL da API, Nome da Instância, Token e Chave Global do servidor são criptografados com AES-256 no banco de dados (`cms_settings`).
  - Interface administrativa protegida: campos tarjados (`••••••••••••`) e com bloqueio de leitura (`readonly` e classe `.tarja-dev-bloqueado`).
  - Desbloqueio e bloqueio dinâmico com **SweetAlert2** mediante validação estrita da Senha Master Exclusiva do Desenvolvedor.
  - Salvamento 100% via **AJAX com preservação de dados (`.keep-data`)**, notificações via Toastr e modais de confirmação SweetAlert2 (zero `alert()` nativo).
- **Modo de Manutenção com Bypass Universal e Multi-Dispositivo:**
  - Validação estrita por Senha Master Exclusiva do Desenvolvedor, Token Técnico (`foco7_vip_bypass_2026`), IP Autorizado ou parâmetro seguro de URL (`?bypass=<senha_master>`).
  - Exclusão do cookie `foco7_maintenance_bypass` da criptografia do Laravel para permitir leitura transparente e interoperabilidade perfeita entre JavaScript e o middleware backend.

### Módulo 33: Reformulação Completa do Painel de Licenciados FOCO 7 & White Label (/admin/licenciados)
- **Hero Header com Status em Tempo Real & Toggle Instantâneo:** Indicador de status do programa White Label (Ativo no Site / Pausado) com switch de 1 clique via AJAX, preços de adesão e versão de distribuição.
- **Grade de KPIs Consolidados:** Total de Licenciados (Ativas, Pendentes, Suspensas), Servidores Conectados Hoje com Telemetria Ativa, Total de Downloads de Pacotes (.ZIP e VPS) e Receita Estimada em Adesões.
- **Navegação por Abas Especializadas:**
  - *Guia 1 (Licenciados & Chaves de Ativação):* Listagem interativa com pesquisa em tempo real, visualização das empresas, cópia de chaves criptográficas com 1 toque, domínios autorizados, limites de veículos/clientes, status com badges coloridos e ações rápidas (Ver Detalhes 360º, Editar, Baixar ZIP, Script VPS, Ativar/Suspender, Regenerar Chave e Excluir).
  - *Guia 2 (Configurações do Programa White Label):* Painel de configuração direta com switch de visibilidade na landing page, badge da oferta, título, subtítulo, taxas de adesão e mensalidade, versão atual e switch de auto-binding no primeiro acesso.
  - *Guia 3 (Guia de Instalação VPS / cPanel / Cloud):* Passo a passo técnico para deploy em servidores próprios, com script bash cURL one-liner para Ubuntu/Debian e guia para cPanel.
  - *Guia 4 (Instâncias WhatsApp dos Licenciados):* Tabela conectando diretamente com as instâncias criadas na Evolution API Go v3.0 para os licenciados, status de cobrança de R$ 60,00/mês e atalho para o checkout transparente PIX.
- **Modal de Detalhes 360º com Telemetria e JSON:** Consulta dados de hardware e servidor do último check-in do parceiro e pré-visualização do arquivo `license.key`.
- **Padrão Estrito Foco 7:** 100% AJAX, SweetAlert2 e Toastr (zero `alert()` nativo do navegador), responsividade completa e compatibilidade com Dark/Light Mode.

### Módulo 34: Editor Summernote no Contrato de Licenciamento White Label & Minuta Jurídica Oficial
- **Integração do Summernote WYSIWYG em `/admin/licenciados`:**
  - Substituição da textarea simples pelo editor visual oficial Summernote BS5 (`v0.8.20`), com toolbar completa, suporte nativo a Dark Mode e altura otimizada (`data-height="450"`).
  - Sincronização automática via AJAX antes do envio do formulário, garantindo salvamento íntegro de toda a formatação HTML do contrato no banco de dados (`cms_settings`).
  - Botão de ação rápida com SweetAlert2 para carregar/restaurar a minuta completa oficial a qualquer momento.
- **Minuta Contratual Jurídica e Comercial Robusta (8 Cláusulas):**
  - *Cláusula 1ª:* Objeto e concessão da licença de uso do software para servidor próprio em regime White Label.
  - *Cláusula 2ª:* Propriedade intelectual e direitos autorais protegidos (Leis Federais nº 9.609/1998 e nº 9.610/1998).
  - *Cláusula 3ª:* Isenção total de royalties e autonomia financeira (zero taxa por placa/veículo, 100% dos lucros para o parceiro).
  - *Cláusula 4ª:* Infraestrutura, servidor próprio, backups e conformidade com a LGPD (Lei nº 13.709/2018).
  - *Cláusula 5ª:* Instâncias de WhatsApp (Evolution API Go v3.0) com tarifação recorrente de R$ 60,00/mês e suspensão por vencimento.
  - *Cláusula 6ª:* Telemetria de integridade, chave criptográfica (`license.key`) e auto-binding de domínio.
  - *Cláusula 7ª:* Suporte técnico operacional e entrega de atualizações de versão.
  - *Cláusula 8ª:* Vigência vitalícia e eleição de foro.
- **Visualização do Contrato no Portal do Licenciado (`/cliente/licenca`):**
  - Nova aba interativa (Aba 5: *Termo de Licença & Diretrizes de Uso*) renderizando em tempo real o contrato formatado com badge de vigência ativa.

### Módulo 35: Gestão Financeira das Licenças White Label & Ciclo de Vida Automatizado das Instâncias WhatsApp
- **Controle Integral de Licenças (Ativação/Desativação e Modos de Operação):**
  - **Modo Automático:** Licença ativada instantaneamente após a compensação do PIX da Taxa de Adesão via Webhook ou baixa bancária; suspensão automatizada caso haja inadimplência na mensalidade recorrente.
  - **Modo Manual:** Permite ao administrador aprovar, liberar, ativar ou suspender licenças sob demanda, com alternância rápida de 1 toque (*toggle*) na listagem e no modal.
- **Parametrização Financeira da Licença:**
  - **Taxa de Adesão / Implantação:** Controle de valor monetário (R$) e status (`pago`, `pendente`, `isento`), com botão para gerar cobrança PIX imediata ou baixa manual.
  - **Mensalidade de Suporte Opcional:** Definição de valor mensal, status de adimplência (`em_dia`, `pendente`, `vencida`, `isento`), dia de vencimento (1 a 31) e data do próximo vencimento.
- **Ciclo de Vida 100% Automatizado das Instâncias WhatsApp Dedicadas:**
  - **Ativação Pós-Pagamento:** Ao quitar a fatura da instância via Webhook (Mercado Pago / Efí PRO), verificação por polling ou baixa manual no painel de faturas, o sistema identifica automaticamente a instância do parceiro (`FAT-WPP-` ou descrição da fatura), chama a Evolution API Go v3.0 central (`https://api.kdkhost.com.br`), estende a vigência em +30 dias e reativa a conexão instantaneamente.
  - **Suspensão por Vencimento:** Rotina diária do Cron (`routes/console.php` às 06:30 e 06:45) varre instâncias e licenças vencidas, desconectando/suspendendo no cluster Evolution Go aquelas que não registraram pagamento.
- **Novo Modal de Cobrança PIX para Licenciados (`#modalPixLicenca`):**
  - Exibição de QR Code de alta resolução em base64, código "Copia e Cola" com cópia em 1 clique, valor atualizado, link pré-formatado para envio da cobrança direto no WhatsApp do parceiro e botão de baixa manual assistida.
- **Painel Administrativo `/admin/licenciados` & Portal do Cliente `/cliente/licenca`:**
  - Tabela com colunas dedicadas para Modo, Adesão, Mensalidade, Status Geral e Telemetria.
  - 100% AJAX, notificações via Toastr e confirmações em SweetAlert2 (zero `alert()` nativo do navegador).

### Módulo 36: Reformulação da Seção White Label na Landing Page & Responsividade Fluida Full-Width
- **Harmonização Visual e Fim do Encaixotamento Estreito:**
  - Expansão do card da oferta (`.card-oferta-licenciado`) para a largura completa da grade (`col-12`), eliminando o efeito comprimido (`col-lg-10 col-xl-9`) e alinhando simetricamente suas extremidades com a esteira dos 4 pilares superiores.
  - Gradiente superior dinâmico oficial FOCO 7 (`#d90000` a `#ff4d4d`), cantos arredondados modernos (`border-radius: 24px`) e sombra de elevação suave em duas camadas.
- **Esteira de 4 Pilares de Benefícios Aprimorada:**
  - Numeração sequencial de passos (`01`, `02`, `03`, `04`) em marca d'água elegante com transição suave no hover.
  - Micro-interações nos ícones com elevação, sombra avermelhada e redimensionamento proporcional.
- **Distribuição de Conteúdo de Alto Impacto:**
  - **Coluna de Especificações & Diferenciais (Esquerda):** Badge de topo pulsante, tipografia imponente e grade fluida de 6 diferenciais técnicos e comerciais em cards com ícones dedicados (Servidor Próprio, 100% White Label, WhatsApp Evolution Go, Gateways PIX/Cartão, Instalador Gráfico `/instalar` e Sem Royalties por Veículo).
  - **Painel de Conversão & Investimento (Direita):** Box interno refinado com fundo claro, preço de adesão em destaque ultra legível (`display-4 fw-black text-danger`), selo "Sem Royalties • Lucro 100% Seu", botão CTA de alta conversão para WhatsApp com mensagem pré-formatada, atalho para o portal do licenciado e micro-selos de credibilidade (Ativação Imediata, Código Homologado e Suporte Técnico).
- **Responsividade Total (Mobile-First):**
  - Adaptação perfeita para smartphones e tablets sem overflow horizontal, com padding proporcional, botões 100% full-width com ampla área de toque para polegares e empilhamento fluido de colunas.
- **Painel CMS Administrativo 100% Integrado (/admin/licenciados#tab-configuracoes):**
  - Controle dinâmico total sobre todos os textos, títulos, ícones e URLs da seção White Label pelo painel administrativo, dividido em 6 blocos organizados:
    1. *Cabeçalho da Seção:* Status de exibição no site, badge superior, título principal e subtítulo.
    2. *Os 4 Pilares de Benefícios:* Ícone FontAwesome, título e descrição de cada um dos 4 pilares.
    3. *Card Principal da Oferta (Esquerda):* Badge de topo, título do card, descrição do pacote e os 6 diferenciais com ícone, título e texto individuais.
    4. *Painel de Conversão & CTAs (Direita):* Badge do box, label do preço, selo sem royalties, subtexto, botão WhatsApp de conversão com mensagem automática pré-formatada, botão do portal do parceiro com URL de destino, e os 3 micro-selos de credibilidade (trust badges no rodapé).
    5. *Parâmetros Técnicos & Automação:* Versão liberada, WhatsApp de suporte, vínculo automático de domínio no 1º acesso (auto-binding), modo padrão para novas licenças e rotina diária de suspensão por inadimplência via Cron.
    6. *Minuta Jurídica Oficial:* Editor Summernote WYSIWYG completo para o Contrato de Licenciamento White Label com sincronização AJAX e restauração da minuta oficial.
  - Atalho direto adicionado na barra de abas de Conteúdo do Site (`/admin/cms`) apontando diretamente para as configurações do módulo White Label.

### Módulo 37: Blindagem Estrita de Custos de Instâncias WhatsApp & Ofuscação por Senha Master
- **Tarifação Real de Instâncias WhatsApp (Master & Extras):**
  - Eliminação do conceito de gratuidade ou "vitalícia" para a instância Master Foco 7. A instância principal (`Empresa Master`) possui acompanhamento financeiro de infraestrutura com mensalidade recorrente (R$ 60,00/mês).
  - Cálculo de Custo Recorrente Total consolidado no backend: `($totalInstanciasExtras + 1) * $custoInstanciaExtra`, refletindo o monitoramento de todas as instâncias ativas do servidor Evolution Go.
- **Proteção Estrita e Ofuscação de Mensalidades (Senha Master do Desenvolvedor):**
  - Na tela `/admin/whatsapp` (Aba *Instâncias & Cobrança PIX* e cards KPI superiores), todos os custos mensais, valores de recorrência, dados de vencimento e identificadores privados são mascarados com tarjas de segurança (`••••••` / Protegido por Senha Master) para administradores comuns.
  - O campo de edição *Valor por Instância (R$)* (`custo_instancia_extra`) e a conta de recebimento (`pix_email_recorrencia`) são blindados tanto no frontend (input protegido com senha/tarja, readonly, botão com chave master) quanto no backend (`WhatsappCobrancaController@salvar`), impedindo alterações não autorizadas via requisições diretas ou inspeção F12 sem a validação da Senha Master na sessão.
  - Ao autenticar com a Senha Master via modal SweetAlert2, o sistema valida a credencial via AJAX, desmascara os dados na sessão e atualiza a interface para edição segura.

### Módulo 38: Recorrência Direta no Mercado Pago do Desenvolvedor, Cobrança Preventiva Automática & Reativação Instantânea em Segundos
- **Recebimento Direto no Mercado Pago do Desenvolvedor:**
  - Suporte ao Access Token exclusivo do Mercado Pago (`evolution_mp_access_token`) protegido por Senha Master, garantindo que as mensalidades das instâncias (R$ 60,00/mês) sejam creditadas 100% direto na conta pessoal do desenvolvedor.
  - Geração de cobranças PIX dinâmicas via API (`POST /v1/payments`) com dados do pagador, valor, expiração e webhook de retorno configurado.
- **Autonomia Total sem Acesso ao Painel do Cliente:**
  - **Página Pública de Pagamento Direto (`/pagar/instancia/{hash}`):** Permite ao cliente ou licenciado abrir o checkout pelo celular ou desktop sem precisar fazer login. A tela exibe o QR Code em alta definição, botão com cópia em 1 clique do código PIX Copia e Cola, e monitoramento em tempo real via AJAX a cada 3 segundos, que transiciona automaticamente para o feedback de sucesso assim que o banco aprova a transferência.
  - **Régua Automática Preventiva Diária (Cron às 08:00):** Varre as instâncias com vencimento em até 3 dias, 1 dia ou no dia do vencimento, gerando o PIX no Mercado Pago e disparando a notificação contextual com o código Copia e Cola e link de pagamento direto no WhatsApp do responsável sem nenhuma intervenção humana.
- **Operação Contínua sem Interrupção de Serviços (Zero Downtime):**
  - **Carência Protetiva de 24 Horas:** Tolerância inteligente na rotina de suspensão para não interromper a operação do cliente de surpresa durante o dia do vencimento.
  - **Prorrogação Cumulativa:** Pagamentos antecipados somam +30 dias a partir da data de vencimento futura, garantindo que o cliente não perca os dias já vigentes.
  - **Reativação Instantânea via Webhook (`/webhook/mercadopago`):** Ao aprovar o pagamento no Mercado Pago, o webhook baixa a fatura em milissegundos, reativa a instância, chama a API da Evolution Go (`/instance/connect`) para religar no cluster imediatamente e envia notificação no WhatsApp do responsável confirmando a renovação e vigência estendida.
  - **Sincronizador Redundante no Cron (A cada 10 min):** Varredura de contingência que consulta faturas pendentes de instâncias no Mercado Pago via API, blindando o sistema contra eventuais atrasos ou instabilidades de rede do webhook.

### Módulo 39: Sincronização em Tempo Real com Cluster Evolution Go v3.0, Detecção de Status `open` & Resiliência Administrativa via Global API Key
- **Sincronização em Tempo Real com o Manager Oficial (`api.kdkhost.com.br/manager/instances`):**
  - Consulta prioritária no cluster Evolution Go v3.0 via `GET /instance/all` e `GET /instance/fetchInstances` utilizando a Global API Key administrativa (`EVOLUTION_GLOBAL_APIKEY`).
  - Identificação imediata da instância ativa (`foco7`), capturando em tempo real o status operacional (`status: open` / `connected`), número proprietário conectado (`5521981900390:35`) e token sincronizado.
  - Renderização instantânea do badge verde pulsante **CONECTADO** tanto no carregamento inicial da página (`WhatsappCobrancaController@index`) quanto nas consultas assíncronas AJAX (`admin.whatsapp.status`), eliminando falsos positivos de desconexão.
- **Resiliência Administrativa Global (Zero Falhas de Autorização 401/403):**
  - Mecanismo de fallback transparente no motor HTTP (`EvolutionApiService::request()`): se qualquer chamada direcionada à instância for rejeitada com HTTP 401 ou 403 por token de instância incompatível ou expirado, o sistema executa automaticamente um retry de superadministrador utilizando a Global API Key com headers de instância (`instance: foco7`).
  - Garantia de que 100% dos comandos do sistema (disparo de mensagens de teste, cobranças PIX, botões interativos, consultas de status e reconexões) operem continuamente sem bloqueios.
- **Suporte Multirrotas para Evolution Go v3.0 e Node.js:**
  - Compatibilidade com as rotas `POST /send/text`, `POST /send/text/{instance}` e `POST /message/sendText/{instance}` com payload adaptativo.
  - Tratamento inteligente no modal "Conectar / QR Code": ao detectar que a instância já está autenticada, informa imediatamente o status operacional com o número pareado sem disparar erros ou desconexões desnecessárias.

### Módulo 40: Segundo Módulo Mercado Pago em Paralelo (Exclusivo do Desenvolvedor), Regra de Instância Própria do Administrador & Padronização Global de Ícones
- **Segundo Módulo Mercado Pago em Paralelo (`/admin/whatsapp` - Aba "Mercado Pago (Desenvolvedor)"):**
  - Módulo 100% independente do gateway Mercado Pago da empresa (`/admin/gateways`), vinculado exclusivamente ao Desenvolvedor para cobrar **somente** as instâncias criadas dentro do Evolution Go através deste painel — **inclusive a instância atual (`foco7`) usada para configuração**.
  - Isolamento estrito de credenciais (`dev_mp_ativo`, `dev_mp_ambiente`, `dev_mp_public_key_prod`, `dev_mp_access_token_prod`, `dev_mp_client_id_prod`, `dev_mp_client_secret_prod`, `dev_mp_public_key_sandbox`, `dev_mp_access_token_sandbox`, `dev_mp_client_id_sandbox`, `dev_mp_client_secret_sandbox`, `dev_mp_webhook_secret`, `dev_mp_beneficiario`), criptografadas com AES-256 e protegidas por **Senha Master Exclusiva**.
  - Botão de diagnóstico em tempo real **"Testar Conexão MP (Dev)"** (`POST /admin/whatsapp/testar-mp-dev`) e roteamento dual inteligente em `/webhook/mercadopago?origem=instancia_wpp`.
- **Regra Obrigatória de Criação de Instância Própria para Cobranças via WhatsApp (Fallback Automático via E-mail):**
  - A instância `foco7` é reservada à configuração técnica do servidor. O Administrador **deve criar e ativar sua própria instância** através deste painel (efetuando o pagamento via PIX no Módulo Mercado Pago do Desenvolvedor) antes de começar a realizar suas cobranças de clientes via WhatsApp.
  - **Fora isso, só é possível cobrar clientes via E-mail:** enquanto o administrador não criar sua própria instância e ela não estiver com pagamento `ativa` e em dia, qualquer cobrança manual (`WhatsappCobrancaController@cobrarFatura`) ou automática (`ReguaCobrancaService::dispararEtapa`) bloqueia o canal WhatsApp e envia a cobrança automaticamente **via E-mail**, exibindo aviso claro com atalho para criação/ativação da instância própria.
  - Ações administrativas de baixa manual ("Confirmar Pagamento Manual"), suspensão, reativação sem compensação bancária e exclusão de instâncias exigem validação da **Senha Master do Desenvolvedor**.
- **Padronização Visual Global de Ícones e Utilitários de Opacidade no Painel Admin:**
  - Suporte completo às classes `.bg-opacity-5` a `.bg-opacity-100` e correção de contraste para `.bg-primary`, `.bg-warning`, `.bg-success`, `.bg-danger` e `.bg-info` nos modos Claro e Escuro em `public/css/admin-foco7.css`, eliminando círculos sólidos sem ícone visível nos KPIs.
  - Padronização de todos os ícones de submenus da sidebar administrativa (`resources/views/admin/layouts/app.blade.php`) com ícones semânticos do FontAwesome 6.

### Módulo 41: Campos Dinâmicos Sandbox/Produção, Webhook Dinâmico por Requisição & Retorno Automático com Checkout Transparente Íntegro (Somente PIX)
- **Alternância Dinâmica entre Sandbox e Produção no Módulo Paralelo (`/admin/whatsapp`):**
  - Seleção em tempo real entre **Produção (Recebimento Real PIX)** e **Sandbox (Homologação / Testes PIX)** via seletor ou botões de ação rápida, exibindo/ocultando dinamicamente os cards de credenciais correspondentes (`#boxCredenciaisMpDevProd` e `#boxCredenciaisMpDevSandbox`) com os 4 campos completos (`Public Key`, `Access Token`, `Client ID` e `Client Secret`).
  - O teste de conexão (`POST /admin/whatsapp/testar-mp-dev`) envia dinamicamente o ambiente selecionado e o token em edição na tela para validação imediata na API do Mercado Pago.
- **Webhook Dinâmico a Cada Requisição (`EvolutionApiService::gerarCobrancaPixInstancia`):**
  - Cada requisição de cobrança PIX gera um `req_token` único e injeta automaticamente na chamada `POST https://api.mercadopago.com/v1/payments` uma `notification_url` exclusiva por requisição (`/webhook/mercadopago?origem=instancia_wpp&modulo=mp_dev_paralelo&metodo=pix&ambiente={producao|sandbox}&wpp_instancia={nome}&fatura_id={id}&fatura_codigo={codigo}&hash={hash}&req_token={token}&ts={timestamp}`) com cabeçalho `X-Idempotency-Key` único.
  - O `WebhookController@mercadopago` lê o ambiente (`producao` ou `sandbox`) e os identificadores diretamente da query string do webhook dinâmico, selecionando automaticamente a credencial correta do Módulo Paralelo do Desenvolvedor para validar e dar baixa instantânea.
- **Checkout Transparente Íntegro (Cobrança via PIX Apenas) com Retorno Automático:**
  - Operação estritamente via PIX transparente (`payment_method_id: 'pix'`), tanto no modal administrativo (`#modalCheckoutPixInstancia`) quanto na página pública (`/pagar/instancia/{hash}`), com botão para regerar QR Code PIX e novo Webhook Dinâmico sob demanda.
  - **Retorno Automático Pós-Compensação:** Assim que o pagamento PIX é confirmado (via Webhook Dinâmico ou radar AJAX a cada 3 segundos), o checkout transparente transiciona automaticamente para a tela interna de confirmação exibindo a nova vigência (+30 dias) e executa contagem regressiva automática para recarregar/retornar ao painel com a instância ativa e cobranças via WhatsApp liberadas.

---

## 🧪 Testes Automatizados e Garantia de Qualidade

A aplicação conta com uma suíte de testes de integração automatizados (`tests/Feature/` e `tests/Unit/`):
- **161 testes executados, 161 aprovados (100% de sucesso), 722 asserções**.
- Cobertura completa: Acesso supervisionado (impersonation) de cliente com retorno contextual na mesma página e bloqueio para clientes comuns, criptografia AES-256 no banco de dados e rejeição de adulteração direta via SQL, avatar do desenvolvedor e exibição no layout, blindagem por senha master e tarja dos dados do desenvolvedor, API REST v1 versionada (ping, planos, filiais, checkout transparente, consulta de faturas, métricas administrativas e emissão de carnê dual), gestão inteligente de carnê (cancelamento contextual de parcela única, sequência pendente, carnê completo e exclusão em lote de todas de uma única vez), segurança de sessão e auto-logout por inatividade, blindagem de cobrança para faturas quitadas na régua e cron jobs, usuários e permissões granulares, ciclo de vida do perfil pessoal e endereço ViaCEP, autenticação em duas etapas (2FA TOTP RFC 6238, QR code, desafio no login e códigos de emergência), unidades, filiais e mapa interativo OpenStreetMap com geocodificação e traçado de rotas, bloqueio geográfico de países fora das Américas, parceiros em carrossel infinito, depoimentos, checkout transparente Efí (Pix, Boleto, Cartão e Carnê em PDF), fullcalendar, webhooks dinâmicos e isolamento ACL.

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


