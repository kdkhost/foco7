<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\Licenca;
use App\Models\User;
use App\Services\LicenciamentoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LicenciadosController extends Controller
{
    /**
     * Listagem de Licenciados & Painel de Controle de Licenças
     */
    public function index(Request $request)
    {
        $busca = $request->input('busca');
        $status = $request->input('status');

        $query = Licenca::with(['user', 'fatura'])->latest();

        if (! empty($busca)) {
            $query->where(function ($q) use ($busca) {
                $q->where('chave_licenca', 'like', "%{$busca}%")
                    ->orWhere('nome_empresa', 'like', "%{$busca}%")
                    ->orWhere('dominio_autorizado', 'like', "%{$busca}%")
                    ->orWhere('ip_autorizado', 'like', "%{$busca}%")
                    ->orWhereHas('user', function ($uq) use ($busca) {
                        $uq->where('name', 'like', "%{$busca}%")
                            ->orWhere('email', 'like', "%{$busca}%")
                            ->orWhere('cpf_cnpj', 'like', "%{$busca}%");
                    });
            });
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $licencas = $query->paginate(15)->withQueryString();

        // KPIs em tempo real
        $kpis = [
            'total' => Licenca::count(),
            'ativas' => Licenca::where('status', 'ativa')->count(),
            'pendentes' => Licenca::where('status', 'pendente')->count(),
            'suspensas' => Licenca::whereIn('status', ['suspensa', 'bloqueada'])->count(),
            'downloads' => (int) Licenca::sum('total_downloads'),
        ];

        $parametros = LicenciamentoService::obterParametrosPrograma();
        $usuarios = User::orderBy('name')->get();

        return view('admin.licenciados.index', compact('licencas', 'kpis', 'parametros', 'usuarios', 'busca', 'status'));
    }

    /**
     * Retorna os dados de uma licença em JSON para modal de edição via AJAX
     */
    public function obter($id)
    {
        $licenca = Licenca::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'licenca' => $licenca,
        ]);
    }

    /**
     * Cria ou edita uma licença de software
     */
    public function salvar(Request $request)
    {
        $request->validate([
            'nome_empresa' => 'required|string|max:150',
            'tipo_licenca' => 'required|in:vitalicia,anual,mensal,teste',
            'status' => 'required|in:pendente,ativa,suspensa,bloqueada,expirada',
            'dominio_autorizado' => 'nullable|string|max:180',
            'ip_autorizado' => 'nullable|string|max:45',
        ]);

        $id = $request->input('licenca_id');

        if ($id) {
            $licenca = Licenca::findOrFail($id);
            $licenca->nome_empresa = $request->input('nome_empresa');
            $licenca->tipo_licenca = $request->input('tipo_licenca');
            $licenca->status = $request->input('status');
            $licenca->dominio_autorizado = $request->input('dominio_autorizado') ? strtolower(trim($request->input('dominio_autorizado'))) : null;
            $licenca->ip_autorizado = $request->input('ip_autorizado');
            $licenca->versao_liberada = $request->input('versao_liberada') ?: 'v3.8.0';
            $licenca->limite_veiculos = $request->filled('limite_veiculos') ? (int) $request->input('limite_veiculos') : null;
            $licenca->limite_clientes = $request->filled('limite_clientes') ? (int) $request->input('limite_clientes') : null;
            $licenca->observacoes = $request->input('observacoes');

            if ($request->filled('data_expiracao')) {
                $licenca->data_expiracao = $request->input('data_expiracao');
            }

            if ($licenca->status === 'ativa' && ! $licenca->data_ativacao) {
                $licenca->data_ativacao = now();
            }

            $licenca->save();

            return response()->json([
                'success' => true,
                'message' => 'Licença atualizada com sucesso!',
            ]);
        }

        // Nova Licença Manual
        $userId = $request->input('user_id');
        if (! $userId) {
            // Cria um novo usuário se informado nome e e-mail
            $request->validate([
                'novo_usuario_nome' => 'required|string|max:120',
                'novo_usuario_email' => 'required|email|unique:users,email',
                'novo_usuario_telefone' => 'nullable|string|max:30',
            ]);

            $user = User::create([
                'name' => $request->input('novo_usuario_nome'),
                'email' => $request->input('novo_usuario_email'),
                'telefone' => $request->input('novo_usuario_telefone'),
                'password' => Hash::make(Str::random(12)),
                'role' => 'licenciado',
                'status' => 'ativo',
            ]);
        } else {
            $user = User::findOrFail($userId);
            if ($user->role !== 'admin') {
                $user->update(['role' => 'licenciado']);
            }
        }

        $licenca = LicenciamentoService::criarLicenca($user, [
            'nome_empresa' => $request->input('nome_empresa'),
            'tipo_licenca' => $request->input('tipo_licenca'),
            'status' => $request->input('status'),
            'dominio_autorizado' => $request->input('dominio_autorizado') ? strtolower(trim($request->input('dominio_autorizado'))) : null,
            'ip_autorizado' => $request->input('ip_autorizado'),
            'versao_liberada' => $request->input('versao_liberada') ?: 'v3.8.0',
            'limite_veiculos' => $request->filled('limite_veiculos') ? (int) $request->input('limite_veiculos') : null,
            'limite_clientes' => $request->filled('limite_clientes') ? (int) $request->input('limite_clientes') : null,
            'data_expiracao' => $request->filled('data_expiracao') ? $request->input('data_expiracao') : null,
            'observacoes' => $request->input('observacoes') ?: 'Licença criada manualmente pelo administrador.',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Licença {$licenca->chave_licenca} criada com sucesso para {$user->name}!",
        ]);
    }

    /**
     * Alterna o status da licença (Ativar, Suspender, Bloquear)
     */
    public function alterarStatus($id, Request $request)
    {
        $licenca = Licenca::findOrFail($id);
        $novoStatus = $request->input('status');

        if (! in_array($novoStatus, ['ativa', 'suspensa', 'bloqueada', 'pendente', 'expirada'])) {
            return response()->json(['success' => false, 'message' => 'Status inválido.'], 422);
        }

        $licenca->status = $novoStatus;
        if ($novoStatus === 'ativa' && ! $licenca->data_ativacao) {
            $licenca->data_ativacao = now();
        }
        $licenca->save();

        return response()->json([
            'success' => true,
            'status' => $novoStatus,
            'message' => "Status da licença alterado para: {$novoStatus}!",
        ]);
    }

    /**
     * Regenera uma nova chave de licença caso a anterior tenha sido comprometida
     */
    public function regenerarChave($id)
    {
        $licenca = Licenca::findOrFail($id);
        $chaveAntiga = $licenca->chave_licenca;
        $licenca->chave_licenca = Licenca::gerarChaveUnica();
        $licenca->save();

        return response()->json([
            'success' => true,
            'nova_chave' => $licenca->chave_licenca,
            'message' => "Nova chave gerada com sucesso! A chave antiga ({$chaveAntiga}) foi revogada.",
        ]);
    }

    /**
     * Salva as configurações gerais do programa de licenciamento
     */
    public function salvarConfiguracoes(Request $request)
    {
        CmsSetting::set('licenciamento_ativo', $request->has('licenciamento_ativo') ? '1' : '0', 'licenciamento');
        CmsSetting::set('licenciamento_badge', trim($request->input('licenciamento_badge', 'OPORTUNIDADE DE NEGÓCIO • WHITE LABEL')), 'licenciamento');
        CmsSetting::set('licenciamento_titulo', trim($request->input('licenciamento_titulo', 'Tenha Sua Própria Empresa de Rastreamento Veicular')), 'licenciamento');
        CmsSetting::set('licenciamento_subtitulo', trim($request->input('licenciamento_subtitulo', '')), 'licenciamento');

        $precoAdesao = str_replace(['R$', ' ', '.'], '', (string) $request->input('licenciamento_preco_adesao', '1497,00'));
        $precoAdesao = str_replace(',', '.', $precoAdesao);
        CmsSetting::set('licenciamento_preco_adesao', is_numeric($precoAdesao) ? $precoAdesao : '1497.00', 'licenciamento');

        $precoMensal = str_replace(['R$', ' ', '.'], '', (string) $request->input('licenciamento_preco_mensal', '0,00'));
        $precoMensal = str_replace(',', '.', $precoMensal);
        CmsSetting::set('licenciamento_preco_mensal', is_numeric($precoMensal) ? $precoMensal : '0.00', 'licenciamento');

        CmsSetting::set('licenciamento_versao_atual', trim($request->input('licenciamento_versao_atual', 'v3.8.0')), 'licenciamento');
        CmsSetting::set('licenciamento_whatsapp_suporte', trim($request->input('licenciamento_whatsapp_suporte', '559180510736')), 'licenciamento');
        CmsSetting::set('licenciamento_termo_licenca', trim($request->input('licenciamento_termo_licenca', '')), 'licenciamento');
        CmsSetting::set('licenciamento_permitir_auto_binding', $request->has('licenciamento_permitir_auto_binding') ? '1' : '0', 'licenciamento');

        return response()->json([
            'success' => true,
            'message' => 'Configurações do Programa de Licenciamento salvas com sucesso!',
        ]);
    }

    /**
     * Download do pacote de distribuição gerado para o licenciado
     */
    public function downloadPacote($id)
    {
        $licenca = Licenca::findOrFail($id);

        try {
            $zipPath = LicenciamentoService::gerarPacoteZipParaDownload($licenca);

            return response()->download($zipPath, "FOCO7_SISTEMA_LICENCIADO_{$licenca->id}.zip", [
                'Content-Type' => 'application/zip',
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Falha ao empacotar sistema: ' . $e->getMessage());
        }
    }

    /**
     * Download do script de instalação VPS Linux
     */
    public function downloadScriptVps($id)
    {
        $licenca = Licenca::findOrFail($id);
        $script = LicenciamentoService::gerarScriptInstalacaoVps($licenca);

        return response($script, 200, [
            'Content-Type' => 'text/x-shellscript',
            'Content-Disposition' => 'attachment; filename="install-vps.sh"',
        ]);
    }

    /**
     * Exclui uma licença
     */
    public function excluir($id)
    {
        $licenca = Licenca::findOrFail($id);
        $chave = $licenca->chave_licenca;
        $licenca->delete();

        return response()->json([
            'success' => true,
            'message' => "Licença {$chave} excluída com sucesso!",
        ]);
    }
}

