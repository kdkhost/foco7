<?php

namespace App\Services;

use App\Models\Fatura;
use App\Models\User;
use App\Models\Plano;
use App\Models\CmsSetting;
use App\Models\Notificacao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReguaCobrancaService
{
    /**
     * Retorna todos os parâmetros operacionais da Régua de Cobrança com defaults seguros
     */
    public static function obterParametros(): array
    {
        $reguaAtiva = CmsSetting::get('cobranca_regua_ativa', CmsSetting::get('regua_cobranca_ativa', '1')) == '1';
        $diasAntecedencia = (int) CmsSetting::get('cobranca_dias_antecedencia_fatura', CmsSetting::get('gerar_mensalidade_dias_antes', 10));

        $lembrete1Dias = (int) CmsSetting::get('cobranca_dias_lembrete_1', CmsSetting::get('regua_lembrete_1_dias', 10));
        $lembrete2Dias = (int) CmsSetting::get('cobranca_dias_lembrete_2', CmsSetting::get('regua_lembrete_2_dias', 5));
        $lembrete3Dias = (int) CmsSetting::get('cobranca_dias_lembrete_3', CmsSetting::get('regua_lembrete_3_dias', 1));

        $atraso1Dias = (int) CmsSetting::get('cobranca_dias_atraso_1', CmsSetting::get('regua_atraso_1_dias', 1));
        $atraso2Dias = (int) CmsSetting::get('cobranca_dias_atraso_2', CmsSetting::get('regua_atraso_2_dias', 5));
        $atraso3Dias = (int) CmsSetting::get('cobranca_dias_atraso_3', CmsSetting::get('regua_atraso_3_dias', 10));

        $multaPerc = (float) CmsSetting::get('cobranca_multa_percentual', CmsSetting::get('regua_multa_percentual', 2.0));
        $jurosMensal = (float) CmsSetting::get('cobranca_juros_mensal_percentual', CmsSetting::get('regua_juros_mensal_percentual', 1.0));
        $jurosDiario = (float) CmsSetting::get('regua_juros_diario_percentual', round($jurosMensal / 30, 4) ?: 0.033);

        return [
            // Controle Mestre
            'regua_ativa' => $reguaAtiva,
            'gerar_mensalidade_dias_antes' => $diasAntecedencia,
            'dias_antecedencia_fatura' => $diasAntecedencia,

            // Pré-Vencimento (Lembretes Preventivos)
            'lembrete_1_ativo' => CmsSetting::get('regua_lembrete_1_ativo', '1') == '1',
            'lembrete_1_dias' => $lembrete1Dias,
            'dias_lembrete_1' => $lembrete1Dias,

            'lembrete_2_ativo' => CmsSetting::get('regua_lembrete_2_ativo', '1') == '1',
            'lembrete_2_dias' => $lembrete2Dias,
            'dias_lembrete_2' => $lembrete2Dias,

            'lembrete_3_ativo' => CmsSetting::get('regua_lembrete_3_ativo', '1') == '1',
            'lembrete_3_dias' => $lembrete3Dias,
            'dias_lembrete_3' => $lembrete3Dias,

            // No Vencimento (D-0)
            'vencimento_hoje_ativo' => CmsSetting::get('regua_vencimento_hoje_ativo', '1') == '1',

            // Pós-Vencimento (Atrasos e Encargos)
            'atraso_1_ativo' => CmsSetting::get('regua_atraso_1_ativo', '1') == '1',
            'atraso_1_dias' => $atraso1Dias,
            'dias_atraso_1' => $atraso1Dias,

            'atraso_2_ativo' => CmsSetting::get('regua_atraso_2_ativo', '1') == '1',
            'atraso_2_dias' => $atraso2Dias,
            'dias_atraso_2' => $atraso2Dias,

            'atraso_3_ativo' => CmsSetting::get('regua_atraso_3_ativo', '1') == '1',
            'atraso_3_dias' => $atraso3Dias,
            'dias_atraso_3' => $atraso3Dias,

            // Parâmetros Financeiros de Atraso
            'multa_percentual' => $multaPerc,
            'juros_mensal_percentual' => $jurosMensal,
            'juros_diario_percentual' => $jurosDiario,
            'dias_tolerancia' => (int) CmsSetting::get('regua_dias_tolerancia', 0),

            // Canais de Notificação
            'enviar_email' => CmsSetting::get('regua_enviar_email', '1') == '1',
            'enviar_whatsapp' => CmsSetting::get('regua_enviar_whatsapp', '0') == '1',
        ];
    }

    /**
     * Calcula os encargos atualizados de multa e juros de mora para uma fatura
     */
    public static function calcularEncargos(Fatura $fatura, ?array $params = null): array
    {
        $params = $params ?: self::obterParametros();
        $base = (float) ($fatura->valor_original ?: $fatura->valor);

        if (!$fatura->data_vencimento) {
            return [
                'dias_atraso' => 0,
                'multa' => 0.00,
                'valor_multa' => 0.00,
                'juros' => 0.00,
                'valor_juros' => 0.00,
                'total_atualizado' => $base,
                'valor_total' => $base,
            ];
        }

        $hoje = now()->startOfDay();
        $venc = $fatura->data_vencimento->copy()->startOfDay();

        if ($hoje->lessThanOrEqualTo($venc)) {
            return [
                'dias_atraso' => 0,
                'multa' => 0.00,
                'valor_multa' => 0.00,
                'juros' => 0.00,
                'valor_juros' => 0.00,
                'total_atualizado' => $base,
                'valor_total' => $base,
            ];
        }

        $diasAtraso = (int) $venc->diffInDays($hoje);
        $tolerancia = $params['dias_tolerancia'] ?? 0;

        if ($diasAtraso <= $tolerancia) {
            return [
                'dias_atraso' => $diasAtraso,
                'multa' => 0.00,
                'valor_multa' => 0.00,
                'juros' => 0.00,
                'valor_juros' => 0.00,
                'total_atualizado' => $base,
                'valor_total' => $base,
            ];
        }

        $multaPerc = (float) ($params['multa_percentual'] ?? 2.0);
        $jurosDiario = (float) ($params['juros_diario_percentual'] ?? 0.033);

        $valorMulta = round($base * ($multaPerc / 100), 2);
        $valorJuros = round($base * (($jurosDiario / 100) * $diasAtraso), 2);
        $totalAtualizado = round($base + $valorMulta + $valorJuros, 2);

        return [
            'dias_atraso' => $diasAtraso,
            'multa' => $valorMulta,
            'valor_multa' => $valorMulta,
            'juros' => $valorJuros,
            'valor_juros' => $valorJuros,
            'total_atualizado' => $totalAtualizado,
            'valor_total' => $totalAtualizado,
        ];
    }

    /**
     * Gera mensalidades recorrentes com antecedência para clientes com planos ativos
     */
    public static function gerarMensalidadesRecorrentes(?int $diasAntes = null): array
    {
        $params = self::obterParametros();
        $dias = $diasAntes ?? $params['gerar_mensalidade_dias_antes'];
        $geradas = 0;
        $detalhes = [];

        // Clientes ativos que possuem plano associado
        $clientes = User::where('role', 'cliente')
            ->where('status', 'ativo')
            ->whereNotNull('plano_id')
            ->with('plano')
            ->get();

        foreach ($clientes as $cliente) {
            $plano = $cliente->plano;
            if (!$plano || (float) $plano->preco_mensal <= 0) {
                continue;
            }

            // Verifica a última fatura gerada para este cliente
            $ultimaFatura = Fatura::where('user_id', $cliente->id)
                ->where('plano_id', $plano->id)
                ->orderBy('data_vencimento', 'desc')
                ->first();

            $hoje = now()->startOfDay();
            $proximoVencimento = null;

            if (!$ultimaFatura) {
                // Primeira fatura recorrente: vence em $dias a partir de hoje
                $proximoVencimento = $hoje->copy()->addDays($dias);
            } else {
                $ultimoVenc = $ultimaFatura->data_vencimento->copy()->startOfDay();
                // O próximo vencimento é 1 mês após a última fatura
                $candidatoVenc = $ultimoVenc->copy()->addMonth();

                // Deve gerar se a data de emissão antecipada ($candidatoVenc - $dias) já foi atingida
                $dataCorteEmissao = $candidatoVenc->copy()->subDays($dias);
                if ($hoje->greaterThanOrEqualTo($dataCorteEmissao)) {
                    // Confirma se já não existe fatura para esse mês/ano de vencimento
                    $jaExiste = Fatura::where('user_id', $cliente->id)
                        ->where('plano_id', $plano->id)
                        ->whereYear('data_vencimento', $candidatoVenc->year)
                        ->whereMonth('data_vencimento', $candidatoVenc->month)
                        ->exists();

                    if (!$jaExiste) {
                        $proximoVencimento = $candidatoVenc;
                    }
                }
            }

            if ($proximoVencimento) {
                $codigoFatura = 'FAT-' . date('Ym') . '-' . strtoupper(Str::random(5));
                $valor = (float) $plano->preco_mensal;

                // Monta PIX padrão ou utiliza EfiPaymentService
                $fatura = Fatura::create([
                    'user_id' => $cliente->id,
                    'plano_id' => $plano->id,
                    'codigo_fatura' => $codigoFatura,
                    'descricao' => "Mensalidade Recorrente: {$plano->nome} (" . $proximoVencimento->format('m/Y') . ")",
                    'valor' => $valor,
                    'valor_original' => $valor,
                    'gateway' => CmsSetting::get('gateway_padrao', 'efi'),
                    'metodo_pagamento' => 'pix',
                    'status' => 'pendente',
                    'data_vencimento' => $proximoVencimento->toDateString(),
                ]);

                // Dispara a criação de cobrança PIX real via EfiPaymentService se disponível
                try {
                    $resPix = EfiPaymentService::criarCobrancaPix($fatura, $cliente);
                    if (!empty($resPix['qr_code'])) {
                        $fatura->qr_code_pix = $resPix['qr_code'];
                        $fatura->qr_code_pix_url = $resPix['imagem_qrcode'] ?? null;
                        $fatura->save();
                    }
                } catch (\Throwable $e) {
                    Log::warning("Falha ao gerar PIX para fatura recorrente {$fatura->codigo_fatura}: " . $e->getMessage());
                }

                $geradas++;
                $detalhes[] = "Fatura recorrente {$codigoFatura} criada para {$cliente->name} com vencimento em {$proximoVencimento->format('d/m/Y')}.";
            }
        }

        return [
            'total_geradas' => $geradas,
            'geradas' => $geradas,
            'detalhes' => $detalhes,
        ];
    }

    /**
     * Processa a régua de cobrança completa:
     * 1. Geração de novas mensalidades com antecedência;
     * 2. Lembretes Pré-Vencimento (1º, 2º e 3º lembretes);
     * 3. Cobrança no Vencimento (Hoje);
     * 4. Lembretes Pós-Vencimento com cálculo de multa e juros (1º, 2º e 3º avisos).
     */
    public static function processarFila(): array
    {
        $params = self::obterParametros();

        if (!$params['regua_ativa']) {
            return [
                'sucesso' => false,
                'mensagem' => 'A régua de cobrança automatizada está desativada no painel administrativo.',
                'metricas' => [],
            ];
        }

        $metricas = [
            'mensalidades_geradas' => 0,
            'lembretes_pre_1' => 0,
            'lembretes_pre_2' => 0,
            'lembretes_pre_3' => 0,
            'vencimento_hoje' => 0,
            'atraso_1' => 0,
            'atraso_2' => 0,
            'atraso_3' => 0,
            'faturas_atualizadas_encargos' => 0,
            'logs' => [],
        ];

        // 1. Geração antecipada de mensalidades
        $resRecorrencia = self::gerarMensalidadesRecorrentes();
        $metricas['mensalidades_geradas'] = $resRecorrencia['total_geradas'];
        $metricas['logs'] = array_merge($metricas['logs'], $resRecorrencia['detalhes']);

        // 2. Busca todas as faturas abertas (pendentes ou vencidas)
        $faturasAbertas = Fatura::with(['user', 'plano'])
            ->whereIn('status', ['pendente', 'vencida'])
            ->whereNotNull('data_vencimento')
            ->get();

        $hoje = now()->startOfDay();

        foreach ($faturasAbertas as $fatura) {
            $venc = $fatura->data_vencimento->copy()->startOfDay();

            // CASO A: FATURA A VENCER (PRÉ-VENCIMENTO)
            if ($venc->greaterThan($hoje)) {
                $diasAteVencimento = (int) $hoje->diffInDays($venc);

                // 1º Lembrete (ex: 10 dias antes)
                if ($params['lembrete_1_ativo'] && $diasAteVencimento === $params['lembrete_1_dias']) {
                    if ($fatura->ultimo_lembrete_enviado !== 'cobranca_lembrete_1') {
                        if (self::dispararEtapa($fatura, 'cobranca_lembrete_1')) {
                            $metricas['lembretes_pre_1']++;
                            $metricas['logs'][] = "1º Lembrete enviado para fatura {$fatura->codigo_fatura} (Vence em {$diasAteVencimento} dias).";
                        }
                    }
                }

                // 2º Lembrete (ex: 5 dias antes)
                if ($params['lembrete_2_ativo'] && $diasAteVencimento === $params['lembrete_2_dias']) {
                    if ($fatura->ultimo_lembrete_enviado !== 'cobranca_lembrete_2') {
                        if (self::dispararEtapa($fatura, 'cobranca_lembrete_2')) {
                            $metricas['lembretes_pre_2']++;
                            $metricas['logs'][] = "2º Lembrete enviado para fatura {$fatura->codigo_fatura} (Vence em {$diasAteVencimento} dias).";
                        }
                    }
                }

                // 3º Lembrete (Véspera - 1 dia antes)
                if ($params['lembrete_3_ativo'] && $diasAteVencimento === $params['lembrete_3_dias']) {
                    if ($fatura->ultimo_lembrete_enviado !== 'cobranca_lembrete_3') {
                        if (self::dispararEtapa($fatura, 'cobranca_lembrete_3')) {
                            $metricas['lembretes_pre_3']++;
                            $metricas['logs'][] = "3º Lembrete enviado para fatura {$fatura->codigo_fatura} (Vence amanhã).";
                        }
                    }
                }
            }

            // CASO B: VENCIMENTO HOJE (D-0)
            elseif ($venc->equalTo($hoje)) {
                if ($params['vencimento_hoje_ativo']) {
                    if ($fatura->ultimo_lembrete_enviado !== 'cobranca_vencimento_hoje') {
                        if (self::dispararEtapa($fatura, 'cobranca_vencimento_hoje')) {
                            $metricas['vencimento_hoje']++;
                            $metricas['logs'][] = "Aviso de Vencimento Hoje disparado para fatura {$fatura->codigo_fatura}.";
                        }
                    }
                }
            }

            // CASO C: FATURA VENCIDA (PÓS-VENCIMENTO)
            else {
                // Atualiza status para 'vencida' se ainda estiver 'pendente'
                if ($fatura->status === 'pendente') {
                    $fatura->status = 'vencida';
                    $fatura->save();
                }

                // Calcula encargos atualizados de multa e juros
                $encargos = self::calcularEncargos($fatura, $params);
                $diasAtraso = $encargos['dias_atraso'];

                // Atualiza os valores na fatura
                if ($encargos['multa'] > 0 || $encargos['juros'] > 0) {
                    $fatura->valor_multa = $encargos['multa'];
                    $fatura->valor_juros = $encargos['juros'];
                    $fatura->save();
                    $metricas['faturas_atualizadas_encargos']++;
                }

                // 1º Aviso de Atraso (ex: 1 dia após o vencimento)
                if ($params['atraso_1_ativo'] && $diasAtraso === $params['atraso_1_dias']) {
                    if ($fatura->ultimo_lembrete_enviado !== 'cobranca_atraso_1') {
                        if (self::dispararEtapa($fatura, 'cobranca_atraso_1', $encargos)) {
                            $metricas['atraso_1']++;
                            $metricas['logs'][] = "1º Aviso de Atraso enviado para fatura {$fatura->codigo_fatura} ({$diasAtraso}d de atraso).";
                        }
                    }
                }

                // 2º Aviso de Atraso (ex: 5 dias após o vencimento)
                if ($params['atraso_2_ativo'] && $diasAtraso === $params['atraso_2_dias']) {
                    if ($fatura->ultimo_lembrete_enviado !== 'cobranca_atraso_2') {
                        if (self::dispararEtapa($fatura, 'cobranca_atraso_2', $encargos)) {
                            $metricas['atraso_2']++;
                            $metricas['logs'][] = "2º Aviso de Atraso enviado para fatura {$fatura->codigo_fatura} ({$diasAtraso}d de atraso).";
                        }
                    }
                }

                // 3º Aviso de Atraso / Risco de Suspensão de Sinal (ex: 10 dias após o vencimento)
                if ($params['atraso_3_ativo'] && $diasAtraso === $params['atraso_3_dias']) {
                    if ($fatura->ultimo_lembrete_enviado !== 'cobranca_atraso_3') {
                        if (self::dispararEtapa($fatura, 'cobranca_atraso_3', $encargos)) {
                            $metricas['atraso_3']++;
                            $metricas['logs'][] = "3º Aviso de Atraso & Suspensão de Sinal enviado para {$fatura->codigo_fatura}.";
                        }
                    }
                }
            }
        }

        // Registra carimbo da última execução da régua
        $agora = now()->format('d/m/Y H:i:s');
        CmsSetting::set('cron_last_run_regua_cobranca', $agora, 'cron');
        CmsSetting::set('cron_last_metricas_regua', json_encode($metricas), 'cron');

        $totalProcessados = $metricas['lembretes_pre_1'] + $metricas['lembretes_pre_2'] + $metricas['lembretes_pre_3']
            + $metricas['vencimento_hoje'] + $metricas['atraso_1'] + $metricas['atraso_2'] + $metricas['atraso_3'];

        return [
            'sucesso' => true,
            'mensagem' => 'Régua de cobrança processada com sucesso.',
            'total_processados' => $totalProcessados,
            'lembrete_1' => $metricas['lembretes_pre_1'],
            'lembrete_2' => $metricas['lembretes_pre_2'],
            'lembrete_3' => $metricas['lembretes_pre_3'],
            'vencimento_hoje' => $metricas['vencimento_hoje'],
            'atraso_1' => $metricas['atraso_1'],
            'atraso_2' => $metricas['atraso_2'],
            'atraso_3' => $metricas['atraso_3'],
            'mensalidades_geradas' => $metricas['mensalidades_geradas'],
            'metricas' => array_merge($metricas, [
                'total_processados' => $totalProcessados,
                'lembrete_1' => $metricas['lembretes_pre_1'],
                'lembrete_2' => $metricas['lembretes_pre_2'],
                'lembrete_3' => $metricas['lembretes_pre_3'],
            ]),
        ];
    }

    /**
     * Dispara a notificação de uma etapa específica para a fatura informada
     */
    public static function dispararEtapa(Fatura $fatura, string $etapa, array $encargosExtras = []): bool
    {
        $cliente = $fatura->user;
        if (!$cliente || empty($cliente->email)) {
            return false;
        }

        $params = self::obterParametros();
        $encargos = !empty($encargosExtras) ? $encargosExtras : self::calcularEncargos($fatura, $params);

        $valorOriginalFormatado = 'R$ ' . number_format($fatura->valor_original ?: $fatura->valor, 2, ',', '.');
        $valorAtualizadoFormatado = 'R$ ' . number_format($encargos['total_atualizado'], 2, ',', '.');
        $multaFormatada = 'R$ ' . number_format($encargos['multa'], 2, ',', '.');
        $jurosFormatados = 'R$ ' . number_format($encargos['juros'], 2, ',', '.');

        $dadosPayload = [
            'cliente_nome' => $cliente->name,
            'cliente_email' => $cliente->email,
            'fatura_id' => $fatura->codigo_fatura,
            'plano_nome' => $fatura->plano->nome ?? $fatura->descricao,
            'valor' => $fatura->valor_formatado,
            'valor_original' => $valorOriginalFormatado,
            'valor_atualizado' => $valorAtualizadoFormatado,
            'multa' => $multaFormatada,
            'juros' => $jurosFormatados,
            'multa_percentual' => number_format($params['multa_percentual'], 1, ',', '.'),
            'dias_atraso' => $encargos['dias_atraso'],
            'data_vencimento' => $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : '',
            'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
            'pix_copia_cola' => $fatura->qr_code_pix ?? '',
            'codigo_barras' => $fatura->linha_digitavel_boleto ?? '',
        ];

        $enviado = false;

        // Disparo por e-mail
        if ($params['enviar_email']) {
            $enviado = EmailTemplateService::enviar($cliente->email, $etapa, $dadosPayload);
        }

        // Notificação Interna do Sistema (Sininho de Notificações do Cliente)
        $tituloNotif = match ($etapa) {
            'cobranca_lembrete_1' => 'Lembrete: Sua fatura Foco 7 está disponível',
            'cobranca_lembrete_2' => 'Sua fatura Foco 7 vence em breve',
            'cobranca_lembrete_3' => 'Sua fatura Foco 7 vence amanhã!',
            'cobranca_vencimento_hoje' => 'Sua fatura Foco 7 vence HOJE!',
            'cobranca_atraso_1' => 'Aviso: Sua fatura venceu ontem',
            'cobranca_atraso_2' => '2º Aviso: Fatura em atraso com encargos',
            'cobranca_atraso_3' => 'Urgente: Risco de suspensão de sinal por atraso',
            default => 'Notificação de Cobrança Foco 7',
        };

        $tipoNotif = str_contains($etapa, 'atraso') ? 'urgente' : 'aviso';

        Notificacao::create([
            'user_id' => $cliente->id,
            'tipo' => $tipoNotif,
            'titulo' => $tituloNotif,
            'mensagem' => "Fatura #{$fatura->codigo_fatura} com vencimento em " . ($fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : '') . ". Valor: {$valorAtualizadoFormatado}.",
            'link' => "/cliente/faturas/{$fatura->id}",
            'lida' => false,
        ]);

        // Grava no histórico da fatura
        $historico = $fatura->historico_cobranca ?: [];
        $historico[] = [
            'data_hora' => now()->format('Y-m-d H:i:s'),
            'etapa' => $etapa,
            'canal' => 'email',
            'valor_base' => $fatura->valor,
            'multa' => $encargos['multa'],
            'juros' => $encargos['juros'],
            'total' => $encargos['total_atualizado'],
            'status' => $enviado ? 'sucesso' : 'pendente',
        ];

        $fatura->ultimo_lembrete_enviado = $etapa;
        $fatura->data_ultimo_lembrete = now();
        $fatura->total_lembretes_enviados = ($fatura->total_lembretes_enviados ?? 0) + 1;
        $fatura->historico_cobranca = $historico;
        $fatura->save();

        return true;
    }
}
