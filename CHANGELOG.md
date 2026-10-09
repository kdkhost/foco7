# Changelog - Sistema Foco 7

Todas as alterações notáveis deste projeto serão documentadas neste arquivo.
O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), mantendo sempre **o registro mais recente no topo** (ordem cronológica inversa).

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
