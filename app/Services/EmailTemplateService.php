<?php

namespace App\Services;

use App\Models\CmsSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Exception;

class EmailTemplateService
{
    /**
     * Retorna a lista de chaves e definições dos templates de e-mail (nativos e customizados)
     */
    public static function getTemplates(): array
    {
        $templates = [
            'fatura_gerada' => [
                'nome' => 'Nova Fatura Gerada',
                'descricao' => 'Disparado quando uma nova cobrança de adesão ou mensalidade é gerada para o cliente.',
                'assunto_padrao' => '📄 Fatura Gerada (#{fatura_id}) - Foco 7 Rastreamento',
                'corpo_padrao' => self::getTemplatePadraoFaturaGerada(),
                'sistema' => true,
            ],
            'pagamento_confirmado' => [
                'nome' => 'Confirmação de Pagamento Aprovado',
                'descricao' => 'Disparado imediatamente após a confirmação do pagamento via PIX, Cartão ou Boleto.',
                'assunto_padrao' => '✅ Pagamento Confirmado com Sucesso! - Foco 7 Rastreamento',
                'corpo_padrao' => self::getTemplatePadraoPagamentoConfirmado(),
                'sistema' => true,
            ],
            'carne_gerado' => [
                'nome' => 'Carnê de Mensalidades Emitido',
                'descricao' => 'Disparado quando um carnê parcelado (Efí PRO) é gerado para um cliente existente.',
                'assunto_padrao' => '📅 Seu Carnê de Mensalidades Foco 7 Foi Emitido',
                'corpo_padrao' => self::getTemplatePadraoCarneGerado(),
                'sistema' => true,
            ],
            'lembrete_vencimento' => [
                'nome' => 'Lembrete Preventivo de Vencimento',
                'descricao' => 'Disparado automaticamente pelo Cron 3 dias antes do vencimento da fatura.',
                'assunto_padrao' => '⏰ Lembrete: Sua fatura Foco 7 vence em breve',
                'corpo_padrao' => self::getTemplatePadraoLembreteVencimento(),
                'sistema' => true,
            ],
            'fatura_vencida' => [
                'nome' => 'Aviso de Fatura Vencida & Alerta de Suspensão',
                'descricao' => 'Disparado pelo Cron para faturas em atraso com instruções para regularização.',
                'assunto_padrao' => '⚠️ Importante: Fatura Vencida (#{fatura_id}) - Evite Suspensão',
                'corpo_padrao' => self::getTemplatePadraoFaturaVencida(),
                'sistema' => true,
            ],
            'lembrete_agendamento' => [
                'nome' => 'Lembrete de Agendamento (Rastreamento & Estética)',
                'descricao' => 'Disparado pelo Cron aos clientes com atendimentos agendados para a data de hoje.',
                'assunto_padrao' => '🔔 Lembrete: Seu Agendamento Foco 7 é Hoje! ({horario})',
                'corpo_padrao' => self::getTemplatePadraoLembreteAgendamento(),
                'sistema' => true,
            ],
            'recuperacao_senha' => [
                'nome' => 'Recuperação de Senha & Redefinição Segura',
                'descricao' => 'Disparado quando o usuário solicita redefinição de sua senha de acesso ao sistema.',
                'assunto_padrao' => '🔐 Redefinição de Senha de Acesso - Foco 7 Rastreamento',
                'corpo_padrao' => self::getTemplatePadraoRecuperacaoSenha(),
                'sistema' => true,
            ],

            // =========================================================================
            // RÉGUA DE COBRANÇA AUTOMATIZADA: PRÉ E PÓS-VENCIMENTO (COM ENCARGOS)
            // =========================================================================
            'cobranca_lembrete_1' => [
                'nome' => 'Régua: 1º Lembrete Preventivo (Fatura Disponível)',
                'descricao' => 'Disparado com antecedência (ex: 10 dias antes) informando que a fatura foi gerada e está aberta para pagamento.',
                'assunto_padrao' => '⏰ 1º Lembrete: Sua fatura Foco 7 está disponível (#{fatura_id})',
                'corpo_padrao' => self::getTemplatePadraoCobrancaLembrete1(),
                'sistema' => true,
            ],
            'cobranca_lembrete_2' => [
                'nome' => 'Régua: 2º Lembrete Preventivo (Vencimento Próximo)',
                'descricao' => 'Disparado quando faltam poucos dias para o vencimento (ex: 5 dias antes).',
                'assunto_padrao' => '🔔 2º Lembrete: Faltam poucos dias para o vencimento da sua fatura (#{fatura_id})',
                'corpo_padrao' => self::getTemplatePadraoCobrancaLembrete2(),
                'sistema' => true,
            ],
            'cobranca_lembrete_3' => [
                'nome' => 'Régua: 3º Lembrete Preventivo (Vence Amanhã)',
                'descricao' => 'Disparado na véspera do vencimento (1 dia antes) para evitar atrasos e encargos.',
                'assunto_padrao' => '⚡ Importante: Sua fatura Foco 7 vence amanhã (#{fatura_id})',
                'corpo_padrao' => self::getTemplatePadraoCobrancaLembrete3(),
                'sistema' => true,
            ],
            'cobranca_vencimento_hoje' => [
                'nome' => 'Régua: Cobrança no Vencimento (Vence Hoje)',
                'descricao' => 'Disparado na data exata de vencimento da fatura (D-0) alertando para o pagamento até 23:59.',
                'assunto_padrao' => '🚨 Atenção: Sua fatura Foco 7 vence HOJE (#{fatura_id})',
                'corpo_padrao' => self::getTemplatePadraoCobrancaVencimentoHoje(),
                'sistema' => true,
            ],
            'cobranca_atraso_1' => [
                'nome' => 'Régua: 1º Aviso de Atraso (Vencida Ontem)',
                'descricao' => 'Disparado logo após o vencimento (ex: 1 dia de atraso) informando a incidência de multa.',
                'assunto_padrao' => '⚠️ Aviso de Atraso: Fatura (#{fatura_id}) vencida ontem - Regularize seu acesso',
                'corpo_padrao' => self::getTemplatePadraoCobrancaAtraso1(),
                'sistema' => true,
            ],
            'cobranca_atraso_2' => [
                'nome' => 'Régua: 2º Aviso de Atraso (Multa e Juros Acumulados)',
                'descricao' => 'Disparado com atraso intermediário (ex: 5 dias) com o valor recalculado com multa e juros de mora.',
                'assunto_padrao' => '❗ 2º Aviso de Cobrança: Fatura (#{fatura_id}) em atraso com encargos',
                'corpo_padrao' => self::getTemplatePadraoCobrancaAtraso2(),
                'sistema' => true,
            ],
            'cobranca_atraso_3' => [
                'nome' => 'Régua: 3º Aviso de Atraso (Alerta de Suspensão de Sinal)',
                'descricao' => 'Disparado em atraso avançado (ex: 10 dias) com alerta formal de corte de sinal do rastreador.',
                'assunto_padrao' => '⛔ Notificação Urgente: Risco iminente de suspensão de sinal do rastreador (#{fatura_id})',
                'corpo_padrao' => self::getTemplatePadraoCobrancaAtraso3(),
                'sistema' => true,
            ],
        ];

        // Mesclar com templates personalizados cadastrados
        $customizadosRaw = CmsSetting::get('email_templates_personalizados', '[]');
        $customizados = json_decode($customizadosRaw, true);
        if (is_array($customizados)) {
            foreach ($customizados as $chave => $dados) {
                if (!isset($templates[$chave])) {
                    $dados['sistema'] = false;
                    $templates[$chave] = $dados;
                }
            }
        }

        return $templates;
    }

    /**
     * Salva ou cria um novo template customizado
     */
    public static function salvarTemplateCustomizado(string $chave, string $nome, string $descricao, string $assunto, string $corpo): bool
    {
        $chave = Str::slug($chave, '_');
        $customizadosRaw = CmsSetting::get('email_templates_personalizados', '[]');
        $customizados = json_decode($customizadosRaw, true) ?: [];
        $customizados[$chave] = [
            'nome' => $nome,
            'descricao' => $descricao,
            'assunto_padrao' => $assunto,
            'corpo_padrao' => $corpo,
            'sistema' => false,
        ];
        CmsSetting::set('email_templates_personalizados', json_encode($customizados));
        CmsSetting::set("email_assunto_{$chave}", $assunto);
        CmsSetting::set("email_corpo_{$chave}", $corpo);
        return true;
    }

    /**
     * Exclui um template customizado
     */
    public static function excluirTemplateCustomizado(string $chave): bool
    {
        $customizadosRaw = CmsSetting::get('email_templates_personalizados', '[]');
        $customizados = json_decode($customizadosRaw, true) ?: [];
        if (isset($customizados[$chave])) {
            unset($customizados[$chave]);
            CmsSetting::set('email_templates_personalizados', json_encode($customizados));
            return true;
        }
        return false;
    }

    /**
     * Obtém o assunto configurado ou padrão
     */
    public static function getAssunto(string $chave): string
    {
        $templates = self::getTemplates();
        $padrao = $templates[$chave]['assunto_padrao'] ?? 'Notificação Foco 7';
        return CmsSetting::get("email_assunto_{$chave}", $padrao);
    }

    /**
     * Obtém o corpo HTML configurado ou padrão
     */
    public static function getCorpo(string $chave): string
    {
        $templates = self::getTemplates();
        $padrao = $templates[$chave]['corpo_padrao'] ?? '<p>Olá, {cliente_nome}.</p>';
        $corpo = CmsSetting::get("email_corpo_{$chave}", $padrao);

        // Remove estilos inline de cor escura fixa (#0f172a, #1e293b, #334155) em títulos h1-h6 para contraste perfeito dark/light
        $corpo = preg_replace('/<h([1-6])([^>]*?)style="([^"]*?)color:\s*(#0f172a|#1e293b|#334155|#000000|#111111|rgb\(15,\s*23,\s*42\));?([^"]*?)"/i', '<h$1$2style="$3$5"', $corpo);

        return $corpo;
    }

    /**
     * Substitui as tags dinâmicas no texto
     */
    public static function preencherTags(string $texto, array $dados): string
    {
        $tags = [
            '{cliente_nome}' => $dados['cliente_nome'] ?? 'Cliente Foco 7',
            '{cliente_email}' => $dados['cliente_email'] ?? 'cliente@email.com',
            '{fatura_id}' => $dados['fatura_id'] ?? 'FAT-20261009-001',
            '{plano_nome}' => $dados['plano_nome'] ?? 'Plano Foco 7 Plus',
            '{servico}' => $dados['servico'] ?? 'Atendimento Especializado',
            '{horario}' => $dados['horario'] ?? '09:00',
            '{endereco}' => $dados['endereco'] ?? 'Sede Foco 7',
            '{telefone_suporte}' => $dados['telefone_suporte'] ?? '(91) 8051-0736',
            '{valor}' => $dados['valor'] ?? 'R$ 59,90',
            '{data_vencimento}' => $dados['data_vencimento'] ?? date('d/m/Y', strtotime('+3 days')),
            '{link_fatura}' => $dados['link_fatura'] ?? url('/cliente/faturas'),
            '{link_carne}' => $dados['link_carne'] ?? url('/cliente/faturas'),
            '{pix_copia_cola}' => $dados['pix_copia_cola'] ?? '00020126580014br.gov.bcb.pix...',
            '{codigo_barras}' => $dados['codigo_barras'] ?? '36490.00001 00000.000000 00000.000000 1 90000000005990',
            '{nome_empresa}' => CmsSetting::get('site_nome', 'Foco 7 - Rastreamento e Segurança'),
            '{telefone_empresa}' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            '{whatsapp_empresa}' => CmsSetting::get('contato_whatsapp_formatado', '(91) 8051-0736'),
            '{ano_atual}' => date('Y'),
        ];

        // Mapear dinamicamente todas as chaves fornecidas em $dados
        foreach ($dados as $k => $v) {
            if (is_scalar($v)) {
                $tags['{' . $k . '}'] = (string) $v;
            }
        }

        return str_replace(array_keys($tags), array_values($tags), $texto);
    }

    /**
     * Retorna o conjunto oficial de dados simulados para preview em tempo real e testes
     */
    public static function getDadosSimulados(array $custom = []): array
    {
        return array_merge([
            'cliente_nome' => 'Carlos Alberto Silva (Demonstração)',
            'cliente_email' => 'carlos.silva@exemplo.com.br',
            'fatura_id' => 'FAT-20261009-0824',
            'plano_nome' => 'FOCO 7 PLUS - Rastreamento Veicular Completo',
            'valor' => 'R$ 89,90',
            'data_vencimento' => date('d/m/Y', strtotime('+5 days')),
            'link_fatura' => url('/cliente/faturas'),
            'link_carne' => url('/cliente/faturas'),
            'pix_copia_cola' => EfiPaymentService::gerarPayloadPixBacen(CmsSetting::get('chave_pix', 'financeiro@foco7.com.br'), 89.90, 'Foco 7 - Mensalidade', 'FAT20261009'),
            'codigo_barras' => '36490.00001 00000.000000 00000.000000 1 90000000008990',
            'valor_multa' => 'R$ 1,80',
            'valor_juros' => 'R$ 0,90',
            'valor_total' => 'R$ 92,60',
            'dias_atraso' => '5',
            'dias_antecedencia' => '10',
            'nome_empresa' => CmsSetting::get('site_nome', 'Foco 7 - Rastreamento e Segurança'),
            'telefone_empresa' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'whatsapp_empresa' => CmsSetting::get('contato_whatsapp_formatado', '(91) 8051-0736'),
            'ano_atual' => date('Y'),
        ], $custom);
    }

    /**
     * Renderiza a pré-visualização em tempo real do template encapsulado no layout master responsivo
     */
    public static function renderPreview(string $chaveTemplate, ?string $corpoCustom = null, ?string $assuntoCustom = null): string
    {
        $dados = self::getDadosSimulados();
        $assunto = $assuntoCustom ?: self::getAssunto($chaveTemplate);
        $corpo = $corpoCustom !== null ? $corpoCustom : self::getCorpo($chaveTemplate);
        $dados['assunto'] = self::preencherTags($assunto, $dados);

        return self::compilarHtml($corpo, $dados);
    }

    /**
     * Envolve o conteúdo no layout HTML oficial responsivo da Foco 7
     */
    public static function compilarHtml(string $conteudo, array $dados): string
    {
        $corpoProcessado = self::preencherTags($conteudo, $dados);
        $nomeEmpresa = CmsSetting::get('site_nome', 'Foco 7 - Rastreamento e Segurança');
        $whatsapp = CmsSetting::get('contato_whatsapp_formatado', '(91) 8051-0736');
        $assunto = $dados['assunto'] ?? ($dados['titulo'] ?? $nomeEmpresa);

        // Logo oficial gravada no banco de dados (CmsSetting::get('site_logo'))
        $logoPath = CmsSetting::get('site_logo') ?: '/img/logo-foco7.png';
        if (str_starts_with($logoPath, 'http://') || str_starts_with($logoPath, 'https://')) {
            $logoUrl = $logoPath;
        } else {
            $logoUrl = url(ltrim($logoPath, '/'));
        }

        if (view()->exists('emails.layouts.master')) {
            return view('emails.layouts.master', [
                'corpo' => $corpoProcessado,
                'nomeEmpresa' => $nomeEmpresa,
                'whatsapp' => $whatsapp,
                'assunto' => $assunto,
                'logoUrl' => $logoUrl,
            ])->render();
        }

        // Fallback seguro caso a view esteja em processo de compilação
        $ano = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$assunto}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 25px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="background: linear-gradient(135deg, #0b0f19 0%, #1e293b 100%); padding: 24px 28px; text-align: center; border-bottom: 4px solid #d90000;">
                            <img src="{$logoUrl}" alt="{$nomeEmpresa}" height="46" style="height: 46px; max-height: 52px; width: auto; max-width: 220px; border: 0; display: block; margin: 0 auto;" />
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 34px 30px; font-size: 15px; line-height: 1.65; color: #334155;">
                            {$corpoProcessado}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #0f172a; padding: 22px 28px; text-align: center; border-top: 1px solid #1e293b; font-size: 12px; color: #94a3b8;">
                            <p style="margin: 0 0 6px 0; font-weight: 700; color: #f8fafc;">{$nomeEmpresa}</p>
                            <p style="margin: 0; font-size: 11px;">&copy; {$ano} Todos os direitos reservados.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Envia qualquer e-mail direto ou do sistema utilizando obrigatoriamente o layout responsivo master
     */
    public static function enviarDireto(string $destinatario, string $assunto, string $corpoHtml, array $dados = []): bool
    {
        $dadosCompletos = array_merge(self::getDadosSimulados(), $dados);
        $assuntoProcessado = self::preencherTags($assunto, $dadosCompletos);
        $dadosCompletos['assunto'] = $assuntoProcessado;
        $htmlCompleto = self::compilarHtml($corpoHtml, $dadosCompletos);

        $host = CmsSetting::get('mail_host', env('MAIL_HOST', 'mail.foco7.com.br'));
        $port = (int) CmsSetting::get('mail_port', env('MAIL_PORT', 465));
        $username = CmsSetting::get('mail_username', env('MAIL_USERNAME', 'notificacoes@foco7.com.br'));
        $password = CmsSetting::get('mail_password', env('MAIL_PASSWORD', 'Foco7#Smtp@2026!'));
        $encryption = CmsSetting::get('mail_encryption', env('MAIL_ENCRYPTION', 'ssl'));
        $fromAddress = CmsSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS', 'notificacoes@foco7.com.br'));
        $fromName = CmsSetting::get('mail_from_name', env('MAIL_FROM_NAME', 'Foco 7 Rastreamento'));

        if (empty($host) || empty($username)) {
            return false;
        }

        try {
            Config::set('mail.mailers.smtp_foco7', [
                'transport' => 'smtp',
                'host' => $host,
                'port' => $port,
                'encryption' => ($encryption === 'nenhum' || empty($encryption)) ? null : $encryption,
                'username' => $username,
                'password' => $password,
                'timeout' => 10,
            ]);

            Mail::mailer('smtp_foco7')->html($htmlCompleto, function ($message) use ($destinatario, $fromAddress, $fromName, $assuntoProcessado) {
                $message->to($destinatario)
                        ->from($fromAddress, $fromName)
                        ->subject($assuntoProcessado);
            });

            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Envia um e-mail transacional usando a configuração SMTP do sistema
     */
    public static function enviar(string $destinatario, string $chaveTemplate, array $dados): bool
    {
        $dadosCompletos = array_merge(self::getDadosSimulados(), $dados);
        $assunto = self::preencherTags(self::getAssunto($chaveTemplate), $dadosCompletos);
        $dadosCompletos['assunto'] = $assunto;
        $corpoRaw = self::getCorpo($chaveTemplate);
        $htmlCompleto = self::compilarHtml($corpoRaw, $dadosCompletos);

        // Carrega configurações SMTP do CMS
        $host = CmsSetting::get('mail_host', env('MAIL_HOST', 'mail.foco7.com.br'));
        $port = (int) CmsSetting::get('mail_port', env('MAIL_PORT', 465));
        $username = CmsSetting::get('mail_username', env('MAIL_USERNAME', 'notificacoes@foco7.com.br'));
        $password = CmsSetting::get('mail_password', env('MAIL_PASSWORD', 'Foco7#Smtp@2026!'));
        $encryption = CmsSetting::get('mail_encryption', env('MAIL_ENCRYPTION', 'ssl'));
        $fromAddress = CmsSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS', 'notificacoes@foco7.com.br'));
        $fromName = CmsSetting::get('mail_from_name', env('MAIL_FROM_NAME', 'Foco 7 Rastreamento'));

        if (empty($host) || empty($username)) {
            // Em ambiente local sem SMTP configurado, não falha
            return false;
        }

        try {
            Config::set('mail.mailers.smtp_foco7', [
                'transport' => 'smtp',
                'host' => $host,
                'port' => $port,
                'encryption' => ($encryption === 'nenhum' || empty($encryption)) ? null : $encryption,
                'username' => $username,
                'password' => $password,
                'timeout' => 10,
            ]);

            Mail::mailer('smtp_foco7')->html($htmlCompleto, function ($message) use ($destinatario, $fromAddress, $fromName, $assunto) {
                $message->to($destinatario)
                        ->from($fromAddress, $fromName)
                        ->subject($assunto);
            });

            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Dispara um e-mail de teste com dados simulados ou conteúdo personalizado de rascunho
     */
    public static function enviarTeste(string $destinatario, string $chaveTemplate, ?string $corpoCustom = null, ?string $assuntoCustom = null): array
    {
        $dadosSimulados = self::getDadosSimulados(['cliente_email' => $destinatario]);

        $assunto = $assuntoCustom ? self::preencherTags($assuntoCustom, $dadosSimulados) : self::preencherTags(self::getAssunto($chaveTemplate), $dadosSimulados);
        $dadosSimulados['assunto'] = $assunto;
        $corpoRaw = $corpoCustom !== null ? $corpoCustom : self::getCorpo($chaveTemplate);
        $htmlCompleto = self::compilarHtml($corpoRaw, $dadosSimulados);

        $host = CmsSetting::get('mail_host', env('MAIL_HOST', 'mail.foco7.com.br'));
        $port = (int) CmsSetting::get('mail_port', env('MAIL_PORT', 465));
        $username = CmsSetting::get('mail_username', env('MAIL_USERNAME', 'notificacoes@foco7.com.br'));
        $password = CmsSetting::get('mail_password', env('MAIL_PASSWORD', 'Foco7#Smtp@2026!'));
        $encryption = CmsSetting::get('mail_encryption', env('MAIL_ENCRYPTION', 'ssl'));
        $fromAddress = CmsSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS', 'notificacoes@foco7.com.br'));
        $fromName = CmsSetting::get('mail_from_name', env('MAIL_FROM_NAME', 'Foco 7 Rastreamento'));

        if (app()->environment('testing')) {
            return [
                'success' => true,
                'message' => "E-mail de demonstração disparado com sucesso para {$destinatario} (Ambiente de Testes)!",
            ];
        }

        if (empty($host) || empty($username)) {
            return [
                'success' => false,
                'message' => 'Servidor SMTP não configurado. Acesse a aba Configurações > SMTP para preencher Host, Porta e Senha antes de enviar testes.',
            ];
        }

        try {
            Config::set('mail.mailers.smtp_foco7_teste', [
                'transport' => 'smtp',
                'host' => $host,
                'port' => $port,
                'encryption' => ($encryption === 'nenhum' || empty($encryption)) ? null : $encryption,
                'username' => $username,
                'password' => $password,
                'timeout' => 10,
            ]);

            Mail::mailer('smtp_foco7_teste')->html($htmlCompleto, function ($message) use ($destinatario, $fromAddress, $fromName, $assunto) {
                $message->to($destinatario)
                        ->from($fromAddress, $fromName)
                        ->subject('[TESTE] ' . $assunto);
            });

            return [
                'success' => true,
                'message' => "E-mail de demonstração enviado com sucesso para {$destinatario}!",
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao enviar e-mail de teste: ' . $e->getMessage(),
            ];
        }
    }

    // =========================================================================
    // TEMPLATES HTML PADRÕES RICOS, BOLEADOS E ELEGANTES
    // =========================================================================

    private static function getTemplatePadraoFaturaGerada(): string
    {
        return <<<HTML
<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px;">Olá, {cliente_nome}!</h2>
<p style="margin-bottom: 20px;">Sua fatura referente ao serviço <strong>{plano_nome}</strong> foi gerada com sucesso e já está disponível para pagamento.</p>

<div style="margin: 24px 0 28px 0; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 24px 14px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Fatura Número</td>
                        <td style="padding: 6px 0; font-size: 14px; font-weight: 800; text-align: right; color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">#{fatura_id}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0 6px 0; font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Data de Vencimento</td>
                        <td style="padding: 8px 0 6px 0; font-size: 15px; font-weight: 800; text-align: right; color: #d90000;">{data_vencimento}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 24px; background-color: #ffffff; border-top: 1px solid #e2e8f0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 15px; font-weight: 800; color: #0f172a; vertical-align: middle;">
                            <span style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 2px;">Total a Liquidar</span>
                            Valor a Pagar:
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span style="font-size: 22px; font-weight: 900; color: #d90000; letter-spacing: -0.5px; line-height: 1;">{valor}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 15px 36px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase; letter-spacing: 0.5px; box-sizing: border-box; max-width: 100%;">Visualizar e Pagar Fatura &rarr;</a>
</div>

<p style="font-size: 13px; margin-bottom: 0;">Você pode efetuar o pagamento via <strong>PIX instantâneo</strong> (com baixa automática em poucos segundos), <strong>Cartão de Crédito</strong> ou <strong>Boleto Bancário</strong>.</p>
HTML;
    }

    private static function getTemplatePadraoPagamentoConfirmado(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #dcfce7; color: #15803d; font-size: 14px; font-weight: 800; padding: 6px 18px; border-radius: 50px;">PAGAMENTO CONFIRMADO</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Tudo Certo, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 24px;">Identificamos o pagamento da sua fatura <strong>#{fatura_id}</strong> no valor de <strong>{valor}</strong>. Seus serviços de monitoramento e proteção veicular estão 100% ativos.</p>

<div style="margin: 24px 0 28px 0; background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 14px rgba(22, 101, 52, 0.04);">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 24px 14px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #166534; text-transform: uppercase; letter-spacing: 0.5px;">Serviço / Plano</td>
                        <td style="padding: 6px 0; font-size: 14px; font-weight: 800; text-align: right; color: #0f172a;">{plano_nome}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0 6px 0; font-size: 13px; font-weight: 600; color: #166534; text-transform: uppercase; letter-spacing: 0.5px;">Status da Proteção</td>
                        <td style="padding: 8px 0 6px 0; font-size: 14px; font-weight: 800; text-align: right; color: #15803d;">PROTEGIDO 24H</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 24px; background-color: #ffffff; border-top: 1px solid #bbf7d0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 15px; font-weight: 800; color: #0f172a; vertical-align: middle;">
                            <span style="display: block; font-size: 11px; font-weight: 700; color: #15803d; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 2px;">Pagamento Aprovado</span>
                            Valor Liquidado:
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span style="font-size: 22px; font-weight: 900; color: #15803d; letter-spacing: -0.5px; line-height: 1;">{valor}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 24px 0 28px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background-color: #0f172a; color: #ffffff !important; text-decoration: none !important; font-size: 14px; font-weight: 700; padding: 14px 32px; border-radius: 8px; box-sizing: border-box; max-width: 100%;">Acessar Área do Cliente &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; text-align: center; margin: 0;">Agradecemos pela confiança na equipe Foco 7!</p>
HTML;
    }

    private static function getTemplatePadraoCarneGerado(): string
    {
        return <<<HTML
<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px;">Olá, {cliente_nome}!</h2>
<p style="margin-bottom: 18px;">Seu <strong>Carnê de Mensalidades Foco 7 (Efí PRO)</strong> foi gerado e já está disponível para consulta e download das lâminas de pagamento.</p>

<div style="margin: 24px 0 28px 0; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 24px 14px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Plano Contratado</td>
                        <td style="padding: 6px 0; font-size: 14px; font-weight: 800; text-align: right; color: #0f172a;">{plano_nome}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0 6px 0; font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Primeiro Vencimento</td>
                        <td style="padding: 8px 0 6px 0; font-size: 15px; font-weight: 800; text-align: right; color: #d90000;">{data_vencimento}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 24px; background-color: #ffffff; border-top: 1px solid #e2e8f0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 15px; font-weight: 800; color: #0f172a; vertical-align: middle;">
                            <span style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 2px;">Mensalidade Fixa</span>
                            Valor da Parcela:
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span style="font-size: 22px; font-weight: 900; color: #d90000; letter-spacing: -0.5px; line-height: 1;">{valor}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_carne}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 15px 36px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase; box-sizing: border-box; max-width: 100%;">Baixar Carnê Completo em PDF &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; margin-bottom: 0;">Você pode pagar cada lâmina do carnê em qualquer banco, casas lotéricas, aplicativos bancários ou via QR Code PIX impresso em cada boleto.</p>
HTML;
    }

    private static function getTemplatePadraoLembreteVencimento(): string
    {
        return <<<HTML
<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px;">Olá, {cliente_nome}!</h2>
<p style="margin-bottom: 18px;">Este é um lembrete preventivo de que sua fatura <strong>#{fatura_id}</strong> da Foco 7 está próxima do vencimento.</p>

<div style="margin: 24px 0 28px 0; background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 14px rgba(180, 83, 9, 0.04);">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 24px 14px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px;">Serviço</td>
                        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #78350f;">{plano_nome}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0 6px 0; font-size: 13px; font-weight: 600; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px;">Data de Vencimento</td>
                        <td style="padding: 8px 0 6px 0; font-size: 15px; font-weight: 800; text-align: right; color: #b45309;">{data_vencimento}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 24px; background-color: #ffffff; border-top: 1px solid #fde68a;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 15px; font-weight: 800; color: #78350f; vertical-align: middle;">
                            <span style="display: block; font-size: 11px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 2px;">Pagamento Preventivo</span>
                            Valor Sem Juros:
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span style="font-size: 22px; font-weight: 900; color: #b45309; letter-spacing: -0.5px; line-height: 1;">{valor}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background-color: #f59e0b; color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 15px 36px; border-radius: 8px; text-transform: uppercase; box-sizing: border-box; max-width: 100%;">Pagar Agora Sem Juros &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; margin-bottom: 0;">Pagar em dia garante a continuidade ininterrupta da transmissão do sinal de rastreamento do seu veículo e o suporte da Ronda Tática 24 Horas.</p>
HTML;
    }

    private static function getTemplatePadraoFaturaVencida(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #fee2e2; color: #b91c1c; font-size: 14px; font-weight: 800; padding: 6px 18px; border-radius: 50px;">FATURA EM ATRASO</span>
</div>

<h2 style="color: #dc2626; font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Atenção, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 24px;">Constatamos que a fatura <strong>#{fatura_id}</strong> no valor de <strong>{valor}</strong> venceu em <strong>{data_vencimento}</strong> e ainda não teve o pagamento compensado.</p>

<div style="margin: 24px 0 28px 0; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 14px rgba(185, 28, 28, 0.04);">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 24px 14px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #991b1b; text-transform: uppercase; letter-spacing: 0.5px;">Identificador Fatura</td>
                        <td style="padding: 6px 0; font-size: 14px; font-weight: 800; text-align: right; color: #7f1d1d; font-family: monospace, sans-serif;">#{fatura_id}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0 6px 0; font-size: 13px; font-weight: 600; color: #991b1b; text-transform: uppercase; letter-spacing: 0.5px;">Vencimento Original</td>
                        <td style="padding: 8px 0 6px 0; font-size: 15px; font-weight: 800; text-align: right; color: #dc2626;">{data_vencimento}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 24px; background-color: #ffffff; border-top: 1px solid #fecaca;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 15px; font-weight: 800; color: #7f1d1d; vertical-align: middle;">
                            <span style="display: block; font-size: 11px; font-weight: 700; color: #991b1b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 2px;">Regularização Pendente</span>
                            Total a Regularizar:
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span style="font-size: 22px; font-weight: 900; color: #dc2626; letter-spacing: -0.5px; line-height: 1;">{valor}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #991b1b 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 15px 36px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase; box-sizing: border-box; max-width: 100%;">Regularizar Fatura Imediatamente &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; margin-bottom: 0; text-align: center;">Para evitar o bloqueio temporário do aplicativo e a suspensão da cobertura da Ronda Tática, regularize sua pendência o quanto antes.</p>
HTML;
    }

    private static function getTemplatePadraoLembreteAgendamento(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #dbeafe; color: #1d4ed8; font-size: 14px; font-weight: 800; padding: 6px 18px; border-radius: 50px;">AGENDAMENTO HOJE</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Olá, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 24px;">Lembramos que o seu atendimento de <strong>{servico}</strong> está agendado para <strong>hoje às {horario}</strong> com a equipe especializada Foco 7.</p>

<div style="margin: 24px 0 28px 0; background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 14px rgba(29, 78, 216, 0.04);">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 24px 14px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">Serviço / Procedimento</td>
                        <td style="padding: 6px 0; font-size: 14px; font-weight: 800; text-align: right; color: #0f172a;">{servico}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0 6px 0; font-size: 13px; font-weight: 600; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">Horário Marcado</td>
                        <td style="padding: 8px 0 6px 0; font-size: 15px; font-weight: 800; text-align: right; color: #1d4ed8;">{horario}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0 6px 0; font-size: 13px; font-weight: 600; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">Local / Endereço</td>
                        <td style="padding: 8px 0 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #334155;">{endereco}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 24px 0 28px 0; width: 100%;">
    <a href="tel:{telefone_empresa}" class="btn-action" style="display: inline-block; background-color: #0f172a; color: #ffffff !important; text-decoration: none !important; font-size: 14px; font-weight: 700; padding: 14px 32px; border-radius: 8px; box-sizing: border-box; max-width: 100%;">Central de Atendimento: {telefone_empresa}</a>
</div>

<p style="font-size: 13px; color: #64748b; text-align: center; margin: 0;">Caso precise reagendar ou tenha dúvidas, por favor entre em contato com antecedência.</p>
HTML;
    }

    private static function getTemplatePadraoRecuperacaoSenha(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #fee2e2; color: #b91c1c; font-size: 14px; font-weight: 800; padding: 6px 18px; border-radius: 50px;">SEGURANÇA DA CONTA</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Olá, {nome}!</h2>
<p style="text-align: center; margin-bottom: 24px; color: #475569;">Recebemos uma solicitação para redefinir a sua senha de acesso ao portal <strong>Foco 7 Rastreamento</strong>.</p>

<div style="margin: 24px 0; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; text-align: center;">
    <p style="font-size: 13px; color: #64748b; margin: 0 0 10px 0;">Para criar uma nova senha com total segurança, clique no botão abaixo:</p>
    <div class="btn-wrapper" style="text-align: center; margin: 16px 0; width: 100%;">
        <a href="{link_redefinir}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #991b1b 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 14px 36px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase; box-sizing: border-box; max-width: 100%;">Redefinir Minha Senha Agora &rarr;</a>
    </div>
    <p style="font-size: 12px; color: #94a3b8; margin: 8px 0 0 0;">Este link é intransferível e expira em <strong>60 minutos</strong> por motivos de segurança.</p>
</div>

<p style="font-size: 12px; color: #94a3b8; text-align: center; margin-bottom: 0;">Se você não solicitou a alteração da sua senha, desconsidere este e-mail. Nenhuma ação será tomada e sua conta permanece totalmente protegida.</p>
HTML;
    }

    // =========================================================================
    // MÉTODOS HTML PADRÕES: RÉGUA DE COBRANÇA (PRÉ E PÓS-VENCIMENTO)
    // =========================================================================

    private static function getTemplatePadraoCobrancaLembrete1(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #dbeafe; color: #1d4ed8; font-size: 13px; font-weight: 800; padding: 6px 18px; border-radius: 50px; text-transform: uppercase;">1º LEMBRETE PREVENTIVO</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Olá, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 20px;">Informamos com antecedência que a fatura referente à sua mensalidade <strong>{plano_nome}</strong> já foi emitida e está disponível para pagamento.</p>

<div style="margin: 24px 0 28px 0; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 24px 14px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase;">Fatura Número</td>
                        <td style="padding: 6px 0; font-size: 14px; font-weight: 800; text-align: right; color: #0f172a;">#{fatura_id}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0 6px 0; font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase;">Data de Vencimento</td>
                        <td style="padding: 8px 0 6px 0; font-size: 15px; font-weight: 800; text-align: right; color: #d90000;">{data_vencimento}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 24px; background-color: #ffffff; border-top: 1px solid #e2e8f0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 15px; font-weight: 800; color: #0f172a; vertical-align: middle;">Valor da Mensalidade:</td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span style="font-size: 22px; font-weight: 900; color: #d90000;">{valor}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 15px 36px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase;">Visualizar Fatura & Pagar Agora &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; text-align: center; margin-bottom: 0;">Você pode pagar com total comodidade via <strong>PIX</strong> (baixa instantânea em segundos), Cartão de Crédito ou Boleto.</p>
HTML;
    }

    private static function getTemplatePadraoCobrancaLembrete2(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #fef3c7; color: #b45309; font-size: 13px; font-weight: 800; padding: 6px 18px; border-radius: 50px; text-transform: uppercase;">2º LEMBRETE PREVENTIVO</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Olá, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 20px;">Lembramos que a sua fatura da <strong>Foco 7 Rastreamento</strong> (#{fatura_id}) vencerá em breve, no dia <strong>{data_vencimento}</strong>.</p>

<div style="margin: 24px 0 28px 0; background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 16px; overflow: hidden;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 13px; font-weight: 600; color: #92400e; text-transform: uppercase;">Plano / Serviço:</td>
                        <td style="font-size: 14px; font-weight: 800; text-align: right; color: #0f172a;">{plano_nome}</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 8px; font-size: 13px; font-weight: 600; color: #92400e; text-transform: uppercase;">Vencimento:</td>
                        <td style="padding-top: 8px; font-size: 15px; font-weight: 800; text-align: right; color: #b45309;">{data_vencimento}</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 8px; font-size: 15px; font-weight: 800; color: #0f172a;">Valor:</td>
                        <td style="padding-top: 8px; font-size: 22px; font-weight: 900; text-align: right; color: #d90000;">{valor}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 15px 36px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase;">Efetuar Pagamento &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; text-align: center; margin-bottom: 0;">Antecipe seu pagamento para evitar encargos e manter seu veículo sempre protegido com central 24h.</p>
HTML;
    }

    private static function getTemplatePadraoCobrancaLembrete3(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #ffedd5; color: #c2410c; font-size: 13px; font-weight: 800; padding: 6px 18px; border-radius: 50px; text-transform: uppercase;">VENCE AMANHÃ ⚡</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Olá, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 20px;">Sua fatura <strong>#{fatura_id}</strong> referente à proteção do seu veículo vence <strong>amanhã ({data_vencimento})</strong>.</p>

<div style="margin: 24px 0 28px 0; background-color: #fff7ed; border: 1px solid #fed7aa; border-radius: 16px; padding: 20px 24px; text-align: center;">
    <p style="font-size: 13px; font-weight: 700; color: #9a3412; text-transform: uppercase; margin: 0 0 6px 0;">Valor a liquidar até amanhã</p>
    <div style="font-size: 28px; font-weight: 900; color: #d90000; margin-bottom: 12px;">{valor}</div>
    <p style="font-size: 13px; color: #475569; margin: 0;">Pague via PIX e garanta baixa imediata sem risco de cobrança de encargos de mora.</p>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 15px 36px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase;">Pagar Agora via PIX &rarr;</a>
</div>
HTML;
    }

    private static function getTemplatePadraoCobrancaVencimentoHoje(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #fee2e2; color: #b91c1c; font-size: 13px; font-weight: 800; padding: 6px 18px; border-radius: 50px; text-transform: uppercase;">VENCE HOJE 🚨</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Atenção, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 20px;">Sua fatura <strong>#{fatura_id}</strong> ({plano_nome}) vence <strong>hoje, dia {data_vencimento}</strong>.</p>

<div style="margin: 24px 0 28px 0; background-color: #fef2f2; border: 1.5px solid #fca5a5; border-radius: 16px; padding: 22px 24px; text-align: center;">
    <p style="font-size: 13px; font-weight: 700; color: #991b1b; text-transform: uppercase; margin: 0 0 4px 0;">Valor da Fatura com Vencimento Hoje</p>
    <div style="font-size: 30px; font-weight: 900; color: #d90000; margin-bottom: 10px;">{valor}</div>
    <p style="font-size: 13px; color: #7f1d1d; margin: 0;">Efetue o pagamento até às 23:59 para evitar a incidência de multa por atraso ({multa_percentual}%) e juros de mora.</p>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #990000 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 16px 38px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.4); text-transform: uppercase;">Pagar Fatura Hoje &rarr;</a>
</div>

<p style="font-size: 12px; color: #64748b; text-align: center;">Caso já tenha efetuado o pagamento via PIX nos últimos minutos, por favor desconsidere este aviso.</p>
HTML;
    }

    private static function getTemplatePadraoCobrancaAtraso1(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #fee2e2; color: #b91c1c; font-size: 13px; font-weight: 800; padding: 6px 18px; border-radius: 50px; text-transform: uppercase;">FATURA VENCIDA (1º AVISO)</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Prezado(a) {cliente_nome},</h2>
<p style="text-align: center; margin-bottom: 20px;">Identificamos que a fatura <strong>#{fatura_id}</strong>, com vencimento em <strong>{data_vencimento}</strong>, ainda não teve o pagamento confirmado em nosso sistema.</p>

<div style="margin: 24px 0 28px 0; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 18px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 13px; color: #64748b;">Valor Original:</td>
                        <td style="font-size: 14px; font-weight: 700; text-align: right; color: #334155;">{valor_original}</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 6px; font-size: 13px; color: #b91c1c;">Multa por Atraso ({multa_percentual}%):</td>
                        <td style="padding-top: 6px; font-size: 14px; font-weight: 700; text-align: right; color: #b91c1c;">+ {multa}</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 6px; font-size: 13px; color: #b91c1c;">Juros de Mora Acumulados:</td>
                        <td style="padding-top: 6px; font-size: 14px; font-weight: 700; text-align: right; color: #b91c1c;">+ {juros}</td>
                    </tr>
                    <tr style="border-top: 1px dashed #cbd5e1;">
                        <td style="padding-top: 12px; font-size: 15px; font-weight: 800; color: #0f172a;">Total Atualizado para Quitação:</td>
                        <td style="padding-top: 12px; font-size: 22px; font-weight: 900; text-align: right; color: #d90000;">{valor_atualizado}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 15px 36px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase;">Regularizar Fatura via PIX &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; text-align: center; margin-bottom: 0;">Regularize hoje mesmo para evitar o bloqueio temporário do aplicativo e dos serviços de telemetria.</p>
HTML;
    }

    private static function getTemplatePadraoCobrancaAtraso2(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #fee2e2; color: #991b1b; font-size: 13px; font-weight: 800; padding: 6px 18px; border-radius: 50px; text-transform: uppercase;">2º AVISO DE COBRANÇA (EM ATRASO)</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Olá, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 20px;">Consta em nosso sistema que a fatura <strong>#{fatura_id}</strong> encontra-se com <strong>{dias_atraso} dias de atraso</strong> (vencimento original em {data_vencimento}).</p>

<div style="margin: 24px 0 28px 0; background-color: #fef2f2; border: 1.5px solid #f87171; border-radius: 16px; padding: 20px 24px;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="font-size: 13px; color: #4b5563;">Valor Original:</td>
            <td style="font-size: 14px; font-weight: 700; text-align: right; color: #111827;">{valor_original}</td>
        </tr>
        <tr>
            <td style="padding-top: 6px; font-size: 13px; color: #991b1b;">Multa por Atraso ({multa_percentual}%):</td>
            <td style="padding-top: 6px; font-size: 14px; font-weight: 700; text-align: right; color: #991b1b;">+ {multa}</td>
        </tr>
        <tr>
            <td style="padding-top: 6px; font-size: 13px; color: #991b1b;">Juros de Mora ({dias_atraso} dias):</td>
            <td style="padding-top: 6px; font-size: 14px; font-weight: 700; text-align: right; color: #991b1b;">+ {juros}</td>
        </tr>
        <tr style="border-top: 1.5px solid #fca5a5;">
            <td style="padding-top: 12px; font-size: 15px; font-weight: 800; color: #0f172a;">Total Atualizado:</td>
            <td style="padding-top: 12px; font-size: 24px; font-weight: 900; text-align: right; color: #d90000;">{valor_atualizado}</td>
        </tr>
    </table>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #7f1d1d 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 16px 38px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.4); text-transform: uppercase;">Quitar Débito Agora &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; text-align: center; margin-bottom: 0;">Para dúvidas ou negociar o pagamento, nossa equipe está disponível no WhatsApp {whatsapp_empresa}.</p>
HTML;
    }

    private static function getTemplatePadraoCobrancaAtraso3(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #450a0a; color: #ffffff; font-size: 13px; font-weight: 800; padding: 6px 18px; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.5px;">ALERTA DE SUSPENSÃO DE SINAL ⛔</span>
</div>

<h2 style="font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center; color: #991b1b;">Notificação Formal de Inadimplência</h2>
<p style="text-align: center; margin-bottom: 20px;">Prezado(a) <strong>{cliente_nome}</strong>, informamos que a fatura <strong>#{fatura_id}</strong> atingiu <strong>{dias_atraso} dias de inadimplência</strong>.</p>

<div style="margin: 24px 0 28px 0; background-color: #fff1f2; border: 2px solid #e11d48; border-radius: 16px; padding: 22px 24px;">
    <p style="font-size: 14px; font-weight: 800; color: #9f1239; margin: 0 0 8px 0; text-transform: uppercase;">Consequências Iminentes:</p>
    <ul style="font-size: 13px; color: #881337; margin: 0 0 14px 0; padding-left: 20px; line-height: 1.6;">
        <li>Bloqueio imediato do sinal de telemetria e rastreamento veicular;</li>
        <li>Suspensão do atendimento da Ronda Tática Presencial 24h;</li>
        <li>Inclusão do CPF/CNPJ nos órgãos de proteção ao crédito (SPC/Serasa).</li>
    </ul>
    <div style="border-top: 1px solid #fecdd3; padding-top: 12px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 14px; font-weight: 800; color: #0f172a;">Valor Total com Encargos:</span>
        <span style="font-size: 24px; font-weight: 900; color: #be123c;">{valor_atualizado}</span>
    </div>
</div>

<div class="btn-wrapper" style="text-align: center; margin: 26px 0 30px 0; width: 100%;">
    <a href="{link_fatura}" class="btn-action" style="display: inline-block; background: linear-gradient(135deg, #be123c 0%, #881337 100%); color: #ffffff !important; text-decoration: none !important; font-size: 15px; font-weight: 800; padding: 16px 38px; border-radius: 8px; box-shadow: 0 4px 15px rgba(190, 18, 60, 0.4); text-transform: uppercase;">Evitar Suspensão - Pagar via PIX &rarr;</a>
</div>

<p style="font-size: 12px; color: #64748b; text-align: center; margin-bottom: 0;">Evite prejuízos e mantenha a proteção do seu patrimônio ativa.</p>
HTML;
    }
}
