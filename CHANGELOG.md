# Changelog - Sistema Foco 7

Todas as alterações notáveis deste projeto serão documentadas neste arquivo.
O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), mantendo sempre **o registro mais recente no topo** (ordem cronológica inversa).

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
