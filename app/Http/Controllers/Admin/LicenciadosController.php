<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\Licenca;
use App\Models\User;
use App\Services\LicenciamentoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LicenciadosController extends Controller
{
    /**
     * Listagem de Licenciados & Painel de Controle de Licenças
     */
    public function index(Request $request)
    {
        // Auto-criação da tabela se ainda não tiver sido criada no banco de dados remoto
        if (! Schema::hasTable('licencas')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                Log::warning('Auto-migração de licenças: ' . $e->getMessage());
            }
        }

        $busca = $request->input('busca');
        $status = $request->input('status');

        try {
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
                'online_hoje' => Licenca::where('ultimo_check_in', '>=', now()->subDay())->count(),
                'faturamento_estimado' => (float) (Licenca::where('status', 'ativa')->count() * (float) CmsSetting::get('licenciamento_preco_adesao', 1497.00)),
            ];
        } catch (\Throwable $e) {
            Log::error('Erro ao consultar licenças: ' . $e->getMessage());
            $licencas = new LengthAwarePaginator([], 0, 15);
            $kpis = [
                'total' => 0,
                'ativas' => 0,
                'pendentes' => 0,
                'suspensas' => 0,
                'downloads' => 0,
                'online_hoje' => 0,
                'faturamento_estimado' => 0.0,
            ];
        }

        $parametros = LicenciamentoService::obterParametrosPrograma();
        $usuarios = User::orderBy('name')->get();
        $instanciasExtras = \App\Services\EvolutionApiService::getInstanciasExtras();

        return view('admin.licenciados.index', compact('licencas', 'kpis', 'parametros', 'usuarios', 'busca', 'status', 'instanciasExtras'));
    }

    /**
     * Retorna os dados de uma licença em JSON para modal de edição via AJAX
     */
    public function obter($id)
    {
        $licenca = Licenca::with('user')->findOrFail($id);
        $licenca->append([
            'status_badge',
            'modo_ativacao',
            'modo_ativacao_badge',
            'valor_adesao',
            'valor_adesao_formatado',
            'status_adesao',
            'status_adesao_badge',
            'valor_mensalidade',
            'valor_mensalidade_formatado',
            'status_mensalidade',
            'status_mensalidade_badge',
            'proximo_vencimento',
        ]);

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

        // Tratamento dos Valores Monetários
        $valorAdesao = null;
        if ($request->filled('valor_adesao')) {
            $limpo = str_replace(['R$', ' ', '.'], '', $request->input('valor_adesao'));
            $limpo = str_replace(',', '.', $limpo);
            $valorAdesao = is_numeric($limpo) ? (float) $limpo : 0.00;
        }

        $valorMensalidade = null;
        if ($request->filled('valor_mensalidade')) {
            $limpo = str_replace(['R$', ' ', '.'], '', $request->input('valor_mensalidade'));
            $limpo = str_replace(',', '.', $limpo);
            $valorMensalidade = is_numeric($limpo) ? (float) $limpo : 0.00;
        }

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

            if ($request->filled('modo_ativacao')) {
                $licenca->modo_ativacao = $request->input('modo_ativacao');
            }
            if ($valorAdesao !== null) {
                $licenca->valor_adesao = $valorAdesao;
            }
            if ($request->filled('status_adesao')) {
                $licenca->status_adesao = $request->input('status_adesao');
            }
            if ($valorMensalidade !== null) {
                $licenca->valor_mensalidade = $valorMensalidade;
            }
            if ($request->filled('status_mensalidade')) {
                $licenca->status_mensalidade = $request->input('status_mensalidade');
            }
            if ($request->filled('dia_vencimento')) {
                $licenca->dia_vencimento = (int) $request->input('dia_vencimento');
            }
            if ($request->filled('proximo_vencimento')) {
                $licenca->proximo_vencimento = $request->input('proximo_vencimento');
            }
            if ($request->filled('data_expiracao')) {
                $licenca->data_expiracao = $request->input('data_expiracao');
            }

            if ($licenca->status === 'ativa' && ! $licenca->data_ativacao) {
                $licenca->data_ativacao = now();
            }

            $licenca->save();

            return response()->json([
                'success' => true,
                'message' => 'Licença e parâmetros financeiros atualizados com sucesso!',
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
            'modo_ativacao' => $request->input('modo_ativacao', 'automatica'),
            'valor_adesao' => $valorAdesao,
            'status_adesao' => $request->input('status_adesao', 'pago'),
            'valor_mensalidade' => $valorMensalidade,
            'status_mensalidade' => $request->input('status_mensalidade', 'em_dia'),
            'dia_vencimento' => $request->filled('dia_vencimento') ? (int) $request->input('dia_vencimento') : 10,
            'proximo_vencimento' => $request->input('proximo_vencimento'),
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
     * Alterna rapidamente entre Ativa e Suspensa com 1 clique (Toggle)
     */
    public function toggleStatus($id)
    {
        $licenca = Licenca::findOrFail($id);
        $novoStatus = $licenca->status === 'ativa' ? 'suspensa' : 'ativa';

        if ($novoStatus === 'ativa') {
            $licenca->ativar('Ativação rápida via painel');
        } else {
            $licenca->desativar('suspensa', 'Suspensão rápida via painel');
        }

        return response()->json([
            'success' => true,
            'status' => $licenca->status,
            'status_badge' => $licenca->status_badge,
            'message' => $licenca->status === 'ativa' ? 'Licença ativada com sucesso!' : 'Licença suspensa com sucesso! Servidor remoto bloqueado.',
        ]);
    }

    /**
     * Alterna o modo de ativação entre 'automatica' e 'manual' com 1 clique
     */
    public function toggleModo($id)
    {
        $licenca = Licenca::findOrFail($id);
        $novoModo = $licenca->modo_ativacao === 'automatica' ? 'manual' : 'automatica';
        $licenca->modo_ativacao = $novoModo;
        $licenca->save();

        return response()->json([
            'success' => true,
            'modo' => $novoModo,
            'modo_badge' => $licenca->modo_ativacao_badge,
            'message' => $novoModo === 'automatica'
                ? 'Modo alterado para Automático (ativa e desativa de acordo com os pagamentos).'
                : 'Modo alterado para Manual (requer aprovação expressa do administrador).',
        ]);
    }

    /**
     * Gera cobrança PIX para Taxa de Adesão da licença
     */
    public function gerarPixAdesao($id)
    {
        $licenca = Licenca::with('user')->findOrFail($id);
        $dados = LicenciamentoService::gerarCobrancaPixAdesao($licenca);

        return response()->json($dados);
    }

    /**
     * Gera cobrança PIX para Mensalidade de Suporte da licença
     */
    public function gerarPixMensalidade($id)
    {
        $licenca = Licenca::with('user')->findOrFail($id);
        $dados = LicenciamentoService::gerarCobrancaPixMensalidade($licenca);

        return response()->json($dados);
    }

    /**
     * Confirmação manual de pagamento (Adesão ou Mensalidade)
     */
    public function confirmarPagamentoManual($id, Request $request)
    {
        $licenca = Licenca::findOrFail($id);
        $tipo = $request->input('tipo', 'adesao'); // 'adesao' ou 'mensalidade'

        if ($tipo === 'mensalidade') {
            $licenca->status_mensalidade = 'em_dia';
            $licenca->proximo_vencimento = now()->addDays(30)->format('Y-m-d');
            if ($licenca->status === 'suspensa') {
                $licenca->ativar('Mensalidade quitada manualmente pelo administrador');
            } else {
                $licenca->save();
            }

            // Dá baixa em faturas pendentes de mensalidade
            Fatura::where('descricao', 'like', "%Mensalidade%Licenca {$licenca->chave_licenca}%")
                ->where('status', 'pendente')
                ->update(['status' => 'pago', 'data_pagamento' => now()]);

            $msg = 'Pagamento da mensalidade confirmado! Vigência prorrogada por +30 dias.';
        } else {
            $licenca->status_adesao = 'pago';
            $licenca->ativar('Taxa de adesão confirmada manualmente pelo administrador');

            // Dá baixa em faturas pendentes de adesão
            Fatura::where('descricao', 'like', "%Adesao%Licenca {$licenca->chave_licenca}%")
                ->where('status', 'pendente')
                ->update(['status' => 'pago', 'data_pagamento' => now()]);

            $msg = 'Taxa de adesão confirmada com sucesso! Licença liberada.';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'status' => $licenca->status,
            'status_badge' => $licenca->status_badge,
            'status_adesao_badge' => $licenca->status_adesao_badge,
            'status_mensalidade_badge' => $licenca->status_mensalidade_badge,
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
        // 1. Cabeçalho & Visibilidade
        CmsSetting::set('licenciamento_ativo', $request->has('licenciamento_ativo') ? '1' : '0', 'licenciamento');
        CmsSetting::set('licenciamento_badge', trim($request->input('licenciamento_badge', 'OPORTUNIDADE DE NEGÓCIO • WHITE LABEL')), 'licenciamento');
        CmsSetting::set('licenciamento_titulo', trim($request->input('licenciamento_titulo', 'Tenha Sua Própria Empresa de Rastreamento Veicular')), 'licenciamento');
        CmsSetting::set('licenciamento_subtitulo', trim($request->input('licenciamento_subtitulo', '')), 'licenciamento');

        // 2. Valores Monetários & Parâmetros Base
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
        CmsSetting::set('licenciamento_modo_padrao', $request->input('licenciamento_modo_padrao', 'automatica'), 'licenciamento');
        CmsSetting::set('licenciamento_suspensao_automatica', $request->has('licenciamento_suspensao_automatica') ? '1' : '0', 'licenciamento');

        // 3. Os 4 Pilares de Benefícios Superiores
        CmsSetting::set('licenciamento_pilar1_icone', trim($request->input('licenciamento_pilar1_icone', 'fa-server')), 'licenciamento');
        CmsSetting::set('licenciamento_pilar1_titulo', trim($request->input('licenciamento_pilar1_titulo', 'Servidor Próprio')), 'licenciamento');
        CmsSetting::set('licenciamento_pilar1_desc', trim($request->input('licenciamento_pilar1_desc', '')), 'licenciamento');

        CmsSetting::set('licenciamento_pilar2_icone', trim($request->input('licenciamento_pilar2_icone', 'fa-paint-roller')), 'licenciamento');
        CmsSetting::set('licenciamento_pilar2_titulo', trim($request->input('licenciamento_pilar2_titulo', '100% White Label')), 'licenciamento');
        CmsSetting::set('licenciamento_pilar2_desc', trim($request->input('licenciamento_pilar2_desc', '')), 'licenciamento');

        CmsSetting::set('licenciamento_pilar3_icone', trim($request->input('licenciamento_pilar3_icone', 'fa-hand-holding-dollar')), 'licenciamento');
        CmsSetting::set('licenciamento_pilar3_titulo', trim($request->input('licenciamento_pilar3_titulo', 'Lucro 100% Seu')), 'licenciamento');
        CmsSetting::set('licenciamento_pilar3_desc', trim($request->input('licenciamento_pilar3_desc', '')), 'licenciamento');

        CmsSetting::set('licenciamento_pilar4_icone', trim($request->input('licenciamento_pilar4_icone', 'fa-magic')), 'licenciamento');
        CmsSetting::set('licenciamento_pilar4_titulo', trim($request->input('licenciamento_pilar4_titulo', 'Instalador Web Fácil')), 'licenciamento');
        CmsSetting::set('licenciamento_pilar4_desc', trim($request->input('licenciamento_pilar4_desc', '')), 'licenciamento');

        // 4. Card Principal da Oferta & 6 Diferenciais
        CmsSetting::set('licenciamento_card_badge', trim($request->input('licenciamento_card_badge', 'PACOTE COMPLETO DE DISTRIBUIÇÃO & WHITE LABEL')), 'licenciamento');
        CmsSetting::set('licenciamento_card_titulo', trim($request->input('licenciamento_card_titulo', 'Software FOCO 7 Completo para o Seu Próprio Servidor')), 'licenciamento');
        CmsSetting::set('licenciamento_card_desc', trim($request->input('licenciamento_card_desc', '')), 'licenciamento');

        CmsSetting::set('licenciamento_feat1_icone', trim($request->input('licenciamento_feat1_icone', 'fa-server')), 'licenciamento');
        CmsSetting::set('licenciamento_feat1_titulo', trim($request->input('licenciamento_feat1_titulo', 'Servidor & Dados Próprios')), 'licenciamento');
        CmsSetting::set('licenciamento_feat1_desc', trim($request->input('licenciamento_feat1_desc', '')), 'licenciamento');

        CmsSetting::set('licenciamento_feat2_icone', trim($request->input('licenciamento_feat2_icone', 'fa-paint-roller')), 'licenciamento');
        CmsSetting::set('licenciamento_feat2_titulo', trim($request->input('licenciamento_feat2_titulo', '100% White Label')), 'licenciamento');
        CmsSetting::set('licenciamento_feat2_desc', trim($request->input('licenciamento_feat2_desc', '')), 'licenciamento');

        CmsSetting::set('licenciamento_feat3_icone', trim($request->input('licenciamento_feat3_icone', 'fa-whatsapp')), 'licenciamento');
        CmsSetting::set('licenciamento_feat3_titulo', trim($request->input('licenciamento_feat3_titulo', 'WhatsApp Evolution Go')), 'licenciamento');
        CmsSetting::set('licenciamento_feat3_desc', trim($request->input('licenciamento_feat3_desc', '')), 'licenciamento');

        CmsSetting::set('licenciamento_feat4_icone', trim($request->input('licenciamento_feat4_icone', 'fa-qrcode')), 'licenciamento');
        CmsSetting::set('licenciamento_feat4_titulo', trim($request->input('licenciamento_feat4_titulo', 'Gateways Integrados')), 'licenciamento');
        CmsSetting::set('licenciamento_feat4_desc', trim($request->input('licenciamento_feat4_desc', '')), 'licenciamento');

        CmsSetting::set('licenciamento_feat5_icone', trim($request->input('licenciamento_feat5_icone', 'fa-magic')), 'licenciamento');
        CmsSetting::set('licenciamento_feat5_titulo', trim($request->input('licenciamento_feat5_titulo', 'Instalador Gráfico /instalar')), 'licenciamento');
        CmsSetting::set('licenciamento_feat5_desc', trim($request->input('licenciamento_feat5_desc', '')), 'licenciamento');

        CmsSetting::set('licenciamento_feat6_icone', trim($request->input('licenciamento_feat6_icone', 'fa-shield-alt')), 'licenciamento');
        CmsSetting::set('licenciamento_feat6_titulo', trim($request->input('licenciamento_feat6_titulo', 'Sem Royalties por Veículo')), 'licenciamento');
        CmsSetting::set('licenciamento_feat6_desc', trim($request->input('licenciamento_feat6_desc', '')), 'licenciamento');

        // 5. Box de Conversão, Preços e Botões
        CmsSetting::set('licenciamento_cta_badge', trim($request->input('licenciamento_cta_badge', 'CONDIÇÃO ESPECIAL DE DISTRIBUIÇÃO')), 'licenciamento');
        CmsSetting::set('licenciamento_cta_label', trim($request->input('licenciamento_cta_label', 'TAXA DE ADESÃO / IMPLANTAÇÃO')), 'licenciamento');
        CmsSetting::set('licenciamento_cta_selo', trim($request->input('licenciamento_cta_selo', 'Pagamento Único • Sem Royalties por Placa')), 'licenciamento');
        CmsSetting::set('licenciamento_cta_subtexto', trim($request->input('licenciamento_cta_subtexto', 'Ativação imediata da licença • Pacote pronto para deploy')), 'licenciamento');
        CmsSetting::set('licenciamento_btn_whatsapp_texto', trim($request->input('licenciamento_btn_whatsapp_texto', 'Quero Ser Licenciado Agora')), 'licenciamento');
        CmsSetting::set('licenciamento_whatsapp_msg', trim($request->input('licenciamento_whatsapp_msg', 'Olá! Gostaria de mais detalhes e adquirir a licença White Label do sistema FOCO 7 para instalar no meu próprio servidor.')), 'licenciamento');
        CmsSetting::set('licenciamento_btn_portal_texto', trim($request->input('licenciamento_btn_portal_texto', 'Já sou Licenciado / Acessar Downloads')), 'licenciamento');
        CmsSetting::set('licenciamento_btn_portal_url', trim($request->input('licenciamento_btn_portal_url', '/login')), 'licenciamento');

        // 6. Micro Trust Badges de Rodapé
        CmsSetting::set('licenciamento_trust1_icone', trim($request->input('licenciamento_trust1_icone', 'fa-bolt')), 'licenciamento');
        CmsSetting::set('licenciamento_trust1_texto', trim($request->input('licenciamento_trust1_texto', 'Ativação Imediata')), 'licenciamento');

        CmsSetting::set('licenciamento_trust2_icone', trim($request->input('licenciamento_trust2_icone', 'fa-code')), 'licenciamento');
        CmsSetting::set('licenciamento_trust2_texto', trim($request->input('licenciamento_trust2_texto', 'Código Homologado')), 'licenciamento');

        CmsSetting::set('licenciamento_trust3_icone', trim($request->input('licenciamento_trust3_icone', 'fa-headset')), 'licenciamento');
        CmsSetting::set('licenciamento_trust3_texto', trim($request->input('licenciamento_trust3_texto', 'Suporte Técnico')), 'licenciamento');

        return response()->json([
            'success' => true,
            'message' => 'Todas as configurações da Seção de Licenciamento foram salvas com sucesso!',
        ]);
    }

    /**
     * Alterna rapidamente a visibilidade do Programa de Licenciados na Landing Page
     */
    public function togglePrograma(Request $request)
    {
        $atual = CmsSetting::get('licenciamento_ativo', '1') == '1';
        $novo = ! $atual;
        CmsSetting::set('licenciamento_ativo', $novo ? '1' : '0', 'licenciamento');

        return response()->json([
            'success' => true,
            'ativo' => $novo,
            'message' => $novo ? 'Módulo de Licenciamento ativado na Landing Page oficial!' : 'Módulo de Licenciamento desativado na Landing Page.',
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

