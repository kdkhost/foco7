<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Carnê de Pagamento - {{ $codigo_carne }}</title>
    <style>
        @page {
            margin: 12mm 10mm 12mm 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.3;
        }
        .page-break {
            page-break-after: always;
        }
        .header-capa {
            background: #11141c;
            color: #ffffff;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .header-capa h1 {
            color: #d90000;
            margin: 0 0 6px 0;
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-capa p {
            margin: 2px 0;
            color: #cbd5e1;
            font-size: 11px;
        }
        .card-info {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 14px;
            margin-bottom: 16px;
            background: #f8fafc;
        }
        .card-info h3 {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #d90000;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .table-resumo {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-resumo th {
            background: #1e293b;
            color: #ffffff;
            padding: 6px 10px;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }
        .table-resumo td {
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .table-resumo tr:nth-child(even) {
            background: #f1f5f9;
        }
        .badge-status {
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .badge-pendente { background: #fef08a; color: #854d0e; }
        .badge-pago { background: #bbf7d0; color: #166534; }

        /* Estilo da Lâmina / Canhoto do Carnê */
        .carne-lamina {
            width: 100%;
            border: 1px dashed #64748b;
            border-radius: 6px;
            margin-bottom: 16px;
            padding: 10px;
            page-break-inside: avoid;
            background: #ffffff;
        }
        .lamina-container {
            width: 100%;
        }
        .canhoto-pagador {
            width: 32%;
            vertical-align: top;
            border-right: 1px dashed #94a3b8;
            padding-right: 10px;
        }
        .ficha-compensacao {
            width: 68%;
            vertical-align: top;
            padding-left: 10px;
        }
        .banco-cabecalho {
            border-bottom: 2px solid #000000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .banco-nome {
            font-size: 14px;
            font-weight: bold;
            color: #d90000;
        }
        .banco-codigo {
            font-size: 14px;
            font-weight: bold;
            border-left: 2px solid #000000;
            border-right: 2px solid #000000;
            padding: 0 8px;
            margin: 0 6px;
        }
        .linha-digitavel {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .campo-box {
            border: 1px solid #94a3b8;
            padding: 3px 6px;
            margin-bottom: 4px;
            background: #ffffff;
        }
        .campo-titulo {
            font-size: 8px;
            text-transform: uppercase;
            color: #475569;
            font-weight: bold;
            display: block;
        }
        .campo-valor {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
        }
        .linha-corte {
            text-align: center;
            font-size: 8px;
            color: #64748b;
            margin: 6px 0 12px 0;
            border-top: 1px dashed #94a3b8;
            padding-top: 2px;
        }
        .pix-mini-box {
            border: 1px solid #10b981;
            background: #ecfdf5;
            padding: 6px;
            border-radius: 4px;
            text-align: center;
            margin-top: 4px;
        }
        .barcode-strip {
            height: 38px;
            background: repeating-linear-gradient(
                90deg,
                #000000 0px,
                #000000 2px,
                #ffffff 2px,
                #ffffff 4px,
                #000000 4px,
                #000000 7px,
                #ffffff 7px,
                #ffffff 9px
            );
            margin-top: 6px;
            border: 1px solid #000000;
        }
    </style>
</head>
<body>

    <!-- =========================================================================
         1. CAPA DO CARNÊ FOCO 7
         ========================================================================= -->
    <div class="header-capa">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: middle;">
                    <h1>FOCO 7 MOTORS & RASTREAMENTO</h1>
                    <p><strong>Carnê de Pagamento de Mensalidades</strong> &bull; Código: {{ $codigo_carne }}</p>
                    <p>{{ $empresa_nome }} &bull; CNPJ: {{ $empresa_cnpj }} &bull; Tel/WhatsApp: {{ $empresa_telefone }}</p>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 6px; display: inline-block; text-align: left;">
                        <span style="font-size: 9px; color: #94a3b8; text-transform: uppercase;">Emissão Oficial:</span><br>
                        <strong style="font-size: 12px; color: #ffffff;">{{ date('d/m/Y') }}</strong><br>
                        <span style="font-size: 9px; color: #94a3b8;">Total: {{ $faturas->count() }} Parcelas</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- DADOS DO CLIENTE & PLANO -->
    <table style="width: 100%; margin-bottom: 12px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                <div class="card-info" style="margin-bottom: 0;">
                    <h3>Dados do Titular / Assinante</h3>
                    <p style="margin: 3px 0;"><strong>Nome:</strong> {{ $cliente->name }}</p>
                    <p style="margin: 3px 0;"><strong>CPF/CNPJ:</strong> {{ $cliente->cpf_cnpj ?: 'Não informado' }}</p>
                    <p style="margin: 3px 0;"><strong>E-mail:</strong> {{ $cliente->email }}</p>
                    <p style="margin: 3px 0;"><strong>WhatsApp/Tel:</strong> {{ $cliente->telefone ?: 'Não informado' }}</p>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 8px;">
                <div class="card-info" style="margin-bottom: 0;">
                    <h3>Serviço / Plano Contratado</h3>
                    <p style="margin: 3px 0;"><strong>Descrição:</strong> {{ $plano->nome ?? $faturas->first()->descricao }}</p>
                    <p style="margin: 3px 0;"><strong>Parcela Mensal:</strong> {{ $faturas->first()->valor_formatado }}</p>
                    @if($faturas->first()->taxa_repassada > 0)
                        <p style="margin: 3px 0; color: #b91c1c; font-size: 10px;">
                            <strong>Taxa de Emissão Repassada:</strong> R$ {{ number_format($faturas->first()->taxa_repassada, 2, ',', '.') }} por parcela
                        </p>
                    @endif
                    <p style="margin: 3px 0;"><strong>Processamento:</strong> Efí Bank S.A. (Compensação Nacional)</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- CRONOGRAMA DE PARCELAS -->
    <div class="card-info">
        <h3>Cronograma de Vencimento das Parcelas</h3>
        <table class="table-resumo">
            <thead>
                <tr>
                    <th>Parcela</th>
                    <th>Fatura</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($faturas as $fat)
                <tr>
                    <td><strong>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($faturas->count(), 2, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $fat->codigo_fatura }}</td>
                    <td><strong>{{ $fat->data_vencimento->format('d/m/Y') }}</strong></td>
                    <td><strong style="color: #047857;">{{ $fat->valor_formatado }}</strong></td>
                    <td>
                        @if($fat->status === 'pago')
                            <span class="badge-status badge-pago">Pago</span>
                        @else
                            <span class="badge-status badge-pendente">A Vencer</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="font-size: 9px; color: #64748b; text-align: center; margin-top: 15px;">
        Página 1: Capa e Termo de Adesão &bull; Destaque as lâminas abaixo para efetuar os pagamentos mensais.
    </div>

    <div class="page-break"></div>

    <!-- =========================================================================
         2. LÂMINAS DE PARCELAS DESTACÁVEIS DO CARNÊ
         ========================================================================= -->
    @foreach($faturas as $index => $fat)
    <div class="carne-lamina">
        <table class="lamina-container">
            <tr>
                <!-- CANHOTO DO PAGADOR (RECIBO DO CLIENTE) -->
                <td class="canhoto-pagador">
                    <div style="font-size: 11px; font-weight: bold; color: #d90000; margin-bottom: 4px;">
                        FOCO 7 &bull; PARCELA {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($faturas->count(), 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="font-size: 8px; color: #64748b; margin-bottom: 6px;">Recibo do Sacado / Cliente</div>

                    <div class="campo-box">
                        <span class="campo-titulo">Vencimento</span>
                        <span class="campo-valor" style="color: #d90000;">{{ $fat->data_vencimento->format('d/m/Y') }}</span>
                    </div>

                    <div class="campo-box">
                        <span class="campo-titulo">Valor do Documento</span>
                        <span class="campo-valor" style="color: #047857;">{{ $fat->valor_formatado }}</span>
                    </div>

                    <div class="campo-box">
                        <span class="campo-titulo">Número do Documento</span>
                        <span class="campo-valor" style="font-size: 9px;">{{ $fat->codigo_fatura }}</span>
                    </div>

                    <div class="campo-box">
                        <span class="campo-titulo">Sacado / Cliente</span>
                        <span class="campo-valor" style="font-size: 9px;">{{ substr($cliente->name, 0, 24) }}</span>
                    </div>

                    <!-- Mini QR Code PIX -->
                    <div class="pix-mini-box">
                        <div style="font-size: 8px; font-weight: bold; color: #047857;">PIX DIRETO NA PARCELA</div>
                        <img src="{{ $fat->qr_code_pix_url }}" alt="PIX" style="width: 65px; height: 65px; margin: 2px 0;">
                        <div style="font-size: 7px; color: #065f46;">Aponte a câmera do banco</div>
                    </div>
                </td>

                <!-- FICHA DE COMPENSAÇÃO (CAIXA / INTERNET BANKING) -->
                <td class="ficha-compensacao">
                    <div class="banco-cabecalho">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 25%;">
                                    <span class="banco-nome">EFÍ BANK</span>
                                    <span class="banco-codigo">364-2</span>
                                </td>
                                <td style="width: 75%; text-align: right;">
                                    <span class="linha-digitavel">{{ $fat->linha_digitavel_boleto }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 65%; vertical-align: top; padding-right: 4px;">
                                <div class="campo-box">
                                    <span class="campo-titulo">Local de Pagamento</span>
                                    <span class="campo-valor" style="font-size: 9px;">Pagável em qualquer banco, lotérica ou aplicativo via PIX/Boleto</span>
                                </div>
                                <div class="campo-box">
                                    <span class="campo-titulo">Beneficiário</span>
                                    <span class="campo-valor" style="font-size: 9px;">{{ $empresa_nome }} - CNPJ: {{ $empresa_cnpj }}</span>
                                </div>
                                <div class="campo-box">
                                    <span class="campo-titulo">Instruções de Responsabilidade do Beneficiário</span>
                                    <div style="font-size: 8px; color: #334155; line-height: 1.2;">
                                        &bull; {{ $instrucoes }}<br>
                                        &bull; Parcela {{ $loop->iteration }} de {{ $faturas->count() }} referente a rastreamento/serviço veicular.<br>
                                        &bull; Em caso de dúvidas, consulte nossa central pelo WhatsApp {{ $empresa_telefone }}.
                                    </div>
                                </div>
                            </td>

                            <td style="width: 35%; vertical-align: top;">
                                <div class="campo-box">
                                    <span class="campo-titulo">Data de Vencimento</span>
                                    <span class="campo-valor" style="color: #d90000; font-size: 12px;">{{ $fat->data_vencimento->format('d/m/Y') }}</span>
                                </div>
                                <div class="campo-box">
                                    <span class="campo-titulo">Valor do Documento</span>
                                    <span class="campo-valor" style="color: #047857; font-size: 12px;">{{ $fat->valor_formatado }}</span>
                                </div>
                                <div class="campo-box">
                                    <span class="campo-titulo">(=) Valor Cobrado</span>
                                    <span class="campo-valor" style="font-size: 11px;">{{ $fat->valor_formatado }}</span>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="campo-box" style="margin-top: 4px;">
                        <span class="campo-titulo">Pagador / Sacado</span>
                        <span class="campo-valor" style="font-size: 9px;">
                            {{ $cliente->name }} &bull; CPF/CNPJ: {{ $cliente->cpf_cnpj ?: 'Não informado' }} &bull; Tel: {{ $cliente->telefone }}
                        </span>
                    </div>

                    <!-- Código de Barras Representativo -->
                    <div class="barcode-strip"></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="linha-corte">&bull; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - CORTE AQUI PARA DESTACAR A PARCELA {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - &bull;</div>

    {{-- A cada 3 parcelas, realiza quebra de página automática --}}
    @if(($index + 1) % 3 === 0 && !$loop->last)
        <div class="page-break"></div>
    @endif
    @endforeach

</body>
</html>
