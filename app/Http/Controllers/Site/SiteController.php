<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsSetting;
use App\Models\BannerSlide;
use App\Models\Plano;
use App\Models\Faq;
use App\Models\Depoimento;
use App\Models\SeoConfig;
use App\Models\Agendamento;
use Illuminate\Support\Facades\Cookie;

class SiteController extends Controller
{
    /**
     * Exibe a Landing Page única da Foco 7 (Inspirada no SOS Rastreamento)
     */
    public function index()
    {
        // Consultas otimizadas com cache e índices
        $slides = BannerSlide::ativo()->tipo('hero_slide')->get();
        $planos = Plano::ativo()->get();
        $faqs = Faq::ativo()->get();
        $depoimentos = Depoimento::ativo()->get();
        $seo = SeoConfig::paraPagina('home');

        $configs = [
            'nome' => CmsSetting::get('site_nome', 'Foco 7 - Rastreamento e Segurança'),
            'slogan' => CmsSetting::get('site_slogan', 'Protegendo o que realmente importa há mais de 10 anos'),
            'telefone' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'whatsapp' => CmsSetting::get('contato_whatsapp', '559180510736'),
            'whatsapp_formatado' => CmsSetting::get('contato_whatsapp_formatado', '(91) 8051-0736'),
            'email' => CmsSetting::get('contato_email', 'contato@foco7.com.br'),
            'endereco' => CmsSetting::get('contato_endereco', 'Tv. Quintino Bocaiúva, 1200 - Nazaré, Belém - PA'),
            'horario' => CmsSetting::get('contato_horario', 'Central 24 Horas | Plantão de Emergência'),
            'instagram' => CmsSetting::get('social_instagram', ''),
            'facebook' => CmsSetting::get('social_facebook', ''),
            'youtube' => CmsSetting::get('social_youtube', ''),
            'logo' => CmsSetting::get('site_logo', '/img/logo-foco7.png'),
            'cor_primaria' => CmsSetting::get('cor_primaria', '#d90000'),
        ];

        return view('site.index', compact('slides', 'planos', 'faqs', 'depoimentos', 'seo', 'configs'));
    }

    /**
     * Processa formulário de contato via AJAX
     */
    public function contato(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:120',
            'telefone' => 'required|string|max:30',
            'email' => 'nullable|email|max:120',
            'veiculo' => 'nullable|string|max:100',
            'mensagem' => 'required|string|max:1000',
        ], [
            'nome.required' => 'Por favor, informe seu nome completo.',
            'telefone.required' => 'Informe seu telefone ou WhatsApp para retorno.',
            'mensagem.required' => 'Por favor, digite sua mensagem ou dúvida.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Obrigado pelo contato! Nossa equipe entrará em contato em instantes.',
        ]);
    }

    /**
     * Processa solicitação rápida de agendamento/cotação via AJAX
     */
    public function agendar(Request $request)
    {
        $validated = $request->validate([
            'cliente_nome' => 'required|string|max:120',
            'cliente_telefone' => 'required|string|max:30',
            'cliente_email' => 'nullable|email|max:120',
            'plano_id' => 'nullable|exists:planos,id',
            'veiculo_modelo' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'data_preferencial' => 'nullable|date',
            'observacoes' => 'nullable|string|max:500',
        ], [
            'cliente_nome.required' => 'Informe seu nome.',
            'cliente_telefone.required' => 'Informe seu WhatsApp.',
            'veiculo_modelo.required' => 'Informe o modelo do veículo (ex: Honda Civic, CG 160).',
            'cidade.required' => 'Informe sua cidade.',
        ]);

        $dataHora = !empty($validated['data_preferencial'])
            ? date('Y-m-d 09:00:00', strtotime($validated['data_preferencial']))
            : date('Y-m-d 09:00:00', strtotime('+2 days'));

        $agendamento = Agendamento::create([
            'cliente_nome' => $validated['cliente_nome'],
            'cliente_telefone' => $validated['cliente_telefone'],
            'cliente_email' => $validated['cliente_email'] ?? null,
            'tipo_servico' => 'Instalação',
            'veiculo_modelo' => $validated['veiculo_modelo'],
            'cidade' => $validated['cidade'],
            'data_hora_inicio' => $dataHora,
            'observacoes' => $validated['observacoes'] ?? 'Solicitação realizada via Landing Page.',
            'status' => 'pendente',
            'cor_calendario' => '#d90000',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de instalação recebida com sucesso! Em até 30 minutos confirmaremos seu horário.',
            'agendamento_id' => $agendamento->id,
        ]);
    }

    /**
     * Permite bypass do modo de manutenção via token
     */
    public function bypassManutencao(string $token)
    {
        $tokenOficial = CmsSetting::get('manutencao_token', 'foco7_vip_bypass_2026');

        if ($token === $tokenOficial) {
            $cookie = cookie('foco7_maintenance_bypass', $token, 60 * 24 * 30); // 30 dias
            return redirect()->route('site.home')->withCookie($cookie)->with('success', 'Dispositivo autorizado com sucesso para acesso prévio.');
        }

        abort(403, 'Token de autorização inválido.');
    }
}
