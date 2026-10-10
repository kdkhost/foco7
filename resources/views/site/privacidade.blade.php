<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Termos de Uso e Política de Privacidade' }} | Foco 7</title>
    <link rel="icon" type="image/png" href="{{ asset($configs['favicon'] ?? '/img/favicon.png') }}">

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --foco-red: #d90000;
            --foco-dark: #0a0e17;
            --foco-card: #121824;
            --foco-border: #1e293b;
        }
        body {
            background-color: var(--foco-dark);
            color: #cbd5e1;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.7;
        }
        .header-privacidade {
            background: linear-gradient(180deg, rgba(217, 0, 0, 0.15) 0%, rgba(10, 14, 23, 0.95) 100%), #0a0e17;
            border-bottom: 1px solid var(--foco-border);
            padding: 40px 0 30px 0;
        }
        .card-termos {
            background: var(--foco-card);
            border: 1px solid var(--foco-border);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        .card-termos h2, .card-termos h3 {
            color: #ffffff;
            font-weight: 800;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .card-termos h2 {
            font-size: 1.35rem;
            border-left: 4px solid var(--foco-red);
            padding-left: 14px;
        }
        .card-termos p, .card-termos li {
            color: #94a3b8;
            font-size: 0.98rem;
        }
        .badge-lgpd {
            background: rgba(46, 213, 115, 0.15);
            border: 1px solid rgba(46, 213, 115, 0.4);
            color: #2ed573;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-voltar {
            background: transparent;
            color: #cbd5e1;
            border: 1px solid var(--foco-border);
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-voltar:hover {
            color: #ffffff;
            border-color: var(--foco-red);
            background: rgba(217, 0, 0, 0.1);
        }
        @media print {
            body { background: #ffffff; color: #000000; }
            .header-privacidade, .btn-voltar, .no-print { display: none !important; }
            .card-termos { border: none; box-shadow: none; padding: 0; background: transparent; }
            .card-termos h2, .card-termos h3, .card-termos p, .card-termos li { color: #000000 !important; }
        }
    </style>
</head>
<body>

    <!-- CABEÇALHO -->
    <header class="header-privacidade">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <a href="{{ route('site.home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset($configs['logo_dark'] ?? '/img/logo-foco7-dark.png') }}" alt="{{ $configs['nome'] }}" height="40" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7.png') }}';">
                </a>
                <div class="d-flex align-items-center gap-2 no-print">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Imprimir Termos
                    </button>
                    <a href="{{ route('site.home') }}" class="btn-voltar btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Voltar ao Site
                    </a>
                </div>
            </div>
            <div class="mt-4">
                <div class="badge-lgpd mb-2">
                    <i class="fas fa-shield-alt"></i> Conformidade Integral com a LGPD (Lei nº 13.709/2018)
                </div>
                <h1 class="display-6 fw-bold text-white mb-2">{{ $titulo }}</h1>
                <p class="text-secondary small mb-0">Última atualização registrada: <strong>{{ $dataAtualizacao }}</strong> • {{ $configs['nome'] }}</p>
            </div>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card-termos">
                        @if(!empty($conteudo))
                            {!! $conteudo !!}
                        @else
                            <h2>1. Introdução e Compromisso com a Privacidade</h2>
                            <p>
                                A <strong>{{ $configs['nome'] }}</strong> tem o compromisso inegociável de zelar pela segurança, confidencialidade e privacidade dos dados de todos os seus usuários, clientes, parceiros e visitantes do site e aplicativos móveis. Esta Política de Privacidade descreve com transparência como coletamos, tratamos, armazenamos e protegemos suas informações pessoais de acordo com as disposições da Lei Federal nº 13.709/2018 (Lei Geral de Proteção de Dados - LGPD).
                            </p>

                            <h2>2. Dados Pessoais Coletados e Finalidade</h2>
                            <p>Para viabilizar a prestação de serviços de rastreamento veicular 24h, bloqueio emergencial, pronta resposta tática e serviços de estética automotiva, realizamos o tratamento das seguintes categorias de dados:</p>
                            <ul>
                                <li><strong>Dados Cadastrais:</strong> Nome completo, CPF/CNPJ, e-mail, telefone/WhatsApp e endereço residencial ou comercial para faturamento, cadastro na base de clientes e contato imediato.</li>
                                <li><strong>Dados do Veículo:</strong> Marca, modelo, ano, placa, chassi e cor do veículo protegido para identificação nas rondas e vistorias.</li>
                                <li><strong>Dados de Telemetria e Localização GPS em Tempo Real:</strong> Coordenadas geográficas (latitude/longitude), velocidade, ignição ligada/desligada e rotas percorridas geradas pelo equipamento rastreador para cumprimento estrito do contrato de segurança patrimonial e recuperação de veículos roubados ou furtados.</li>
                            </ul>

                            <h2>3. Bases Legais para o Tratamento de Dados</h2>
                            <p>O tratamento de seus dados pessoais ocorre exclusivamente sob amparo das hipóteses legais previstas no art. 7º da LGPD:</p>
                            <ul>
                                <li><strong>Execução de Contrato (Inciso V):</strong> Para fornecimento dos serviços de monitoramento, bloqueio e apoio tático presencial solicitados pelo contratante.</li>
                                <li><strong>Proteção da Vida e da Incolumidade Física (Inciso VII):</strong> Em situações de emergência, roubo, sequestro ou sinistro notificado à nossa Central de Operações 24 Horas.</li>
                                <li><strong>Cumprimento de Obrigação Legal ou Regulatória (Inciso II):</strong> Para emissão de documentos fiscais e atendimento a determinações de autoridades públicas competentes.</li>
                            </ul>

                            <h2>4. Ronda Tática e Cooperação com Autoridades Policiais</h2>
                            <p>
                                Em caso de roubo, furto ou emergência declarada pelo titular, a Central 24h da Foco 7 poderá compartilhar as coordenadas do veículo em tempo real com as equipes de Ronda Tática e com os órgãos de segurança pública competentes (Polícia Militar, Polícia Civil, Polícia Rodoviária Federal), visando a preservação da integridade física e a recuperação célere do bem.
                            </p>

                            <h2>5. Armazenamento Seguro e Criptografia</h2>
                            <p>
                                Adotamos padrões rigorosos de segurança da informação, incluindo conexões criptografadas via SSL/TLS (HTTPS), servidores protegidos por firewalls e senhas criptografadas em algoritmo Bcrypt. Os dados de localização histórica são retidos exclusivamente pelo período estritamente necessário para fins contratuais e auditoria de segurança.
                            </p>

                            <h2>6. Direitos do Titular de Dados Pessoais</h2>
                            <p>Em conformidade com o art. 18 da LGPD, você possui o direito de solicitar a qualquer momento:</p>
                            <ul>
                                <li>Confirmação da existência de tratamento dos seus dados;</li>
                                <li>Acesso e correção de dados incompletos, inexatos ou desatualizados;</li>
                                <li>Anonimização, bloqueio ou eliminação de dados desnecessários;</li>
                                <li>Informações sobre o compartilhamento de dados com entidades públicas ou privadas;</li>
                                <li>Revogação do consentimento, quando aplicável.</li>
                            </ul>

                            <h2>7. Uso de Cookies e Tecnologias Semelhantes</h2>
                            <p>
                                Nosso website utiliza cookies estritamente necessários para manter sua sessão conectada, garantir a integridade do sistema contra ataques de injeção ou força bruta e lembrar suas preferências de navegação. Você pode gerenciar ou desativar cookies nas configurações de seu navegador a qualquer momento.
                            </p>

                            <h2>8. Canal de Contato do Encarregado de Proteção de Dados (DPO)</h2>
                            <p>
                                Caso tenha dúvidas sobre esta política, deseje exercer algum de seus direitos ou necessite de esclarecimentos adicionais, entre em contato diretamente com nossa equipe de privacidade:
                            </p>
                            <div class="p-3 rounded-3 mt-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--foco-border);">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <span class="small text-secondary d-block">E-mail para Privacidade & LGPD:</span>
                                        <strong class="text-white">{{ $configs['contato_email'] }}</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="small text-secondary d-block">Central Telefônica / WhatsApp:</span>
                                        <strong class="text-white">{{ $configs['contato_telefone'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- RODAPÉ COMPACTO -->
    <footer class="border-top py-4 text-center small text-secondary" style="border-color: var(--foco-border) !important;">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} {{ $configs['nome'] }}. Todos os direitos reservados. {{ $configs['endereco'] }}.</p>
        </div>
    </footer>

</body>
</html>
