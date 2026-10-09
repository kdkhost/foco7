<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="x-apple-disable-message-reformatting" />
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no" />
    <title>{{ $assunto ?? 'Notificação Foco 7 Rastreamento' }}</title>
    <style type="text/css">
        /* RESET GERAL DE CLIENTES DE EMAIL */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; min-width: 100% !important; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        
        /* ESTILOS DE ELEMENTOS DO CORPO */
        p { margin: 0 0 16px 0; line-height: 1.65; color: #334155; font-size: 15px; }
        h1, h2, h3, h4, h5, h6 { margin: 0 0 14px 0; color: #0f172a; font-weight: 800; line-height: 1.3; }
        ul, ol { margin: 0 0 16px 20px; padding: 0; color: #334155; line-height: 1.6; }
        li { margin-bottom: 6px; }
        a { color: #d90000; text-decoration: underline; font-weight: 600; }
        
        /* BOTÕES DE AÇÃO RESPONSIVOS E DINÂMICOS */
        .btn-action {
            display: inline-block;
            background: linear-gradient(135deg, #d90000 0%, #b80000 100%);
            color: #ffffff !important;
            text-decoration: none !important;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(217, 0, 0, 0.25);
            text-align: center;
            margin: 12px auto 20px auto;
            letter-spacing: 0.3px;
            max-width: 100%;
            box-sizing: border-box;
            line-height: 1.35;
        }
        .btn-action:hover {
            background: #b80000 !important;
            box-shadow: 0 6px 16px rgba(217, 0, 0, 0.35);
        }
        .btn-wrapper {
            text-align: center;
            margin: 18px 0 24px 0;
            width: 100%;
        }

        /* CAIXAS DE DESTAQUE / CARDS */
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #d90000;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 16px 0 20px 0;
        }
        
        .badge-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* TABELAS INTERNAS RESPONSIVAS */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            font-size: 14px;
        }
        .content-table th, .content-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .content-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
        }

        /* REGRAS MOBILE RESPONSIVAS (DINÂMICAS POR DISPOSITIVO) */
        @media only screen and (max-width: 620px) {
            .wrapper-table {
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0 !important;
                border: none !important;
            }
            .header-cell {
                padding: 24px 18px !important;
            }
            .content-cell {
                padding: 26px 18px !important;
                font-size: 15px !important;
            }
            .footer-cell {
                padding: 22px 18px !important;
            }
            .brand-name {
                font-size: 24px !important;
            }
            .btn-action {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
                padding: 16px 18px !important;
                font-size: 16px !important;
                text-align: center !important;
                margin: 16px auto 22px auto !important;
                border-radius: 10px !important;
            }
            .btn-wrapper {
                width: 100% !important;
                display: block !important;
            }
            .fluid-stack {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
        }
        @media only screen and (max-width: 420px) {
            .btn-action {
                padding: 15px 12px !important;
                font-size: 15px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <!-- CONTAINER GLOBAL -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f1f5f9; width: 100%; margin: 0; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- CARD PRINCIPAL DO EMAIL -->
                <table role="presentation" class="wrapper-table" width="100%" style="max-width: 620px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0" border="0">
                    
                    <!-- CABEÇALHO OFICIAL FOCO 7 -->
                    <tr>
                        <td class="header-cell" style="background: linear-gradient(135deg, #0b0f19 0%, #1e293b 100%); padding: 30px 32px; text-align: center; border-bottom: 4px solid #d90000;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <div class="brand-name" style="font-size: 28px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px; line-height: 1.1;">
                                            <span style="color: #d90000;">FOCO</span> 7
                                        </div>
                                        <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; letter-spacing: 2.5px; margin-top: 6px; font-weight: 600;">
                                            Rastreamento &bull; Bloqueio 24h &bull; Ronda Tática
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CORPO DA MENSAGEM (INJEÇÃO DINÂMICA) -->
                    <tr>
                        <td class="content-cell" style="padding: 38px 36px 30px 36px; font-size: 15px; line-height: 1.65; color: #334155; background-color: #ffffff;">
                            {!! $corpo !!}
                        </td>
                    </tr>

                    <!-- BANNER DE SUPORTE WHATSAPP 24H -->
                    <tr>
                        <td style="padding: 0 36px 20px 36px; background-color: #ffffff;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 14px 18px;">
                                <tr>
                                    <td align="center" style="font-size: 13px; color: #475569; line-height: 1.5;">
                                        <span style="display: inline-block; margin-right: 6px; font-weight: 700; color: #0f172a;">Precisa de Ajuda ou Suporte?</span>
                                        Central 24h WhatsApp: <a href="https://wa.me/55{{ preg_replace('/\D/', '', $whatsapp ?? '9180510736') }}" style="color: #25d366; font-weight: 800; text-decoration: none;" target="_blank">📱 {{ $whatsapp ?? '(91) 8051-0736' }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- RODAPÉ INSTITUCIONAL PADRONIZADO -->
                    <tr>
                        <td class="footer-cell" style="background-color: #0f172a; padding: 26px 36px; text-align: center; border-top: 1px solid #1e293b; font-size: 12px; color: #94a3b8; line-height: 1.6;">
                            <p style="margin: 0 0 6px 0; font-weight: 700; color: #f8fafc; font-size: 13px;">{{ $nomeEmpresa ?? 'Foco 7 Rastreamento e Segurança' }}</p>
                            <p style="margin: 0 0 8px 0; font-size: 12px; color: #94a3b8;">
                                Tecnologia de alta precisão e pronta resposta para você e seu veículo.
                            </p>
                            <p style="margin: 0 0 10px 0; font-size: 11px; color: #64748b;">
                                Este é um e-mail transacional automático enviado aos clientes cadastrados. Por motivos de segurança, não responda a esta mensagem.
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #475569; border-top: 1px solid #1e293b; padding-top: 10px;">
                                &copy; {{ date('Y') }} {{ $nomeEmpresa ?? 'Foco 7' }}. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
                <!-- /CARD PRINCIPAL -->
            </td>
        </tr>
    </table>
</body>
</html>
