# Changelog - Sistema Foco 7

Todas as alterações notáveis deste projeto serão documentadas neste arquivo.
O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), mantendo sempre **o registro mais recente no topo** (ordem cronológica inversa).

## [1.8.0] - 2026-10-27

### 💳 Gestão Financeira: Cancelamento Inteligente de Carnê/Parcelas e Exclusão em Lote de Todas de uma Única Vez
- **Lógica Inteligente e Contextual de Cancelamento de Carnê:**
  - Ao solicitar o cancelamento de uma parcela vinculada a carnê, o sistema consulta em tempo real o histórico financeiro contábil e abre diálogo modal com SweetAlert2 contendo opções adaptadas:
    - **Se houver parcelas já pagas:** O sistema pergunta se o usuário deseja cancelar **somente esta parcela** ou **esta e toda a sequência ainda pendente** (da parcela selecionada em diante), preservando intactas as parcelas já quitadas para conformidade contábil.
    - **Se não houver nenhuma parcela paga:** O sistema pergunta se o usuário deseja cancelar **somente esta parcela** ou **o carnê completo / todas as parcelas** de uma só vez.
- **Opção de Deletar Todas as Parcelas de uma Única Vez:**
  - Implementação de funcionalidade para **deletar todas as parcelas do carnê de uma única vez**, disponível tanto no diálogo de confirmação de exclusão quanto no menu rápido de ações em cascata (botão split na listagem).
  - Proteção contábil ativa: caso existam parcelas pagas, apenas as pendentes são deletadas e as quitadas permanecem salvas para auditoria. Caso não haja parcelas pagas, todas as parcelas vinculadas ao carnê são removidas e o carnê é cancelado na API da Efí.
- **Integração com a API Efí (v1):**
  - Métodos `cancelarCarneEfi` e `cancelarParcelaCarneEfi` integrados ao `EfiPaymentService` para sincronização automática com o gateway de pagamento.
- **Interface e Experiência do Usuário (UI/UX):**
  - Badges informativos de carnê na listagem de faturas (`Parcela X/Y`).
  - Menu de ações rápido em cascata no botão de exclusão de parcelas de carnê.
  - Conformidade estrita com SweetAlert2, Toastr e AJAX sem recarregar a tela desnecessariamente.
- **Suíte de Testes Automatizados:**
  - Novo arquivo de testes `tests/Feature/CancelamentoEExclusaoCarneTest.php` com 8 testes cobrindo todos os cenários de cancelamento individual, sequência pendente, cancelamento integral e deleção em lote de todas de uma única vez. Totalizando 141 testes e 643 asserções com 100% de sucesso.

---

## [1.7.1] - 2026-10-12

### 🛠️ Modo de Manutenção: Persistência de Dados via AJAX (.keep-data), Wallpaper Drag & Drop (FocoDropzone) e Barra de Alerta Administrativa
- **Correção da Persistência dos Campos do Formulário (`.keep-data`):**
  - Adicionada a classe `keep-data` ao formulário de manutenção (`resources/views/admin/manutencao/index.blade.php`), impedindo o reset automático universal do `global-ajax.js` ao salvar com sucesso. Todas as informações inseridas (título, mensagem explicativa, data de contagem regressiva, token de bypass, IPs liberados e opções de contato) permanecem preservadas na tela.
- **Sincronização de Status Direta no Formulário:**
  - Inclusão do interruptor switch (`manutencao_ativa`) diretamente dentro do formulário de configurações, além do card de ativação rápida no topo. O método `ManutencaoController::salvar` agora sincroniza o status ativo/inativo enviado no formulário com validação e cache inteligente.
- **Upload de Imagem de Fundo (Wallpaper) com Drag & Drop (FocoDropzone):**
  - Implementação completa do componente global `FocoDropzone` no painel de manutenção com arrastar e soltar (drag & drop), seleção manual de arquivo, detecção automática de proporções (1920x1080), preview em tempo real via `FileReader`, limites de tamanho (5MB) e botão de remoção/restauração para o wallpaper padrão.
  - Disponibilização do wallpaper oficial de alta tecnologia automotiva (`public/img/manutencao-bg.jpg`) com linhas de telemetria GPS e iluminação neon.
  - Suporte ao upload com salvamento em `public/uploads/manutencao/` e limpeza preventiva da imagem anterior customizada.
- **Página de Manutenção com Wallpaper Dinâmico e Glassmorphism (`site.manutencao`):**
  - Estilização premium da tela de manutenção com suporte a `background-image` dinâmico em tela cheia, overlay com gradiente e desfoque (`backdrop-filter: blur(6px)`), contagem regressiva em tempo real com números monoespaçados tabulares e botões de contato oficiais.
- **Barra de Aviso Fixa na Landing Page para Administradores:**
  - Quando o modo de manutenção está ativado e o administrador navega pela Landing Page pública (`site.index`), uma barra fixa de topo em gradiente vermelho alerta que a manutenção está ativa para o público comum, fornecendo atalhos diretos para "Ver como Visitante" e "Painel de Manutenção".
- **Pré-Visualização em Tempo Real (`admin.manutencao.preview`):**
  - Nova rota `/admin/manutencao/preview` e parâmetro `?preview_manutencao=1` permitindo que administradores visualizem a qualquer momento a experiência exata exibida aos visitantes normais, acompanhado de selo de identificação prévia.
- **Sanitização de IPs Liberados Padrão:**
  - Remoção de `127.0.0.1` dos IPs liberados por padrão nos seeders para evitar que testes em janelas anônimas no ambiente local ignorassem o modo de manutenção involuntariamente.

---

## [1.7.0] - 2026-10-09

### ⚙️ Gestão Dinâmica de Tarefas Cron, Cadastro Manual, 5 Rotinas Oficiais & Animações no Botão
- **Crontab Principal Confirmado e Monitorado no cPanel:**
  - Linha mestra `* * * * * cd /home/fococomb/public_html && /usr/local/bin/php artisan schedule:run >> /home/fococomb/public_html/storage/logs/cron.log 2>&1` ativa e monitorada por batimento cardíaco (*pulse dot* verde neon) a cada 60 segundos.
- **Tabela e Model de Tarefas Cron Dinâmicas (`cron_jobs`):**
  - Criação da migration `2026_10_09_000011_create_cron_jobs_table` e model `CronJob` para gerenciamento em banco de dados com suporte a comandos Artisan e requisições HTTP URL.
  - Orquestração dinâmica em `routes/console.php`: qualquer tarefa ativa criada pelo administrador passa a ser executada automaticamente pelo agendador do Laravel de acordo com sua expressão cron.
- **5 Rotinas Oficiais Pré-Cadastradas no Sistema:**
  1. `Verificação de Faturas Vencidas` (`faturas:verificar-vencidas`): Diariamente às 06:00
  2. `Lembrete Preventivo de Vencimento` (`faturas:enviar-lembretes`): Diariamente às 08:00
  3. `Sincronização de Pagamentos dos Gateways` (`gateways:sincronizar-pagamentos`): A cada 15 minutos
  4. `Limpeza e Manutenção do Sistema` (`sistema:limpeza-manutencao`): Diariamente às 03:00 (limpeza de tokens expirados e poda preventiva de logs)
  5. `Lembrete de Agendamentos e Instalações` (`agendamentos:lembrete-dia`): Diariamente às 07:00 (lembrete de serviços de rastreamento e estética agendados para a data)
- **Cadastro e Gestão Completa de Tarefas Personalizadas (CRUD):**
  - Modal interativo para cadastro de novas tarefas manuais (`+ Cadastrar Outra Tarefa`) com seletor de frequências pré-definidas (a cada minuto, 5 min, 15 min, 30 min, 1 hora, diário, semanal ou expressão customizada).
  - Suporte a edição de horários e exclusão de rotinas manuais com proteção contra remoção acidental de rotinas nativas de sistema.
  - Switches em tempo real para ativar ou pausar qualquer tarefa via AJAX sem recarregar a página.
- **Micro-Animações no Botão do Cron:**
  - Efeito *shimmer* reflexivo com elevação suave (`transform: translateY(-2px)`).
  - Ícone de play animado no hover (`animation: cronIconPulse`).
  - Efeito radar com rotação contínua de engrenagem (`animation: cronSpin` e `cronRadarGlow`) durante a execução sob demanda com modal informativo SweetAlert2.

---

## [1.6.9] - 2026-10-09

### ⏱️ Monitoramento em Tempo Real do Cron, Batimento Cardíaco (Heartbeat) & Registro Persistente
- **Batimento Cardíaco a Cada Minuto (`routes/console.php`):**
  - Implementação da rotina `cron-heartbeat` disparada a cada 60 segundos pelo agendador do Laravel no cPanel, atualizando continuamente o timestamp de atividade do servidor.
- **Monitoramento em Tempo Real no Painel (`/admin/cron`):**
  - **Status Visual do Agendador:** Card com indicador pulsante (*pulse dot*) verde neon, status de integridade (*Ativo & Operacional*), temporizador do último pulso e sincronização contínua via AJAX sem recarregamento de página.
  - **Console Terminal de Logs (`storage/logs/cron.log`):** Visualizador incorporado em tema escuro exibindo as últimas saídas do `php artisan schedule:run` com botão de recarga instantânea.
- **Registro Persistente de Execução nos Comandos Artisan:**
  - `VerificarFaturasVencidas` (`faturas:verificar-vencidas`): Grava o horário da última execução no banco de dados (`cms_settings`).
  - `EnviarLembretesFaturas` (`faturas:enviar-lembretes`): Grava o horário da última execução no banco de dados.
  - `SincronizarPagamentosGateways` (`gateways:sincronizar-pagamentos`): Grava o horário da última execução no banco de dados.
  - Exibição de checkmark verde e data/hora oficial em todas as rotinas após execução automática ou manual.
- **Sincronização de Horários e Endpoints AJAX:**
  - Alinhamento dos horários agendados em `routes/console.php` (`06:00` e `08:00`) com as frequências descritas na interface.
  - Nova rota `/admin/cron/status` para consumo de status e logs via AJAX.

---

## [1.6.8] - 2026-10-09

### ✉️ Botões Fluidos em E-mails, Páginas Dedicadas de Templates, Central de Cron, SMTP Exclusivo e Harmonização Dark/Light
- **Botão Dinâmico e Responsivo em E-mails (`resources/views/emails/layouts/master.blade.php`):**
  - **Ajuste Fluido ao Tamanho de Tela:** O botão `.btn-action` agora possui largura fluida com `max-width: 90%` no desktop, centralização garantida via container `.btn-wrapper` (`table-cell/block`), quebra inteligente de linha e transição para largura 100% (bloco total) em dispositivos móveis (`@media only screen and (max-width: 620px)` e `@media only screen and (max-width: 420px)`), garantindo legibilidade perfeita e acabamento premium sem transbordamento em telas pequenas.
  - **Padronização nos Templates Nativos:** Todos os 5 modelos padrão de e-mail foram atualizados para incluir as classes e estilos dinâmicos de botão.
- **Páginas Dedicadas para Gestão de E-mails (`/admin/emails`):**
  - **Fim da Sobrecarga de Abas em Gateways:** Os templates de e-mail foram completamente desacoplados do módulo de gateways de pagamento e ganharam seu próprio ecossistema dedicado no menu principal do painel administrativo.
  - **Listagem e Status do SMTP (`/admin/emails`):** Tabela elegante de templates, identificação de tipo (Sistema vs Personalizado), card com status do servidor SMTP e atalho para envio de demonstração global.
  - **Edição em Página Dedicada (`/admin/emails/{chave}/editar`):** Tela exclusiva em split-screen com editor Summernote BS5, inserção rápida de variáveis dinâmicas em 1 clique e simulador de preview responsivo ao vivo multi-dispositivo (Desktop, Tablet 520px e Smartphone 375px).
  - **Criação de Templates Customizados (`/admin/emails/novo`):** Página dedicada para criação de novos modelos de e-mail com geração automática de chave/slug e preview instantâneo.
- **Central de Cron Jobs & Automação do Agendador (`/admin/cron`):**
  - **Módulo Independente:** Criado `CronController` e tela exclusiva para visualização e controle dos cron jobs do sistema.
  - **Linha Única da Hospedagem:** Exibição clara e botão de cópia do comando oficial configurado no cPanel: `* * * * * cd /home/fococomb/public_html && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1`.
  - **Execução Sob Demanda via AJAX:** Disparo manual com feedback SweetAlert2 e console modal exibindo os logs da rotina executada.
- **Desduplicação e Organização do Portal de Gateways (`/admin/gateways`):**
  - Eliminação de campos duplicados e de abas não relacionadas a meios de pagamento; a tela de gateways agora foca estritamente nos 3 pilares de cobrança: **Mercado Pago**, **Efí PRO** e **Cobrança por Carnê**.
- **Harmonização Global de Cores Dark / Light Mode no Painel:**
  - Sincronização do tema nos seletores `<html>` e `<body>` via atributo `data-bs-theme`.
  - Correção de contrastes em `admin-foco7.css` para cabeçalhos de cards (`.card-header.bg-light`), caixas de alerta (`.alert-light`), tabelas (`.table-light`) e elementos com fundo claro que ficavam ilegíveis no modo escuro.
- **Servidor SMTP Exclusivo e Cron Configurados na Hospedagem cPanel:**
  - Criação da conta de e-mail dedicada `notificacoes@foco7.com.br` no servidor cPanel.
  - Configuração das variáveis `MAIL_*` diretamente no arquivo `/home/fococomb/public_html/.env` e no fallback do `EmailTemplateService`.
  - Ativação do cron job oficial a cada minuto na crontab da conta cPanel.

---

## [1.6.7] - 2026-10-09

### ✉️ Rota Única de E-mails, Layout Master Responsivo & Preview em Tempo Real por Dispositivo
- **Pipeline Unificada de E-mails (`EmailTemplateService`):**
  - **Rota Única Obrigatória:** Todos os e-mails disparados pelo sistema (transacionais, cobranças, notificações de atraso, faturas, carnês, lembretes de cron e testes de servidor SMTP) passam estritamente pela mesma pipeline unificada de compilação.
  - **Layout Master Responsivo Oficial (`resources/views/emails/layouts/master.blade.php`):** Criado template mestre compatível com clientes móveis e webmail (Gmail, Outlook, Apple Mail), com cabeçalho corporativo Foco 7 (`#0b0f19` e `#d90000`), card com tipografia calibrada, bloco de suporte 24h via WhatsApp, rodapé institucional padronizado e media queries `@media only screen and (max-width: 620px)` garantindo que botões virem bloco 100% e nenhum dispositivo quebre a leitura.
- **Preview em Tempo Real Multi-Dispositivo (`/admin/gateways`):**
  - **Divisão Split-Screen 2 Colunas:** A aba de templates de e-mail foi reestruturada para exibir o Editor Summernote + Assunto à esquerda e o Simulador de Pré-Visualização à direita.
  - **Alternador de Resolução Instantâneo:** Modos **Desktop (100%)**, **Tablet (520px com moldura)** e **Mobile (375px estilo smartphone)** para inspeção responsiva ao vivo.
  - **Renderização com Latência Zero (0ms):** Injeção em tempo real no `iframe` ao digitar qualquer caractere no campo de Assunto ou no Summernote, complementada por sincronização debounced (350ms) via AJAX com a rota `/admin/gateways/preview-email`.
  - **Criação e Gestão de Novos Templates:** Modal interativo para criação de templates personalizados com slug automático, inclusão e exclusão com SweetAlert2.
  - **Inserção de Variáveis em 1 Clique:** Badges dinâmicos (`{cliente_nome}`, `{valor}`, etc.) que copiam e colam no ponto exato do cursor no Summernote ativo.

---

## [1.6.6] - 2026-10-09

### 📤 Padronização Global de Uploads (Arrasta e Solta / foco-dropzone)
- **Eliminação Total de Inputs Legados de Upload:**
  - **Varredura Completa no Sistema:** Substituídos todos os campos de upload que ainda utilizavam `<input type="file">` básico pelo componente oficial `foco-dropzone`.
  - **Certificados Digitais .PEM da Efí PRO (`/admin/gateways`):** Ambos os campos de envio de certificado (Sandbox e Produção) agora utilizam a caixa interativa com arrasta e solta, exibição de ícone de certificado/chave criptográfica, indicação de arquivo anexado e botão de remoção sincronizado via AJAX.
  - **Mockup do Aplicativo Mobile (`/admin/cms`):** A seção de tecnologia mobile teve seu campo de screenshot/mockup migrado para `foco-dropzone` com preview vertical em tempo real (500x800).
  - **Imagem de Compartilhamento Open Graph (`/admin/seo`):** O upload da imagem para redes sociais (1200x630) foi totalmente padronizado com proporção social, pré-visualização instantânea e detecção dimensional em pixels.
- **Aprimoramentos no Componente Global (`global-dropzone.js` & `admin-foco7.css`):**
  - **Suporte Híbrido a Imagens e Arquivos Não-Imagem:** Renderização inteligente de previews gráficos para imagens e blocos estilizados com nome e extensão para arquivos técnicos (`.pem`, `.pdf`).
  - **Adaptação Cromática Claro/Escuro:** Ajuste automático de bordas tracejadas, fundos e tipografia contrastante em containers de tema claro (`#f8fafc`) e escuro (`rgba(18, 22, 31, 0.6)`).
  - **Novos Helpers de API Global:** Disponibilizados `window.FocoDropzone.setPreview(selector, url, name)` e `window.FocoDropzone.reset(selector)` para controle programático fluido em modais dinâmicos (Banners, Depoimentos, etc.).

---

## [1.6.5] - 2026-10-09

### 💳 Gateways, Cobrança por Carnê Efí, Templates de E-mail & Central de Cron
- **Portal de Gateways Reorganizado em Guias (`/admin/gateways`):**
  - **Interface Modular por Abas:** Painel segmentado em 5 guias Bootstrap 5: Mercado Pago, Efí PRO (Gerencianet), Cobrança por Carnê, Templates de E-mails Próprios e Central de Cron & Automação.
  - **Seleção Dinâmica Sandbox vs Produção:** Alternador instantâneo via rádio que exibe e oculta os campos de credenciais correspondentes de cada ambiente (Public Key, Access Token, Client ID, Client Secret, Chave PIX e Certificados .PEM), gravando chaves isoladas no banco e mantendo retrocompatibilidade transparente com o fluxo de checkout da landing page.
- **Módulo de Cobrança por Carnê Efí PRO para Clientes Existentes:**
  - **Emissão sob Demanda:** Permite selecionar clientes ativos já cadastrados, plano opcional, valor customizado de parcela e quantidade de parcelas (2x a 24x).
  - **Faturamento em Lote e Vencimentos Consecutivos:** Geração de faturas vinculadas com prefixo unificado `CARNE-YYYYMMDD-XXXX-XX` e vencimentos sequenciais automáticos com intervalo mensal.
  - **Notificação Automática:** Disparo opcional do e-mail do carnê com resumo das parcelas e instruções financeiras.
- **Central de Templates de E-mails Próprios com Editor Summernote:**
  - **5 Modelos Transacionais Nativos:** Fatura Gerada, Pagamento Confirmado, Carnê Emitido, Lembrete Preventivo e Fatura Vencida.
  - **Tags Dinâmicas Clicáveis:** Inserção em 1 clique de tags operacionais (`{cliente_nome}`, `{valor}`, `{data_vencimento}`, `{link_fatura}`, etc.).
  - **Disparo de E-mail de Teste:** Ferramenta integrada com validação dinâmica de SMTP a partir das configurações do CMS e feedback visual via SweetAlert2.
- **Central de Cron & Automação de Faturas:**
  - **Comandos Artisan Desenvolvidos:** `faturas:verificar-vencidas` (marca faturas vencidas e notifica), `faturas:enviar-lembretes` (lembrete preventivo 3 dias antes) e `gateways:sincronizar-pagamentos` (consulta de status via API).
  - **Agendamento Laravel:** Rotinas agendadas registradas em `routes/console.php`.
  - **Painel de Monitoramento & Execução Sob Demanda:** Visualização do comando do cPanel (`* * * * * cd ... && php artisan schedule:run`) e botão "Executar Agora" com feedback AJAX e exibição de log no SweetAlert2.

---

## [1.6.4] - 2026-10-09

### 🎛️ Gestão de Conteúdo (CMS & Painel Administrativo)
- **Painel Administrativo Completo para a Seção de Tecnologia Mobile (App Foco 7):**
  - **Cabeçalho & Textos Dinâmicos:** Adicionada gestão completa de Badge/Tag superior (`secao_app_badge`), Título principal (`secao_app_titulo`) e Texto explicativo/narrativo (`secao_app_descricao`).
  - **Gestão dos 4 Recursos do Aplicativo:** Cada um dos 4 cards de funcionalidades agora possui campos dedicados no painel com seletor de ícone FontAwesome, título do recurso e descrição operacional (Bloqueio Remoto, Cerca Virtual, Alerta de Ignição e Histórico de 90 Dias).
  - **Card Lateral de Download & Imagem/Mockup:** Adicionada personalização do título e subtítulo do card, URLs diretas para a Apple App Store e Google Play Store, além de suporte a upload de screenshot/mockup do aplicativo com pré-visualização e opção de remoção.
- **Painel Administrativo para os 4 Diferenciais e Selos da Estética Automotiva:**
  - **Gestão Integrada no CMS e no Módulo de Estética:** Criados campos para os 4 selos de diferenciais técnicos e garantia da estética automotiva (Ícone, Título e Descrição/Marcas) acessíveis tanto na aba *Textos das Seções* do CMS quanto diretamente na tela de gerenciamento de *Estética Automotiva* (`/admin/estetica`).
  - **Operações 100% AJAX & Sem Page Reload:** Formulários conectados ao sistema global de feedback com SweetAlert2 e Toastr.

---

## [1.6.3] - 2026-10-09

### 📐 Layout & Responsividade
- **Padronização Visual e de Largura dos Cards de Planos e Preços:**
  - **Distribuição Dinâmica de Colunas:** Ajustada a grade Bootstrap da seção `#planos` para calcular dinamicamente a proporção ideal de acordo com a quantidade de planos cadastrados. Para 3 planos ativos, a grade aplica `col-lg-4 col-md-6 d-flex` (ocupando 100% das 12 colunas da linha de forma balanceada e uniforme), eliminando o aperto e o espaçamento ocioso causado pelo antigo `col-xl-3` (25% por card).
  - **Largura Uniforme e Altura Consistente:** Aplicada classe `w-100` e propriedades CSS `width: 100%` com `box-sizing: border-box` no `.card-plano`, assegurando que todos os cards preencham suas respectivas colunas de maneira simétrica.
  - **Equalização de Bordas e Dimensões:** Padronizada a borda de todos os cards para exatamente `2px solid` (`#e2e8f0` nos cards convencionais e `var(--foco-red)` no card em destaque), eliminando qualquer disparidade subpixel de largura.
  - **Alinhamento Perfeito de Subtítulos e Preços:** Definidas alturas mínimas uniformes para `.plano-subtitulo` (`min-height: 44px`) e `.bloco-preco` (`min-height: 94px`) com centralização flex, garantindo que as listas de recursos e os botões de contratação iniciem e terminem na mesma linha horizontal em todos os cards.

---

## [1.6.2] - 2026-10-09

### 🐛 Correção Crítica
- **Correção da Sobreposição e Encavalamento de Slides no Hero Carousel:**
  - **Causa Raiz Identificada:** A regra CSS `.hero-section .carousel-item { display: flex; }` forçava todos os slides inativos a ficarem visíveis simultaneamente, sobrepondo os textos e botões de todos os slides no mesmo espaço.
  - **Correção do Ciclo de Exibição:** Removido o `display: flex` forçado de `.carousel-item`. Slides inativos agora permanecem estritamente com `display: none !important`, permitindo que apenas o slide ativo seja renderizado.
  - **Transição Cross-Fade Cinematográfica:** Ativada a classe oficial `carousel-fade` no Bootstrap Carousel com animação de opacidade suave (0.7s) entre transições de slides.
  - **Controles Unificados:** Movido o container de navegação (`.hero-carousel-nav`) para fora do loop de slides, eliminando a duplicação dos indicadores e setas prev/next.

---

## [1.6.1] - 2026-10-09

### 🎨 Identidade Visual & Consistência de Paleta
- **Harmonização Completa da Seção de Estética Automotiva com a Marca Foco 7:**
  - **Eliminação Definitiva do Roxo / Lilás:** Removidas todas as cores roxas (`#6f42c1`) da Landing Page, painel do cliente e painel administrativo.
  - **Paleta Oficial Aplicada:** 
    - Badge superior redesenhado com fundo suave e borda vermelha oficial (`#d90000`).
    - Ícones dos cards com caixa vermelha Foco 7 (`rgba(217, 0, 0, 0.08)` e ícone `#d90000`), transformando-se em gradiente vermelho vibrante com glow no hover.
    - Tipografia de preços padronizada em vermelho Foco 7 de alto impacto (`#d90000`), consistente com os cards de planos.
    - Botão *"Agendar Este Serviço"* padronizado com gradiente vermelho oficial (`#d90000` a `#b80000`) e sombra suave de alta conversão.
    - Cards de diferenciais técnicos com ícones em vermelho (`text-danger`) e bordas responsivas ao hover.
  - **Consistência nos Painéis:** Agendamentos de estética no calendário do AdminLTE 4 padronizados com tom Grafite Tático Nobre (`#1e293b`), mantendo distinção clara e elegância sem dissonância cromática.

---

## [1.6.0] - 2026-10-09

### ✨ Adicionado & Aprimorado
- **Integração Global & Definitiva do Editor Summernote WYSIWYG (Bootstrap 5 & AdminLTE 4):**
  - **Correção da CDN e Suporte Oficial:** Migrado da CDN inexistente/quebrada (0.8.18-bs5 com erro 404) para a versão estável oficial `summernote@0.8.20-bs5` com biblioteca de idioma completa em Português do Brasil (`summernote-pt-BR.min.js`).
  - **Inicializador Global (`public/js/global-summernote.js`):** Script modular com detecção automática de `.summernote`, toolbar profissional completa (estilos de título, negrito, itálico, cores, listas, tabelas, links, imagens, fullscreen e codeview), suporte a `data-height` e sincronização automática.
  - **Sincronização com Modais e AJAX:** Garantida a atualização bidirecional de conteúdo nos modais de Banners/Slides, FAQs, Depoimentos e Estética Automotiva (`window.setSummernoteContent` e `window.clearSummernote`), além da sincronização forçada dos campos antes de qualquer submissão de formulário via `global-ajax.js`.
  - **Estilização Visual & Dark Mode:** Adicionadas regras CSS refinadas em `public/css/admin-foco7.css` harmonizando a barra de ferramentas, botões e área de edição com o tema escuro/claro do AdminLTE 4.
  - **Renderização Rica no Site:** Respostas de FAQ, subtítulos de banners, depoimentos e descrições de estética agora renderizam tags e formatações visuais ricas sem quebra de layout.
- **Remoção do Bloco Branco e Novo Overlay Cinematográfico do Hero Carousel:**
  - Eliminado o container branco rígido central que cortava a foto de fundo do veículo.
  - Carrossel expandido para largura total (100% full-bleed) com imagem de fundo cobrindo todo o viewport.
  - Aplicado degradê horizontal direcional escuro em grafite tático (`#0b0f19`) garantindo máxima legibilidade aos textos e botões à esquerda, enquanto preserva a nitidez e o impacto visual da foto do veículo à direita.

---

## [1.5.0] - 2026-10-09

### ✨ Adicionado & Aprimorado
- **Módulo Completo de Estética Automotiva & Detalhamento:**
  - **Tabela e Modelos:** Criada a migration `2026_10_09_000010_create_servicos_estetica_and_update_agendamentos_table` e o model `ServicoEstetica` com relacionamentos, formatações de moeda e escopos ativos.
  - **CRUD Administrativo (`/admin/estetica`):** Interface AdminLTE 4 com métricas, cards visuais, modal AJAX para criação/edição de serviços (Polimento Técnico, Vitrificação Cerâmica 9H, Higienização Interna, Lavagem Detalhada, Cristalização e PPF), controle de ordem, preços e tempo estimado, além de ativação/desativação instantânea e exclusão protegida.
  - **Seção Exclusiva no Site Oficial (`#estetica`):** Seção responsiva com cards refinados, badges de tempo de aplicação, preços "A partir de R$...", 4 pilares de diferenciais técnicos (Produtos Nobres Vonixx/Menzerna, Box Climatizado, Certificado de Garantia, Atendimento com Hora Marcada) e link na navbar e rodapé.
  - **Agendamento Online Inteligente (Multi-Categoria):** Modal `#modalAgendamentoSite` permitindo agendar tanto serviços de estética quanto instalações de rastreamento com cálculo automático de valor e submissão AJAX limpa.
  - **FullCalendar 6 com Categorização por Cores:** Eventos identificados visualmente com roxo (#6f42c1) para estética e verde/azul para rastreamento, além de carregamento completo dos dados via modal e suporte a drag & drop.
- **Gestão de Clientes no Painel Administrativo (`/admin/clientes`):**
  - Módulo completo para consulta, filtros em tempo real, cadastro de novos clientes, edição de dados pessoais e redefinição segura de senhas via modal AJAX.
- **Gestão de Faturas & Financeiro no Painel Administrativo (`/admin/faturas`):**
  - Dashboard financeiro com métricas de total faturado, pendente e vencido, listagem com filtros rápidos, emissão de faturas avulsas ou atreladas a planos de rastreamento e alternância de status de quitação em 1 clique.
- **Edição Avançada no CMS & Textos Dinâmicos das Seções:**
  - Modais de edição direta para Banners/Slides (`/admin/cms/banner/obter/{id}`), Perguntas Frequentes (`/admin/cms/faq/obter/{id}`) e Depoimentos de Clientes (`/admin/cms/depoimento/obter/{id}`).
  - Nova aba "Textos das Seções" no CMS (`/admin/cms`) para personalização de títulos, subtítulos e descrições de todas as seções da Landing Page.
- **Área do Cliente com Solicitação de Agendamentos:**
  - Clientes podem solicitar serviços de estética automotiva ou manutenções de rastreador diretamente pelo painel do assinante com acompanhamento de status em tempo real.
- **Suíte de Testes de Integração:** 24 testes executados e 100% aprovados com 84 asserções no PHPUnit.

---

## [1.4.0] - 2026-10-09

### ✨ Adicionado & Aprimorado
- **Plano de Fundo Dinâmico & Controle de Opacidade do Hero Carousel:**
  - Gerenciador completo integrado à aba *Banners & Slides* do CMS (`/admin/cms`).
  - Upload de imagem de fundo panorâmica (1920×800px) com Drag & Drop e preview instantâneo.
  - Slider interativo de controle de opacidade (0% a 100%) com mostrador dinâmico em tempo real.
  - Seletor de cor da sobreposição (overlay) e desfoque gaussiano suave (blur de 0 a 15px).
  - Pré-visualização ao vivo (*live preview box*) no painel administrativo simulando o contraste antes de salvar.
  - Renderização otimizada no site oficial (`hero-bg-media` e `hero-bg-overlay`) com preservação total de legibilidade do texto e do simulador.
- **Aprimoramento Visual dos Cards de Planos de Rastreamento:**
  - Layout refinado com bordas harmonizadas, cantos arredondados (22px), sombras tridimensionais e efeito hover suave com elevação.
  - **Checados Restaurados para a Cor Oficial:** Ícones da lista de recursos voltaram à cor vermelha característica da Foco 7 (`var(--foco-red)` / `#d90000`).
  - Tag de destaque reluzente com ícone de fogo (`<i class="fas fa-fire"></i> MAIS ESCOLHIDO`) e gradiente avermelhado.
  - Box de ícone com rotação e escala sutil ao passar o cursor, badges de adesão grátis estilizados e botões de alta conversão com micro-interações.
- **Unificação Total da Área do Cliente ao Padrão AdminLTE 4:**
  - A Área do Cliente agora compartilha 100% da estrutura, componentes, navbar fixa, sidebar com treeview e scripts originais do AdminLTE 4.
  - Aparência rigorosamente idêntica ao painel administrativo (zero divergência visual), com segregação precisa de menus pelo papel de usuário (`admin` vs `cliente`).
  - Suporte nativo ao seletor de tema Dark/Light mode com persistência em `localStorage` e paleta oficial Foco 7.
  - Telas de Dashboard do Assinante e Minhas Faturas modernizadas com cards e tabelas responsivas do AdminLTE 4.
- **Suíte de Testes Automatizados:** 17 testes passando com 100% de sucesso e 57 asserções.

---

## [1.3.0] - 2026-10-08

### ✨ Adicionado & Aprimorado
- **Upload Global com Arrasta e Solta (Drag & Drop), Proporções e Preview:**
  - Componente universal `public/js/global-dropzone.js` integrado a todos os formulários administrativos.
  - Exibição de dimensões ideais recomendadas (ex: 1920×650px para Banners, 500×120px para Logo, 512×512px para Favicon, 300×300px para Avatar, 200×200px para Depoimentos e `.pem` para Certificados SSL).
  - Pré-visualização instantânea do arquivo com leitura em memória, cálculo em tempo real da resolução e peso em KB/MB, e botões para troca e remoção.
- **Botões Flutuantes no Site Oficial:**
  - **Botão Flutuante do WhatsApp:** Ícone pulsante verde oficial no canto inferior direito com link dinâmico para o WhatsApp da Foco 7 `(91) 8051-0736` e tooltip de chamada rápida.
  - **Botão Voltar ao Topo:** Botão elegante que surge após rolagem da tela (> 320px) com animação suave de subida até o início da página.
- **Correção dos Controles do Carrossel Hero:**
  - Removido o overlay invasivo de 15% do Bootstrap que bloqueava os botões de ação e o simulador rápido de veículos.
  - Posicionamento dedicado com botões circulares elegantes e indicadores interativos em barra na base do slide.
- **Página de Perfil do Administrador (`/admin/perfil`):**
  - Gerenciamento completo de informações pessoais (Nome, E-mail, Telefone e CPF/CNPJ).
  - Upload de avatar com drag & drop e preview imediato.
  - Alteração segura de senha com verificação obrigatória da senha atual e confirmação.
- **Página de Configurações do Sistema & Servidor SMTP (`/admin/configuracoes`):**
  - Painel com abas para Dados Institucionais, Identidade Visual (Logo e Favicon), Servidor SMTP e Redes Sociais.
  - Disparo de e-mail de teste em tempo real via AJAX com feedback via SweetAlert2 e Toastr.
- **Portal de Pagamentos Expandido (Gateways):**
  - Campos completos de Mercado Pago (Public Key, Access Token, Client ID, Client Secret, Webhook Secret, Sandbox e Ativação).
  - Campos completos de Efí PRO (Client ID, Client Secret, Chave PIX, Upload do Certificado `.pem` com Drag & Drop, Sandbox e Ativação).
  - Regras Comerciais & Financeiras: Desconto no PIX (%), parcelamento no cartão, parcelas sem juros, taxa de juros ao mês, vencimento de boleto, multa por atraso e juros diários.
- **Suíte de Testes Automatizados:** 16 testes passando com 100% de sucesso e 44 asserções.

---

## [1.2.0] - 2026-10-08

### ✨ Adicionado & Atualizado
- **Logomarca e Favicon Oficiais Transparentes:**
  - Aplicação dos arquivos oficiais fornecidos pelo cliente (`public/img/logo-foco7.png`, `public/img/favicon.png` e `public/favicon.ico`).
  - Inserção em todas as interfaces: Navbar e Rodapé da Landing Page, Portal de Login, Área do Cliente, Sidebar do Admin CMS, Assistente de Instalação e Páginas de Erro.
- **Centralização Dinâmica do Telefone de Atendimento no Banco de Dados:**
  - Telefone oficial atualizado para **(91) 8051-0736** e WhatsApp para **559180510736** em 100% dos pontos de contato do sistema via `cms_settings`.
  - Links com disparo direto para chamada telefônica (`tel:`) e conversação no WhatsApp (`https://wa.me/559180510736`).
  - Banco de dados em produção e local sincronizados com os novos dados de contato.

---

## [1.1.0] - 2026-10-08

### ✨ Adicionado
- **Páginas de Erro Personalizadas do Sistema (`resources/views/errors/`):**
  - Layout mestre padronizado (`layout.blade.php`) com identidade Foco 7 (Dark Mode, acentos em vermelho `#d90000`, glassmorphism, suporte 24h via WhatsApp e retorno ao início).
  - Página de erro `404.blade.php` (Página Não Encontrada) com animação e links de navegação.
  - Página de erro `500.blade.php` (Instabilidade Temporária no Servidor / Erro Inesperado).
  - Página de erro `403.blade.php` (Acesso Restrito / Não Autorizado).
  - Página de erro `419.blade.php` (Sessão Expirada por Inatividade).
  - Página de erro `503.blade.php` (Manutenção Programada em Andamento).
- **Publicação Automática Remota no cPanel:** Deploy completo via SSH/SFTP com PHP 8.4 em `/home/fococomb/public_html/`.

---

## [1.0.0] - 2026-10-08

### ✨ Adicionado
- **Configurações 100% no Banco de Dados (`cms_settings`):** Todas as credenciais de gateways (Mercado Pago e Efí PRO), identificação visual, telefones, WhatsApp, horários, endereço, redes sociais, modo de manutenção e SEO agora são gerenciados exclusivamente pela tabela `cms_settings` com cache inteligente, mantendo o arquivo `.env` apenas para a conexão do banco de dados MySQL e o instalador.
- **Módulo 1 - Infraestrutura e Instalador Web Wizard (`/instalar`):**
  - Checagem automática dos requisitos do PHP 8.2+ e extensões (PDO MySQL, cURL, GD, Mbstring, OpenSSL, XML).
  - Teste de conexão MySQL em tempo real via AJAX.
  - Execução automatizada de migrações e seeders com criação de conta master e trava de segurança `installed.lock`.
  - `.htaccess` raiz blindado para execução direta no `public_html` do cPanel/LiteSpeed.
- **Módulo 2 - Landing Page de Alta Conversão:**
  - Layout Single Page inspirado na excelência e distribuição do `sosrastreamento.com`.
  - Paleta de cores oficial da Foco 7 (`#d90000`, tons escuros e neutros).
  - Header fixo com canais de atendimento 24h e acesso à Área do Cliente.
  - Hero Section dinâmico com carrossel de slides gerenciáveis via CMS.
  - Seção "O Risco Não Avisa Antes de Chegar" (riscos vs soluções).
  - Seção "Quem Somos" com mais de 10 anos de autoridade no Norte e Nordeste.
  - Tabela comparativa e cards de Planos (Carros, Motos, Cargas e Frotas) com modal de checkout transparente.
  - Infográfico da Ronda Tática Foco 7 em 4 etapas animadas.
  - Demonstração dos recursos do aplicativo móvel (Android e iOS).
  - FAQ interativo com acordeão e prova social com depoimentos e avaliações em estrelas.
  - Rodapé institucional blindado com selos de segurança SSL.
- **Módulo 3 - Página de Manutenção Avançada:**
  - Ativação/Desativação com 1 clique pelo CMS.
  - Contador regressivo dinâmico em tempo real.
  - Liberação automática por IP e por token de identificação de dispositivo.
  - Exibição de plantão 24h no WhatsApp e redes sociais.
- **Módulo 4 - Painel Administrativo CMS (AdminLTE 4 com Bootstrap 5):**
  - Suporte nativo a Modo Escuro (Dark) e Claro (Light) com persistência no navegador via `localStorage`.
  - Navbar fixa e Sidebar com Treeview e indicação de rota ativa.
  - Summernote integrado em todas as textareas gerenciáveis.
  - Gerenciador visual de Logos, Banners, Carrossel, FAQs, Depoimentos e Textos.
- **Módulo 5 - Sistema de Agendamentos (FullCalendar 6):**
  - Calendário interativo para controle de instalações, vistorias e suporte com Drag & Drop.
  - Filtros por status (Pendente, Confirmado, Concluído, Cancelado) com cores dinâmicas.
- **Módulo 6 - Módulo de SEO com Pontuação em Tempo Real:**
  - Gestão de Meta Title, Meta Description, Meta Keywords, Open Graph e Robôs.
  - Motor de cálculo de SEO Score em tempo real com barra de progresso, preview do Google e checklist.
  - Geração automática de `robots.txt` e `sitemap.xml`.
- **Módulo 7 - Área do Cliente e Controle de Acesso (ACL):**
  - Autenticação unificada com segregação estrita entre perfil `Admin` e `Cliente`.
  - Visualização de faturas, faturas pagas e pendentes com emissão de 2ª via.
- **Módulo 8 - Gateways de Pagamento (Mercado Pago e Efí PRO):**
  - Checkout Transparente com emissão imediata de PIX (QR Code e Copia e Cola) e Boleto.
  - Webhooks dinâmicos com logs e baixa automática de faturas.
- **Blindagem Global de Notificações:**
  - Proibição terminante de `alert()` nativo do navegador, substituído globalmente por SweetAlert2.
  - Notificações não intrusivas via Toastr para todas as operações AJAX.
  - Máscaras brasileiras completas (Telefone, CPF/CNPJ, CEP com busca automática via ViaCEP, Placa e Moeda R$).
- **Suíte de Testes Automatizados:** 11 testes de integração com 100% de aprovação e 25 asserções (`tests/Feature/Foco7SystemTest.php`).

---
