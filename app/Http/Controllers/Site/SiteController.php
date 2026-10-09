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
use App\Models\ServicoEstetica;
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
        $servicosEstetica = ServicoEstetica::where('ativo', true)->orderBy('ordem')->get();
        $faqs = Faq::ativo()->get();
        $depoimentos = Depoimento::ativo()->get();
        $seo = SeoConfig::paraPagina('home');

        $logo = CmsSetting::get('site_logo', '/img/logo-foco7.png');
        $cleanLogoPath = parse_url($logo, PHP_URL_PATH) ?: $logo;
        if (empty($logo) || !file_exists(public_path(ltrim($cleanLogoPath, '/')))) {
            $logo = '/img/logo-foco7.png';
        }

        $favicon = CmsSetting::get('site_favicon', '/img/favicon.png');
        $cleanFavPath = parse_url($favicon, PHP_URL_PATH) ?: $favicon;
        if (empty($favicon) || !file_exists(public_path(ltrim($cleanFavPath, '/')))) {
            $favicon = '/img/favicon.png';
        }

        $logoDark = '/img/logo-foco7-dark.png';
        if (!file_exists(public_path('img/logo-foco7-dark.png'))) {
            $logoDark = $logo;
        }

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
            'logo' => $logo,
            'logo_dark' => $logoDark,
            'favicon' => $favicon,
            'cor_primaria' => CmsSetting::get('cor_primaria', '#d90000'),
            'hero_bg_imagem' => CmsSetting::get('hero_bg_imagem', ''),
            'hero_bg_opacidade' => (int) CmsSetting::get('hero_bg_opacidade', 85),
            'hero_overlay_cor' => CmsSetting::get('hero_overlay_cor', '#ffffff'),
            'hero_bg_blur' => (int) CmsSetting::get('hero_bg_blur', 0),
            // Textos das seções gerenciados via CMS
            'secao_sobre_titulo' => CmsSetting::get('secao_sobre_titulo', 'Protegendo seu patrimônio há mais de uma década'),
            'secao_sobre_texto' => CmsSetting::get('secao_sobre_texto', 'A Foco 7 nasceu em Belém com o compromisso de trazer paz de espírito para motoristas, famílias e frotistas.'),
            'secao_riscos_titulo' => CmsSetting::get('secao_riscos_titulo', 'Você sabe o real risco de rodar desprotegido?'),
            'secao_riscos_subtitulo' => CmsSetting::get('secao_riscos_subtitulo', 'Compare a incerteza do prejuízo com a tranquilidade da tecnologia Foco 7.'),
            'secao_tatica_titulo' => CmsSetting::get('secao_tatica_titulo', 'Pronta Resposta Tática 24 Horas em Toda a Região'),
            'secao_tatica_texto' => CmsSetting::get('secao_tatica_texto', 'Equipes armadas e treinadas prontas para agir ao menor sinal de furto ou roubo.'),
            'secao_app_titulo' => CmsSetting::get('secao_app_titulo', 'Controle Completo na Palma da Sua Mão'),
            'secao_app_subtitulo' => CmsSetting::get('secao_app_subtitulo', 'Aplicativo moderno para Android e iOS com telemetria e bloqueio instantâneo.'),
            'secao_estetica_titulo' => CmsSetting::get('secao_estetica_titulo', 'Estética Automotiva de Alta Performance'),
            'secao_estetica_subtitulo' => CmsSetting::get('secao_estetica_subtitulo', 'Além de proteger com tecnologia, cuidamos do visual, valorização e conservação do seu veículo com padrão premium.'),
            'secao_estetica_descricao' => CmsSetting::get('secao_estetica_descricao', 'Serviços especializados com produtos nobres, vitrificação técnica, polimento com brilho espelhado e higienização profunda.'),
        ];

        return view('site.index', compact('slides', 'planos', 'servicosEstetica', 'faqs', 'depoimentos', 'seo', 'configs'));
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
            'categoria' => 'nullable|string|in:rastreamento,estetica',
            'servico_estetica_id' => 'nullable|exists:servicos_estetica,id',
            'tipo_servico' => 'nullable|string|max:100',
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

        $categoria = $validated['categoria'] ?? (!empty($validated['servico_estetica_id']) ? 'estetica' : 'rastreamento');
        $tipoServico = $validated['tipo_servico'] ?? ($categoria === 'estetica' ? 'Estética Automotiva' : 'Instalação de Rastreador');
        $valor = null;

        if ($categoria === 'estetica' && !empty($validated['servico_estetica_id'])) {
            $servico = ServicoEstetica::find($validated['servico_estetica_id']);
            if ($servico) {
                $tipoServico = $servico->nome;
                $valor = $servico->preco;
            }
        }

        $dataHora = !empty($validated['data_preferencial'])
            ? date('Y-m-d 09:00:00', strtotime($validated['data_preferencial']))
            : date('Y-m-d 09:00:00', strtotime('+2 days'));

        $agendamento = Agendamento::create([
            'categoria' => $categoria,
            'servico_estetica_id' => $validated['servico_estetica_id'] ?? null,
            'cliente_nome' => $validated['cliente_nome'],
            'cliente_telefone' => $validated['cliente_telefone'],
            'cliente_email' => $validated['cliente_email'] ?? null,
            'tipo_servico' => $tipoServico,
            'valor' => $valor,
            'veiculo_modelo' => $validated['veiculo_modelo'],
            'cidade' => $validated['cidade'],
            'data_hora_inicio' => $dataHora,
            'observacoes' => $validated['observacoes'] ?? 'Solicitação realizada via Landing Page.',
            'status' => 'pendente',
            'cor_calendario' => $categoria === 'estetica' ? '#6f42c1' : '#d90000',
        ]);

        $msg = $categoria === 'estetica'
            ? 'Solicitação de agendamento de Estética Automotiva enviada com sucesso! Entraremos em contato via WhatsApp para confirmar o horário.'
            : 'Solicitação de instalação recebida com sucesso! Em até 30 minutos confirmaremos seu horário.';

        return response()->json([
            'success' => true,
            'message' => $msg,
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
