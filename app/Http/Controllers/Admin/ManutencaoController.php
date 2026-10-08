<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsSetting;

class ManutencaoController extends Controller
{
    /**
     * Exibe o painel de controle do Modo de Manutenção
     */
    public function index(Request $request)
    {
        $statusAtivo = CmsSetting::get('manutencao_ativa', '0') == '1';
        $titulo = CmsSetting::get('manutencao_titulo', 'SITE EM MANUTENÇÃO PROGRAMADA');
        $mensagem = CmsSetting::get('manutencao_mensagem', 'Estamos realizando melhorias em nossos servidores de monitoramento.');
        $previsao = CmsSetting::get('manutencao_previsao', date('Y-m-d H:i', strtotime('+24 hours')));
        $ips = CmsSetting::get('manutencao_ips_liberados', '');
        $token = CmsSetting::get('manutencao_token', 'foco7_vip_bypass_2026');
        $mostrarRedes = CmsSetting::get('manutencao_mostrar_redes', '1') == '1';
        $mostrarWhatsApp = CmsSetting::get('manutencao_mostrar_whatsapp', '1') == '1';
        $meuIp = $request->ip();

        return view('admin.manutencao.index', compact(
            'statusAtivo',
            'titulo',
            'mensagem',
            'previsao',
            'ips',
            'token',
            'mostrarRedes',
            'mostrarWhatsApp',
            'meuIp'
        ));
    }

    /**
     * Alterna o status do modo de manutenção com 1 clique via AJAX
     */
    public function toggle(Request $request)
    {
        $statusAtual = CmsSetting::get('manutencao_ativa', '0') == '1';
        $novoStatus = $statusAtual ? '0' : '1';

        CmsSetting::set('manutencao_ativa', $novoStatus, 'manutencao', 'boolean');

        $mensagem = $novoStatus === '1'
            ? 'Modo de Manutenção ATIVADO! Visitantes verão a página de aviso.'
            : 'Modo de Manutenção DESATIVADO! Landing Page online para todo o público.';

        return response()->json([
            'success' => true,
            'ativo' => $novoStatus === '1',
            'message' => $mensagem,
        ]);
    }

    /**
     * Salva as configurações detalhadas do modo de manutenção
     */
    public function salvar(Request $request)
    {
        $validated = $request->validate([
            'manutencao_titulo' => 'required|string|max:200',
            'manutencao_mensagem' => 'required|string',
            'manutencao_previsao' => 'nullable|string',
            'manutencao_ips_liberados' => 'nullable|string',
            'manutencao_token' => 'required|string|max:100',
            'manutencao_mostrar_redes' => 'nullable|boolean',
            'manutencao_mostrar_whatsapp' => 'nullable|boolean',
        ]);

        CmsSetting::set('manutencao_titulo', $validated['manutencao_titulo'], 'manutencao');
        CmsSetting::set('manutencao_mensagem', $validated['manutencao_mensagem'], 'manutencao');
        CmsSetting::set('manutencao_previsao', $validated['manutencao_previsao'], 'manutencao');
        CmsSetting::set('manutencao_ips_liberados', $validated['manutencao_ips_liberados'] ?? '', 'manutencao');
        CmsSetting::set('manutencao_token', $validated['manutencao_token'], 'manutencao');
        CmsSetting::set('manutencao_mostrar_redes', $request->boolean('manutencao_mostrar_redes') ? '1' : '0', 'manutencao');
        CmsSetting::set('manutencao_mostrar_whatsapp', $request->boolean('manutencao_mostrar_whatsapp') ? '1' : '0', 'manutencao');

        return response()->json([
            'success' => true,
            'message' => 'Configurações do Modo Manutenção atualizadas com sucesso!',
        ]);
    }
}
