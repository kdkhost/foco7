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
     * Exibe a Landing Page Ãºnica da Foco 7 (Inspirada no SOS Rastreamento)
     */
    public function index()
    {
        // Consultas otimizadas com cache e Ã­ndices
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

        $heroOverlayCor = CmsSetting::get('hero_overlay_cor', '#0b0f19');
        if (empty($heroOverlayCor) || strtolower($heroOverlayCor) === '#ffffff') {
            $heroOverlayCor = '#0b0f19';
        }
        $heroOpacidade = (int) CmsSetting::get('hero_bg_opacidade', 70);

        $configs = [
            'nome' => CmsSetting::get('site_nome', 'Foco 7 - Rastreamento e SeguranÃ§a'),
            'slogan' => CmsSetting::get('site_slogan', 'Protegendo o que realmente importa hÃ¡ mais de 10 anos'),
            'telefone' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'whatsapp' => CmsSetting::get('contato_whatsapp', '559180510736'),
            'whatsapp_formatado' => CmsSetting::get('contato_whatsapp_formatado', '(91) 8051-0736'),
            'email' => CmsSetting::get('contato_email', 'contato@foco7.com.br'),
            'endereco' => CmsSetting::get('contato_endereco', 'Tv. Quintino BocaiÃºva, 1200 - NazarÃ©, BelÃ©m - PA'),
            'horario' => CmsSetting::get('contato_horario', 'Central 24 Horas | PlantÃ£o de EmergÃªncia'),
            'instagram' => CmsSetting::get('social_instagram', ''),
            'facebook' => CmsSetting::get('social_facebook', ''),
            'youtube' => CmsSetting::get('social_youtube', ''),
            'logo' => $logo,
            'logo_dark' => $logoDark,
            'favicon' => $favicon,
            'cor_primaria' => CmsSetting::get('cor_primaria', '#d90000'),
            'hero_bg_imagem' => CmsSetting::get('hero_bg_imagem', ''),
            'hero_bg_opacidade' => $heroOpacidade,
            'hero_overlay_cor' => $heroOverlayCor,
            'hero_bg_blur' => (int) CmsSetting::get('hero_bg_blur', 0),
            // Textos das seÃ§Ãµes gerenciados via CMS
            'secao_sobre_titulo' => CmsSetting::get('secao_sobre_titulo', 'Protegendo seu patrimônio há mais de uma década'),
            'secao_sobre_texto' => CmsSetting::get('secao_sobre_texto', 'A Foco 7 nasceu em Belém com o compromisso de trazer paz de espírito para motoristas, famílias e frotistas.'),
            'secao_riscos_titulo' => CmsSetting::get('secao_riscos_titulo', 'Você sabe o real risco de rodar desprotegido?'),
            'secao_riscos_subtitulo' => CmsSetting::get('secao_riscos_subtitulo', 'Compare a incerteza do prejuízo com a tranquilidade da tecnologia Foco 7.'),
            'secao_tatica_titulo' => CmsSetting::get('secao_tatica_titulo', 'Pronta Resposta Tática 24 Horas em Toda a Região'),
            'secao_tatica_texto' => CmsSetting::get('secao_tatica_texto', 'Equipes armadas e treinadas prontas para agir ao menor sinal de furto ou roubo.'),

            // Seção Aplicativo Mobile
            'secao_app_badge' => CmsSetting::get('secao_app_badge', 'Tecnologia Mobile'),
            'secao_app_titulo' => CmsSetting::get('secao_app_titulo', 'Controle Total do Seu Veículo na Palma da Mão'),
            'secao_app_subtitulo' => CmsSetting::get('secao_app_subtitulo', 'Aplicativo moderno para Android e iOS com telemetria e bloqueio instantâneo.'),
            'secao_app_descricao' => CmsSetting::get('secao_app_descricao', 'Disponível para Android e iOS, o app Foco 7 foi projetado para oferecer rapidez instantânea em situações de risco e relatórios completos para o dia a dia.'),
            'secao_app_rec1_icone' => CmsSetting::get('secao_app_rec1_icone', 'fa-lock'),
            'secao_app_rec1_titulo' => CmsSetting::get('secao_app_rec1_titulo', 'Bloqueio Remoto'),
            'secao_app_rec1_desc' => CmsSetting::get('secao_app_rec1_desc', 'Corte de combustível com 1 toque no celular.'),
            'secao_app_rec2_icone' => CmsSetting::get('secao_app_rec2_icone', 'fa-draw-polygon'),
            'secao_app_rec2_titulo' => CmsSetting::get('secao_app_rec2_titulo', 'Cerca Virtual'),
            'secao_app_rec2_desc' => CmsSetting::get('secao_app_rec2_desc', 'Receba avisos imediatos se o carro sair do perímetro.'),
            'secao_app_rec3_icone' => CmsSetting::get('secao_app_rec3_icone', 'fa-key'),
            'secao_app_rec3_titulo' => CmsSetting::get('secao_app_rec3_titulo', 'Alerta de Ignição'),
            'secao_app_rec3_desc' => CmsSetting::get('secao_app_rec3_desc', 'Notificação push assim que a chave for virada.'),
            'secao_app_rec4_icone' => CmsSetting::get('secao_app_rec4_icone', 'fa-route'),
            'secao_app_rec4_titulo' => CmsSetting::get('secao_app_rec4_titulo', 'Histórico de 90 Dias'),
            'secao_app_rec4_desc' => CmsSetting::get('secao_app_rec4_desc', 'Rotas, paradas e velocidade com mapa 3D.'),
            'secao_app_card_titulo' => CmsSetting::get('secao_app_card_titulo', 'Aplicativo Oficial Foco 7'),
            'secao_app_card_subtitulo' => CmsSetting::get('secao_app_card_subtitulo', 'Baixe gratuitamente na App Store e Google Play Store'),
            'secao_app_link_playstore' => CmsSetting::get('secao_app_link_playstore', 'https://play.google.com/store/apps'),
            'secao_app_link_appstore' => CmsSetting::get('secao_app_link_appstore', 'https://www.apple.com/app-store/'),
            'secao_app_mockup_imagem' => CmsSetting::get('secao_app_mockup_imagem', ''),

            // Seção Estética Automotiva e Diferenciais Técnicos
            'secao_estetica_titulo' => CmsSetting::get('secao_estetica_titulo', 'Estética Automotiva de Alta Performance'),
            'secao_estetica_subtitulo' => CmsSetting::get('secao_estetica_subtitulo', 'Além de proteger com tecnologia, cuidamos do visual, valorização e conservação do seu veículo com padrão premium.'),
            'secao_estetica_descricao' => CmsSetting::get('secao_estetica_descricao', 'Serviços especializados com produtos nobres, vitrificação técnica, polimento com brilho espelhado e higienização profunda.'),
            'secao_estetica_dif1_icone' => CmsSetting::get('secao_estetica_dif1_icone', 'fa-award'),
            'secao_estetica_dif1_titulo' => CmsSetting::get('secao_estetica_dif1_titulo', 'Produtos Nobres'),
            'secao_estetica_dif1_desc' => CmsSetting::get('secao_estetica_dif1_desc', 'Vonixx, CarPro e Menzerna'),
            'secao_estetica_dif2_icone' => CmsSetting::get('secao_estetica_dif2_icone', 'fa-warehouse'),
            'secao_estetica_dif2_titulo' => CmsSetting::get('secao_estetica_dif2_titulo', 'Box Climatizado'),
            'secao_estetica_dif2_desc' => CmsSetting::get('secao_estetica_dif2_desc', 'Sem poeira ou sol direto'),
            'secao_estetica_dif3_icone' => CmsSetting::get('secao_estetica_dif3_icone', 'fa-certificate'),
            'secao_estetica_dif3_titulo' => CmsSetting::get('secao_estetica_dif3_titulo', 'Certificado de Garantia'),
            'secao_estetica_dif3_desc' => CmsSetting::get('secao_estetica_dif3_desc', 'Proteção com laudo técnico'),
            'secao_estetica_dif4_icone' => CmsSetting::get('secao_estetica_dif4_icone', 'fa-user-clock'),
            'secao_estetica_dif4_titulo' => CmsSetting::get('secao_estetica_dif4_titulo', 'Horário Marcado'),
            'secao_estetica_dif4_desc' => CmsSetting::get('secao_estetica_dif4_desc', 'Entrega pontual sem filas'),
        ];

        return view('site.index', compact('slides', 'planos', 'servicosEstetica', 'faqs', 'depoimentos', 'seo', 'configs'));
    }

    /**
     * Processa formulÃ¡rio de contato via AJAX
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
            'mensagem.required' => 'Por favor, digite sua mensagem ou dÃºvida.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Obrigado pelo contato! Nossa equipe entrarÃ¡ em contato em instantes.',
        ]);
    }

    /**
     * Processa solicitaÃ§Ã£o rÃ¡pida de agendamento/cotaÃ§Ã£o via AJAX
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
            'veiculo_modelo.required' => 'Informe o modelo do veÃ­culo (ex: Honda Civic, CG 160).',
            'cidade.required' => 'Informe sua cidade.',
        ]);

        $categoria = $validated['categoria'] ?? (!empty($validated['servico_estetica_id']) ? 'estetica' : 'rastreamento');
        $tipoServico = $validated['tipo_servico'] ?? ($categoria === 'estetica' ? 'EstÃ©tica Automotiva' : 'InstalaÃ§Ã£o de Rastreador');
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
            'observacoes' => $validated['observacoes'] ?? 'SolicitaÃ§Ã£o realizada via Landing Page.',
            'status' => 'pendente',
            'cor_calendario' => $categoria === 'estetica' ? '#1e293b' : '#d90000',
        ]);

        $msg = $categoria === 'estetica'
            ? 'SolicitaÃ§Ã£o de agendamento de EstÃ©tica Automotiva enviada com sucesso! Entraremos em contato via WhatsApp para confirmar o horÃ¡rio.'
            : 'SolicitaÃ§Ã£o de instalaÃ§Ã£o recebida com sucesso! Em atÃ© 30 minutos confirmaremos seu horÃ¡rio.';

        return response()->json([
            'success' => true,
            'message' => $msg,
            'agendamento_id' => $agendamento->id,
        ]);
    }

    /**
     * Permite bypass do modo de manutenÃ§Ã£o via token
     */
    public function bypassManutencao(string $token)
    {
        $tokenOficial = CmsSetting::get('manutencao_token', 'foco7_vip_bypass_2026');

        if ($token === $tokenOficial) {
            $cookie = cookie('foco7_maintenance_bypass', $token, 60 * 24 * 30); // 30 dias
            return redirect()->route('site.home')->withCookie($cookie)->with('success', 'Dispositivo autorizado com sucesso para acesso prÃ©vio.');
        }

        abort(403, 'Token de autorizaÃ§Ã£o invÃ¡lido.');
    }
}
