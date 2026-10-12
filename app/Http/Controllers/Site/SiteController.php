<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsSetting;
use App\Models\BannerSlide;
use App\Models\Plano;
use App\Models\Faq;
use App\Models\Depoimento;
use App\Models\Parceiro;
use App\Models\SeoConfig;
use App\Models\Agendamento;
use App\Models\ServicoEstetica;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $parceiros = Parceiro::ativo()->ordenado()->get();
        $seo = SeoConfig::paraPagina('home');

        $logoFundoEscuro = CmsSetting::getLogo('escuro');
        $logoFundoClaro = CmsSetting::getLogo('claro');
        $favicon = CmsSetting::get('site_favicon', '/img/favicon.png');
        $cleanFavPath = parse_url($favicon, PHP_URL_PATH) ?: $favicon;
        if (empty($favicon) || !file_exists(public_path(ltrim($cleanFavPath, '/\\')))) {
            $favicon = '/img/favicon.png';
        }


        $heroOverlayCor = CmsSetting::get('hero_overlay_cor', '#0b0f19');
        if (empty($heroOverlayCor) || strtolower($heroOverlayCor) === '#ffffff') {
            $heroOverlayCor = '#0b0f19';
        }
        $heroOpacidade = (int) CmsSetting::get('hero_bg_opacidade', 70);

        $enderecoPartes = array_filter([
            CmsSetting::get('endereco_logradouro'),
            CmsSetting::get('endereco_numero'),
            CmsSetting::get('endereco_bairro'),
            CmsSetting::get('endereco_cidade') ? (CmsSetting::get('endereco_cidade') . ' - ' . CmsSetting::get('endereco_estado')) : null
        ]);
        $enderecoFinal = !empty($enderecoPartes) ? implode(', ', $enderecoPartes) : CmsSetting::get('contato_endereco', 'Tv. Quintino Bocaiúva, 1200 - Nazaré, Belém - PA');
        $horarioFinal = CmsSetting::get('horario_atendimento') ?: CmsSetting::get('contato_horario', 'Central 24 Horas | Plantão de Emergência');

        $configs = [
            'nome' => CmsSetting::get('site_nome', 'Foco 7 - Rastreamento e Segurança'),
            'slogan' => CmsSetting::get('site_slogan', 'Protegendo o que realmente importa há mais de 10 anos'),
            'telefone' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'whatsapp' => CmsSetting::get('contato_whatsapp', '559180510736'),
            'whatsapp_formatado' => CmsSetting::get('contato_whatsapp_formatado', '(91) 8051-0736'),
            'email' => CmsSetting::get('contato_email', 'contato@foco7.com.br'),
            'endereco' => $enderecoFinal,
            'horario' => $horarioFinal,
            'instagram' => CmsSetting::get('social_instagram', ''),
            'facebook' => CmsSetting::get('social_facebook', ''),
            'youtube' => CmsSetting::get('social_youtube', ''),
            'linkedin' => CmsSetting::get('social_linkedin', ''),
            'logo' => $logoFundoEscuro,
            'logo_dark' => $logoFundoClaro,
            'logo_fundo_escuro' => $logoFundoEscuro,
            'logo_fundo_claro' => $logoFundoClaro,
            'favicon' => $favicon,
            'cor_primaria' => CmsSetting::get('cor_primaria', '#d90000'),
            'hero_bg_imagem' => CmsSetting::get('hero_bg_imagem', ''),
            'hero_bg_opacidade' => $heroOpacidade,
            'hero_overlay_cor' => $heroOverlayCor,
            'hero_bg_blur' => (int) CmsSetting::get('hero_bg_blur', 0),
            // Textos das seções gerenciados via CMS
            'secao_sobre_titulo' => CmsSetting::get('secao_sobre_titulo', 'Protegendo seu patrimônio há mais de uma década'),
            'secao_sobre_texto' => CmsSetting::get('secao_sobre_texto', 'A Foco 7 nasceu em Belém com o compromisso de trazer paz de espírito para motoristas, famílias e frotistas.'),
            'secao_riscos_titulo' => CmsSetting::get('secao_riscos_titulo', 'Você sabe o real risco de rodar desprotegido?'),
            'secao_riscos_subtitulo' => CmsSetting::get('secao_riscos_subtitulo', 'Compare a incerteza do prejuízo com a tranquilidade da tecnologia Foco 7.'),
            'secao_tatica_titulo' => CmsSetting::get('secao_tatica_titulo', 'Pronta Resposta Tática 24 Horas em Toda a Região'),
            'secao_tatica_texto' => CmsSetting::get('secao_tatica_texto', 'Equipes armadas e treinadas prontas para agir ao menor sinal de furto ou roubo.'),

            // Seção Ronda Tática (Como Funciona & 4 Etapas)
            'secao_ronda_badge' => CmsSetting::get('secao_ronda_badge', 'Apoio Presencial Especializado'),
            'secao_ronda_titulo' => CmsSetting::get('secao_ronda_titulo', 'Como Funciona a Ronda Tática Foco 7'),
            'secao_ronda_subtitulo' => CmsSetting::get('secao_ronda_subtitulo', 'Tecnologia aliada à ação humana imediata para localizar e recuperar seu patrimônio.'),
            'secao_ronda_etapa1_titulo' => CmsSetting::get('secao_ronda_etapa1_titulo', 'Alarme ou Sinistro'),
            'secao_ronda_etapa1_desc' => CmsSetting::get('secao_ronda_etapa1_desc', 'Você aciona o botão de emergência pelo app ou a central detecta movimentação ou bloqueio não autorizado.'),
            'secao_ronda_etapa2_titulo' => CmsSetting::get('secao_ronda_etapa2_titulo', 'Central 24h Aciona'),
            'secao_ronda_etapa2_desc' => CmsSetting::get('secao_ronda_etapa2_desc', 'Nossos operadores isolam o perímetro, realizam o bloqueio progressivo e geram o mapa da rota do veículo.'),
            'secao_ronda_etapa3_titulo' => CmsSetting::get('secao_ronda_etapa3_titulo', 'Viatura a Caminho'),
            'secao_ronda_etapa3_desc' => CmsSetting::get('secao_ronda_etapa3_desc', 'A viatura ou moto tática mais próxima é despachada imediatamente com as coordenadas exatas do alvo.'),
            'secao_ronda_etapa4_titulo' => CmsSetting::get('secao_ronda_etapa4_titulo', 'Recuperação & Apoio'),
            'secao_ronda_etapa4_desc' => CmsSetting::get('secao_ronda_etapa4_desc', 'A equipe chega ao local, aciona as autoridades competentes e preserva seu veículo até sua devolução.'),

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
            'secao_app_mockup_imagem' => CmsSetting::get('secao_app_mockup_imagem', '/img/app-mockup-foco7.jpg'),

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

            // LGPD e Termos de Privacidade
            'lgpd_ativo' => CmsSetting::get('lgpd_ativo', '1'),
            'lgpd_texto' => CmsSetting::get('lgpd_texto', 'Utilizamos cookies essenciais e tecnologias semelhantes para garantir o funcionamento seguro do nosso sistema, personalizar sua experiência e analisar o tráfego em conformidade com a LGPD (Lei nº 13.709/2018).'),
            'lgpd_btn_aceitar' => CmsSetting::get('lgpd_btn_aceitar', 'Aceitar e Continuar'),
            'lgpd_btn_termos' => CmsSetting::get('lgpd_btn_termos', 'Ler Política de Privacidade'),

            // WhatsApp Flutuante com Box Multicanais
            'whatsapp_box_ativo' => CmsSetting::get('whatsapp_box_ativo', '1'),
            'whatsapp_box_titulo' => CmsSetting::get('whatsapp_box_titulo', 'Central de Atendimento Foco 7'),
            'whatsapp_box_subtitulo' => CmsSetting::get('whatsapp_box_subtitulo', 'Atendimento Humanizado • Selecione o canal desejado:'),

            // Canal 1: Ronda & Emergência 24h
            'whatsapp_canal_1_ativo' => CmsSetting::get('whatsapp_canal_1_ativo', '1'),
            'whatsapp_canal_1_nome' => CmsSetting::get('whatsapp_canal_1_nome', 'Plantão Ronda Tática & Emergência 24h'),
            'whatsapp_canal_1_desc' => CmsSetting::get('whatsapp_canal_1_desc', 'Sinistros, roubo, furto ou assistência imediata'),
            'whatsapp_canal_1_num' => CmsSetting::get('whatsapp_canal_1_num', '559180510736'),
            'whatsapp_canal_1_msg' => CmsSetting::get('whatsapp_canal_1_msg', 'Emergência Foco 7: Preciso de apoio da Ronda Tática ou Central 24h.'),
            'whatsapp_canal_1_icone' => CmsSetting::get('whatsapp_canal_1_icone', 'fa-shield-halved'),

            // Canal 2: Comercial & Planos
            'whatsapp_canal_2_ativo' => CmsSetting::get('whatsapp_canal_2_ativo', '1'),
            'whatsapp_canal_2_nome' => CmsSetting::get('whatsapp_canal_2_nome', 'Comercial & Novos Planos'),
            'whatsapp_canal_2_desc' => CmsSetting::get('whatsapp_canal_2_desc', 'Cotações de carros, motos, frotas e promoções'),
            'whatsapp_canal_2_num' => CmsSetting::get('whatsapp_canal_2_num', '559180510736'),
            'whatsapp_canal_2_msg' => CmsSetting::get('whatsapp_canal_2_msg', 'Olá! Gostaria de uma cotação de rastreamento veicular com a Foco 7.'),
            'whatsapp_canal_2_icone' => CmsSetting::get('whatsapp_canal_2_icone', 'fa-tags'),

            // Canal 3: Suporte Técnico & App
            'whatsapp_canal_3_ativo' => CmsSetting::get('whatsapp_canal_3_ativo', '1'),
            'whatsapp_canal_3_nome' => CmsSetting::get('whatsapp_canal_3_nome', 'Suporte Técnico & Aplicativo'),
            'whatsapp_canal_3_desc' => CmsSetting::get('whatsapp_canal_3_desc', 'Dúvidas sobre o app, bloqueio, login ou sinal GPS'),
            'whatsapp_canal_3_num' => CmsSetting::get('whatsapp_canal_3_num', '559180510736'),
            'whatsapp_canal_3_msg' => CmsSetting::get('whatsapp_canal_3_msg', 'Olá! Preciso de suporte técnico sobre o rastreador / aplicativo.'),
            'whatsapp_canal_3_icone' => CmsSetting::get('whatsapp_canal_3_icone', 'fa-headset'),

            // Canal 4: Estética Automotiva
            'whatsapp_canal_4_ativo' => CmsSetting::get('whatsapp_canal_4_ativo', '1'),
            'whatsapp_canal_4_nome' => CmsSetting::get('whatsapp_canal_4_nome', 'Estética Automotiva & Detalhamento'),
            'whatsapp_canal_4_desc' => CmsSetting::get('whatsapp_canal_4_desc', 'Vitrificação, polimento técnico e higienização'),
            'whatsapp_canal_4_num' => CmsSetting::get('whatsapp_canal_4_num', '559180510736'),
            'whatsapp_canal_4_msg' => CmsSetting::get('whatsapp_canal_4_msg', 'Olá! Gostaria de agendar um serviço de Estética Automotiva.'),
            'whatsapp_canal_4_icone' => CmsSetting::get('whatsapp_canal_4_icone', 'fa-spray-can-sparkles'),

            // Canal 5: Financeiro & 2ª Via
            'whatsapp_canal_5_ativo' => CmsSetting::get('whatsapp_canal_5_ativo', '1'),
            'whatsapp_canal_5_nome' => CmsSetting::get('whatsapp_canal_5_nome', 'Financeiro & 2ª Via de Boletos'),
            'whatsapp_canal_5_desc' => CmsSetting::get('whatsapp_canal_5_desc', 'Segunda via de mensalidades, PIX e notas fiscais'),
            'whatsapp_canal_5_num' => CmsSetting::get('whatsapp_canal_5_num', '559180510736'),
            'whatsapp_canal_5_msg' => CmsSetting::get('whatsapp_canal_5_msg', 'Olá! Preciso de auxílio com minha fatura/mensalidade Foco 7.'),
            'whatsapp_canal_5_icone' => CmsSetting::get('whatsapp_canal_5_icone', 'fa-file-invoice-dollar'),

            // Configurações de Pagamento & Checkout Transparente Efí PRO / MP
            'gateway_padrao' => CmsSetting::get('gateway_padrao', 'efi'),
            'efi_aceita_pix' => CmsSetting::get('efi_aceita_pix', '1') == '1',
            'efi_aceita_boleto' => CmsSetting::get('efi_aceita_boleto', '1') == '1',
            'efi_aceita_cartao' => CmsSetting::get('efi_aceita_cartao', '1') == '1',
            'efi_aceita_carne' => CmsSetting::get('efi_aceita_carne', '1') == '1',
            'carne_max_parcelas' => (int) CmsSetting::get('carne_max_parcelas', '12'),
            'repassar_taxas_cliente' => CmsSetting::get('repassar_taxas_cliente', '0') == '1',
            'pix_desconto' => (float) CmsSetting::get('pix_desconto', '5'),

            // Proteção Google reCAPTCHA v3 & Blindagem Anti-Spam
            'recaptcha_site_key' => CmsSetting::get('recaptcha_site_key', ''),
            'recaptcha_secret_key' => CmsSetting::get('recaptcha_secret_key', ''),
            'recaptcha_ativo' => CmsSetting::get('recaptcha_ativo', '0'),

            // Seção Parceiros & Homologações
            'secao_parceiros_badge' => CmsSetting::get('secao_parceiros_badge', 'Ecossistema & Homologações'),
            'secao_parceiros_titulo' => CmsSetting::get('secao_parceiros_titulo', 'Empresas Parceiras & Tecnologias Homologadas'),
            'secao_parceiros_subtitulo' => CmsSetting::get('secao_parceiros_subtitulo', 'Conectados às principais seguradoras, fabricantes de rastreadores e operadoras de telecomunicação do Brasil.'),
        ];

        return view('site.index', compact('slides', 'planos', 'servicosEstetica', 'faqs', 'depoimentos', 'parceiros', 'seo', 'configs'));
    }

    /**
     * Exibe a página oficial de Termos de Uso e Política de Privacidade (LGPD)
     */
    public function privacidade()
    {
        $titulo = CmsSetting::get('privacidade_titulo', 'Termos de Uso e Política de Privacidade de Dados (LGPD)');
        $conteudo = CmsSetting::get('privacidade_conteudo', '') ?: CmsSetting::get('politica_privacidade', '');
        $dataAtualizacao = CmsSetting::get('privacidade_atualizacao', date('d/m/Y'));

        $configs = [
            'nome' => CmsSetting::get('site_nome', 'Foco 7 Rastreamento'),
            'telefone' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'whatsapp' => CmsSetting::get('contato_whatsapp', '559180510736'),
            'whatsapp_formatado' => CmsSetting::get('whatsapp_formatado', '(91) 8051-0736'),
            'email' => CmsSetting::get('contato_email', 'contato@foco7.com.br'),
            'horario' => CmsSetting::get('horario_atendimento') ?: CmsSetting::get('contato_horario', 'Central 24 Horas | Plantão de Emergência'),
            'instagram' => CmsSetting::get('rede_instagram', ''),
            'facebook' => CmsSetting::get('rede_facebook', ''),
            'logo' => CmsSetting::getLogo('escuro'),
            'logo_dark' => CmsSetting::getLogo('claro'),
            'logo_fundo_escuro' => CmsSetting::getLogo('escuro'),
            'logo_fundo_claro' => CmsSetting::getLogo('claro'),
            'favicon' => CmsSetting::get('site_favicon', '/img/favicon.png'),
            'hero_bg_imagem' => CmsSetting::get('hero_bg_imagem', '/img/og-foco7.jpg'),
            'politica_privacidade' => $conteudo,
            'endereco' => CmsSetting::get('endereco_cidade', 'Belém') . ' - ' . CmsSetting::get('endereco_estado', 'PA'),
        ];

        return view('site.privacidade', compact('titulo', 'conteudo', 'dataAtualizacao', 'configs'));
    }

    /**
     * Processa formulário de contato via AJAX com proteção Google reCAPTCHA v3 e blindagem anti-spam/injeção
     */
    public function contato(Request $request)
    {
        // 1. Blindagem Anti-Bot Camada 1: Honeypot invisível
        if ($request->filled('_foco_hp_website')) {
            // Robô preencheu o campo invisível: retorna sucesso simulado para desarmar o bot sem salvar lixo
            return response()->json([
                'success' => true,
                'message' => 'Obrigado pelo contato! Nossa equipe entrará em contato em instantes.',
            ]);
        }

        // 2. Blindagem Anti-Bot Camada 2: Time-Trap (validação de tempo mínimo de preenchimento)
        $formTime = $request->input('_foco_form_time');
        if ($formTime && is_numeric($formTime)) {
            $tempoDecorrido = time() - (int)$formTime;
            // Se submetido em menos de 1 segundo após o carregamento da página, é disparo robótico
            if ($tempoDecorrido < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Envio muito rápido detectado. Por favor, aguarde alguns segundos e tente novamente.',
                ], 422);
            }
        }

        // 3. Validação dos Dados
        $validated = $request->validate([
            'nome' => 'required|string|max:120',
            'telefone' => 'required|string|max:30',
            'email' => 'nullable|email|max:120',
            'veiculo' => 'nullable|string|max:100',
            'mensagem' => 'required|string|max:1500',
        ], [
            'nome.required' => 'Por favor, informe seu nome completo.',
            'telefone.required' => 'Informe seu telefone ou WhatsApp para retorno.',
            'mensagem.required' => 'Por favor, digite sua mensagem ou dúvida.',
            'email.email' => 'Informe um endereço de e-mail válido.',
        ]);

        // 4. Blindagem Anti-Injeção Camada 3: Detecção de Padrões Maliciosos (SQL Injection e XSS)
        $textosParaVerificar = [
            $validated['nome'],
            $validated['telefone'],
            $validated['email'] ?? '',
            $validated['veiculo'] ?? '',
            $validated['mensagem'],
        ];

        $padroesInjecao = [
            // Padrões de SQL Injection
            '/(union\s+(all\s+)?select)/i',
            '/(select\s+.+\s+from)/i',
            '/(insert\s+into.+values)/i',
            '/(drop\s+(table|database|procedure))/i',
            '/(update\s+.+\s+set)/i',
            '/(delete\s+from)/i',
            '/(information_schema|sys\.tables|pg_stat)/i',
            '#(--\s*$|/\*.*?\*/|;\s*--)#i',
            "/('|\")\s*(or|and)\s*('|\")?\d+('|\")?\s*=\s*('|\")?\d+/i",
            // Padrões de Cross-Site Scripting (XSS)
            '#<script.*?>.*?</script>#is',
            '#javascript\s*:#i',
            '#(onload|onerror|onclick)\s*=#i',
            '#<iframe.*?>#i',
            '#<img.*?src=[\'"]?javascript:#i',
        ];

        foreach ($textosParaVerificar as $texto) {
            foreach ($padroesInjecao as $padrao) {
                if (preg_match($padrao, $texto)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Caracteres ou formatos não permitidos detectados no envio. Por favor, revise sua mensagem.',
                    ], 422);
                }
            }
        }

        // 5. Validação Google reCAPTCHA v3 Camada 4
        $recaptchaAtivo = CmsSetting::get('recaptcha_ativo', '0') === '1';
        $recaptchaSecret = CmsSetting::get('recaptcha_secret_key', '');

        if ($recaptchaAtivo && !empty($recaptchaSecret)) {
            $recaptchaToken = $request->input('g-recaptcha-response');

            if (empty($recaptchaToken)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Falha na verificação de segurança reCAPTCHA. Por favor, atualize a página e tente novamente.',
                ], 422);
            }

            try {
                $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $recaptchaSecret,
                    'response' => $recaptchaToken,
                    'remoteip' => $request->ip(),
                ]);

                $recaptchaResult = $response->json();

                if (!($recaptchaResult['success'] ?? false) || (isset($recaptchaResult['score']) && $recaptchaResult['score'] < 0.3)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A verificação de segurança automática não confirmou a autenticidade humana do envio. Se necessário, entre em contato via WhatsApp.',
                    ], 422);
                }
            } catch (\Exception $e) {
                Log::warning('Falha momentânea na comunicação com a API do Google reCAPTCHA: ' . $e->getMessage());
            }
        }

        // Sanitização final e remoção de tags HTML residuais
        $nomeLimpo = strip_tags(trim($validated['nome']));
        $telefoneLimpo = strip_tags(trim($validated['telefone']));
        $emailLimpo = !empty($validated['email']) ? strip_tags(trim($validated['email'])) : null;
        $veiculoLimpo = !empty($validated['veiculo']) ? strip_tags(trim($validated['veiculo'])) : null;
        $mensagemLimpa = strip_tags(trim($validated['mensagem']));

        // Registra exclusivamente na Central de Notificações dedicada (não polui o calendário de serviços)
        Notificacao::registrar(
            tipo: 'contato',
            titulo: 'Nova Mensagem de Contato do Site',
            mensagem: "{$nomeLimpo} ({$telefoneLimpo}) enviou uma mensagem: \"" . Str::limit($mensagemLimpa, 110) . "\"",
            linkAcao: route('admin.notificacoes.index'),
            dados: [
                'origem' => 'Formulário de Contato da Landing Page (Protegido por reCAPTCHA v3)',
                'cliente' => $nomeLimpo,
                'telefone' => $telefoneLimpo,
                'email' => $emailLimpo ?? 'Não informado',
                'veiculo' => $veiculoLimpo ?? 'Não informado',
                'mensagem' => $mensagemLimpa,
                'ip' => $request->ip(),
            ],
            notificarEmail: true
        );

        return response()->json([
            'success' => true,
            'message' => 'Obrigado pelo contato! Nossa equipe entrará em contato em instantes.',
        ]);
    }

    /**
     * Recebe novos depoimentos e avaliações do site público com estrelas dinâmicas
     */
    public function enviarDepoimento(Request $request)
    {
        $validated = $request->validate([
            'nome_cliente' => 'required|string|max:120',
            'cidade_uf' => 'nullable|string|max:100',
            'veiculo' => 'nullable|string|max:100',
            'estrelas' => 'required|integer|min:1|max:5',
            'texto' => 'required|string|min:10|max:1000',
        ], [
            'nome_cliente.required' => 'Por favor, informe o seu nome.',
            'estrelas.required' => 'Selecione uma nota de 1 a 5 estrelas.',
            'estrelas.min' => 'A nota mínima é de 1 estrela.',
            'estrelas.max' => 'A nota máxima é de 5 estrelas.',
            'texto.required' => 'Conte um pouco sobre a sua experiência com a Foco 7.',
            'texto.min' => 'Seu relato deve conter pelo menos 10 caracteres.',
        ]);

        // Grava como pendente de aprovação (moderação do administrador)
        $depoimento = Depoimento::create([
            'nome_cliente' => trim($validated['nome_cliente']),
            'cidade_uf' => trim($validated['cidade_uf'] ?? 'Belém - PA'),
            'veiculo' => trim($validated['veiculo'] ?? 'Cliente Foco 7'),
            'estrelas' => (int) $validated['estrelas'],
            'texto' => strip_tags(trim($validated['texto'])),
            'ordem' => 99,
            'ativo' => false, // Aguardando aprovação do administrador
        ]);

        // Direciona para a Central de Notificações com link direto para a página de Depoimentos & Moderação
        Notificacao::registrar(
            tipo: 'depoimento',
            titulo: "Novo Depoimento ({$validated['estrelas']} Estrelas) - Pendente de Moderação",
            mensagem: "{$validated['nome_cliente']} enviou uma avaliação de {$validated['estrelas']} estrelas aguardando aprovação: \"" . Str::limit($validated['texto'], 110) . "\"",
            linkAcao: route('admin.depoimentos.index', ['status' => 'pendentes']),
            dados: [
                'origem' => 'Avaliação do Site Público',
                'cliente' => $validated['nome_cliente'],
                'estrelas' => $validated['estrelas'] . ' de 5 estrelas',
                'veiculo' => $validated['veiculo'] ?? 'Não informado',
                'depoimento' => $validated['texto'],
                'status' => 'Pendente de Moderação',
            ],
            notificarEmail: true
        );

        return response()->json([
            'success' => true,
            'message' => 'Muito obrigado pela sua avaliação! Seu relato foi recebido com sucesso e será publicado após moderação da equipe.',
            'depoimento' => $depoimento,
        ]);
    }

    /**
     * Processa solicitação de agendamento/cotação via AJAX
     * EXIGE AUTENTICAÇÃO OBRIGATÓRIA E SUPORTA PAGAMENTO ONLINE OU LOCAL
     */
    public function agendar(Request $request)
    {
        // 1. Verificação rigorosa de autenticação exigida pelo usuário
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'auth_required' => true,
                'message' => 'Para concluir seu agendamento com segurança e acompanhar seu protocolo, é necessário estar conectado à sua conta. Entre ou crie seu cadastro em instantes!',
            ], 401);
        }

        $user = Auth::user();

        $validated = $request->validate([
            'cliente_nome' => 'nullable|string|max:120',
            'cliente_telefone' => 'nullable|string|max:30',
            'cliente_email' => 'nullable|email|max:120',
            'categoria' => 'nullable|string|in:rastreamento,estetica',
            'servico_estetica_id' => 'nullable|exists:servicos_estetica,id',
            'tipo_servico' => 'nullable|string|max:100',
            'plano_id' => 'nullable|exists:planos,id',
            'veiculo_modelo' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'tipo_pagamento' => 'required|string|in:online,local',
            'data_preferencial' => 'nullable|date',
            'observacoes' => 'nullable|string|max:500',
        ], [
            'veiculo_modelo.required' => 'Informe o modelo do veículo (ex: Honda Civic, CG 160).',
            'cidade.required' => 'Informe sua cidade.',
            'tipo_pagamento.required' => 'Escolha como deseja pagar (Online ou no Local).',
            'tipo_pagamento.in' => 'Opção de pagamento inválida.',
        ]);

        $clienteNome = !empty($validated['cliente_nome']) ? $validated['cliente_nome'] : $user->name;
        $clienteTelefone = !empty($validated['cliente_telefone']) ? $validated['cliente_telefone'] : ($user->telefone ?: '(91) 98000-0000');
        $clienteEmail = !empty($validated['cliente_email']) ? $validated['cliente_email'] : $user->email;

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

        $tipoPagamento = $validated['tipo_pagamento'] ?? 'online';

        $agendamento = Agendamento::create([
            'user_id' => $user->id,
            'categoria' => $categoria,
            'servico_estetica_id' => $validated['servico_estetica_id'] ?? null,
            'cliente_nome' => $clienteNome,
            'cliente_telefone' => $clienteTelefone,
            'cliente_email' => $clienteEmail,
            'tipo_servico' => $tipoServico,
            'valor' => $valor,
            'veiculo_modelo' => $validated['veiculo_modelo'],
            'cidade' => $validated['cidade'],
            'tipo_pagamento' => $tipoPagamento,
            'data_hora_inicio' => $dataHora,
            'observacoes' => $validated['observacoes'] ?? 'Solicitação realizada via Landing Page pelo cliente autenticado.',
            'status' => 'pendente',
            'cor_calendario' => $categoria === 'estetica' ? '#1e293b' : '#d90000',
        ]);

        $metodoDescricao = $tipoPagamento === 'online' ? 'com Pagamento Online' : 'com Pagamento no Local';

        // Direciona para a Central de Notificações com alerta push e e-mail
        Notificacao::registrar(
            tipo: 'agendamento',
            titulo: "Novo Agendamento: {$tipoServico}",
            mensagem: "Cliente {$clienteNome} ({$clienteTelefone}) realizou agendamento para " . date('d/m/Y H:i', strtotime($dataHora)) . " ({$metodoDescricao}).",
            linkAcao: route('admin.agendamentos.index'),
            dados: [
                'origem' => 'Agendamento Online da Landing Page',
                'protocolo' => '#' . $agendamento->id,
                'cliente' => $clienteNome,
                'telefone' => $clienteTelefone,
                'email' => $clienteEmail,
                'servico' => $tipoServico,
                'veiculo' => $validated['veiculo_modelo'],
                'cidade' => $validated['cidade'],
                'pagamento' => $tipoPagamento === 'online' ? 'Online (PIX / Cartão)' : 'Presencial (No Local)',
                'data_hora' => date('d/m/Y H:i', strtotime($dataHora)),
            ],
            notificarEmail: true
        );

        $msg = $categoria === 'estetica'
            ? "Solicitação de agendamento de Estética Automotiva ({$metodoDescricao}) confirmada com sucesso! Entraremos em contato via WhatsApp para finalizar a confirmação."
            : "Solicitação de instalação ({$metodoDescricao}) confirmada com sucesso! Protocolo #{$agendamento->id} gerado com sucesso.";

        return response()->json([
            'success' => true,
            'message' => $msg,
            'agendamento_id' => $agendamento->id,
            'tipo_pagamento' => $tipoPagamento,
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

    /**
     * Exibe a página oficial de manutenção (para acesso direto ou teste público)
     */
    public function manutencaoView(Request $request)
    {
        $bg = CmsSetting::get('manutencao_bg');
        if (empty($bg)) {
            $bg = '/img/manutencao-bg.jpg';
        } else {
            $cleanPath = parse_url($bg, PHP_URL_PATH) ?: $bg;
            if (!file_exists(public_path(ltrim($cleanPath, '/\\')))) {
                $bg = '/img/manutencao-bg.jpg';
            }
        }

        $dadosManutencao = [
            'titulo' => CmsSetting::get('manutencao_titulo', 'SITE EM MANUTENÇÃO PROGRAMADA'),
            'mensagem' => CmsSetting::get('manutencao_mensagem', 'Estamos realizando melhorias em nossos servidores de monitoramento.'),
            'previsao' => CmsSetting::get('manutencao_previsao', date('Y-m-d H:i:s', strtotime('+24 hours'))),
            'mostrar_redes' => CmsSetting::get('manutencao_mostrar_redes', '1') == '1',
            'mostrar_whatsapp' => CmsSetting::get('manutencao_mostrar_whatsapp', '1') == '1',
            'whatsapp' => CmsSetting::get('contato_whatsapp', '559180510736'),
            'telefone' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'instagram' => CmsSetting::get('social_instagram', ''),
            'facebook' => CmsSetting::get('social_facebook', ''),
            'youtube' => CmsSetting::get('social_youtube', ''),
            'logo' => CmsSetting::getLogo('escuro'),
            'bg_imagem' => $bg,
            'client_ip' => $request->ip(),
        ];

        return response()->view('site.manutencao', $dadosManutencao, 200);
    }

    /**
     * Rota de contingência para servir arquivos de upload sem quebrar imagens em servidores cPanel/LiteSpeed
     */
    public function servirMidiaUpload(string $path)
    {
        $cleanPath = ltrim(str_replace(['../', '..\\'], '', $path), '/\\');

        $candidatos = [
            public_path('uploads/' . $cleanPath),
            public_path($cleanPath),
            storage_path('app/public/uploads/' . $cleanPath),
            storage_path('app/public/' . $cleanPath),
            base_path('public/uploads/' . $cleanPath),
            base_path('uploads/' . $cleanPath),
        ];

        foreach ($candidatos as $arquivo) {
            if (file_exists($arquivo) && is_file($arquivo)) {
                $mime = @mime_content_type($arquivo) ?: 'application/octet-stream';
                return response()->file($arquivo, [
                    'Content-Type' => $mime,
                    'Cache-Control' => 'public, max-age=31536000',
                ]);
            }
        }

        return $this->gerarImagemFallback($cleanPath);
    }

    /**
     * Rota de contingência para servir arquivos da pasta storage sem depender do symlink
     */
    public function servirMidiaStorage(string $path)
    {
        $cleanPath = ltrim(str_replace(['../', '..\\'], '', $path), '/\\');

        $candidatos = [
            storage_path('app/public/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            public_path('uploads/' . $cleanPath),
            public_path($cleanPath),
            base_path('storage/app/public/' . $cleanPath),
        ];

        foreach ($candidatos as $arquivo) {
            if (file_exists($arquivo) && is_file($arquivo)) {
                $mime = @mime_content_type($arquivo) ?: 'application/octet-stream';
                return response()->file($arquivo, [
                    'Content-Type' => $mime,
                    'Cache-Control' => 'public, max-age=31536000',
                ]);
            }
        }

        return $this->gerarImagemFallback($cleanPath);
    }

    /**
     * Gera fallback visual limpo para evitar espaços com ícone de imagem quebrada no navegador
     */
    protected function gerarImagemFallback(string $path)
    {
        // Se for imagem de avatar ou depoimento: retorna avatar SVG com silhueta moderna
        if (str_contains($path, 'depoimento') || str_contains($path, 'avatar')) {
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">'
                 . '<circle cx="50" cy="50" r="50" fill="#2d3748"/>'
                 . '<circle cx="50" cy="38" r="18" fill="#a0aec0"/>'
                 . '<path d="M 22 84 C 22 64, 34 58, 50 58 C 66 58, 78 64, 78 84 Z" fill="#a0aec0"/>'
                 . '</svg>';
            return response($svg, 200)->header('Content-Type', 'image/svg+xml')->header('Cache-Control', 'public, max-age=86400');
        }

        // Se for banner ou hero: retorna o wallpaper de alta definição do sistema se existir
        if (str_contains($path, 'banner') || str_contains($path, 'hero')) {
            $bgHeroPadrao = public_path('img/manutencao-bg.jpg');
            if (file_exists($bgHeroPadrao)) {
                return response()->file($bgHeroPadrao, ['Content-Type' => 'image/jpeg', 'Cache-Control' => 'public, max-age=86400']);
            }
        }

        // Se for mockup do aplicativo: retorna a imagem padrão do app se existir
        if (str_contains($path, 'app') || str_contains($path, 'mockup')) {
            $appMockup = public_path('img/app-mockup-foco7.jpg');
            if (file_exists($appMockup)) {
                return response()->file($appMockup, ['Content-Type' => 'image/jpeg', 'Cache-Control' => 'public, max-age=86400']);
            }
        }

        // Fallback universal: pixel PNG 1x1 transparente (impede ícone de imagem quebrada)
        $pngTransparente = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
        return response($pngTransparente, 200)->header('Content-Type', 'image/png')->header('Cache-Control', 'public, max-age=86400');
    }
}
