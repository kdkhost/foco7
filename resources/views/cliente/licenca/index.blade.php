@extends('cliente.layouts.app')

@section('title', 'Minha Licença FOCO 7 | Servidor Próprio')
@section('page_title', 'Minha Licença FOCO 7')
@section('page_description', 'Gerencie sua licença de software White Label, baixe o sistema completo e configure seu servidor próprio (cPanel, VPS, Cloud).')

@section('page_actions')
    <a href="https://wa.me/{{ preg_replace('/\D/', '', $parametros['whatsapp_suporte'] ?? '559180510736') }}?text={{ urlencode('Olá, sou licenciado Foco 7 e preciso de suporte com a instalação no meu servidor.') }}" target="_blank" class="btn btn-outline-success fw-bold shadow-sm">
        <i class="fab fa-whatsapp me-1"></i> Suporte ao Licenciado
    </a>
@endsection

@section('styles')
<style>
    .card-chave-container {
        background: linear-gradient(135deg, rgba(217, 0, 0, 0.05) 0%, rgba(217, 0, 0, 0.12) 100%);
        border: 2px dashed rgba(217, 0, 0, 0.35);
        border-radius: 14px;
        padding: 24px;
    }
    .display-chave-licenca {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: 1.5px;
        color: #d90000;
        background: #ffffff;
        padding: 12px 20px;
        border-radius: 8px;
        display: inline-block;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.1);
        word-break: break-all;
    }
    [data-bs-theme="dark"] .display-chave-licenca {
        background: #1e2227;
        color: #ff6b6b;
        border-color: rgba(255,255,255,0.15);
    }
    .badge-passo-numero {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #d90000;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1rem;
        margin-right: 12px;
    }
    .card-download-cta {
        border: 1px solid rgba(25, 135, 84, 0.3);
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-download-cta:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(25, 135, 84, 0.15) !important;
    }
    .code-box-server {
        background: #1e1e1e;
        color: #d4d4d4;
        font-family: Consolas, 'Courier New', monospace;
        padding: 14px 18px;
        border-radius: 8px;
        position: relative;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
@if($licencaAtiva)
    <!-- ========================================== -->
    <!-- CENÁRIO A: LICENCIADO COM LICENÇA ATIVA    -->
    <!-- ========================================== -->

    <!-- BANNER DE BOAS-VINDAS AO LICENCIADO -->
    <div class="card shadow-sm border-0 mb-4 bg-body">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger text-white p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                        <i class="fas fa-crown fs-3"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h4 class="fw-bold mb-0 text-body">{{ $licencaAtiva->nome_empresa }}</h4>
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> LICENÇA ATIVA</span>
                            <span class="badge bg-primary text-uppercase">{{ $licencaAtiva->tipo_licenca }}</span>
                        </div>
                        <p class="text-secondary small mb-0 mt-1">
                            Você possui autorização para instalar o sistema completo em seu próprio servidor dedicado, VPS ou hospedagem.
                        </p>
                    </div>
                </div>
                <div>
                    <span class="badge bg-light text-dark border px-3 py-2 small">
                        <i class="fas fa-code-branch text-primary me-1"></i> Versão: <strong>{{ $licencaAtiva->versao_liberada ?? 'v3.8.0' }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- CARD DA CHAVE DE LICENÇA (1 CLIQUE PARA COPIAR) -->
    <div class="card shadow-sm border-0 mb-4 bg-body">
        <div class="card-body p-4">
            <div class="card-chave-container text-center">
                <span class="text-uppercase small fw-bold text-danger d-block mb-2">
                    <i class="fas fa-key me-1"></i> Sua Chave Única de Licença
                </span>
                <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap my-2">
                    <span class="display-chave-licenca" id="textoChaveLicenca">{{ $licencaAtiva->chave_licenca }}</span>
                    <button type="button" class="btn btn-danger btn-lg px-4 fw-bold shadow-sm" onclick="copiarChave()" title="Copiar Chave para a Área de Transferência">
                        <i class="far fa-copy me-1"></i> Copiar Chave
                    </button>
                </div>
                <p class="small text-secondary mb-0 mt-3">
                    Esta chave é incluída automaticamente no arquivo <code class="text-danger fw-bold">license.key</code> do pacote de instalação. Guarde-a com segurança.
                </p>
            </div>
        </div>
    </div>

    <!-- CARD DE VINCULAÇÃO DE DOMÍNIO E IP -->
    <div class="card shadow-sm border-0 mb-4 bg-body">
        <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-body">
                <i class="fas fa-globe text-primary me-2"></i> Servidor &amp; Domínio Autorizado
            </h5>
            <span class="badge bg-secondary-subtle text-secondary small">Vínculo Automático</span>
        </div>
        <div class="card-body p-4">
            <form id="formVincularServidor" onsubmit="salvarVinculoServidor(event)">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Domínio ou Subdomínio do Seu Sistema <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-link text-secondary"></i></span>
                            <input type="text" name="dominio_autorizado" id="dominio_autorizado" class="form-control font-monospace" placeholder="Ex: rastreamento.meusite.com.br" value="{{ $licencaAtiva->dominio_autorizado }}" required>
                        </div>
                        <div class="form-text small">Insira o endereço que seus clientes usarão para acessar seu sistema.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">IP do Servidor (Opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-network-wired text-secondary"></i></span>
                            <input type="text" name="ip_autorizado" id="ip_autorizado" class="form-control font-monospace" placeholder="Ex: 147.182.195.42" value="{{ $licencaAtiva->ip_autorizado }}">
                        </div>
                        <div class="form-text small">IP público da sua VPS ou servidor dedicado.</div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm" id="btnSalvarVinculo">
                            <i class="fas fa-save me-1"></i> Atualizar Vínculo
                        </button>
                    </div>
                </div>
            </form>
            @if(!empty($licencaAtiva->ultimo_checkin_em))
                <div class="alert alert-light border mt-3 mb-0 d-flex align-items-center gap-2 py-2 small">
                    <i class="fas fa-info-circle text-info"></i>
                    <span>Última validação remota realizada pelo seu servidor: <strong>{{ $licencaAtiva->ultimo_checkin_em->format('d/m/Y \à\s H:i:s') }}</strong> (IP detectado: {{ $licencaAtiva->ultimo_checkin_ip ?? 'N/D' }})</span>
                </div>
            @endif
        </div>
    </div>

    <!-- SEÇÃO DE DOWNLOADS DO PACOTE OFICIAL -->
    <div class="row g-4 mb-4">
        <!-- Download ZIP Completo -->
        <div class="col-lg-6">
            <div class="card card-download-cta shadow-sm bg-body h-100 p-3">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 bg-success text-white p-3 fs-3">
                                <i class="fas fa-file-archive"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-success">Pacote Completo do Sistema (.ZIP)</h5>
                                <span class="badge bg-success-subtle text-success border border-success-subtle small mt-1">Pronto para Instalação</span>
                            </div>
                        </div>
                        <p class="text-secondary small">
                            Arquivo compactado contendo todo o código-fonte, dependências, instalador web gráfico interativo (em <code>/instalar</code>) e o arquivo de ativação <code class="text-danger fw-bold">license.key</code> já configurado para a sua empresa.
                        </p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('cliente.licenca.download_pacote', $licencaAtiva->id) }}" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                            <i class="fas fa-cloud-download-alt me-2"></i> Baixar Sistema Completo (.ZIP)
                        </a>
                        <div class="text-center small text-muted mt-2">
                            Total de downloads realizados: <strong>{{ $licencaAtiva->total_downloads ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Download Script VPS Linux -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 bg-body h-100 p-3">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 bg-dark text-white p-3 fs-3">
                                <i class="fas fa-terminal"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-body">Script de Instalação VPS (Ubuntu/Debian)</h5>
                                <span class="badge bg-dark-subtle text-body border small mt-1">Automatizado para Nginx + PHP + MariaDB</span>
                            </div>
                        </div>
                        <p class="text-secondary small">
                            Script em Shell Bash personalizado que configura sua máquina virtual do zero: instala PHP 8.3/8.5, Composer, Nginx, MariaDB, SSL Let's Encrypt e publica seu sistema automaticamente.
                        </p>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('cliente.licenca.download_vps', $licencaAtiva->id) }}" class="btn btn-outline-dark btn-lg flex-grow-1 fw-bold">
                            <i class="fas fa-download me-1"></i> Baixar install-vps.sh
                        </a>
                        <a href="{{ route('cliente.licenca.download_manual', $licencaAtiva->id) }}" class="btn btn-outline-secondary btn-lg fw-bold" title="Baixar Guia em Markdown">
                            <i class="fas fa-book me-1"></i> Manual .MD
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GUIAS DE INSTALAÇÃO PASSO A PASSO (TABS INTERATIVAS) -->
    <div class="card shadow-sm border-0 mb-4 bg-body">
        <div class="card-header bg-transparent py-3 border-bottom">
            <h5 class="fw-bold mb-0 text-body">
                <i class="fas fa-book-reader text-danger me-2"></i> Guia Oficial de Instalação no Seu Servidor
            </h5>
        </div>
        <div class="card-body p-4">
            <!-- Navegação por Abas -->
            <ul class="nav nav-pills mb-4 gap-2" id="guiaInstalacaoTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="cpanel-tab" data-bs-toggle="pill" data-bs-target="#tab-cpanel" type="button" role="tab" aria-selected="true">
                        <i class="fas fa-hdd me-1"></i> 1. Hospedagem cPanel
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="vps-tab" data-bs-toggle="pill" data-bs-target="#tab-vps" type="button" role="tab" aria-selected="false">
                        <i class="fas fa-server me-1"></i> 2. VPS Linux (Ubuntu / Nginx)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="dns-tab" data-bs-toggle="pill" data-bs-target="#tab-dns" type="button" role="tab" aria-selected="false">
                        <i class="fas fa-network-wired me-1"></i> 3. Configuração de DNS
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="suporte-tab" data-bs-toggle="pill" data-bs-target="#tab-suporte" type="button" role="tab" aria-selected="false">
                        <i class="fas fa-headset me-1"></i> 4. Suporte &amp; Ajuda
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="guiaInstalacaoTabContent">
                <!-- ABA 1: CPANEL -->
                <div class="tab-pane fade show active" id="tab-cpanel" role="tabpanel">
                    <div class="alert alert-info border-info-subtle d-flex align-items-center gap-2 mb-4">
                        <i class="fas fa-info-circle fs-4"></i>
                        <span>O sistema conta com um <strong>Instalador Web Gráfico</strong>. Você não precisa usar terminal se estiver no cPanel!</span>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <span class="badge-passo-numero">1</span>
                        <div>
                            <h6 class="fw-bold mb-1">Criar Banco de Dados MySQL</h6>
                            <p class="text-secondary small mb-0">Acesse o seu cPanel &gt; <strong>Bancos de dados MySQL</strong>. Crie um novo banco (ex: <code>usuario_foco7</code>), um novo usuário e conceda TODOS os privilégios a ele. Guarde o nome do banco, usuário e senha.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <span class="badge-passo-numero">2</span>
                        <div>
                            <h6 class="fw-bold mb-1">Upload do Arquivo ZIP</h6>
                            <p class="text-secondary small mb-0">Acesse o <strong>Gerenciador de Arquivos</strong> no cPanel. Navegue até a pasta do seu domínio (geralmente <code>public_html</code> ou uma subpasta) e faça o upload do arquivo <code class="fw-bold">FOCO7_SISTEMA_LICENCIADO.zip</code> que você baixou acima.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <span class="badge-passo-numero">3</span>
                        <div>
                            <h6 class="fw-bold mb-1">Extrair os Arquivos</h6>
                            <p class="text-secondary small mb-0">Clique com o botão direito no arquivo ZIP enviado e selecione <strong>Extract (Extrair)</strong> na pasta de destino. O arquivo <code>license.key</code> já estará presente na raiz.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-3">
                        <span class="badge-passo-numero">4</span>
                        <div>
                            <h6 class="fw-bold mb-1">Executar o Instalador Web</h6>
                            <p class="text-secondary small mb-2">Abra o seu navegador e acesse a URL de instalação:</p>
                            <div class="code-box-server">
                                https://{{ $licencaAtiva->dominio_autorizado ?: 'seusite.com.br' }}/instalar
                            </div>
                            <p class="text-secondary small mt-2 mb-0">
                                O instalador verificará os requisitos do PHP (versão 8.2+), testará a conexão com seu MySQL, criará as tabelas e solicitará a criação do seu usuário administrador master com sua marca.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ABA 2: VPS LINUX -->
                <div class="tab-pane fade" id="tab-vps" role="tabpanel">
                    <p class="text-secondary small mb-3">
                        Para servidores VPS (Ubuntu 22.04 LTS ou 24.04 LTS em DigitalOcean, AWS Lightsail, Hetzner, Contabo ou Linode), você pode usar o nosso script automatizado:
                    </p>

                    <h6 class="fw-bold">Passo 1: Enviar o script para a VPS</h6>
                    <p class="text-secondary small">Conecte via SSH como root e faça o download do instalador:</p>
                    <div class="code-box-server mb-3">
                        wget -O install-vps.sh "{{ route('cliente.licenca.download_vps', $licencaAtiva->id) }}"<br>
                        chmod +x install-vps.sh<br>
                        ./install-vps.sh
                    </div>

                    <h6 class="fw-bold mt-4">Passo 2: Configurar o Cron Job (Agendador de Tarefas)</h6>
                    <p class="text-secondary small">Para o envio automático de cobranças WhatsApp e faturas, execute <code>crontab -e</code> e adicione a linha:</p>
                    <div class="code-box-server mb-3">
                        * * * * * cd /var/www/foco7 && php artisan schedule:run >> /dev/null 2>&1
                    </div>

                    <h6 class="fw-bold mt-4">Passo 3: Acessar o Instalador Web</h6>
                    <p class="text-secondary small mb-0">Acesse <code>https://{{ $licencaAtiva->dominio_autorizado ?: 'seu-ip-ou-dominio' }}/instalar</code> para finalizar a configuração do banco e do seu admin.</p>
                </div>

                <!-- ABA 3: DNS -->
                <div class="tab-pane fade" id="tab-dns" role="tabpanel">
                    <h6 class="fw-bold">Como apontar seu Domínio ou Subdomínio:</h6>
                    <p class="text-secondary small mb-3">No seu provedor de domínio (Cloudflare, Registro.br, HostGator, GoDaddy), adicione o registro:</p>

                    <div class="table-responsive">
                        <table class="table table-bordered small">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Nome / Host</th>
                                    <th>Valor / Destino</th>
                                    <th>TTL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-primary">A</span></td>
                                    <td><code>rastreamento</code> (ou <code>@</code> se for domínio raiz)</td>
                                    <td><code>{{ $licencaAtiva->ip_autorizado ?: 'IP_DA_SUA_VPS' }}</code></td>
                                    <td>Auto / 14400</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ABA 4: SUPORTE -->
                <div class="tab-pane fade" id="tab-suporte" role="tabpanel">
                    <div class="text-center py-4">
                        <div class="rounded-circle bg-success text-white p-3 d-inline-flex mb-3 fs-3">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h5 class="fw-bold">Precisa de Ajuda para Instalar?</h5>
                        <p class="text-secondary small mb-4" style="max-width: 500px; margin: 0 auto;">
                            Nossa equipe de engenharia e suporte a licenciados pode auxiliá-lo ou realizar a instalação remota do sistema no seu servidor.
                        </p>
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $parametros['whatsapp_suporte'] ?? '559180510736') }}?text={{ urlencode('Olá, sou licenciado Foco 7 (Chave: ' . $licencaAtiva->chave_licenca . ') e gostaria de tirar uma dúvida sobre a instalação no meu servidor.') }}" target="_blank" class="btn btn-success btn-lg fw-bold px-4 shadow-sm">
                            <i class="fab fa-whatsapp me-2"></i> Chamar Suporte Técnico no WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@else
    <!-- ============================================== -->
    <!-- CENÁRIO B: CLIENTE AINDA NÃO É LICENCIADO      -->
    <!-- ============================================== -->

    <div class="card shadow-sm border-0 mb-4 bg-body overflow-hidden">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge bg-danger px-3 py-2 text-uppercase mb-3 fw-bold">
                        <i class="fas fa-certificate me-1"></i> {{ $parametros['badge'] ?? 'OPORTUNIDADE DE NEGÓCIO • WHITE LABEL' }}
                    </span>
                    <h2 class="fw-extrabold display-6 mb-3 text-body">
                        {{ $parametros['titulo'] ?? 'Tenha Sua Própria Empresa de Rastreamento Veicular' }}
                    </h2>
                    <p class="lead text-secondary mb-4">
                        {{ $parametros['subtitulo'] ?? 'Instale o sistema FOCO 7 completo em seu próprio servidor (VPS, cPanel ou Cloud). Faturamento 100% seu, sua própria marca, logo e domínio próprio.' }}
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Marca 100% Sua (White Label)</h6>
                                    <span class="text-secondary small">Coloque seu nome, logotipo e paleta de cores.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Servidor Próprio</h6>
                                    <span class="text-secondary small">Instale em cPanel, VPS Ubuntu ou Cloud dedicada.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Sem Mensalidade Obrigatória</h6>
                                    <span class="text-secondary small">Você não paga por veículo rastreado ou mensalidade fixa.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Instalador Web Gráfico</h6>
                                    <span class="text-secondary small">Assistente passo a passo que cria banco e admin.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card shadow border-2 border-danger rounded-4 p-4 text-center bg-body">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle small fw-bold mb-2">LICENÇA OFICIAL FOCO 7</span>
                        <h4 class="fw-bold mb-1">Licença White Label</h4>
                        <p class="text-secondary small mb-3">Acesso imediato para download e ativação</p>

                        <div class="my-3">
                            <span class="text-secondary small">A partir de</span>
                            <div class="display-5 fw-extrabold text-danger">
                                R$ {{ number_format($parametros['preco_adesao'] ?? 1497.00, 2, ',', '.') }}
                            </div>
                            <span class="badge bg-light text-dark border small mt-1">Pagamento Único • Sem Royalties</span>
                        </div>

                        <div class="mt-4">
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $parametros['whatsapp_suporte'] ?? '559180510736') }}?text={{ urlencode('Olá! Tenho interesse em me tornar um Licenciado FOCO 7 e adquirir a licença do sistema para instalar no meu próprio servidor.') }}" target="_blank" class="btn btn-danger btn-lg w-100 fw-bold shadow-sm py-3">
                                <i class="fab fa-whatsapp me-2"></i> Adquirir Minha Licença Agora
                            </a>
                            <div class="small text-muted mt-2">Ativação instantânea após confirmação</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function copiarChave() {
        const el = document.getElementById('textoChaveLicenca');
        if (!el) return;
        const chave = el.innerText.trim();

        navigator.clipboard.writeText(chave).then(() => {
            if (typeof toastr !== 'undefined') {
                toastr.success('Chave de licença copiada com sucesso!');
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Chave Copiada!',
                    text: chave,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    function salvarVinculoServidor(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSalvarVinculo');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';

        @if($licencaAtiva)
            const licencaId = "{{ $licencaAtiva->id }}";
        @else
            const licencaId = null;
        @endif

        if (!licencaId) return;

        const form = document.getElementById('formVincularServidor');
        const formData = new FormData(form);

        fetch(`/cliente/licenca/vincular-servidor/${licencaId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Atualizar Vínculo';

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Servidor Vinculado!',
                    text: data.message,
                    confirmButtonColor: '#d90000'
                });
            } else {
                Swal.fire('Atenção', data.message || 'Erro ao vincular.', 'warning');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Atualizar Vínculo';
            Swal.fire('Erro', 'Não foi possível se comunicar com o servidor.', 'error');
        });
    }
</script>
@endsection

