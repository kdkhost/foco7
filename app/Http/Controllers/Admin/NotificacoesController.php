<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notificacao;
use App\Models\CmsSetting;

class NotificacoesController extends Controller
{
    /**
     * Exibe a listagem completa da Central de Notificações
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'todas');
        $tipo = $request->get('tipo', 'todos');

        $query = Notificacao::query()->recentes();

        if ($status === 'nao_lidas') {
            $query->where('lida', false);
        } elseif ($status === 'lidas') {
            $query->where('lida', true);
        }

        if ($tipo !== 'todos' && !empty($tipo)) {
            $query->where('tipo', $tipo);
        }

        $notificacoes = $query->paginate(15)->appends($request->all());

        $totalNaoLidas = Notificacao::naoLidas()->count();
        $totalGeral = Notificacao::count();
        $totalContatos = Notificacao::where('tipo', 'contato')->count();
        $totalAgendamentos = Notificacao::where('tipo', 'agendamento')->count();
        $totalDepoimentos = Notificacao::where('tipo', 'depoimento')->count();

        $adminEmail = CmsSetting::get('admin_email_notificacoes')
            ?: CmsSetting::get('contato_email')
            ?: 'contato@foco7.com.br';

        return view('admin.notificacoes.index', compact(
            'notificacoes',
            'status',
            'tipo',
            'totalNaoLidas',
            'totalGeral',
            'totalContatos',
            'totalAgendamentos',
            'totalDepoimentos',
            'adminEmail'
        ));
    }

    /**
     * Retorna as notificações pendentes (não lidas) via AJAX para o sino da navbar e polling de Push
     */
    public function ultimasAjax(Request $request)
    {
        $naoLidasCount = Notificacao::naoLidas()->count();
        // A lista do sino exibe estritamente as notificações NÃO LIDAS.
        // Assim que forem lidas, o dropdown fica 100% limpo!
        $ultimas = Notificacao::naoLidas()->recentes()->take(6)->get();

        // Verifica se há alguma notificação muito recente (últimos 35 segundos) para emitir som e Push nativo
        $novaPush = Notificacao::where('lida', false)
            ->where('notificado_push', false)
            ->orderBy('id', 'desc')
            ->first();

        if ($novaPush) {
            $novaPush->update(['notificado_push' => true]);
        }

        return response()->json([
            'success' => true,
            'nao_lidas_count' => $naoLidasCount,
            'ultimas' => $ultimas->map(function ($n) {
                return [
                    'id' => $n->id,
                    'tipo' => $n->tipo,
                    'titulo' => $n->titulo,
                    'mensagem' => $n->mensagem,
                    'icone' => $n->icone,
                    'cor' => $n->cor,
                    'link_acao' => $n->link_acao,
                    'lida' => $n->lida,
                    'tempo_relativo' => $n->created_at ? $n->created_at->diffForHumans() : '',
                ];
            }),
            'nova_notificacao_push' => $novaPush ? [
                'id' => $novaPush->id,
                'titulo' => $novaPush->titulo,
                'mensagem' => $novaPush->mensagem,
                'link_acao' => $novaPush->link_acao ?: route('admin.notificacoes.index'),
                'icone' => asset('img/favicon.png'),
            ] : null,
        ]);
    }

    /**
     * Marca uma notificação individual como lida
     */
    public function marcarLida(Notificacao $notificacao)
    {
        $notificacao->marcarComoLida();

        return response()->json([
            'success' => true,
            'message' => 'Notificação marcada como lida com sucesso.',
            'nao_lidas_count' => Notificacao::naoLidas()->count(),
        ]);
    }

    /**
     * Retorna os detalhes completos de uma notificação para leitura em modal e marca como lida automaticamente
     */
    public function obter(Notificacao $notificacao)
    {
        $foiMarcadaAgora = false;
        if (!$notificacao->lida) {
            $notificacao->marcarComoLida();
            $foiMarcadaAgora = true;
        }

        return response()->json([
            'success' => true,
            'notificacao' => [
                'id' => $notificacao->id,
                'tipo' => $notificacao->tipo,
                'tipo_formatado' => ucfirst($notificacao->tipo),
                'titulo' => $notificacao->titulo,
                'mensagem' => $notificacao->mensagem,
                'icone' => $notificacao->icone,
                'cor' => $notificacao->cor,
                'link_acao' => $notificacao->link_acao,
                'dados' => $notificacao->dados,
                'lida' => true,
                'data_formatada' => $notificacao->created_at ? $notificacao->created_at->format('d/m/Y H:i') : '',
                'tempo_relativo' => $notificacao->created_at ? $notificacao->created_at->diffForHumans() : '',
            ],
            'foi_marcada_agora' => $foiMarcadaAgora,
            'nao_lidas_count' => Notificacao::naoLidas()->count(),
        ]);
    }

    /**
     * Marca como lida ao clicar e redireciona para a página de destino da notificação
     */
    public function abrirERedirecionar(Request $request, Notificacao $notificacao)
    {
        if (!$notificacao->lida) {
            $notificacao->marcarComoLida();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'link' => $notificacao->link_acao ?: route('admin.notificacoes.index'),
                'nao_lidas_count' => Notificacao::naoLidas()->count(),
            ]);
        }

        $destino = $notificacao->link_acao ?: route('admin.notificacoes.index');
        return redirect()->to($destino);
    }

    /**
     * Marca todas as notificações como lidas
     */
    public function marcarTodasLidas()
    {
        Notificacao::where('lida', false)->update([
            'lida' => true,
            'lida_em' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Todas as notificações foram marcadas como lidas!',
            'nao_lidas_count' => 0,
        ]);
    }

    /**
     * Limpa e remove do histórico todas as notificações que já foram lidas
     */
    public function limparLidas()
    {
        $totalLidas = Notificacao::where('lida', true)->count();
        Notificacao::where('lida', true)->delete();

        return response()->json([
            'success' => true,
            'message' => $totalLidas > 0 
                ? "{$totalLidas} notificações lidas foram limpas do histórico com sucesso!" 
                : "Nenhuma notificação lida encontrada para limpar.",
            'nao_lidas_count' => Notificacao::naoLidas()->count(),
        ]);
    }

    /**
     * Exclui uma notificação
     */
    public function excluir(Notificacao $notificacao)
    {
        $notificacao->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificação removida com sucesso.',
            'nao_lidas_count' => Notificacao::naoLidas()->count(),
        ]);
    }

    /**
     * Salva as preferências de notificação do administrador
     */
    public function salvarConfiguracoes(Request $request)
    {
        $validated = $request->validate([
            'admin_email_notificacoes' => 'required|email|max:150',
            'notificacoes_push_ativas' => 'nullable|in:0,1',
            'notificar_email_contatos' => 'nullable|in:0,1',
            'notificar_email_agendamentos' => 'nullable|in:0,1',
        ]);

        CmsSetting::set('admin_email_notificacoes', $validated['admin_email_notificacoes']);
        CmsSetting::set('notificacoes_push_ativas', $request->has('notificacoes_push_ativas') ? '1' : '0');
        CmsSetting::set('notificar_email_contatos', $request->has('notificar_email_contatos') ? '1' : '0');
        CmsSetting::set('notificar_email_agendamentos', $request->has('notificar_email_agendamentos') ? '1' : '0');

        return response()->json([
            'success' => true,
            'message' => 'Preferências da Central de Notificações salvas com sucesso!',
        ]);
    }

    /**
     * Envia uma notificação Push de teste para o celular/navegador do administrador
     */
    public function dispararTeste()
    {
        $notif = Notificacao::registrar(
            tipo: 'sistema',
            titulo: 'Teste de Notificação Push & E-mail',
            mensagem: 'A Central de Notificações da Foco 7 está 100% ativa e conectada ao seu dispositivo móvel!',
            linkAcao: route('admin.notificacoes.index'),
            dados: [
                'origem' => 'Disparo Manual de Teste',
                'hora' => date('d/m/Y H:i:s'),
                'dispositivo' => request()->userAgent() ?: 'Dispositivo Móvel',
            ],
            notificarEmail: true
        );

        return response()->json([
            'success' => true,
            'message' => 'Notificação de teste disparada com sucesso! Verifique seu e-mail e as notificações na tela.',
            'notificacao' => $notif,
        ]);
    }
}
