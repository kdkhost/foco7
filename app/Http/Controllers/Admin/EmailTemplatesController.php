<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailTemplatesController extends Controller
{
    /**
     * Lista organizada de todos os templates de e-mail do sistema
     */
    public function index()
    {
        $templatesRaw = EmailTemplateService::getTemplates();
        $templates = [];

        foreach ($templatesRaw as $chave => $tpl) {
            $tpl['chave'] = $chave;
            $tpl['assunto_atual'] = EmailTemplateService::getAssunto($chave);
            $tpl['corpo_atual'] = EmailTemplateService::getCorpo($chave);
            $tpl['sistema'] = $tpl['sistema'] ?? true;
            $templates[$chave] = $tpl;
        }

        $smtpConfig = [
            'host' => CmsSetting::get('mail_host', env('MAIL_HOST', 'localhost')),
            'port' => CmsSetting::get('mail_port', env('MAIL_PORT', 587)),
            'user' => CmsSetting::get('mail_username', env('MAIL_USERNAME', 'notificacoes@foco7.com.br')),
            'from' => CmsSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS', 'notificacoes@foco7.com.br')),
            'from_name' => CmsSetting::get('mail_from_name', env('MAIL_FROM_NAME', 'Foco 7 Rastreamento')),
        ];

        return view('admin.emails.index', compact('templates', 'smtpConfig'));
    }

    /**
     * Página dedicada para criação de novo template personalizado
     */
    public function create()
    {
        $dadosSimulados = EmailTemplateService::getDadosSimulados();

        return view('admin.emails.create', compact('dadosSimulados'));
    }

    /**
     * Armazena novo template de e-mail personalizado
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:120',
            'chave' => 'nullable|string|max:60',
            'descricao' => 'nullable|string|max:255',
            'assunto' => 'required|string|max:255',
            'corpo' => 'required|string',
        ], [
            'nome.required' => 'Informe o nome do template.',
            'assunto.required' => 'Informe o assunto do e-mail.',
            'corpo.required' => 'O corpo do e-mail não pode ficar em branco.',
        ]);

        $chave = !empty($validated['chave']) ? Str::slug($validated['chave'], '_') : Str::slug($validated['nome'], '_');
        $descricao = $validated['descricao'] ?: 'Template de e-mail personalizado da Foco 7';

        EmailTemplateService::salvarTemplateCustomizado(
            $chave,
            $validated['nome'],
            $descricao,
            $validated['assunto'],
            $validated['corpo']
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Template '{$validated['nome']}' criado com sucesso!",
                'redirect' => route('admin.emails.edit', $chave),
            ]);
        }

        return redirect()->route('admin.emails.edit', $chave)->with('success', "Template '{$validated['nome']}' criado com sucesso!");
    }

    /**
     * Página dedicada para edição do template de e-mail com preview ao vivo em tempo real
     */
    public function edit(string $chave)
    {
        $templates = EmailTemplateService::getTemplates();

        if (!isset($templates[$chave])) {
            return redirect()->route('admin.emails.index')->with('error', 'Template de e-mail não encontrado.');
        }

        $template = $templates[$chave];
        $template['chave'] = $chave;
        $template['assunto_atual'] = EmailTemplateService::getAssunto($chave);
        $template['corpo_atual'] = EmailTemplateService::getCorpo($chave);
        $template['sistema'] = $template['sistema'] ?? true;

        $dadosSimulados = EmailTemplateService::getDadosSimulados();

        return view('admin.emails.edit', compact('template', 'dadosSimulados'));
    }

    /**
     * Salva as alterações de um template existente
     */
    public function update(Request $request, string $chave)
    {
        $validated = $request->validate([
            'assunto' => 'required|string|max:255',
            'corpo' => 'required|string',
            'nome' => 'nullable|string|max:120',
            'descricao' => 'nullable|string|max:255',
        ]);

        CmsSetting::set("email_assunto_{$chave}", $validated['assunto'], 'emails');
        CmsSetting::set("email_corpo_{$chave}", $validated['corpo'], 'emails');

        // Se for template customizado, atualiza também os metadados
        $templates = EmailTemplateService::getTemplates();
        if (isset($templates[$chave]) && empty($templates[$chave]['sistema']) && !empty($validated['nome'])) {
            EmailTemplateService::salvarTemplateCustomizado(
                $chave,
                $validated['nome'],
                $validated['descricao'] ?? ($templates[$chave]['descricao'] ?? ''),
                $validated['assunto'],
                $validated['corpo']
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template de e-mail atualizado com sucesso no sistema!',
            ]);
        }

        return back()->with('success', 'Template de e-mail atualizado com sucesso!');
    }

    /**
     * Exclui um template customizado
     */
    public function destroy(string $chave)
    {
        $removido = EmailTemplateService::excluirTemplateCustomizado($chave);

        if ($removido) {
            return response()->json([
                'success' => true,
                'message' => 'Template personalizado removido com sucesso!',
                'redirect' => route('admin.emails.index'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Templates nativos do sistema não podem ser removidos.',
        ], 422);
    }

    /**
     * Renderiza o preview em tempo real no layout master oficial responsivo
     */
    public function preview(Request $request)
    {
        $chave = $request->input('template_chave', 'fatura_gerada');
        $assunto = $request->input('assunto');
        $corpo = $request->input('corpo');

        $html = EmailTemplateService::renderPreview($chave, $corpo, $assunto);

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Dispara e-mail de teste para o endereço informado
     */
    public function testar(Request $request)
    {
        $request->validate([
            'template_chave' => 'required|string',
            'email_destinatario' => 'required|email',
        ]);

        $chave = $request->input('template_chave');
        $email = $request->input('email_destinatario');
        $corpo = $request->input('corpo');
        $assunto = $request->input('assunto');

        $resultado = EmailTemplateService::enviarTeste($email, $chave, $corpo, $assunto);

        return response()->json($resultado, $resultado['success'] ? 200 : 422);
    }
}
