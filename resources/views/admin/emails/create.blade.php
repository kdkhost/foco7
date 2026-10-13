@extends('admin.layouts.app')

@section('title', 'Criar Novo Template de E-mail')
@section('page_title', 'Novo Template de E-mail')
@section('page_description', 'Cadastre um novo modelo de e-mail com editor WYSIWYG e pré-visualização ao vivo.')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.emails.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
        </a>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
            Novo Modelo Personalizado
        </span>
    </div>
    <div>
        <button type="button" class="btn btn-danger fw-bold shadow-sm" onclick="$('#formNovoTemplate').submit()">
            <i class="fas fa-check me-1"></i> Salvar &amp; Ativar Template
        </button>
    </div>
</div>

<form action="{{ route('admin.emails.store') }}" method="POST" class="ajax-form" id="formNovoTemplate">
    @csrf
    <div class="row g-4 align-items-stretch">
        <!-- COLUNA ESQUERDA: CAMPOS DO NOVO TEMPLATE -->
        <div class="col-xl-6 col-lg-12">
            <div class="card border shadow-sm h-100 d-flex flex-column">
                <div class="card-header py-3 px-4 border-bottom">
                    <h6 class="fw-bold mb-0 text-body"><i class="fas fa-plus-circle text-danger me-2"></i> Configurações do Template</h6>
                </div>
                <div class="card-body p-3 p-md-4 flex-grow-1">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nome do Template <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="templateNome" class="form-control" required placeholder="Ex: Boas-Vindas ao Cliente VIP" onkeyup="gerarSlug(this.value)">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Identificador Único (Chave)</label>
                            <input type="text" name="chave" id="templateChave" class="form-control font-monospace" placeholder="boas_vindas_vip">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Descrição da Finalidade</label>
                            <input type="text" name="descricao" class="form-control" placeholder="Descreva brevemente quando este e-mail deve ser acionado...">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Assunto do E-mail <span class="text-danger">*</span></label>
                            <input type="text" name="assunto" id="templateAssunto" class="form-control" required placeholder="Ex: 🚀 Seja muito bem-vindo à Foco 7, {cliente_nome}!" value="🚀 Seja muito bem-vindo à Foco 7, {cliente_nome}!">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">Corpo do E-mail (HTML Formatado) <span class="text-danger">*</span></label>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-magic me-1"></i> Summernote WYSIWYG</span>
                        </div>
                        <small class="text-muted d-block mb-2" style="font-size: 11px;">O corpo será automaticamente encapsulado no layout master responsivo Foco 7.</small>
                        <textarea name="corpo" id="summernoteTemplate" class="form-control summernote" data-height="300"><p>Olá, <strong>{cliente_nome}</strong>!</p><p>É uma imensa satisfação ter você como cliente da <strong>{nome_empresa}</strong>. Sua segurança e tranquilidade são nossa prioridade máxima.</p><div class="info-card"><p><strong>Dados do Contrato:</strong></p><p>Plano Contratado: {plano_nome}<br>Mensalidade: {valor}</p></div><div class="btn-wrapper"><a href="https://foco7.com.br/cliente/faturas" class="btn-action">Acessar Área do Assinante</a></div><p>Em caso de dúvidas técnicas ou suporte, entre em contato com nossa Central 24h:</p><p>WhatsApp: <strong>{whatsapp_empresa}</strong></p></textarea>
                    </div>
                </div>
                <div class="card-footer p-3 border-top text-end">
                    <button type="submit" class="btn btn-danger fw-bold shadow-sm">
                        <i class="fas fa-check me-1"></i> Concluir Criação do Template
                    </button>
                </div>
            </div>
        </div>

        <!-- COLUNA DIREITA: SIMULADOR AO VIVO EM TEMPO REAL -->
        <div class="col-xl-6 col-lg-12">
            <div class="card border shadow-sm h-100 preview-card d-flex flex-column">
                <div class="card-header bg-dark text-white py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success text-white py-1 px-2" style="font-size: 11px;"><i class="fas fa-circle text-white me-1 blink-dot"></i> Ao Vivo</span>
                        <span class="fw-bold small text-light">Simulador em Tempo Real</span>
                    </div>

                    <!-- SELETOR DE DISPOSITIVOS -->
                    <div class="card-tools ms-auto btn-group btn-group-sm" role="group" aria-label="Seletor de Dispositivo">
                        <button type="button" class="btn btn-outline-light active btn-device" data-device="desktop">
                            <i class="fas fa-desktop me-1"></i> <span class="d-none d-sm-inline">Desktop</span>
                        </button>
                        <button type="button" class="btn btn-outline-light btn-device" data-device="tablet">
                            <i class="fas fa-tablet-alt me-1"></i> <span class="d-none d-sm-inline">Tablet</span>
                        </button>
                        <button type="button" class="btn btn-outline-light btn-device" data-device="mobile">
                            <i class="fas fa-mobile-alt me-1"></i> <span class="d-none d-sm-inline">Mobile</span>
                        </button>
                    </div>
                </div>

                <div class="card-body p-3 bg-secondary-subtle d-flex flex-column align-items-center justify-content-start overflow-hidden flex-grow-1" style="min-height: 580px;">
                    <div id="previewFrameWrapper" class="device-frame-wrapper w-100 shadow rounded bg-white overflow-hidden" style="transition: max-width 0.35s ease, border 0.35s ease, border-radius 0.35s ease; max-width: 100%; border: 1px solid #cbd5e1;">
                        <div class="device-top-bar bg-body-secondary py-1 px-3 border-bottom d-flex justify-content-between align-items-center text-body-secondary" style="font-size: 11px;">
                            <span id="deviceModeLabel" class="font-monospace text-truncate"><i class="fas fa-desktop text-danger me-1"></i> Modo: Desktop (100%)</span>
                            <span class="badge bg-body-tertiary text-body border">Layout Master Responsivo</span>
                        </div>
                        <iframe id="iframePreviewEmail" class="w-100 border-0" style="height: 520px; display: block; background: #f1f5f9;" sandbox="allow-same-origin allow-scripts"></iframe>
                    </div>
                </div>

                <div class="card-footer py-2 px-3 border-top small text-muted">
                    <i class="fas fa-info-circle text-primary me-1"></i> O layout acima é compilado dinamicamente com dados simulados para validação antes de salvar.
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
@php
    $logoPath = \App\Models\CmsSetting::get('site_logo') ?: '/img/logo-foco7.png';
    $logoOficialUrl = (str_starts_with($logoPath, 'http://') || str_starts_with($logoPath, 'https://')) ? $logoPath : asset(ltrim($logoPath, '/'));
@endphp
<script>
    const LOGO_OFICIAL_URL = "{{ $logoOficialUrl }}";
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
        table { border-collapse: separate; }
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        p { margin: 0 0 16px 0; line-height: 1.65; color: #334155; font-size: 15px; }
        h1, h2, h3, h4, h5, h6 { margin: 0 0 14px 0; color: #0f172a; font-weight: 800; }
        a { color: #d90000; text-decoration: underline; font-weight: 600; }
        .btn-action { display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff !important; text-decoration: none !important; padding: 14px 32px; font-size: 15px; font-weight: 700; border-radius: 8px; box-shadow: 0 4px 12px rgba(217, 0, 0, 0.25); text-align: center; margin: 12px auto 20px auto; max-width: 100%; box-sizing: border-box; }
        .info-card, .data-card, .invoice-summary-card, .highlight-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px !important; overflow: hidden !important; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04); margin: 22px 0 26px 0; }
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
                        <td class="header-cell" style="background: linear-gradient(135deg, #0b0f19 0%, #1e293b 100%); padding: 26px 30px; text-align: center; border-bottom: 4px solid #d90000;">
                            <a href="#" style="text-decoration: none; display: inline-block;">
                                <img src="${LOGO_OFICIAL_URL}" alt="Foco 7 Rastreamento" height="48" style="height: 48px; max-height: 54px; width: auto; max-width: 230px; border: 0; outline: none; display: block; margin: 0 auto;" />
                            </a>
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

    function atualizarPreview() {
        var assunto = $('#templateAssunto').val();
        var corpo = '';

        if ($.fn.summernote && $('#summernoteTemplate').next('.note-editor').length) {
            corpo = $('#summernoteTemplate').summernote('code');
        } else {
            corpo = $('#summernoteTemplate').val();
        }

        var iframe = document.getElementById('iframePreviewEmail');
        if (!iframe) return;

        var htmlLocal = compilarHtmlLocal(corpo, assunto);
        try {
            var doc = iframe.contentWindow || iframe.contentDocument;
            if (doc.document) doc = doc.document;
            doc.open();
            doc.write(htmlLocal);
            doc.close();
        } catch (e) {}
    }

    function gerarSlug(nome) {
        var slug = (nome || '').toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z0-9]/g, '_')
            .replace(/_+/g, '_')
            .replace(/^_+|_+$/g, '');
        $('#templateChave').val(slug);
    }

    $(function () {
        setTimeout(function () {
            atualizarPreview();
        }, 400);

        $('#templateAssunto').on('input keyup', function () {
            atualizarPreview();
        });

        $('#summernoteTemplate').on('summernote.change change input', function () {
            atualizarPreview();
        });

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
