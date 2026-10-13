<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Exception;

class ConfiguracoesController extends Controller
{
    /**
     * Exibe o painel de configurações do sistema e SMTP
     */
    public function index()
    {
        $configs = [
            // Dados da Empresa
            'site_nome' => CmsSetting::get('site_nome', 'Foco 7 - Rastreamento e Segurança'),
            'razao_social' => CmsSetting::get('razao_social', 'Foco 7 Monitoramento e Rastreamento LTDA'),
            'cnpj' => CmsSetting::get('cnpj', '00.000.000/0001-00'),
            'site_slogan' => CmsSetting::get('site_slogan', 'Proteger hoje para que você viva o amanhã com tranquilidade'),
            'contato_email' => CmsSetting::get('contato_email', 'contato@foco7.com.br'),
            'contato_telefone' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'contato_whatsapp' => CmsSetting::get('contato_whatsapp', '559180510736'),
            'contato_whatsapp_formatado' => CmsSetting::get('contato_whatsapp_formatado', '(91) 8051-0736'),
            'endereco_logradouro' => CmsSetting::get('endereco_logradouro', 'Av. Central'),
            'endereco_numero' => CmsSetting::get('endereco_numero', '700'),
            'endereco_bairro' => CmsSetting::get('endereco_bairro', 'Marco'),
            'endereco_cidade' => CmsSetting::get('endereco_cidade', 'Belém'),
            'endereco_estado' => CmsSetting::get('endereco_estado', 'PA'),
            'endereco_cep' => CmsSetting::get('endereco_cep', '66000-000'),
            'horario_atendimento' => CmsSetting::get('horario_atendimento', 'Central 24 Horas / Comercial Seg a Sex 08h às 18h'),

            // Identidade Visual (Fundo Escuro e Fundo Claro)
            'site_logo' => CmsSetting::getLogo('escuro'),
            'site_logo_clara' => CmsSetting::getLogo('claro'),
            'site_favicon' => CmsSetting::get('site_favicon', '/img/favicon.png'),

            // Servidor de E-mail SMTP
            'mail_mailer' => CmsSetting::get('mail_mailer', 'smtp'),
            'mail_host' => CmsSetting::get('mail_host', 'smtp.foco7.com.br'),
            'mail_port' => CmsSetting::get('mail_port', '465'),
            'mail_encryption' => CmsSetting::get('mail_encryption', 'ssl'),
            'mail_username' => CmsSetting::get('mail_username', 'noreply@foco7.com.br'),
            'mail_password' => CmsSetting::get('mail_password', ''),
            'mail_from_address' => CmsSetting::get('mail_from_address', 'contato@foco7.com.br'),
            'mail_from_name' => CmsSetting::get('mail_from_name', 'Foco 7 Rastreamento'),

            // Redes Sociais
            'social_instagram' => CmsSetting::get('social_instagram', 'https://instagram.com/foco7rastreamento'),
            'social_facebook' => CmsSetting::get('social_facebook', 'https://facebook.com/foco7rastreamento'),
            'social_linkedin' => CmsSetting::get('social_linkedin', ''),
            'social_youtube' => CmsSetting::get('social_youtube', ''),

            // Google reCAPTCHA v3 (Anti-Spam & Blindagem)
            'recaptcha_site_key' => CmsSetting::get('recaptcha_site_key', ''),
            'recaptcha_secret_key' => CmsSetting::get('recaptcha_secret_key', ''),
            'recaptcha_ativo' => CmsSetting::get('recaptcha_ativo', '0'),

            // Bloqueio Geográfico & Proteção Regional (Apenas Américas)
            'geoblock_ativo' => CmsSetting::get('geoblock_ativo', '1'),
            'geoblock_regioes' => CmsSetting::get('geoblock_regioes', 'Americas'),
            'geoblock_ips_liberados' => CmsSetting::get('geoblock_ips_liberados', '127.0.0.1,::1'),

            // Segurança de Sessão & Desconexão por Inatividade
            'sessao_inatividade_minutos' => (int) CmsSetting::get('sessao_inatividade_minutos', 30),

            // Notificações Push & Canais (OneSignal e E-mail de Alerta)
            'onesignal_ativo' => CmsSetting::get('onesignal_ativo', '0') == '1',
            'onesignal_app_id' => CmsSetting::get('onesignal_app_id', ''),
            'onesignal_rest_api_key' => CmsSetting::get('onesignal_rest_api_key', ''),
            'onesignal_imagem_padrao' => CmsSetting::get('onesignal_imagem_padrao', asset('img/og-foco7.jpg')),
            'admin_email_notificacoes' => CmsSetting::get('admin_email_notificacoes') ?: CmsSetting::get('contato_email', 'contato@foco7.com.br'),
            'notificar_email_contatos' => CmsSetting::get('notificar_email_contatos', '1') == '1',
            'notificar_email_agendamentos' => CmsSetting::get('notificar_email_agendamentos', '1') == '1',
            'notificacoes_push_ativas' => CmsSetting::get('notificacoes_push_ativas', '1') == '1',

            // Desenvolvedor do Sistema & Suporte Técnico
            'desenvolvedor_nome' => CmsSetting::get('desenvolvedor_nome', 'Marcelo Brad'),
            'desenvolvedor_link' => CmsSetting::get('desenvolvedor_link', 'https://wa.me/5591980510736'),
            'desenvolvedor_whatsapp' => CmsSetting::get('desenvolvedor_whatsapp', '5591980510736'),
        ];

        return view('admin.configuracoes.index', compact('configs'));
    }

    /**
     * Salva as configurações gerais e identidade via AJAX
     */
    public function salvar(Request $request)
    {
        // 1. Dados da Empresa e Segurança
        $camposTexto = [
            'site_nome', 'razao_social', 'cnpj', 'site_slogan',
            'contato_email', 'contato_telefone', 'contato_whatsapp', 'contato_whatsapp_formatado',
            'endereco_logradouro', 'endereco_numero', 'endereco_bairro', 'endereco_cidade',
            'endereco_estado', 'endereco_cep', 'horario_atendimento',
            'mail_mailer', 'mail_host', 'mail_port', 'mail_encryption',
            'mail_username', 'mail_from_address', 'mail_from_name',
            'social_instagram', 'social_facebook', 'social_linkedin', 'social_youtube',
            'recaptcha_site_key', 'recaptcha_secret_key',
            'geoblock_regioes', 'geoblock_ips_liberados',
            'admin_email_notificacoes', 'onesignal_app_id', 'onesignal_rest_api_key', 'onesignal_imagem_padrao',
            'desenvolvedor_nome', 'desenvolvedor_link', 'desenvolvedor_whatsapp',
        ];

        foreach ($camposTexto as $campo) {
            if ($request->has($campo)) {
                CmsSetting::set($campo, trim((string)$request->input($campo, '')), 'sistema');
            }
        }

        // Tempo de inatividade da sessão (mínimo 1 minuto, padrão 30)
        if ($request->has('sessao_inatividade_minutos')) {
            $minutos = max(1, min(1440, (int) $request->input('sessao_inatividade_minutos', 30)));
            CmsSetting::set('sessao_inatividade_minutos', $minutos, 'seguranca');
        }

        // Status do reCAPTCHA e Geo-Bloqueio
        CmsSetting::set('recaptcha_ativo', $request->has('recaptcha_ativo') ? '1' : '0', 'sistema');
        CmsSetting::set('geoblock_ativo', $request->has('geoblock_ativo') ? '1' : '0', 'sistema');

        // Preferências de Notificação & Push OneSignal
        CmsSetting::set('onesignal_ativo', $request->has('onesignal_ativo') ? '1' : '0', 'notificacoes');
        CmsSetting::set('notificar_email_contatos', $request->has('notificar_email_contatos') ? '1' : '0', 'notificacoes');
        CmsSetting::set('notificar_email_agendamentos', $request->has('notificar_email_agendamentos') ? '1' : '0', 'notificacoes');
        CmsSetting::set('notificacoes_push_ativas', $request->has('notificacoes_push_ativas') ? '1' : '0', 'notificacoes');

        // Senha SMTP (só atualiza se preenchida)
        if ($request->filled('mail_password')) {
            CmsSetting::set('mail_password', $request->input('mail_password'), 'sistema');
        }

        $uploadPath = public_path('uploads/branding');
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true, true);
        }

        // 2. Upload de Logotipo Principal (Fundo Escuro / Preto) com Drag & Drop
        if ($request->hasFile('logo_upload')) {
            $file = $request->file('logo_upload');
            $fileName = 'logo_dark_bg_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $logoUrl = '/uploads/branding/' . $fileName;
            CmsSetting::set('site_logo', $logoUrl, 'branding');
            @copy(public_path(ltrim($logoUrl, '/\\')), public_path('img/logo-foco7.png'));
        } elseif ($request->boolean('remover_logo_upload') || $request->boolean('remover_logo') || $request->input('remover_logo_upload') == '1' || $request->input('remover_logo') == '1') {
            CmsSetting::set('site_logo', '', 'branding');
        }

        // 3. Upload de Logotipo Secundário (Fundo Claro / Branco) com Drag & Drop
        if ($request->hasFile('logo_light_upload')) {
            $file = $request->file('logo_light_upload');
            $fileName = 'logo_light_bg_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $logoLightUrl = '/uploads/branding/' . $fileName;
            CmsSetting::set('site_logo_clara', $logoLightUrl, 'branding');
            CmsSetting::set('site_logo_light', $logoLightUrl, 'branding');
            @copy(public_path(ltrim($logoLightUrl, '/\\')), public_path('img/logo-foco7-dark.png'));
        } elseif ($request->boolean('remover_logo_light_upload') || $request->boolean('remover_logo_light') || $request->input('remover_logo_light_upload') == '1' || $request->input('remover_logo_light') == '1') {
            CmsSetting::set('site_logo_clara', '', 'branding');
            CmsSetting::set('site_logo_light', '', 'branding');
        }

        // 4. Upload de Favicon com Drag & Drop
        if ($request->hasFile('favicon_upload')) {
            $file = $request->file('favicon_upload');
            $fileName = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $favUrl = '/uploads/branding/' . $fileName;
            CmsSetting::set('site_favicon', $favUrl, 'branding');
            @copy(public_path(ltrim($favUrl, '/\\')), public_path('img/favicon.png'));
            @copy(public_path(ltrim($favUrl, '/\\')), public_path('favicon.ico'));
        } elseif ($request->boolean('remover_favicon_upload') || $request->boolean('remover_favicon') || $request->input('remover_favicon_upload') == '1' || $request->input('remover_favicon') == '1') {
            CmsSetting::set('site_favicon', '', 'branding');
        }

        return response()->json([
            'success' => true,
            'message' => 'Configurações do sistema e SMTP salvas com sucesso!',
        ]);
    }

    /**
     * Testa o envio de e-mail via servidor SMTP configurado
     */
    public function testarSmtp(Request $request)
    {
        $request->validate([
            'email_teste' => 'required|email',
            'host' => 'required|string',
            'port' => 'required|numeric',
            'username' => 'required|string',
            'from_address' => 'required|email',
        ], [
            'email_teste.required' => 'Informe o e-mail de destino para o teste.',
            'email_teste.email' => 'Informe um e-mail válido.',
            'host.required' => 'Informe o Host SMTP.',
            'port.required' => 'Informe a Porta SMTP.',
            'username.required' => 'Informe o Usuário SMTP.',
            'from_address.required' => 'Informe o e-mail do Remetente.',
        ]);

        $destinatario = $request->input('email_teste');
        $host = $request->input('host');
        $port = $request->input('port');
        $encryption = $request->input('encryption');
        $username = $request->input('username');
        $password = $request->input('password') ?: CmsSetting::get('mail_password', '');
        $fromAddress = $request->input('from_address');
        $fromName = $request->input('from_name', 'Foco 7 Rastreamento');

        try {
            // Configura dinamicamente a conexão SMTP do Laravel para o teste
            Config::set('mail.mailers.smtp_teste', [
                'transport' => 'smtp',
                'host' => $host,
                'port' => (int) $port,
                'encryption' => ($encryption === 'nenhum' || empty($encryption)) ? null : $encryption,
                'username' => $username,
                'password' => $password,
                'timeout' => 10,
            ]);

            Config::set('mail.from.address', $fromAddress);
            Config::set('mail.from.name', $fromName);

            $dataHora = date('d/m/Y H:i:s');
            $corpoHtml = "
                <div style='text-align: center; margin-bottom: 24px;'>
                    <span style='background-color: #dcfce7; color: #166534; padding: 6px 18px; border-radius: 50px; font-weight: 800; font-size: 13px; display: inline-block; border: 1px solid #bbf7d0;'>
                        ✓ SERVIDOR SMTP CONECTADO COM SUCESSO
                    </span>
                </div>
                <h3 style='color: #0f172a; margin-bottom: 12px;'>Validação de Comunicação por E-mail</h3>
                <p>Este é um e-mail de teste disparado pelo <strong>Sistema Foco 7</strong> para certificar a integridade das credenciais SMTP e a entrega via layout responsivo padronizado.</p>
                <div class='info-card'>
                    <p style='margin: 0 0 6px 0;'><strong>Data e Hora do Envio:</strong> {$dataHora}</p>
                    <p style='margin: 0 0 6px 0;'><strong>Host Conectado:</strong> {$host}:{$port}</p>
                    <p style='margin: 0 0 6px 0;'><strong>Criptografia:</strong> {$encryption}</p>
                    <p style='margin: 0;'><strong>Remetente Oficial:</strong> {$fromName} &lt;{$fromAddress}&gt;</p>
                </div>
                <p>Todas as notificações do sistema (faturas, carnês, pagamentos e avisos de segurança) estão configuradas para utilizar este canal oficial com entrega em formato responsivo para todos os dispositivos.</p>
            ";

            $htmlCompleto = \App\Services\EmailTemplateService::compilarHtml($corpoHtml, [
                'assunto' => '✅ Teste de Conexão SMTP - Foco 7 Rastreamento',
            ]);

            Mail::mailer('smtp_teste')->html($htmlCompleto, function ($message) use ($destinatario, $fromAddress, $fromName) {
                $message->to($destinatario)
                        ->from($fromAddress, $fromName)
                        ->subject('✅ Teste de Conexão SMTP - Foco 7 Rastreamento');
            });

            return response()->json([
                'success' => true,
                'message' => "E-mail de teste enviado com sucesso para {$destinatario}!",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Falha na conexão SMTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Testa ou simula bloqueio geográfico por IP ou código de país via AJAX
     */
    public function testarGeoip(Request $request)
    {
        $termo = $request->input('ip') ?? $request->input('termo', 'BR');
        $resultado = \App\Services\GeoIpService::simular($termo);

        return response()->json([
            'success' => true,
            'resultado' => $resultado,
            'dados' => $resultado,
        ]);
    }
}
