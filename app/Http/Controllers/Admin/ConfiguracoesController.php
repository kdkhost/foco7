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

            // Identidade Visual
            'site_logo' => CmsSetting::get('site_logo', '/img/logo-foco7.png'),
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
        ];

        return view('admin.configuracoes.index', compact('configs'));
    }

    /**
     * Salva as configurações gerais e identidade via AJAX
     */
    public function salvar(Request $request)
    {
        // 1. Dados da Empresa
        $camposTexto = [
            'site_nome', 'razao_social', 'cnpj', 'site_slogan',
            'contato_email', 'contato_telefone', 'contato_whatsapp', 'contato_whatsapp_formatado',
            'endereco_logradouro', 'endereco_numero', 'endereco_bairro', 'endereco_cidade',
            'endereco_estado', 'endereco_cep', 'horario_atendimento',
            'mail_mailer', 'mail_host', 'mail_port', 'mail_encryption',
            'mail_username', 'mail_from_address', 'mail_from_name',
            'social_instagram', 'social_facebook', 'social_linkedin', 'social_youtube'
        ];

        foreach ($camposTexto as $campo) {
            if ($request->has($campo)) {
                CmsSetting::set($campo, $request->input($campo, ''), 'sistema');
            }
        }

        // Senha SMTP (só atualiza se preenchida)
        if ($request->filled('mail_password')) {
            CmsSetting::set('mail_password', $request->input('mail_password'), 'sistema');
        }

        $uploadPath = public_path('uploads/branding');
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true, true);
        }

        // 2. Upload de Logotipo Principal com Drag & Drop
        if ($request->hasFile('logo_upload')) {
            $file = $request->file('logo_upload');
            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $logoUrl = '/uploads/branding/' . $fileName;
            CmsSetting::set('site_logo', $logoUrl, 'branding');
            @copy(public_path($logoUrl), public_path('img/logo-foco7.png'));
        }

        // 3. Upload de Favicon com Drag & Drop
        if ($request->hasFile('favicon_upload')) {
            $file = $request->file('favicon_upload');
            $fileName = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $favUrl = '/uploads/branding/' . $fileName;
            CmsSetting::set('site_favicon', $favUrl, 'branding');
            @copy(public_path($favUrl), public_path('img/favicon.png'));
            @copy(public_path($favUrl), public_path('favicon.ico'));
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
}
