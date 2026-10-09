@extends('admin.layouts.app')

@section('title', 'Editar Template: ' . $template['nome'])
@section('page_title', 'Editar Template de E-mail')
@section('page_description', 'Página dedicada para personalização do assunto e corpo com preview responsivo ao vivo.')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.emails.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
        </a>
        <span class="badge {{ !empty($template['sistema']) ? 'bg-secondary-subtle text-secondary border border-secondary-subtle' : 'bg-success-subtle text-success border border-success-subtle' }}">
            {{ !empty($template['sistema']) ? 'Template de Sistema' : 'Template Personalizado' }}
        </span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="abrirModalTeste()">
            <i class="fas fa-vial me-1"></i> Testar Envio
        </button>
        <button type="button" class="btn btn-danger fw-bold shadow-sm btn-sm" onclick="$('#formSalvarTemplate').submit()">
            <i class="fas fa-save me-1"></i> Salvar Alterações
        </button>
    </div>
</div>

<!-- VARIÁVEIS DINÂMICAS DISPONÍVEIS -->
<div class="alert alert-light border mb-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="small fw-bold text-body"><i class="fas fa-tags text-danger me-1"></i> Variáveis Dinâmicas do Sistema:</span>
        <span class="small text-muted" style="font-size: 11px;"><i class="fas fa-mouse-pointer me-1"></i> Clique em qualquer tag para inserir no cursor do editor</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{cliente_nome}')" title="Nome Completo do Cliente">{cliente_nome}</span>
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{cliente_email}')" title="E-mail do Cliente">{cliente_email}</span>
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{fatura_id}')" title="Código Identificador da Fatura">{fatura_id}</span>
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{plano_nome}')" title="Nome do Plano / Serviço">{plano_nome}</span>
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{valor}')" title="Valor Formatado">{valor}</span>
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{data_vencimento}')" title="Data de Vencimento">{data_vencimento}</span>
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{link_fatura}')" title="Link de Pagamento">{link_fatura}</span>
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{link_carne}')" title="Link do Carnê">{link_carne}</span>
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{nome_empresa}')" title="Nome da Empresa">{nome_empresa}</span>
        <span class="badge bg-secondary-subtle text-body border font-monospace cursor-pointer px-2 py-1" onclick="inserirTag('{whatsapp_empresa}')" title="WhatsApp Oficial">{whatsapp_empresa}</span>
    </div>
</div>

<div class="row g-4 align-items-stretch">
    <!-- COLUNA ESQUERDA: EDITOR DEDICADO WYSIWYG (col-xl-6) -->
    <div class="col-xl-6 col-lg-12">
        <form action="{{ route('admin.emails.update', $template['chave']) }}" method="POST" class="ajax-form h-100" id="formSalvarTemplate">
            @csrf
            <div class="card border shadow-sm h-100 d-flex flex-column">
                <div class="card-header py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-body"><i class="fas fa-edit text-danger me-2"></i> {{ $template['nome'] }}</h6>
                        <small class="text-secondary">{{ $template['descricao'] }}</small>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4 flex-grow-1">
                    @if(empty($template['sistema']))
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nome do Template</label>
                            <input type="text" name="nome" class="form-control" value="{{ $template['nome'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Descrição</label>
                            <input type="text" name="descricao" class="form-control" value="{{ $template['descricao'] }}">
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Assunto do E-mail <span class="text-danger">*</span></label>
                        <input type="text" name="assunto" id="templateAssunto" class="form-control" value="{{ $template['assunto_atual'] }}" required placeholder="Assunto exibido na caixa de entrada do cliente...">
                        <small class="text-muted" style="font-size: 11px;">O assunto suporta tags dinâmicas como {cliente_nome} e {fatura_id}.</small>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">Corpo do E-mail (HTML Formatado) <span class="text-danger">*</span></label>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-magic me-1"></i> Summernote WYSIWYG</span>
                        </div>
                        <small class="text-muted d-block mb-2" style="font-size: 11px;">O corpo é automaticamente compilado no layout master responsivo Foco 7 (cabeçalho tático, botões fluidos, suporte WhatsApp e rodapé).</small>
                        <textarea name="corpo" id="summernoteTemplate" class="form-control summernote" data-height="350">{{ $template['corpo_atual'] }}</textarea>
                    </div>
                </div>
                <div class="card-footer p-3 border-top d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="abrirModalTeste()">
                        <i class="fas fa-paper-plane me-1"></i> Disparar Teste Deste Template
                    </button>
                    <button type="submit" class="btn btn-danger fw-bold shadow-sm">
                        <i class="fas fa-save me-1"></i> Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- COLUNA DIREITA: SIMULADOR MULTI-DISPOSITIVO EM TEMPO REAL (col-xl-6) -->
    <div class="col-xl-6 col-lg-12">
        <div class="card border shadow-sm h-100 preview-card d-flex flex-column">
            <div class="card-header bg-dark text-white py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success text-white py-1 px-2" style="font-size: 11px;"><i class="fas fa-circle text-white me-1 blink-dot"></i> Ao Vivo</span>
                    <span class="fw-bold small text-light">Simulador em Tempo Real</span>
                </div>

                <!-- SELETOR DE DISPOSITIVOS -->
                <div class="btn-group btn-group-sm" role="group" aria-label="Seletor de Dispositivo">
                    <button type="button" class="btn btn-outline-light active btn-device" data-device="desktop" title="Computador / Desktop (Largura 100%)">
                        <i class="fas fa-desktop me-1"></i> <span class="d-none d-sm-inline">Desktop</span>
                    </button>
                    <button type="button" class="btn btn-outline-light btn-device" data-device="tablet" title="Tablet (Largura 520px com moldura)">
                        <i class="fas fa-tablet-alt me-1"></i> <span class="d-none d-sm-inline">Tablet</span>
                    </button>
                    <button type="button" class="btn btn-outline-light btn-device" data-device="mobile" title="Smartphone / Celular (Largura 375px estilo iPhone)">
                        <i class="fas fa-mobile-alt me-1"></i> <span class="d-none d-sm-inline">Mobile</span>
                    </button>
                </div>
            </div>

            <div class="card-body p-3 bg-secondary-subtle d-flex flex-column align-items-center justify-content-start overflow-hidden flex-grow-1" style="min-height: 580px;">
                <!-- MOLDURA SIMULADORA RESPONSIVA -->
                <div id="previewFrameWrapper" class="device-frame-wrapper w-100 shadow rounded bg-white overflow-hidden" style="transition: max-width 0.35s ease, border 0.35s ease, border-radius 0.35s ease; max-width: 100%; border: 1px solid #cbd5e1;">
                    <!-- BARRA SUPERIOR DO DISPOSITIVO -->
                    <div class="device-top-bar bg-light py-1 px-3 border-bottom d-flex justify-content-between align-items-center text-secondary" style="font-size: 11px;">
                        <span id="deviceModeLabel" class="font-monospace text-truncate"><i class="fas fa-desktop text-danger me-1"></i> Modo: Desktop (100%)</span>
                        <span class="badge bg-light text-dark border">Layout Master Responsivo</span>
                    </div>
                    <!-- IFRAME DO E-MAIL RENDERIZADO -->
                    <iframe id="iframePreviewEmail" class="w-100 border-0" style="height: 520px; display: block; background: #f1f5f9;" sandbox="allow-same-origin allow-scripts"></iframe>
                </div>
            </div>

            <div class="card-footer py-2 px-3 border-top d-flex justify-content-between align-items-center small text-muted">
                <span><i class="fas fa-shield-alt text-success me-1"></i> Rota única e botões com largura fluida</span>
                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-danger fw-bold" onclick="atualizarPreview(true)">
                    <i class="fas fa-sync-alt me-1"></i> Forçar Atualização
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE TESTE -->
<div class="modal fade" id="modalTesteDedicado" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="fas fa-paper-plane text-danger me-2"></i> Disparar Teste: {{ $template['nome'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.emails.testar') }}" method="POST" class="ajax-form">
                @csrf
                <input type="hidden" name="template_chave" value="{{ $template['chave'] }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">E-mail de Destino</label>
                        <input type="email" name="email_destinatario" class="form-control" value="{{ auth()->user()->email }}" required placeholder="seuemail@empresa.com">
                        <small class="text-secondary">O e-mail será disparado com o assunto e corpo atuais do editor compilados no layout oficial.</small>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-paper-plane me-1"></i> Disparar Demonstração</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const DADOS_SIMULADOS = {
        '{cliente_nome}': 'Carlos Alberto Silva (Demonstração)',
        '{cliente_email}': 'carlos.silva@exemplo.com.br',
        '{fatura_id}': 'FAT-20261009-0824',
        '{plano_nome}': 'FOCO 7 PLUS - Rastreamento Veicular Completo',
        '{valor}': 'R$ 89,90',
        '{data_vencimento}': '15/10/2026',
        '{link_fatura}': 'https://foco7.com.br/cliente/faturas',
        '{link_carne}': 'https://foco7.com.br/cliente/faturas',
        '{nome_empresa}': 'Foco 7 - Rastreamento e Segurança',
        '{whatsapp_empresa}': '(91) 8051-0736'
    };

    function compilarHtmlLocal(corpo, assunto) {
        let corpoProc = corpo || '<p>Digite o conteúdo do e-mail no editor ao lado...</p>';
        let assuntoProc = assunto || 'Notificação Foco 7';
        for (const [tag, val] of Object.entries(DADOS_SIMULADOS)) {
            corpoProc = corpoProc.split(tag).join(val);
            assuntoProc = assuntoProc.split(tag).join(val);
        }

        return `<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${assuntoProc}</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        p { margin: 0 0 16px 0; line-height: 1.65; color: #334155; font-size: 15px; }
        h1, h2, h3, h4, h5, h6 { margin: 0 0 14px 0; color: #0f172a; font-weight: 800; }
        a { color: #d90000; text-decoration: underline; font-weight: 600; }
        .btn-action { display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff !important; text-decoration: none !important; padding: 14px 32px; font-size: 15px; font-weight: 700; border-radius: 8px; box-shadow: 0 4px 12px rgba(217, 0, 0, 0.25); text-align: center; margin: 12px auto 20px auto; max-width: 100%; box-sizing: border-box; }
        .info-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #d90000; border-radius: 8px; padding: 16px 20px; margin: 16px 0 20px 0; }
        @media only screen and (max-width: 620px) {
            .wrapper-table { width: 100% !important; max-width: 100% !important; border-radius: 0 !important; border: none !important; }
            .header-cell { padding: 22px 18px !important; }
            .content-cell { padding: 24px 18px !important; font-size: 15px !important; }
            .btn-action { display: block !important; width: 100% !important; max-width: 100% !important; padding: 16px 18px !important; margin: 16px auto 22px auto !important; }
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 25px 8px; width: 100%;">
        <tr>
            <td align="center">
                <table role="presentation" class="wrapper-table" width="100%" style="max-width: 620px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="header-cell" style="background: linear-gradient(135deg, #0b0f19 0%, #1e293b 100%); padding: 28px 30px; text-align: center; border-bottom: 4px solid #d90000;">
                            <div style="font-size: 26px; font-weight: 900; color: #ffffff; line-height: 1.1;"><span style="color: #d90000;">FOCO</span> 7</div>
                            <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; letter-spacing: 2.5px; margin-top: 6px; font-weight: 600;">Rastreamento &bull; Bloqueio 24h &bull; Ronda Tática</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="content-cell" style="padding: 34px 32px 26px 32px; font-size: 15px; line-height: 1.65; color: #334155;">
                            ${corpoProc}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 32px 18px 32px; background-color: #ffffff;">
                            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 12px 16px; text-align: center; font-size: 13px; color: #475569;">
                                <strong style="color: #0f172a;">Suporte 24h WhatsApp:</strong> <a href="#" style="color: #25d366; text-decoration: none; font-weight: 800;">📱 (91) 8051-0736</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #0f172a; padding: 24px 30px; text-align: center; border-top: 1px solid #1e293b; font-size: 12px; color: #94a3b8; line-height: 1.6;">
                            <p style="margin: 0 0 4px 0; font-weight: 700; color: #f8fafc;">Foco 7 - Rastreamento e Segurança</p>
                            <p style="margin: 0; font-size: 11px; color: #64748b;">Tecnologia de alta precisão e pronta resposta para você e seu veículo.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>`;
    }

    var debounceTimer = null;

    function atualizarPreview(forcarAjax) {
        var assunto = $('#templateAssunto').val();
        var corpo = '';

        if ($.fn.summernote && $('#summernoteTemplate').next('.note-editor').length) {
            corpo = $('#summernoteTemplate').summernote('code');
        } else {
            corpo = $('#summernoteTemplate').val();
        }

        var iframe = document.getElementById('iframePreviewEmail');
        if (!iframe) return;

        // 1. Injeção instantânea no navegador (0ms)
        var htmlLocal = compilarHtmlLocal(corpo, assunto);
        try {
            var doc = iframe.contentWindow || iframe.contentDocument;
            if (doc.document) doc = doc.document;
            doc.open();
            doc.write(htmlLocal);
            doc.close();
        } catch (e) {}

        // 2. Sincronização via AJAX com a view Blade do backend
        if (debounceTimer) clearTimeout(debounceTimer);

        var delay = forcarAjax ? 0 : 350;
        debounceTimer = setTimeout(function () {
            $.ajax({
                url: '{{ route("admin.emails.preview") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    template_chave: '{{ $template["chave"] }}',
                    assunto: assunto,
                    corpo: corpo
                },
                success: function (res) {
                    if (res.success && res.html) {
                        try {
                            var d = iframe.contentWindow || iframe.contentDocument;
                            if (d.document) d = d.document;
                            d.open();
                            d.write(res.html);
                            d.close();
                        } catch (err) {}
                    }
                }
            });
        }, delay);
    }

    function inserirTag(tag) {
        navigator.clipboard.writeText(tag);
        if ($.fn.summernote && $('#summernoteTemplate').next('.note-editor').length) {
            $('#summernoteTemplate').summernote('pasteHTML', ' ' + tag + ' ');
            atualizarPreview(false);
            window.foco7Notify.success('Tag ' + tag + ' inserida no editor!');
        } else {
            window.foco7Notify.success('Tag ' + tag + ' copiada para a área de transferência!');
        }
    }

    function abrirModalTeste() {
        new bootstrap.Modal(document.getElementById('modalTesteDedicado')).show();
    }

    $(function () {
        // Carga inicial
        setTimeout(function () {
            atualizarPreview(false);
        }, 400);

        // Digitação no assunto
        $('#templateAssunto').on('input keyup', function () {
            atualizarPreview(false);
        });

        // Digitação no summernote
        $('#summernoteTemplate').on('summernote.change change input', function () {
            atualizarPreview(false);
        });

        // Alternância de Dispositivos Responsivos
        $('.btn-device').on('click', function () {
            var device = $(this).data('device');
            $('.btn-device').removeClass('active');
            $(this).addClass('active');

            var $wrapper = $('#previewFrameWrapper');
            var $label = $('#deviceModeLabel');

            if (device === 'desktop') {
                $wrapper.css({ 'max-width': '100%', 'border': '1px solid #cbd5e1', 'border-radius': '8px' });
                $label.html('<i class="fas fa-desktop text-danger me-1"></i> Modo: Desktop (100%)');
            } else if (device === 'tablet') {
                $wrapper.css({ 'max-width': '520px', 'border': '4px solid #334155', 'border-radius': '16px' });
                $label.html('<i class="fas fa-tablet-alt text-danger me-1"></i> Modo: Tablet (520px)');
            } else if (device === 'mobile') {
                $wrapper.css({ 'max-width': '375px', 'border': '6px solid #0f172a', 'border-radius': '26px' });
                $label.html('<i class="fas fa-mobile-alt text-danger me-1"></i> Modo: Smartphone Mobile (375px)');
            }
        });
    });
</script>
@endsection
