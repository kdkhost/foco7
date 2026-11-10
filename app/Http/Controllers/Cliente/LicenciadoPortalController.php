<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Licenca;
use App\Services\LicenciamentoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenciadoPortalController extends Controller
{
    /**
     * Portal do Licenciado: Visualização da Licença, Downloads e Guias de Servidor
     */
    public function index()
    {
        $user = Auth::user();
        $licencas = Licenca::where('user_id', $user->id)->latest()->get();
        $licencaAtiva = $licencas->first(fn ($l) => $l->isAtiva());
        $parametros = LicenciamentoService::obterParametrosPrograma();

        return view('cliente.licenca.index', compact('user', 'licencas', 'licencaAtiva', 'parametros'));
    }

    /**
     * Atualização do domínio e IP vinculados à licença pelo próprio licenciado
     */
    public function vincularServidor(Request $request, $id)
    {
        $user = Auth::user();
        $licenca = Licenca::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'dominio_autorizado' => 'required|string|max:180',
            'ip_autorizado' => 'nullable|string|max:45',
            'nome_empresa' => 'nullable|string|max:150',
        ]);

        $dominio = strtolower(trim($request->input('dominio_autorizado')));
        // Remove https://, http:// e barras caso o usuário cole com protocolo
        $dominio = preg_replace('#^https?://#', '', $dominio);
        $dominio = rtrim($dominio, '/');

        $licenca->update([
            'dominio_autorizado' => $dominio,
            'ip_autorizado' => trim($request->input('ip_autorizado', '')),
            'nome_empresa' => $request->input('nome_empresa', $licenca->nome_empresa),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Servidor e domínio vinculados com sucesso à sua licença!',
        ]);
    }

    /**
     * Download do Pacote ZIP completo do Sistema para o servidor do licenciado
     */
    public function downloadPacote($id)
    {
        $user = Auth::user();
        $licenca = Licenca::where('user_id', $user->id)->findOrFail($id);

        if (! $licenca->isAtiva()) {
            return back()->with('error', 'Sua licença não está ativa para download.');
        }

        try {
            $zipPath = LicenciamentoService::gerarPacoteZipParaDownload($licenca);

            return response()->download($zipPath, "FOCO7_SISTEMA_LICENCIADO.zip", [
                'Content-Type' => 'application/zip',
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao preparar arquivo de download: ' . $e->getMessage());
        }
    }

    /**
     * Download do Script de Instalação Automática para VPS (Ubuntu/Debian)
     */
    public function downloadScriptVps($id)
    {
        $user = Auth::user();
        $licenca = Licenca::where('user_id', $user->id)->findOrFail($id);

        if (! $licenca->isAtiva()) {
            return back()->with('error', 'Sua licença não está ativa.');
        }

        $script = LicenciamentoService::gerarScriptInstalacaoVps($licenca);

        return response($script, 200, [
            'Content-Type' => 'text/x-shellscript',
            'Content-Disposition' => 'attachment; filename="install-vps.sh"',
        ]);
    }

    /**
     * Download do Guia de Instalação em formato Markdown
     */
    public function downloadManual($id)
    {
        $user = Auth::user();
        $licenca = Licenca::where('user_id', $user->id)->findOrFail($id);

        $guia = LicenciamentoService::gerarGuiaInstalacaoMarkdown($licenca);

        return response($guia, 200, [
            'Content-Type' => 'text/markdown',
            'Content-Disposition' => 'attachment; filename="GUIA_INSTALACAO_SERVIDOR.md"',
        ]);
    }
}

