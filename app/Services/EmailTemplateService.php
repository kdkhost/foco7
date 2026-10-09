<?php

namespace App\Services;

use App\Models\CmsSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Exception;

class EmailTemplateService
{
    /**
     * Retorna a lista de chaves e definições dos templates de e-mail
     */
    public static function getTemplates(): array
    {
        return [
            'fatura_gerada' => [
                'nome' => 'Nova Fatura Gerada',
                'descricao' => 'Disparado quando uma nova cobrança de adesão ou mensalidade é gerada para o cliente.',
                'assunto_padrao' => '📄 Fatura Gerada (#{fatura_id}) - Foco 7 Rastreamento',
                'corpo_padrao' => self::getTemplatePadraoFaturaGerada(),
            ],
            'pagamento_confirmado' => [
                'nome' => 'Confirmação de Pagamento Aprovado',
                'descricao' => 'Disparado imediatamente após a confirmação do pagamento via PIX, Cartão ou Boleto.',
                'assunto_padrao' => '✅ Pagamento Confirmado com Sucesso! - Foco 7 Rastreamento',
                'corpo_padrao' => self::getTemplatePadraoPagamentoConfirmado(),
            ],
            'carne_gerado' => [
                'nome' => 'Carnê de Mensalidades Emitido',
                'descricao' => 'Disparado quando um carnê parcelado (Efí PRO) é gerado para um cliente existente.',
                'assunto_padrao' => '📅 Seu Carnê de Mensalidades Foco 7 Foi Emitido',
                'corpo_padrao' => self::getTemplatePadraoCarneGerado(),
            ],
            'lembrete_vencimento' => [
                'nome' => 'Lembrete Preventivo de Vencimento',
                'descricao' => 'Disparado automaticamente pelo Cron 3 dias antes do vencimento da fatura.',
                'assunto_padrao' => '⏰ Lembrete: Sua fatura Foco 7 vence em breve',
                'corpo_padrao' => self::getTemplatePadraoLembreteVencimento(),
            ],
            'fatura_vencida' => [
                'nome' => 'Aviso de Fatura Vencida & Alerta de Suspensão',
                'descricao' => 'Disparado pelo Cron para faturas em atraso com instruções para regularização.',
                'assunto_padrao' => '⚠️ Importante: Fatura Vencida (#{fatura_id}) - Evite Suspensão',
                'corpo_padrao' => self::getTemplatePadraoFaturaVencida(),
            ],
        ];
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
        return CmsSetting::get("email_corpo_{$chave}", $padrao);
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

        return str_replace(array_keys($tags), array_values($tags), $texto);
    }

    /**
     * Envolve o conteúdo em um layout HTML elegante com a identidade visual da Foco 7
     */
    public static function compilarHtml(string $conteudo, array $dados): string
    {
        $corpoProcessado = self::preencherTags($conteudo, $dados);
        $nomeEmpresa = CmsSetting::get('site_nome', 'Foco 7 - Rastreamento e Segurança');
        $whatsapp = CmsSetting::get('contato_whatsapp_formatado', '(91) 8051-0736');
        $ano = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$nomeEmpresa}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    <!-- CABEÇALHO COM IDENTIDADE FOCO 7 -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0b0f19 0%, #1e293b 100%); padding: 28px 32px; text-align: center; border-bottom: 3px solid #d90000;">
                            <div style="font-size: 26px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px;">
                                <span style="color: #d90000;">FOCO</span> 7
                            </div>
                            <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; letter-spacing: 2px; margin-top: 4px;">
                                Rastreamento &amp; Segurança Veicular
                            </div>
                        </td>
                    </tr>
                    <!-- CORPO DA MENSAGEM -->
                    <tr>
                        <td style="padding: 36px 32px; font-size: 15px; line-height: 1.6; color: #334155;">
                            {$corpoProcessado}
                        </td>
                    </tr>
                    <!-- RODAPÉ OFICIAL -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 32px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b;">
                            <p style="margin: 0 0 6px 0; font-weight: 700; color: #334155;">{$nomeEmpresa}</p>
                            <p style="margin: 0 0 8px 0;">Central 24h &bull; WhatsApp de Suporte: <strong>{$whatsapp}</strong></p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">&copy; {$ano} Todos os direitos reservados. Mensagem automática do portal de segurança.</p>
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
     * Envia um e-mail transacional usando a configuração SMTP do sistema
     */
    public static function enviar(string $destinatario, string $chaveTemplate, array $dados): bool
    {
        $assunto = self::preencherTags(self::getAssunto($chaveTemplate), $dados);
        $corpoRaw = self::getCorpo($chaveTemplate);
        $htmlCompleto = self::compilarHtml($corpoRaw, $dados);

        // Carrega configurações SMTP do CMS
        $host = CmsSetting::get('mail_host', env('MAIL_HOST', 'smtp.foco7.com.br'));
        $port = (int) CmsSetting::get('mail_port', env('MAIL_PORT', 587));
        $username = CmsSetting::get('mail_username', env('MAIL_USERNAME', 'contato@foco7.com.br'));
        $password = CmsSetting::get('mail_password', env('MAIL_PASSWORD', ''));
        $encryption = CmsSetting::get('mail_encryption', env('MAIL_ENCRYPTION', 'tls'));
        $fromAddress = CmsSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS', 'financeiro@foco7.com.br'));
        $fromName = CmsSetting::get('mail_from_name', env('MAIL_FROM_NAME', 'Foco 7 Financeiro'));

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
     * Dispara um e-mail de teste com dados simulados
     */
    public static function enviarTeste(string $destinatario, string $chaveTemplate): array
    {
        $dadosSimulados = [
            'cliente_nome' => 'Marcelo Silva (Demonstração)',
            'cliente_email' => $destinatario,
            'fatura_id' => 'FAT-20261009-TESTE',
            'plano_nome' => 'FOCO7 PLUS - Rastreamento Completo',
            'valor' => 'R$ 59,90',
            'data_vencimento' => date('d/m/Y', strtotime('+3 days')),
            'link_fatura' => url('/cliente/faturas'),
            'link_carne' => url('/cliente/faturas'),
            'pix_copia_cola' => '00020126580014br.gov.bcb.pix0136123e4567-e89b-12d3-a456-426614174000520400005303986540559.905802BR5915FOCO 7 SISTEMAS6009BELEM62070503FAT6304ABCD',
            'codigo_barras' => '36490.00001 00000.000000 00000.000000 1 90000000005990',
        ];

        $assunto = self::preencherTags(self::getAssunto($chaveTemplate), $dadosSimulados);
        $corpoRaw = self::getCorpo($chaveTemplate);
        $htmlCompleto = self::compilarHtml($corpoRaw, $dadosSimulados);

        $host = CmsSetting::get('mail_host', env('MAIL_HOST', ''));
        $port = (int) CmsSetting::get('mail_port', env('MAIL_PORT', 587));
        $username = CmsSetting::get('mail_username', env('MAIL_USERNAME', ''));
        $password = CmsSetting::get('mail_password', env('MAIL_PASSWORD', ''));
        $encryption = CmsSetting::get('mail_encryption', env('MAIL_ENCRYPTION', 'tls'));
        $fromAddress = CmsSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS', 'teste@foco7.com.br'));
        $fromName = CmsSetting::get('mail_from_name', env('MAIL_FROM_NAME', 'Foco 7 Notificações'));

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
                'message' => 'Erro ao enviar e-mail via SMTP: ' . $e->getMessage(),
            ];
        }
    }

    // =========================================================================
    // TEMPLATES HTML PADRÕES RICOS E ELEGANTES
    // =========================================================================

    private static function getTemplatePadraoFaturaGerada(): string
    {
        return <<<HTML
<h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px;">Olá, {cliente_nome}!</h2>
<p style="margin-bottom: 20px;">Sua fatura referente ao serviço <strong>{plano_nome}</strong> foi gerada com sucesso e já está disponível para pagamento.</p>

<table width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #64748b;">Fatura Número:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #0f172a;">#{fatura_id}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #64748b;">Data de Vencimento:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #d90000;">{data_vencimento}</td>
    </tr>
    <tr>
        <td style="padding: 8px 0 0 0; font-size: 16px; font-weight: 800; color: #0f172a; border-top: 1px dashed #cbd5e1;">Valor a Pagar:</td>
        <td style="padding: 8px 0 0 0; font-size: 20px; font-weight: 900; text-align: right; color: #d90000; border-top: 1px dashed #cbd5e1;">{valor}</td>
    </tr>
</table>

<div style="text-align: center; margin-bottom: 28px;">
    <a href="{link_fatura}" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; padding: 14px 34px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase; letter-spacing: 0.5px;">Visualizar e Pagar Fatura &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; margin-bottom: 0;">Você pode efetuar o pagamento via <strong>PIX instantâneo</strong> (com baixa automática em poucos segundos), <strong>Cartão de Crédito</strong> ou <strong>Boleto Bancário</strong>.</p>
HTML;
    }

    private static function getTemplatePadraoPagamentoConfirmado(): string
    {
        return <<<HTML
<div style="text-align: center; margin-bottom: 20px;">
    <span style="display: inline-block; background-color: #dcfce7; color: #15803d; font-size: 14px; font-weight: 800; padding: 6px 18px; border-radius: 50px;">PAGAMENTO CONFIRMADO</span>
</div>

<h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Tudo Certo, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 24px;">Identificamos o pagamento da sua fatura <strong>#{fatura_id}</strong> no valor de <strong>{valor}</strong>. Seus serviços de monitoramento e proteção veicular estão 100% ativos.</p>

<table width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #64748b;">Serviço / Plano:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #0f172a;">{plano_nome}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #64748b;">Valor Liquidado:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #15803d;">{valor}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #64748b;">Status da Conta:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #15803d;">PROTEGIDO 24H</td>
    </tr>
</table>

<div style="text-align: center; margin-bottom: 24px;">
    <a href="{link_fatura}" style="display: inline-block; background-color: #0f172a; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 12px 28px; border-radius: 8px;">Acessar Área do Cliente &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; text-align: center; margin: 0;">Agradecemos pela confiança na equipe Foco 7!</p>
HTML;
    }

    private static function getTemplatePadraoCarneGerado(): string
    {
        return <<<HTML
<h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px;">Olá, {cliente_nome}!</h2>
<p style="margin-bottom: 18px;">Seu <strong>Carnê de Mensalidades Foco 7 (Efí PRO)</strong> foi gerado e já está disponível para consulta e download das lâminas de pagamento.</p>

<table width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #64748b;">Plano Contratado:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #0f172a;">{plano_nome}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #64748b;">Primeiro Vencimento:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #d90000;">{data_vencimento}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #64748b;">Valor da Parcela:</td>
        <td style="padding: 6px 0; font-size: 16px; font-weight: 800; text-align: right; color: #d90000;">{valor}</td>
    </tr>
</table>

<div style="text-align: center; margin-bottom: 28px;">
    <a href="{link_carne}" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; padding: 14px 34px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase;">Baixar Carnê Completo em PDF &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; margin-bottom: 0;">Você pode pagar cada lâmina do carnê em qualquer banco, casas lotéricas, aplicativos bancários ou via QR Code PIX impresso em cada boleto.</p>
HTML;
    }

    private static function getTemplatePadraoLembreteVencimento(): string
    {
        return <<<HTML
<h2 style="color: #0f172a; font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px;">Olá, {cliente_nome}!</h2>
<p style="margin-bottom: 18px;">Este é um lembrete preventivo de que sua fatura <strong>#{fatura_id}</strong> da Foco 7 está próxima do vencimento.</p>

<table width="100%" style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #92400e;">Serviço:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #78350f;">{plano_nome}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #92400e;">Data de Vencimento:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 800; text-align: right; color: #b45309;">{data_vencimento}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #92400e;">Valor:</td>
        <td style="padding: 6px 0; font-size: 18px; font-weight: 900; text-align: right; color: #b45309;">{valor}</td>
    </tr>
</table>

<div style="text-align: center; margin-bottom: 28px;">
    <a href="{link_fatura}" style="display: inline-block; background-color: #f59e0b; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; padding: 14px 34px; border-radius: 8px; text-transform: uppercase;">Pagar Agora Sem Juros &rarr;</a>
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

<h2 style="color: #991b1b; font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 12px; text-align: center;">Atenção, {cliente_nome}!</h2>
<p style="text-align: center; margin-bottom: 24px;">Constatamos que a fatura <strong>#{fatura_id}</strong> no valor de <strong>{valor}</strong> venceu em <strong>{data_vencimento}</strong> e ainda não teve o pagamento compensado.</p>

<table width="100%" style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #991b1b;">Fatura:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #7f1d1d;">#{fatura_id}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #991b1b;">Vencimento Original:</td>
        <td style="padding: 6px 0; font-size: 14px; font-weight: 700; text-align: right; color: #dc2626;">{data_vencimento}</td>
    </tr>
    <tr>
        <td style="padding: 6px 0; font-size: 14px; color: #991b1b;">Valor com Encargos:</td>
        <td style="padding: 6px 0; font-size: 18px; font-weight: 900; text-align: right; color: #dc2626;">{valor}</td>
    </tr>
</table>

<div style="text-align: center; margin-bottom: 28px;">
    <a href="{link_fatura}" style="display: inline-block; background: linear-gradient(135deg, #d90000 0%, #991b1b 100%); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; padding: 14px 34px; border-radius: 8px; box-shadow: 0 4px 15px rgba(217, 0, 0, 0.35); text-transform: uppercase;">Regularizar Fatura Imediatamente &rarr;</a>
</div>

<p style="font-size: 13px; color: #64748b; margin-bottom: 0; text-align: center;">Para evitar o bloqueio temporário do aplicativo e a suspensão da cobertura da Ronda Tática, regularize sua pendência o quanto antes.</p>
HTML;
    }
}
