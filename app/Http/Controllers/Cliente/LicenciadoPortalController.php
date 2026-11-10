<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Licenca;
use App\Services\EvolutionApiService;
use App\Services\LicenciamentoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $whatsappDados = null;
        if ($licencaAtiva) {
            $dadosServidor = $licencaAtiva->dados_servidor ?? [];
            $whatsappDados = $dadosServidor['whatsapp'] ?? null;
        }

        $custoInstanciaExtra = EvolutionApiService::getCustoInstanciaExtra(); // R$ 60,00/mês
        $evolutionBaseUrl = EvolutionApiService::getBaseUrl(); // https://whatsapp.km.site.nom.br
        $evolutionDocsUrl = 'https://whatsapp.km.site.nom.br/api/docs';

        return view('cliente.licenca.index', compact(
            'user',
            'licencas',
            'licencaAtiva',
            'parametros',
            'whatsappDados',
            'custoInstanciaExtra',
            'evolutionBaseUrl',
            'evolutionDocsUrl'
        ));
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

    /**
     * Ativa ou provisiona a instância de WhatsApp dedicada do licenciado no servidor central
     * Custo de R$ 60,00/mês com recorrência controlada pelo servidor oficial
     */
    public function ativarWhatsapp(Request $request, $id)
    {
        $user = Auth::user();
        $licenca = Licenca::where('user_id', $user->id)->findOrFail($id);

        if (! $licenca->isAtiva()) {
            return response()->json([
                'success' => false,
                'message' => 'Sua licença precisa estar ativa para provisionar a instância WhatsApp.',
            ], 403);
        }

        $dadosServidor = $licenca->dados_servidor ?? [];
        $existing = $dadosServidor['whatsapp'] ?? null;

        // Se já possui instância criada, reaproveita o nome e token
        if (! empty($existing['instancia']) && ! empty($existing['token'])) {
            $instName = $existing['instancia'];
            $token = $existing['token'];
        } else {
            $slugNome = Str::slug($licenca->nome_empresa ?: 'licenciado', '_');
            $instName = 'licenciado_' . $licenca->id . '_' . substr($slugNome, 0, 15);
            $token = Str::random(32);
        }

        // 1. Cria ou valida a instância no servidor oficial https://whatsapp.km.site.nom.br
        $res = EvolutionApiService::criarInstancia($instName, $token);

        if (! $res['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao provisionar instância no servidor central de WhatsApp: ' . ($res['error'] ?? 'Verifique com o suporte.'),
            ], 400);
        }

        // 2. Registra como instância extra tarifada a R$ 60,00/mês recorrente
        EvolutionApiService::registrarInstanciaExtra($instName, $token, 'licenciado', $licenca->id);

        // 3. Atualiza os dados no registro da Licença
        $whatsappConfig = [
            'instancia' => $instName,
            'token' => $token,
            'api_url' => EvolutionApiService::getBaseUrl(),
            'docs_url' => 'https://whatsapp.km.site.nom.br/api/docs',
            'custo_recorrente' => EvolutionApiService::getCustoInstanciaExtra(),
            'ativo' => true,
            'criado_em' => now()->format('Y-m-d H:i:s'),
        ];

        $dadosServidor['whatsapp'] = $whatsappConfig;
        $licenca->update(['dados_servidor' => $dadosServidor]);

        // 4. Solicita o QR Code de pareamento
        $qrRes = EvolutionApiService::obterQrCodeParaInstancia($instName, $token);
        $qrcode = $qrRes['data']['qrcode']['base64'] ?? ($qrRes['data']['base64'] ?? null);

        return response()->json([
            'success' => true,
            'message' => "Instância de WhatsApp '{$instName}' provisionada com sucesso no servidor central! Custo recorrente: R$ 60,00/mês.",
            'config' => $whatsappConfig,
            'qrcode' => $qrcode,
        ]);
    }

    /**
     * Consulta o QR Code atual para pareamento do aparelho do licenciado
     */
    public function qrWhatsapp($id)
    {
        $user = Auth::user();
        $licenca = Licenca::where('user_id', $user->id)->findOrFail($id);

        $dadosServidor = $licenca->dados_servidor ?? [];
        $whatsapp = $dadosServidor['whatsapp'] ?? null;

        if (empty($whatsapp['instancia'])) {
            return response()->json([
                'success' => false,
                'message' => 'Instância WhatsApp ainda não foi ativada para esta licença.',
            ], 404);
        }

        $res = EvolutionApiService::obterQrCodeParaInstancia($whatsapp['instancia'], $whatsapp['token'] ?? null);
        $qrcode = $res['data']['qrcode']['base64'] ?? ($res['data']['base64'] ?? null);

        return response()->json([
            'success' => true,
            'qrcode' => $qrcode,
            'data' => $res['data'] ?? null,
            'instancia' => $whatsapp['instancia'],
        ]);
    }

    /**
     * Consulta o status de conexão da instância do licenciado
     */
    public function statusWhatsapp($id)
    {
        $user = Auth::user();
        $licenca = Licenca::where('user_id', $user->id)->findOrFail($id);

        $dadosServidor = $licenca->dados_servidor ?? [];
        $whatsapp = $dadosServidor['whatsapp'] ?? null;

        if (empty($whatsapp['instancia'])) {
            return response()->json([
                'success' => false,
                'conectado' => false,
                'estado' => 'nao_criada',
                'message' => 'Instância WhatsApp ainda não configurada.',
            ]);
        }

        $res = EvolutionApiService::obterStatusInstancia($whatsapp['instancia'], $whatsapp['token'] ?? null);
        $conectado = false;
        $estado = 'desconectado';

        if ($res['success']) {
            $data = $res['data'] ?? [];
            $rawStatus = $data['status'] ?? ($data['instance']['status'] ?? ($data['state'] ?? ''));
            $estado = strtolower((string) $rawStatus);
            if (in_array($estado, ['open', 'connected', 'conectado'])) {
                $conectado = true;
                $estado = 'conectado';
            }
        }

        return response()->json([
            'success' => true,
            'conectado' => $conectado,
            'estado' => $estado,
            'instancia' => $whatsapp['instancia'],
            'api_url' => EvolutionApiService::getBaseUrl(),
        ]);
    }

    /**
     * Desconecta o WhatsApp da licença
     */
    public function desconectarWhatsapp($id)
    {
        $user = Auth::user();
        $licenca = Licenca::where('user_id', $user->id)->findOrFail($id);

        $dadosServidor = $licenca->dados_servidor ?? [];
        $whatsapp = $dadosServidor['whatsapp'] ?? null;

        if (empty($whatsapp['instancia'])) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma instância WhatsApp encontrada.',
            ], 404);
        }

        $res = EvolutionApiService::desconectarInstanciaEspecifica($whatsapp['instancia'], $whatsapp['token'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Instância WhatsApp desconectada com sucesso.',
        ]);
    }
}

