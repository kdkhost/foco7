<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CmsSetting;
use App\Models\BannerSlide;
use App\Models\Plano;
use App\Models\Agendamento;
use App\Models\Fatura;
use App\Models\Faq;
use App\Models\Depoimento;
use App\Models\SeoConfig;
use App\Models\ServicoEstetica;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuário Administrador Master
        $admin = User::firstOrCreate(
            ['email' => 'admin@foco7.com.br'],
            [
                'name' => 'Diretoria Foco 7',
                'password' => Hash::make('foco7@2026'),
                'role' => 'admin',
                'telefone' => '(91) 98111-7777',
                'status' => 'ativo',
            ]
        );

        // 2. Cliente Demo
        $cliente = User::firstOrCreate(
            ['email' => 'cliente@foco7.com.br'],
            [
                'name' => 'Marcelo Silva Oliveira',
                'password' => Hash::make('cliente@2026'),
                'role' => 'cliente',
                'cpf_cnpj' => '123.456.789-00',
                'telefone' => '(91) 99876-5432',
                'cep' => '66000-000',
                'endereco' => 'Av. Nazaré',
                'numero' => '700',
                'bairro' => 'Nazaré',
                'cidade' => 'Belém',
                'estado' => 'PA',
                'status' => 'ativo',
            ]
        );

        // 3. Configurações CMS (Identidade, Contato, Cores e Manutenção)
        $configs = [
            // Geral e Identidade
            ['chave' => 'site_nome', 'valor' => 'Foco 7 - Rastreamento e Segurança', 'grupo' => 'geral', 'tipo' => 'text', 'descricao' => 'Nome da empresa'],
            ['chave' => 'site_slogan', 'valor' => 'Protegendo o que realmente importa há mais de 10 anos', 'grupo' => 'geral', 'tipo' => 'text', 'descricao' => 'Slogan principal'],
            ['chave' => 'site_logo', 'valor' => '/img/logo-foco7.png', 'grupo' => 'geral', 'tipo' => 'image', 'descricao' => 'Caminho do logotipo oficial'],
            ['chave' => 'site_favicon', 'valor' => '/img/favicon.png', 'grupo' => 'geral', 'tipo' => 'image', 'descricao' => 'Favicon do site'],
            ['chave' => 'cor_primaria', 'valor' => '#d90000', 'grupo' => 'aparencia', 'tipo' => 'text', 'descricao' => 'Cor primária Foco 7'],
            ['chave' => 'cor_secundaria', 'valor' => '#111111', 'grupo' => 'aparencia', 'tipo' => 'text', 'descricao' => 'Cor de fundo escura'],
            ['chave' => 'cor_destaque', 'valor' => '#e60000', 'grupo' => 'aparencia', 'tipo' => 'text', 'descricao' => 'Cor de botões e destaques'],

            // Contato e Atendimento
            ['chave' => 'contato_telefone', 'valor' => '(91) 8051-0736', 'grupo' => 'contato', 'tipo' => 'text', 'descricao' => 'Telefone de contato da central'],
            ['chave' => 'contato_whatsapp', 'valor' => '559180510736', 'grupo' => 'contato', 'tipo' => 'text', 'descricao' => 'WhatsApp com DDI e DDD (somente dígitos)'],
            ['chave' => 'contato_whatsapp_formatado', 'valor' => '(91) 8051-0736', 'grupo' => 'contato', 'tipo' => 'text', 'descricao' => 'WhatsApp exibido no cabeçalho'],
            ['chave' => 'contato_email', 'valor' => 'contato@foco7.com.br', 'grupo' => 'contato', 'tipo' => 'text', 'descricao' => 'E-mail comercial'],
            ['chave' => 'contato_horario', 'valor' => 'Central 24 Horas | Plantão de Emergência', 'grupo' => 'contato', 'tipo' => 'text', 'descricao' => 'Horário exibido no topo'],
            ['chave' => 'contato_endereco', 'valor' => 'Tv. Quintino Bocaiúva, 1200 - Nazaré, Belém - PA', 'grupo' => 'contato', 'tipo' => 'text', 'descricao' => 'Endereço da sede'],

            // Redes Sociais
            ['chave' => 'social_instagram', 'valor' => 'https://instagram.com/foco7rastreamento', 'grupo' => 'redes_sociais', 'tipo' => 'text', 'descricao' => 'Link do Instagram'],
            ['chave' => 'social_facebook', 'valor' => 'https://facebook.com/foco7rastreamento', 'grupo' => 'redes_sociais', 'tipo' => 'text', 'descricao' => 'Link do Facebook'],
            ['chave' => 'social_youtube', 'valor' => 'https://youtube.com/@foco7rastreamento', 'grupo' => 'redes_sociais', 'tipo' => 'text', 'descricao' => 'Link do YouTube'],

            // Modo Manutenção Avançado
            ['chave' => 'manutencao_ativa', 'valor' => '0', 'grupo' => 'manutencao', 'tipo' => 'boolean', 'descricao' => 'Status do Modo Manutenção (1=Ativo, 0=Inativo)'],
            ['chave' => 'manutencao_titulo', 'valor' => 'SITE EM MANUTENÇÃO PROGRAMADA', 'grupo' => 'manutencao', 'tipo' => 'text', 'descricao' => 'Título da página de manutenção'],
            ['chave' => 'manutencao_mensagem', 'valor' => 'Estamos aplicando melhorias em nossos servidores de monitoramento para proporcionar ainda mais velocidade e precisão. Nossa Central 24h continua ativa sem interrupções.', 'grupo' => 'manutencao', 'tipo' => 'textarea', 'descricao' => 'Mensagem exibida ao público'],
            ['chave' => 'manutencao_previsao', 'valor' => date('Y-m-d H:i:s', strtotime('+48 hours')), 'grupo' => 'manutencao', 'tipo' => 'text', 'descricao' => 'Data e hora limite para contagem regressiva'],
            ['chave' => 'manutencao_ips_liberados', 'valor' => '127.0.0.1,::1,177.192.4.34', 'grupo' => 'manutencao', 'tipo' => 'textarea', 'descricao' => 'IPs autorizados separados por vírgula'],
            ['chave' => 'manutencao_token', 'valor' => 'foco7_vip_bypass_2026', 'grupo' => 'manutencao', 'tipo' => 'text', 'descricao' => 'Token de liberação direta de dispositivo'],
            ['chave' => 'manutencao_mostrar_redes', 'valor' => '1', 'grupo' => 'manutencao', 'tipo' => 'boolean', 'descricao' => 'Exibir links sociais na manutenção'],
            ['chave' => 'manutencao_mostrar_whatsapp', 'valor' => '1', 'grupo' => 'manutencao', 'tipo' => 'boolean', 'descricao' => 'Exibir botão do WhatsApp na manutenção'],

            // Gateways de Pagamento
            ['chave' => 'mercadopago_ativo', 'valor' => '1', 'grupo' => 'gateways', 'tipo' => 'boolean', 'descricao' => 'Ativar Mercado Pago Checkout'],
            ['chave' => 'mercadopago_sandbox', 'valor' => '1', 'grupo' => 'gateways', 'tipo' => 'boolean', 'descricao' => 'Ambiente Sandbox Mercado Pago'],
            ['chave' => 'efi_ativo', 'valor' => '1', 'grupo' => 'gateways', 'tipo' => 'boolean', 'descricao' => 'Ativar Efí PRO (Gerencianet)'],
            ['chave' => 'efi_sandbox', 'valor' => '1', 'grupo' => 'gateways', 'tipo' => 'boolean', 'descricao' => 'Ambiente Sandbox Efí PRO'],

            // Textos das Seções do Site (Gerenciamento Total via CMS)
            ['chave' => 'secao_sobre_titulo', 'valor' => 'Mais de 10 Anos de Autoridade em Segurança Veicular', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Título da seção Sobre'],
            ['chave' => 'secao_sobre_subtitulo', 'valor' => 'A Foco 7 nasceu com a missão de transformar tecnologia de rastreamento em tranquilidade real.', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Subtítulo da seção Sobre'],
            ['chave' => 'secao_sobre_texto', 'valor' => 'Com sede própria e infraestrutura de ponta, monitoramos milhares de veículos em tempo real, combinando telemetria avançada, bloqueio remoto instantâneo e uma equipe de Ronda Tática presencial pronta para agir.', 'grupo' => 'secoes', 'tipo' => 'textarea', 'descricao' => 'Texto descritivo da seção Sobre'],
            ['chave' => 'secao_sobre_anos', 'valor' => '10+', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Contador: Anos de Experiência'],
            ['chave' => 'secao_sobre_veiculos', 'valor' => '15.000+', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Contador: Veículos Monitorados'],
            ['chave' => 'secao_sobre_recuperacao', 'valor' => '98.7%', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Contador: Índice de Recuperação'],

            ['chave' => 'secao_riscos_titulo', 'valor' => 'O Risco Não Avisa Antes de Chegar', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Título da seção Riscos'],
            ['chave' => 'secao_riscos_subtitulo', 'valor' => 'Todos os dias centenas de veículos são roubados ou furtados. Veja a diferença entre estar desprotegido e ter a Foco 7:', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Subtítulo da seção Riscos'],

            ['chave' => 'secao_ronda_titulo', 'valor' => 'Ronda Tática Foco 7: Apoio Presencial Imediato', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Título da seção Ronda Tática'],
            ['chave' => 'secao_ronda_descricao', 'valor' => 'Em caso de roubo ou furto, nossa equipe tática especializada se desloca rapidamente até as coordenadas exatas do veículo para efetuar a recuperação em conjunto com as autoridades policiais.', 'grupo' => 'secoes', 'tipo' => 'textarea', 'descricao' => 'Descrição da Ronda Tática'],

            ['chave' => 'secao_app_titulo', 'valor' => 'Controle Total do Seu Veículo na Palma da Mão', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Título do Aplicativo Móvel'],
            ['chave' => 'secao_app_descricao', 'valor' => 'Acompanhe velocidade em tempo real, rotas históricas, crie cercas virtuais, receba alertas de ignição e bloqueie o motor com apenas um toque no celular.', 'grupo' => 'secoes', 'tipo' => 'textarea', 'descricao' => 'Descrição do Aplicativo Móvel'],
            ['chave' => 'secao_app_link_playstore', 'valor' => 'https://play.google.com/store/apps', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Link Google Play'],
            ['chave' => 'secao_app_link_appstore', 'valor' => 'https://www.apple.com/app-store/', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Link Apple App Store'],

            ['chave' => 'secao_estetica_titulo', 'valor' => 'Estética Automotiva Premium Foco 7', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Título da seção Estética'],
            ['chave' => 'secao_estetica_subtitulo', 'valor' => 'Cuidado minucioso, proteção de pintura e detalhamento automotivo de alta precisão para o seu veículo.', 'grupo' => 'secoes', 'tipo' => 'text', 'descricao' => 'Subtítulo da seção Estética'],
            ['chave' => 'secao_estetica_chamada', 'valor' => 'Agende o serviço de estética automotiva do seu carro ou moto com profissionais certificados e produtos de primeira linha.', 'grupo' => 'secoes', 'tipo' => 'textarea', 'descricao' => 'Chamada da seção Estética'],
        ];

        foreach ($configs as $cfg) {
            CmsSetting::updateOrCreate(['chave' => $cfg['chave']], $cfg);
        }

        // 4. Hero Slides / Carrossel Dinâmico
        $slides = [
            [
                'tipo' => 'hero_slide',
                'badge' => '🛡️ TECNOLOGIA DE PONTA',
                'titulo' => 'Proteger hoje para que você viva o amanhã com tranquilidade',
                'subtitulo' => 'A Foco 7 combina tecnologia de ponta, equipe própria e Ronda Tática ágil para blindar seu patrimônio 24 horas por dia em todo o Brasil.',
                'texto_botao_1' => 'PROTEGER MEU VEÍCULO AGORA',
                'link_botao_1' => '#planos',
                'texto_botao_2' => 'FALAR NO WHATSAPP',
                'link_botao_2' => 'https://wa.me/559180510736?text=Olá,%20gostaria%20de%20proteger%20meu%20veículo%20com%20a%20Foco%207!',
                'imagem' => '/img/hero-banner-1.jpg',
                'ordem' => 1,
                'ativo' => true,
            ],
            [
                'tipo' => 'hero_slide',
                'badge' => '⚡ RESPOSTA IMEDIATA',
                'titulo' => 'Ronda Tática Foco 7 & Central de Monitoramento 24 Horas',
                'subtitulo' => 'O risco não avisa antes de chegar. Quando o alarme dispara, nossa central aciona imediatamente a viatura mais próxima para verificação presencial.',
                'texto_botao_1' => 'CONHEÇA NOSSA RONDA TÁTICA',
                'link_botao_1' => '#ronda',
                'texto_botao_2' => 'VER PLANOS E PREÇOS',
                'link_botao_2' => '#planos',
                'imagem' => '/img/hero-banner-2.jpg',
                'ordem' => 2,
                'ativo' => true,
            ],
            [
                'tipo' => 'hero_slide',
                'badge' => '📱 CONTROLE NA PALMA DA MÃO',
                'titulo' => 'Bloqueie o motor e acompanhe rotas direto do seu Smartphone',
                'subtitulo' => 'Aplicativo intuitivo e de alta precisão para Android e iOS. Cerca virtual, alerta de ignição, histórico de velocidade e suporte dedicado.',
                'texto_botao_1' => 'CONHECER O APLICATIVO',
                'link_botao_1' => '#app',
                'texto_botao_2' => 'ÁREA DO CLIENTE',
                'link_botao_2' => '/login',
                'imagem' => '/img/hero-banner-3.jpg',
                'ordem' => 3,
                'ativo' => true,
            ],
        ];

        foreach ($slides as $sld) {
            BannerSlide::updateOrCreate(['titulo' => $sld['titulo']], $sld);
        }

        // 5. Planos de Rastreamento Foco 7
        $planos = [
            [
                'nome' => 'Plano Moto Protegida',
                'slug' => 'plano-moto-protegida',
                'categoria' => 'moto',
                'subtitulo' => 'Ideal para motociclistas e entregadores que precisam de máxima segurança',
                'preco_mensal' => 49.90,
                'taxa_adesao' => 0.00,
                'icone' => 'fa-motorcycle',
                'destaque' => false,
                'tag_destaque' => null,
                'ordem' => 1,
                'ativo' => true,
                'recursos' => [
                    'Rastreamento em tempo real 24 horas',
                    'Bloqueio remoto instantâneo pelo app',
                    'Cerca virtual com alerta de saída',
                    'Alerta de ignição e vibração suspeita',
                    'Histórico completo de trajetos e paradas',
                    'Central de emergência e apoio 24h',
                    'Instalação oculta e de baixo consumo',
                ],
            ],
            [
                'nome' => 'Plano Carro Protegido',
                'slug' => 'plano-carro-protegido',
                'categoria' => 'carro',
                'subtitulo' => 'O plano mais contratado para veículos de passeio, SUVs e motoristas de app',
                'preco_mensal' => 69.90,
                'taxa_adesao' => 0.00,
                'icone' => 'fa-car',
                'destaque' => true,
                'tag_destaque' => 'MAIS POPULAR ⭐',
                'ordem' => 2,
                'ativo' => true,
                'recursos' => [
                    'Tudo do Plano Moto Protegida',
                    'Apoio da Ronda Tática Foco 7',
                    'Cobertura nacional Multioperadora 4G',
                    'Alerta de excesso de velocidade',
                    'Relatório detalhado de hodômetro e horários',
                    'Aplicativo completo para iOS e Android',
                    'Assistência e suporte prioritário 24h',
                ],
            ],
            [
                'nome' => 'Plano Caminhão & Carga',
                'slug' => 'plano-caminhao-carga',
                'categoria' => 'carga',
                'subtitulo' => 'Blindagem veicular e telemetria avançada para transporte rodoviário',
                'preco_mensal' => 99.90,
                'taxa_adesao' => 50.00,
                'icone' => 'fa-truck',
                'destaque' => false,
                'tag_destaque' => 'MÁXIMA SEGURANÇA',
                'ordem' => 3,
                'ativo' => true,
                'recursos' => [
                    'Sensor de violação e corte de alimentação',
                    'Botão de pânico silencioso para o motorista',
                    'Bloqueio progressivo de segurança',
                    'Telemetria de velocidade e RPM',
                    'Controle de jornada e paradas de descanso',
                    'Integração com principais gerenciadoras de risco',
                    'Ronda tática com viaturas de pronta resposta',
                ],
            ],
            [
                'nome' => 'Plano Gestão de Frotas Pro',
                'slug' => 'plano-gestao-frotas',
                'categoria' => 'frota',
                'subtitulo' => 'Redução de custos operacionais e controle total para empresas',
                'preco_mensal' => 79.90,
                'taxa_adesao' => 0.00,
                'icone' => 'fa-network-wired',
                'destaque' => false,
                'tag_destaque' => 'EMPRESAS',
                'ordem' => 4,
                'ativo' => true,
                'recursos' => [
                    'Painel web gerencial com múltiplos veículos',
                    'Controle de combustível e rotas inteligentes',
                    'Relatórios consolidados em PDF e Excel',
                    'Subcontas com diferentes níveis de permissão',
                    'Alertas em tempo real via WhatsApp e E-mail',
                    'Treinamento da equipe e suporte dedicado',
                    'Descontos progressivos a partir de 5 veículos',
                ],
            ],
        ];

        foreach ($planos as $pln) {
            Plano::updateOrCreate(['slug' => $pln['slug']], $pln);
        }

        // 6. Perguntas Frequentes (FAQ)
        $faqs = [
            [
                'pergunta' => 'O que é o Monitoramento Foco 7 e como funciona a proteção?',
                'resposta' => 'O sistema Foco 7 utiliza rastreadores modernos com tecnologia GPS e conexão 4G Multioperadora. Você acompanha a localização exata do veículo em tempo real pelo celular e conta com uma Central de Operações 24 horas pronta para agir a qualquer momento.',
                'categoria' => 'geral',
                'ordem' => 1,
            ],
            [
                'pergunta' => 'Como funciona a Ronda Tática Foco 7?',
                'resposta' => 'Nossa Ronda Tática é composta por viaturas e agentes treinados de pronta resposta. Ao receber um alerta de sinistro ou roubo, a Central 24h despacha imediatamente a equipe mais próxima para localização e preservação do veículo.',
                'categoria' => 'ronda',
                'ordem' => 2,
            ],
            [
                'pergunta' => 'Consigo bloquear meu veículo pelo celular se for assaltado?',
                'resposta' => 'Sim! Pelo aplicativo Foco 7 você consegue enviar o comando de bloqueio do motor em poucos segundos com total segurança. A Central 24h também pode realizar o bloqueio imediato caso você não esteja com seu aparelho.',
                'categoria' => 'aplicativo',
                'ordem' => 3,
            ],
            [
                'pergunta' => 'A instalação do equipamento descarrega a bateria do veículo?',
                'resposta' => 'Não. Nossos equipamentos são homologados pela Anatel e possuem modo inteligente de hibernação (Sleep Mode). Quando o veículo é desligado, o rastreador consome energia mínima, preservando 100% a vida útil da sua bateria.',
                'categoria' => 'instalacao',
                'ordem' => 4,
            ],
            [
                'pergunta' => 'Qual é o prazo para instalação após a contratação?',
                'resposta' => 'Nossos técnicos realizam a instalação em domicílio ou em nossa sede em até 24 a 48 horas úteis após a confirmação. O serviço leva em média de 40 a 60 minutos com total sigilo sobre o local do módulo.',
                'categoria' => 'instalacao',
                'ordem' => 5,
            ],
            [
                'pergunta' => 'Quais são as opções de pagamento para os planos?',
                'resposta' => 'Aceitamos PIX (com desconto e ativação imediata), Cartão de Crédito com cobrança mensal automática (sem comprometer o limite total) e Boleto Bancário.',
                'categoria' => 'financeiro',
                'ordem' => 6,
            ],
        ];

        foreach ($faqs as $fq) {
            Faq::updateOrCreate(['pergunta' => $fq['pergunta']], $fq);
        }

        // 7. Depoimentos de Clientes (Prova Social)
        $depoimentos = [
            [
                'nome_cliente' => 'Carlos Eduardo Ribeiro',
                'cidade_uf' => 'Belém - PA',
                'veiculo' => 'Toyota Hilux SRX',
                'foto' => '/img/depoimentos/carlos.jpg',
                'texto' => 'Tive minha caminhonete roubada no início do mês. Liguei desesperado para a Foco 7 e em menos de 25 minutos a Ronda Tática localizou o carro intacto. O suporte foi impecável, recomendo de olhos fechados!',
                'estrelas' => 5,
                'ordem' => 1,
            ],
            [
                'nome_cliente' => 'Juliana Vasconcelos',
                'cidade_uf' => 'Fortaleza - CE',
                'veiculo' => 'Honda HR-V',
                'foto' => '/img/depoimentos/juliana.jpg',
                'texto' => 'O aplicativo é extremamente rápido e fácil de mexer. Consigo ver onde meu carro está a qualquer momento, além de receber alertas quando o manobrista liga o carro. Segurança e tranquilidade não têm preço.',
                'estrelas' => 5,
                'ordem' => 2,
            ],
            [
                'nome_cliente' => 'Marcos Vinicius Freitas',
                'cidade_uf' => 'São Luís - MA',
                'veiculo' => 'Gestor de Frota (14 Veículos)',
                'foto' => '/img/depoimentos/marcos.jpg',
                'texto' => 'Conseguimos reduzir mais de 22% do gasto mensal de combustível da nossa frota só monitorando as rotas e velocidades pelo painel da Foco 7. Além da proteção contra roubos, virou uma ferramenta essencial de gestão.',
                'estrelas' => 5,
                'ordem' => 3,
            ],
            [
                'nome_cliente' => 'Rodrigo Santana',
                'cidade_uf' => 'Ananindeua - PA',
                'veiculo' => 'Yamaha MT-07',
                'foto' => '/img/depoimentos/rodrigo.jpg',
                'texto' => 'Motos de alta cilindrada são visadas demais. Coloquei o rastreador Foco 7 na minha moto e a instalação foi super discreta, não mexeu na fiação original e a bateria continua perfeita.',
                'estrelas' => 5,
                'ordem' => 4,
            ],
        ];

        foreach ($depoimentos as $dep) {
            Depoimento::updateOrCreate(['nome_cliente' => $dep['nome_cliente']], $dep);
        }

        // 8. Configuração de SEO Otimizada
        SeoConfig::updateOrCreate(
            ['pagina' => 'home'],
            [
                'meta_title' => 'Foco 7 | Rastreamento Veicular 24 Horas, Bloqueio e Ronda Tática',
                'meta_description' => 'Proteja seu carro, moto ou frota com o rastreador mais confiável do Norte e Nordeste. Bloqueio imediato pelo celular, central 24h, ronda tática ágil e planos a partir de R$ 49,90/mês.',
                'meta_keywords' => 'rastreador veicular, rastreamento de carros, rastreamento de motos, ronda tática, bloqueio veicular, foco 7 rastreamento, rastreador belém, monitoramento 24h',
                'og_title' => 'Foco 7 Rastreamento Veicular - Proteger Hoje para Viver em Paz',
                'og_description' => 'Tecnologia de rastreamento de alta precisão com suporte de ronda tática presencial. Conheça nossos planos!',
                'og_image' => '/img/og-foco7.jpg',
                'canonical_url' => 'https://foco7.com.br',
                'robots' => 'index, follow',
                'seo_score' => 96,
            ]
        );

        // 9. Agendamento e Fatura Demonstração para o Cliente
        $planoCarro = Plano::where('slug', 'plano-carro-protegido')->first();

        if ($cliente && $planoCarro) {
            Agendamento::firstOrCreate(
                ['cliente_telefone' => '(91) 99876-5432', 'tipo_servico' => 'Instalação'],
                [
                    'user_id' => $cliente->id,
                    'cliente_nome' => $cliente->name,
                    'cliente_email' => $cliente->email,
                    'tipo_servico' => 'Instalação',
                    'veiculo_modelo' => 'Jeep Renegade Longitude 2023',
                    'veiculo_placa' => 'QDA-7F22',
                    'data_hora_inicio' => date('Y-m-d 10:00:00', strtotime('+2 days')),
                    'data_hora_fim' => date('Y-m-d 11:30:00', strtotime('+2 days')),
                    'endereco_atendimento' => 'Av. Nazaré, 700 - Nazaré',
                    'cidade' => 'Belém',
                    'observacoes' => 'Cliente solicitou instalação no período da manhã no domicílio.',
                    'status' => 'confirmado',
                    'cor_calendario' => '#28a745',
                ]
            );

            Fatura::firstOrCreate(
                ['codigo_fatura' => 'FAT-2026-0001'],
                [
                    'user_id' => $cliente->id,
                    'plano_id' => $planoCarro->id,
                    'codigo_fatura' => 'FAT-2026-0001',
                    'descricao' => 'Mensalidade Foco 7 - Plano Carro Protegido (Jeep Renegade)',
                    'valor' => 69.90,
                    'gateway' => 'mercadopago',
                    'metodo_pagamento' => 'pix',
                    'status' => 'pendente',
                    'data_vencimento' => date('Y-m-d', strtotime('+5 days')),
                    'qr_code_pix' => '00020126580014br.gov.bcb.pix0136123e4567-e89b-12d3-a456-426614174000520400005303986540569.905802BR5915FOCO 7 SISTEMAS6009SAO PAULO62070503***6304ABCD',
                    'qr_code_pix_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=FOCO7PIXDEMO',
                ]
            );
        }

        // 10. Serviços Oficiais de Estética Automotiva
        $servicosEstetica = [
            [
                'nome' => 'Polimento Técnico & Correção de Pintura',
                'slug' => 'polimento-tecnico',
                'subtitulo' => 'Restauração do brilho espelhado e remoção de riscos superficiais',
                'descricao' => 'Procedimento minucioso em 3 etapas (corte, refino e lustro) utilizando compostos alemães de alta precisão que removem até 95% das imperfeições do verniz, trazendo de volta o brilho de zero km.',
                'icone' => 'fa-car-side',
                'imagem' => '/img/estetica/polimento.jpg',
                'preco' => 450.00,
                'tempo_estimado' => '4 a 6 horas',
                'recursos' => ['Descontaminação ferrosa e clay bar', 'Remoção de micro-riscos e marcas de lavagem', 'Lustro com proteção de cera carnaúba', 'Acabamento minucioso de borrachas e frisos'],
                'destaque' => true,
                'ordem' => 1,
                'ativo' => true,
            ],
            [
                'nome' => 'Vitrificação Cerâmica 9H (Proteção Premium)',
                'slug' => 'vitrificacao-ceramica-9h',
                'subtitulo' => 'Blindagem química de altíssima dureza e efeito hidrofóbico por até 3 anos',
                'descricao' => 'Aplicação de revestimento nano-cerâmico 9H que forma uma película de quartzo sobre o verniz contra raios UV, dejetos de pássaros, maresia e riscos leves, garantindo repelência absoluta à água e sujeira.',
                'icone' => 'fa-shield-halved',
                'imagem' => '/img/estetica/vitrificacao.jpg',
                'preco' => 890.00,
                'tempo_estimado' => '1 dia útil',
                'recursos' => ['Polimento técnico de preparação incluso', 'Camada de quartzo cerâmico dureza 9H', 'Efeito super hidrofóbico (repele água e sujeira)', 'Certificado e garantia de proteção de até 3 anos'],
                'destaque' => true,
                'ordem' => 2,
                'ativo' => true,
            ],
            [
                'nome' => 'Higienização Interna & Oxi-sanitização',
                'slug' => 'higienizacao-interna-oxi',
                'subtitulo' => 'Limpeza profunda de estofados, carpetes e eliminação de fungos e odores',
                'descricao' => 'Higienização com extratora profissional a quente e bactericidas potentes em todos os bancos, teto, assoalho e portas. Finalização com Oxi-sanitização por gerador de Ozônio para eliminar 99,9% de bactérias e maus odores.',
                'icone' => 'fa-spray-can-sparkles',
                'imagem' => '/img/estetica/higienizacao.jpg',
                'preco' => 290.00,
                'tempo_estimado' => '3 a 4 horas',
                'recursos' => ['Extração profunda de bancos e carpetes', 'Limpeza e hidratação de plásticos e couro', 'Higienização de teto e porta-malas', 'Oxi-sanitização por Ozônio (elimina bactérias)'],
                'destaque' => false,
                'ordem' => 3,
                'ativo' => true,
            ],
            [
                'nome' => 'Lavagem Técnica Detalhada & Chassi',
                'slug' => 'lavagem-detalhada-chassi',
                'subtitulo' => 'Limpeza artesanal sem atrito com snow foam de pH neutro e pincéis de cerdas naturais',
                'descricao' => 'Lavagem técnica método de dois baldes, snow foam desengraxante com pH balanceado, detalhamento com pincéis em emblemas, grades, frestas, rodas, caixas de rodas e motor a seco.',
                'icone' => 'fa-soap',
                'imagem' => '/img/estetica/lavagem.jpg',
                'preco' => 160.00,
                'tempo_estimado' => '2 a 3 horas',
                'recursos' => ['Snow foam desengraxante de pH neutro', 'Detalhamento com pincéis em frisos e emblemas', 'Limpeza profunda de rodas e caixas de rodas', 'Proteção com selante UV e brilho de pneus duradouro'],
                'destaque' => false,
                'ordem' => 4,
                'ativo' => true,
            ],
            [
                'nome' => 'Cristalização & Repelência de Vidros',
                'slug' => 'cristalizacao-vidros',
                'subtitulo' => 'Visibilidade impecável sob chuva forte e segurança total ao dirigir',
                'descricao' => 'Remoção química de manchas de chuva ácida dos vidros e para-brisas, seguida da aplicação de selante fluoropolímero de alta repelência. Acima de 60 km/h a água escorre sozinha.',
                'icone' => 'fa-cloud-rain',
                'imagem' => '/img/estetica/cristalizacao.jpg',
                'preco' => 140.00,
                'tempo_estimado' => '1 a 2 horas',
                'recursos' => ['Descontaminação química de manchas de chuva ácida', 'Aplicação de selante repelente de alta durabilidade', 'Visibilidade superior em tempestades', 'Preservação das palhetas do limpador'],
                'destaque' => false,
                'ordem' => 5,
                'ativo' => true,
            ],
            [
                'nome' => 'Aplicação de Película Solar & PPF',
                'slug' => 'pelicula-ppf-protecao',
                'subtitulo' => 'Película térmica nano-cerâmica e filme de proteção de pintura anti-impacto (PPF)',
                'descricao' => 'Aplicação de películas térmicas de alta tecnologia com até 90% de retenção de calor infravermelho e bloqueio 99% UV. Aplicação de PPF regenerativo em quinas, conchas e áreas vulneráveis.',
                'icone' => 'fa-sun',
                'imagem' => '/img/estetica/ppf.jpg',
                'preco' => 380.00,
                'tempo_estimado' => '3 a 4 horas',
                'recursos' => ['Película nano-cerâmica de alto controle solar', 'Redução térmica superior e bloqueio 99% UV', 'Proteção contra arranhões em maçanetas e soleiras', 'Garantia de 3 a 5 anos contra desbotamento'],
                'destaque' => false,
                'ordem' => 6,
                'ativo' => true,
            ],
        ];

        foreach ($servicosEstetica as $serv) {
            ServicoEstetica::updateOrCreate(['slug' => $serv['slug']], $serv);
        }
    }
}
