<?php

namespace App\Services;

use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Models\Licenca;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

class LicenciamentoService
{
    /**
     * Retorna os parâmetros gerais do Programa de Licenciados FOCO 7
     */
    public static function obterParametrosPrograma(): array
    {
        $termoSalvo = CmsSetting::get('licenciamento_termo_licenca');
        $termoLegado = 'Ao adquirir a licença FOCO 7, o licenciado adquire o direito de instalar o sistema em seu próprio servidor, operando sob sua marca própria sem cobrança de taxas por placa rastreada.';

        // Se o termo estiver vazio ou ainda contiver o texto de amostra legado de 1 linha, carrega a minuta completa profissional
        if (empty($termoSalvo) || trim((string) $termoSalvo) === $termoLegado) {
            $termoSalvo = self::obterMinutaPadraoContrato();
        }

        return [
            'ativo' => CmsSetting::get('licenciamento_ativo', '1') == '1',
            'badge' => CmsSetting::get('licenciamento_badge', 'OPORTUNIDADE DE NEGÓCIO • WHITE LABEL'),
            'titulo' => CmsSetting::get('licenciamento_titulo', 'Tenha Sua Própria Empresa de Rastreamento Veicular'),
            'subtitulo' => CmsSetting::get('licenciamento_subtitulo', 'Adquira a licença oficial do Sistema FOCO 7 e instale no seu próprio servidor (VPS, cPanel, Cloud ou Dedicado) com 100% da sua marca e lucro.'),
            'preco_adesao' => (float) CmsSetting::get('licenciamento_preco_adesao', 1497.00),
            'preco_adesao_formatado' => 'R$ ' . number_format((float) CmsSetting::get('licenciamento_preco_adesao', 1497.00), 2, ',', '.'),
            'preco_mensal' => (float) CmsSetting::get('licenciamento_preco_mensal', 0.00),
            'preco_mensal_formatado' => 'R$ ' . number_format((float) CmsSetting::get('licenciamento_preco_mensal', 0.00), 2, ',', '.'),
            'versao_atual' => CmsSetting::get('licenciamento_versao_atual', 'v3.8.0'),
            'whatsapp_suporte' => CmsSetting::get('licenciamento_whatsapp_suporte', '559180510736'),
            'termo_licenca' => $termoSalvo,
            'permitir_auto_binding' => CmsSetting::get('licenciamento_permitir_auto_binding', '1') == '1',
            'modo_padrao' => CmsSetting::get('licenciamento_modo_padrao', 'automatica'),
            'suspensao_automatica' => CmsSetting::get('licenciamento_suspensao_automatica', '1') == '1',

            // 4 Pilares de Benefícios Superiores
            'pilar1_icone' => CmsSetting::get('licenciamento_pilar1_icone', 'fa-server'),
            'pilar1_titulo' => CmsSetting::get('licenciamento_pilar1_titulo', 'Servidor Próprio'),
            'pilar1_desc' => CmsSetting::get('licenciamento_pilar1_desc', 'Instale no seu próprio cPanel, VPS Linux (Ubuntu/Debian), Dedicated ou Cloud (AWS, DigitalOcean). O código e os dados são 100% seus.'),

            'pilar2_icone' => CmsSetting::get('licenciamento_pilar2_icone', 'fa-paint-roller'),
            'pilar2_titulo' => CmsSetting::get('licenciamento_pilar2_titulo', '100% White Label'),
            'pilar2_desc' => CmsSetting::get('licenciamento_pilar2_desc', 'Sua própria marca, domínio, logotipo, cores e CNPJ. Seus clientes utilizam o sistema com a sua identidade, sem nenhuma menção à Foco 7.'),

            'pilar3_icone' => CmsSetting::get('licenciamento_pilar3_icone', 'fa-hand-holding-dollar'),
            'pilar3_titulo' => CmsSetting::get('licenciamento_pilar3_titulo', 'Lucro 100% Seu'),
            'pilar3_desc' => CmsSetting::get('licenciamento_pilar3_desc', 'Receba mensalidades via Pix, Boleto e Cartão direto na sua conta (Mercado Pago ou Efí Bank). Sem cobrança por veículo rastreado.'),

            'pilar4_icone' => CmsSetting::get('licenciamento_pilar4_icone', 'fa-magic'),
            'pilar4_titulo' => CmsSetting::get('licenciamento_pilar4_titulo', 'Instalador Web Fácil'),
            'pilar4_desc' => CmsSetting::get('licenciamento_pilar4_desc', 'Assistente web gráfico integrado (/instalar). Criação automática de banco de dados, migrações e administrador em poucos cliques.'),

            // Card Principal da Oferta
            'card_badge' => CmsSetting::get('licenciamento_card_badge', 'PACOTE COMPLETO DE DISTRIBUIÇÃO & WHITE LABEL'),
            'card_titulo' => CmsSetting::get('licenciamento_card_titulo', 'Software FOCO 7 Completo para o Seu Próprio Servidor'),
            'card_desc' => CmsSetting::get('licenciamento_card_desc', 'Receba o pacote completo .ZIP pronto para descompactar, script de instalação automatizada VPS (install-vps.sh) e sua chave de licença criptografada com ativação imediata. Faturamento 100% seu direto na sua conta bancária sem intermediários e sem cobrança por placa rastreada.'),

            // 6 Diferenciais do Card
            'feat1_icone' => CmsSetting::get('licenciamento_feat1_icone', 'fa-server'),
            'feat1_titulo' => CmsSetting::get('licenciamento_feat1_titulo', 'Servidor & Dados Próprios'),
            'feat1_desc' => CmsSetting::get('licenciamento_feat1_desc', 'Instale na sua VPS Ubuntu/Debian, cPanel ou Cloud com total autonomia.'),

            'feat2_icone' => CmsSetting::get('licenciamento_feat2_icone', 'fa-paint-roller'),
            'feat2_titulo' => CmsSetting::get('licenciamento_feat2_titulo', '100% White Label'),
            'feat2_desc' => CmsSetting::get('licenciamento_feat2_desc', 'Sua marca, logotipo, cores, domínio próprio e sem nenhuma menção à FOCO 7.'),

            'feat3_icone' => CmsSetting::get('licenciamento_feat3_icone', 'fa-whatsapp'),
            'feat3_titulo' => CmsSetting::get('licenciamento_feat3_titulo', 'WhatsApp Evolution Go'),
            'feat3_desc' => CmsSetting::get('licenciamento_feat3_desc', 'Envio de faturas, régua de cobrança automática e botões interativos PIX.'),

            'feat4_icone' => CmsSetting::get('licenciamento_feat4_icone', 'fa-qrcode'),
            'feat4_titulo' => CmsSetting::get('licenciamento_feat4_titulo', 'Gateways Integrados'),
            'feat4_desc' => CmsSetting::get('licenciamento_feat4_desc', 'Receba via PIX Copia e Cola, Mercado Pago e Efí Bank direto na sua conta.'),

            'feat5_icone' => CmsSetting::get('licenciamento_feat5_icone', 'fa-magic'),
            'feat5_titulo' => CmsSetting::get('licenciamento_feat5_titulo', 'Instalador Gráfico /instalar'),
            'feat5_desc' => CmsSetting::get('licenciamento_feat5_desc', 'Setup em minutos com criação automática do banco de dados e admin.'),

            'feat6_icone' => CmsSetting::get('licenciamento_feat6_icone', 'fa-shield-alt'),
            'feat6_titulo' => CmsSetting::get('licenciamento_feat6_titulo', 'Sem Royalties por Veículo'),
            'feat6_desc' => CmsSetting::get('licenciamento_feat6_desc', 'Cadastre clientes e veículos ilimitados retendo 100% do seu lucro.'),

            // Box de Conversão, Preços e Botões
            'cta_badge' => CmsSetting::get('licenciamento_cta_badge', 'CONDIÇÃO ESPECIAL DE DISTRIBUIÇÃO'),
            'cta_label' => CmsSetting::get('licenciamento_cta_label', 'TAXA DE ADESÃO / IMPLANTAÇÃO'),
            'cta_selo' => CmsSetting::get('licenciamento_cta_selo', 'Pagamento Único • Sem Royalties por Placa'),
            'cta_subtexto' => CmsSetting::get('licenciamento_cta_subtexto', 'Ativação imediata da licença • Pacote pronto para deploy'),
            'btn_whatsapp_texto' => CmsSetting::get('licenciamento_btn_whatsapp_texto', 'Quero Ser Licenciado Agora'),
            'whatsapp_msg' => CmsSetting::get('licenciamento_whatsapp_msg', 'Olá! Gostaria de mais detalhes e adquirir a licença White Label do sistema FOCO 7 para instalar no meu próprio servidor.'),
            'btn_portal_texto' => CmsSetting::get('licenciamento_btn_portal_texto', 'Já sou Licenciado / Acessar Downloads'),
            'btn_portal_url' => CmsSetting::get('licenciamento_btn_portal_url', '/login'),

            // 3 Trust Badges de Rodapé
            'trust1_icone' => CmsSetting::get('licenciamento_trust1_icone', 'fa-bolt'),
            'trust1_texto' => CmsSetting::get('licenciamento_trust1_texto', 'Ativação Imediata'),

            'trust2_icone' => CmsSetting::get('licenciamento_trust2_icone', 'fa-code'),
            'trust2_texto' => CmsSetting::get('licenciamento_trust2_texto', 'Código Homologado'),

            'trust3_icone' => CmsSetting::get('licenciamento_trust3_icone', 'fa-headset'),
            'trust3_texto' => CmsSetting::get('licenciamento_trust3_texto', 'Suporte Técnico'),
        ];
    }

    /**
     * Retorna a minuta padrão robusta e profissional do Termo de Licenciamento White Label
     */
    public static function obterMinutaPadraoContrato(): string
    {
        return <<<'HTML'
<div class="termo-licenca-wrapper">
    <div class="text-center pb-3 mb-4 border-bottom">
        <h4 class="fw-bold text-uppercase text-danger mb-1">CONTRATO DE LICENCIAMENTO DE SOFTWARE WHITE LABEL &amp; DIRETRIZES DE USO DA TECNOLOGIA FOCO 7</h4>
        <p class="text-secondary small mb-0">Instrumento Jurídico Particular de Concessão de Licença de Uso de Programa de Computador (Software as a Service - On-Premise / Self-Hosted)</p>
    </div>

    <div class="alert alert-light border border-danger-subtle p-3 rounded-3 mb-4">
        <strong class="d-block text-danger fw-bold mb-1"><i class="fas fa-shield-alt me-1"></i> SÍNTESE DA AUTONOMIA DO LICENCIADO:</strong>
        <p class="small text-secondary mb-0">Ao adquirir a licença oficial da plataforma FOCO 7, o LICENCIADO recebe o direito comercial e técnico de instalar e operar o ecossistema completo em seu próprio servidor (VPS, Cloud ou cPanel), explorando comercialmente o serviço sob sua própria marca, logotipo e domínio, usufruindo de 100% das receitas obtidas sem qualquer repasse de royalties por veículo ou placa rastreada.</p>
    </div>

    <h5 class="fw-bold text-dark mt-4 mb-2"><span class="badge bg-danger me-2">CLÁUSULA 1ª</span> DO OBJETO E DA CONCESSÃO DA LICENÇA</h5>
    <p>1.1. O presente instrumento tem por objeto a cessão de direito de uso, não exclusiva, intransferível e de abrangência territorial ampla, do software de gestão, rastreamento veicular e automação de cobranças de titularidade originária da <strong>FOCO 7 TECNOLOGIA</strong>.</p>
    <p>1.2. A licença outorga ao LICENCIADO a faculdade de instalar o código-fonte homologado em infraestrutura de hospedagem própria (servidor VPS Linux Ubuntu, Cloud Dedicado ou painel cPanel compatível), operando em regime <em>White Label</em>.</p>
    <p>1.3. O modelo White Label autoriza a completa personalização da identidade visual, compreendendo logomarca, favicon, esquemas de cores corporativas, nome comercial, dados de contato e domínio personalizado (ex.: <em>rastreamento.suaempresa.com.br</em>).</p>

    <h5 class="fw-bold text-dark mt-4 mb-2"><span class="badge bg-danger me-2">CLÁUSULA 2ª</span> DA PROPRIEDADE INTELECTUAL &amp; DIREITOS AUTORAIS</h5>
    <p>2.1. A tecnologia, arquitetura de software, códigos-fonte, rotinas de agendamento (Cron), esquemas de banco de dados e algoritmos de proteção são protegidos pela legislação brasileira de direitos autorais e propriedade intelectual (Lei Federal nº 9.609/1998 - Lei do Software, e Lei Federal nº 9.610/1998 - Lei de Direitos Autorais).</p>
    <p>2.2. A concessão da licença limita-se ao <strong>direito de uso e exploração comercial do serviço</strong>, não implicando na alienação ou transferência dos direitos de propriedade intelectual do software à parte LICENCIADA.</p>
    <p>2.3. É expressamente vedado ao LICENCIADO realizar engenharia reversa para criar produtos derivados visando a comercialização da plataforma de software como produto concorrente, sublicenciar ou revender o instalador ou código-fonte original a terceiros sem prévia anuência por escrito da FOCO 7.</p>

    <h5 class="fw-bold text-dark mt-4 mb-2"><span class="badge bg-danger me-2">CLÁUSULA 3ª</span> DA ISENÇÃO DE ROYALTIES &amp; AUTONOMIA FINANCEIRA</h5>
    <p>3.1. A FOCO 7 <strong>NÃO</strong> cobra taxa por placa, mensalidade por veículo ativo, nem percentual sobre os contratos celebrados pelo LICENCIADO junto aos seus clientes finais.</p>
    <p>3.2. A totalidade dos pagamentos, mensalidades, faturas via PIX, boletos e cartões processados através das integrações de pagamento (Mercado Pago, Efí PRO e similares) é creditada de forma direta e exclusiva nas contas bancárias e carteiras do LICENCIADO.</p>

    <h5 class="fw-bold text-dark mt-4 mb-2"><span class="badge bg-danger me-2">CLÁUSULA 4ª</span> DA INFRAESTRUTURA, HOSPEDAGEM E BACKUPS</h5>
    <p>4.1. Constitui responsabilidade exclusiva do LICENCIADO a contratação, provisionamento e pagamento dos custos de infraestrutura de servidor necessários para manter seu sistema em funcionamento (VPS, memória RAM, processamento, certificados SSL e armazenamento).</p>
    <p>4.2. O LICENCIADO é o único responsável pela rotina de cópias de segurança (backup diário/semanal) de sua base de dados MySQL e dos arquivos armazenados em seu ambiente de produção.</p>
    <p>4.3. O LICENCIADO declara estar ciente de suas obrigações de conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018 - LGPD), respondendo como Controlador pelo tratamento dos dados cadastrais e financeiros de seus clientes e usuários cadastrados.</p>

    <h5 class="fw-bold text-dark mt-4 mb-2"><span class="badge bg-danger me-2">CLÁUSULA 5ª</span> DAS INSTÂNCIAS DE WHATSAPP &amp; MENSAGERIA AUTOMATIZADA</h5>
    <p>5.1. Para a execução da régua de cobrança automatizada via WhatsApp (disparos antes e no dia do vencimento, avisos de atraso e notificações com botão PIX nativo), o sistema disponibiliza conexão com a API Go dedicada.</p>
    <p>5.2. As instâncias adicionais provisionadas diretamente no cluster central possuem tarifação mensal fixa e recorrente de <strong>R$ 60,00 (sessenta reais)</strong> por instância ativa, administrada com checkout transparente e cobrança automatizada via PIX.</p>
    <p>5.3. Na hipótese de inadimplemento da mensalidade da instância dedicada de WhatsApp no prazo de vencimento estipulado, a conexão do serviço de mensageria daquela instância poderá ser suspensa temporariamente até a compensação da fatura correspondente, não interferindo no funcionamento da plataforma de rastreamento em si.</p>

    <h5 class="fw-bold text-dark mt-4 mb-2"><span class="badge bg-danger me-2">CLÁUSULA 6ª</span> DA TELEMETRIA DE INTEGRIDADE &amp; VINCULAÇÃO DE DOMÍNIO</h5>
    <p>6.1. Para resguardo da autenticidade da cópia e prevenção de pirataria ou clonagem não autorizada, o software realiza verificações periódicas remotas (<em>heartbeat</em> criptografado) com o servidor de licenciamento da FOCO 7, validando a chave de ativação (<code>license.key</code>), o domínio e o IP autorizado.</p>
    <p>6.2. Caso seja detectada utilização simultânea da mesma chave em múltiplos domínios não autorizados ou tentativa de violação dos mecanismos de autenticação, a chave de licença poderá ser bloqueada administrativamente até a devida regularização.</p>
    <p>6.3. Havendo necessidade de troca de domínio ou migração de servidor pelo LICENCIADO, este poderá solicitar a atualização do apontamento de forma ágil junto ao suporte técnico ou através do auto-binding configurado.</p>

    <h5 class="fw-bold text-dark mt-4 mb-2"><span class="badge bg-danger me-2">CLÁUSULA 7ª</span> DO SUPORTE TÉCNICO, ATUALIZAÇÕES &amp; VERSÕES</h5>
    <p>7.1. A FOCO 7 disponibiliza canal oficial de suporte técnico especializado para auxiliar o LICENCIADO no processo de implantação, configuração do ambiente e dúvidas operacionais.</p>
    <p>7.2. Correções de segurança (patches), otimizações de performance e novas versões homologadas do sistema serão disponibilizadas ao LICENCIADO de acordo com a vigência e os termos de seu plano contratado.</p>

    <h5 class="fw-bold text-dark mt-4 mb-2"><span class="badge bg-danger me-2">CLÁUSULA 8ª</span> DA VIGÊNCIA, RESCISÃO E FORO</h5>
    <p>8.1. A licença em modalidade Vitalícia vigorará por prazo indeterminado, permanecendo ativa enquanto forem respeitadas as condições estabelecidas neste instrumento e as boas práticas de uso da tecnologia.</p>
    <p>8.2. Para dirimir quaisquer litígios, dúvidas ou controvérsias decorrentes do presente termo, as partes elegem o Foro da Comarca da sede da empresa licenciadora (FOCO 7 TECNOLOGIA), com expressa renúncia a qualquer outro, por mais privilegiado que seja.</p>
</div>
HTML;
    }

    /**
     * Cria ou localiza uma licença para um usuário
     */
    public static function criarLicenca(User $user, array $dados = [], ?Fatura $fatura = null): Licenca
    {
        $chave = Licenca::gerarChaveUnica();
        $params = self::obterParametrosPrograma();

        $modoAtivacao = $dados['modo_ativacao'] ?? $params['modo_padrao'] ?? 'automatica';
        $valorAdesao = isset($dados['valor_adesao']) ? (float) $dados['valor_adesao'] : (float) $params['preco_adesao'];
        $statusAdesao = $dados['status_adesao'] ?? ($fatura ? 'pago' : (($dados['status'] ?? '') === 'ativa' ? 'pago' : 'pendente'));
        $valorMensalidade = isset($dados['valor_mensalidade']) ? (float) $dados['valor_mensalidade'] : (float) $params['preco_mensal'];
        $statusMensalidade = $dados['status_mensalidade'] ?? ($valorMensalidade > 0 ? 'em_dia' : 'isento');
        $diaVencimento = isset($dados['dia_vencimento']) ? (int) $dados['dia_vencimento'] : 10;
        $proximoVencimento = $dados['proximo_vencimento'] ?? ($valorMensalidade > 0 ? now()->addDays(30)->format('Y-m-d') : null);

        $initialStatus = $dados['status'] ?? ($fatura ? 'ativa' : 'pendente');

        $licenca = Licenca::create([
            'user_id' => $user->id,
            'fatura_id' => $fatura?->id,
            'chave_licenca' => $chave,
            'tipo_licenca' => $dados['tipo_licenca'] ?? 'vitalicia',
            'nome_empresa' => $dados['nome_empresa'] ?? ($user->name . ' Rastreamento'),
            'dominio_autorizado' => $dados['dominio_autorizado'] ?? null,
            'ip_autorizado' => $dados['ip_autorizado'] ?? null,
            'status' => $initialStatus,
            'modo_ativacao' => $modoAtivacao,
            'valor_adesao' => $valorAdesao,
            'status_adesao' => $statusAdesao,
            'valor_mensalidade' => $valorMensalidade,
            'status_mensalidade' => $statusMensalidade,
            'dia_vencimento' => $diaVencimento,
            'proximo_vencimento' => $proximoVencimento,
            'versao_liberada' => $dados['versao_liberada'] ?? $params['versao_atual'],
            'limite_veiculos' => $dados['limite_veiculos'] ?? null,
            'limite_clientes' => $dados['limite_clientes'] ?? null,
            'data_ativacao' => $initialStatus === 'ativa' ? now() : null,
            'data_expiracao' => $dados['data_expiracao'] ?? null,
            'dados_servidor' => [
                'financeiro' => [
                    'modo_ativacao' => $modoAtivacao,
                    'valor_adesao' => $valorAdesao,
                    'status_adesao' => $statusAdesao,
                    'valor_mensalidade' => $valorMensalidade,
                    'status_mensalidade' => $statusMensalidade,
                    'dia_vencimento' => $diaVencimento,
                    'proximo_vencimento' => $proximoVencimento,
                ],
            ],
            'observacoes' => $dados['observacoes'] ?? 'Licença gerada no sistema.',
        ]);

        return $licenca;
    }

    /**
     * Ativação automática ou atualização da licença após confirmação do pagamento
     */
    public static function ativarLicencaPosPagamento(Fatura $fatura): ?Licenca
    {
        $user = $fatura->user;
        if (! $user) {
            return null;
        }

        // Localiza a licença vinculada à fatura ou busca licença do usuário
        $licenca = Licenca::where('fatura_id', $fatura->id)->first();
        if (! $licenca) {
            $licenca = Licenca::where('user_id', $user->id)
                ->whereIn('status', ['pendente', 'suspensa'])
                ->latest()
                ->first();
        }

        $descricao = strtolower($fatura->descricao ?? '');
        $isMensalidade = str_contains($descricao, 'mensal')
            || str_contains($descricao, 'suporte')
            || ((float)$fatura->valor < 500 && !str_contains($descricao, 'whatsapp') && !str_contains($descricao, 'ades'));

        if (! $licenca) {
            // Se não encontrou, cria a licença ativa para o comprador
            $licenca = self::criarLicenca($user, [
                'status' => 'ativa',
                'tipo_licenca' => 'vitalicia',
                'nome_empresa' => $user->name . ' Rastreamento',
                'status_adesao' => 'pago',
                'valor_adesao' => (float)$fatura->valor,
            ], $fatura);
        } else {
            if ($isMensalidade) {
                // Pagamento de Mensalidade da Licença
                $licenca->status_mensalidade = 'em_dia';
                $licenca->proximo_vencimento = now()->addDays(30)->format('Y-m-d');
                if ($licenca->status === 'suspensa' && $licenca->isModoAutomatico()) {
                    $licenca->status = 'ativa';
                    $licenca->observacoes = trim(($licenca->observacoes ? $licenca->observacoes . "\n" : '') . '[' . now()->format('d/m/Y H:i') . '] Reativada automaticamente após pagamento de mensalidade #' . $fatura->codigo_fatura);
                }
                $licenca->save();
            } else {
                // Pagamento de Taxa de Adesão / Implantação
                $licenca->status_adesao = 'pago';
                $licenca->fatura_id = $fatura->id;

                if ($licenca->isModoAutomatico()) {
                    $licenca->status = 'ativa';
                    if (! $licenca->data_ativacao) {
                        $licenca->data_ativacao = now();
                    }
                } else {
                    $licenca->observacoes = trim(($licenca->observacoes ? $licenca->observacoes . "\n" : '') . '[' . now()->format('d/m/Y H:i') . '] Adesão PAGA via Fatura #' . $fatura->codigo_fatura . '. Aguardando liberação manual do administrador.');
                }
                $licenca->save();
            }
        }

        // Atualiza a role do usuário para 'licenciado' mantendo acesso total
        if ($user->role !== 'admin') {
            $user->update(['role' => 'licenciado']);
        }

        if ($licenca->status === 'ativa') {
            Log::info("Licença FOCO 7 ativada com sucesso para {$user->name} (#{$licenca->chave_licenca}).");

            // Disparo de e-mail ao Licenciado
            try {
                EmailTemplateService::enviar($user->email, 'licenca_ativada', [
                    'cliente_nome' => $user->name,
                    'chave_licenca' => $licenca->chave_licenca,
                    'link_portal' => url('/cliente/licenca'),
                    'versao' => $licenca->versao_liberada,
                    'nome_empresa' => $licenca->nome_empresa,
                ]);
            } catch (\Throwable $e) {
                Log::warning("Falha ao enviar e-mail de ativação de licença: " . $e->getMessage());
            }

            // Disparo de notificação WhatsApp se Evolution API estiver ativa
            if (EvolutionApiService::isAtivo() && ! empty($user->telefone)) {
                try {
                    $msgWpp = "🎉 *PARABÉNS! SUA LICENÇA FOCO 7 ESTÁ ATIVA!*\n\n"
                        . "Olá *{$user->name}*, seu pagamento foi confirmado e sua licença exclusiva foi liberada com sucesso!\n\n"
                        . "🔑 *Sua Chave de Licença:* `{$licenca->chave_licenca}`\n"
                        . "🏢 *Empresa:* {$licenca->nome_empresa}\n"
                        . "📦 *Versão Liberada:* {$licenca->versao_liberada}\n\n"
                        . "Acesse agora seu portal para baixar o instalador oficial e o pacote completo para seu servidor (VPS ou cPanel):\n"
                        . "👉 " . url('/cliente/licenca') . "\n\n"
                        . "_Seja muito bem-vindo à rede de Licenciados FOCO 7!_";

                    EvolutionApiService::enviarTexto($user->telefone, $msgWpp);
                } catch (\Throwable $e) {
                    Log::warning("Falha ao enviar WhatsApp de licença: " . $e->getMessage());
                }
            }
        }

        return $licenca;
    }

    /**
     * Gera cobrança PIX com checkout transparente para Taxa de Adesão da Licença
     */
    public static function gerarCobrancaPixAdesao(Licenca $licenca): array
    {
        $user = $licenca->user;
        $valor = $licenca->valor_adesao ?: (float) CmsSetting::get('licenciamento_preco_adesao', 1497.00);
        $chavePix = EvolutionApiService::getPixEmailRecorrencia();
        $codigoFatura = 'FAT-LIC-ADESAO-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $licenca->chave_licenca), 0, 8)) . '-' . date('Ymd');

        $fatura = Fatura::where('descricao', 'like', "%Licenca {$licenca->chave_licenca}%")
            ->where('descricao', 'like', '%Adesao%')
            ->where('status', 'pendente')
            ->latest()
            ->first();

        if (! $fatura) {
            $fatura = Fatura::create([
                'user_id' => $user?->id ?: 1,
                'codigo_fatura' => $codigoFatura,
                'descricao' => "Foco 7 - Taxa de Adesao - Licenca {$licenca->chave_licenca} ({$licenca->nome_empresa})",
                'valor' => $valor,
                'valor_original' => $valor,
                'gateway' => 'mercadopago',
                'metodo_pagamento' => 'pix',
                'status' => 'pendente',
                'data_vencimento' => now()->addDays(3),
            ]);
        }

        $copiaCola = EvolutionApiService::gerarPixBrCode($chavePix, $valor, 'FOCO 7 TECNOLOGIA', 'BELEM', 'LIC' . substr($licenca->chave_licenca, -10));
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&data=' . urlencode($copiaCola);

        $fatura->update([
            'qr_code_pix' => $copiaCola,
            'qr_code_pix_url' => $qrUrl,
        ]);

        return [
            'success' => true,
            'licenca_id' => $licenca->id,
            'fatura_id' => $fatura->id,
            'codigo_fatura' => $fatura->codigo_fatura,
            'tipo' => 'adesao',
            'valor' => $valor,
            'valor_formatado' => 'R$ ' . number_format($valor, 2, ',', '.'),
            'descricao' => $fatura->descricao,
            'pix_copia_cola' => $copiaCola,
            'pix_qr_url' => $qrUrl,
            'chave_pix' => $chavePix,
        ];
    }

    /**
     * Gera cobrança PIX com checkout transparente para Mensalidade de Suporte da Licença
     */
    public static function gerarCobrancaPixMensalidade(Licenca $licenca): array
    {
        $user = $licenca->user;
        $valor = $licenca->valor_mensalidade ?: (float) CmsSetting::get('licenciamento_preco_mensal', 0.00);
        if ($valor <= 0) {
            return [
                'success' => false,
                'message' => 'Esta licença está configurada como isenta de mensalidade (R$ 0,00).',
            ];
        }

        $chavePix = EvolutionApiService::getPixEmailRecorrencia();
        $codigoFatura = 'FAT-LIC-MENSAL-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $licenca->chave_licenca), 0, 8)) . '-' . date('Ym');

        $fatura = Fatura::where('descricao', 'like', "%Licenca {$licenca->chave_licenca}%")
            ->where('descricao', 'like', '%Mensalidade%')
            ->where('status', 'pendente')
            ->latest()
            ->first();

        if (! $fatura) {
            $fatura = Fatura::create([
                'user_id' => $user?->id ?: 1,
                'codigo_fatura' => $codigoFatura,
                'descricao' => "Foco 7 - Mensalidade de Suporte - Licenca {$licenca->chave_licenca} ({$licenca->nome_empresa})",
                'valor' => $valor,
                'valor_original' => $valor,
                'gateway' => 'mercadopago',
                'metodo_pagamento' => 'pix',
                'status' => 'pendente',
                'data_vencimento' => now()->addDays(2),
            ]);
        }

        $copiaCola = EvolutionApiService::gerarPixBrCode($chavePix, $valor, 'FOCO 7 TECNOLOGIA', 'BELEM', 'MSL' . substr($licenca->chave_licenca, -10));
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&data=' . urlencode($copiaCola);

        $fatura->update([
            'qr_code_pix' => $copiaCola,
            'qr_code_pix_url' => $qrUrl,
        ]);

        return [
            'success' => true,
            'licenca_id' => $licenca->id,
            'fatura_id' => $fatura->id,
            'codigo_fatura' => $fatura->codigo_fatura,
            'tipo' => 'mensalidade',
            'valor' => $valor,
            'valor_formatado' => 'R$ ' . number_format($valor, 2, ',', '.'),
            'descricao' => $fatura->descricao,
            'pix_copia_cola' => $copiaCola,
            'pix_qr_url' => $qrUrl,
            'chave_pix' => $chavePix,
        ];
    }

    /**
     * Varre licenças com mensalidade e suspende automaticamente aquelas com vencimento expirado
     */
    public static function processarVencimentoLicencas(): array
    {
        $hoje = date('Y-m-d');
        $licencas = Licenca::where('status', 'ativa')
            ->whereNotNull('proximo_vencimento')
            ->where('proximo_vencimento', '<', $hoje)
            ->get();

        $suspensas = 0;
        foreach ($licencas as $lic) {
            if ($lic->valor_mensalidade > 0 && $lic->isModoAutomatico()) {
                $lic->status_mensalidade = 'vencida';
                $lic->desativar('suspensa', "Vencimento automático da mensalidade em {$lic->proximo_vencimento}");
                $suspensas++;
            }
        }

        return [
            'total_verificadas' => $licencas->count(),
            'suspensas' => $suspensas,
            'mensagem' => "Varredura concluída. {$suspensas} licença(s) suspensa(s) por inadimplência da mensalidade.",
        ];
    }

    /**
     * API: Valida a licença enviada por um servidor instalado externamente
     */
    public static function validarLicencaRemota(string $chave, ?string $dominio = null, ?string $ip = null, array $dadosServidor = []): array
    {
        $licenca = Licenca::where('chave_licenca', trim($chave))->first();

        if (! $licenca) {
            return [
                'valida' => false,
                'status' => 'inexistente',
                'mensagem' => 'Chave de licença inválida ou não encontrada no servidor central.',
            ];
        }

        if ($licenca->status === 'bloqueada') {
            return [
                'valida' => false,
                'status' => 'bloqueada',
                'mensagem' => 'Esta licença foi bloqueada pela administração do sistema.',
            ];
        }

        if ($licenca->status === 'suspensa') {
            return [
                'valida' => false,
                'status' => 'suspensa',
                'mensagem' => 'Esta licença está temporariamente suspensa por pendências financeiras.',
            ];
        }

        if ($licenca->data_expiracao && $licenca->data_expiracao->isPast()) {
            return [
                'valida' => false,
                'status' => 'expirada',
                'mensagem' => 'Esta licença expirou em ' . $licenca->data_expiracao->format('d/m/Y') . '.',
            ];
        }

        $params = self::obterParametrosPrograma();

        // Auto-Binding: Se o domínio da licença ainda estiver vazio, vincula ao primeiro domínio requisitante
        if (empty($licenca->dominio_autorizado) && ! empty($dominio) && $params['permitir_auto_binding']) {
            $licenca->dominio_autorizado = strtolower(trim($dominio));
            if (! empty($ip)) {
                $licenca->ip_autorizado = trim($ip);
            }
        }

        // Validação de Domínio (se configurado e diferente de wildcard '*')
        if (! empty($licenca->dominio_autorizado) && $licenca->dominio_autorizado !== '*') {
            $domReq = strtolower(trim((string) $dominio));
            $domAut = strtolower(trim($licenca->dominio_autorizado));

            // Permite subdomínio ou correspondência exata
            if (! empty($domReq) && ! str_contains($domReq, $domAut) && ! str_contains($domAut, $domReq)) {
                return [
                    'valida' => false,
                    'status' => 'dominio_invalido',
                    'mensagem' => "Domínio '{$domReq}' não está autorizado para esta chave. Domínio cadastrado: '{$domAut}'.",
                ];
            }
        }

        // Atualiza Check-in e dados técnicos da instalação do licenciado
        $licenca->update([
            'ultimo_check_in' => now(),
            'dados_servidor' => array_merge($licenca->dados_servidor ?? [], $dadosServidor, [
                'ultimo_ip' => $ip,
                'ultimo_dominio' => $dominio,
                'data_checkin' => now()->toDateTimeString(),
            ]),
        ]);

        return [
            'valida' => true,
            'status' => 'ativa',
            'empresa' => $licenca->nome_empresa,
            'tipo' => $licenca->tipo_licenca,
            'versao' => $licenca->versao_liberada,
            'dominio' => $licenca->dominio_autorizado,
            'limite_veiculos' => $licenca->limite_veiculos,
            'limite_clientes' => $licenca->limite_clientes,
            'mensagem' => 'Licença autêntica e ativa.',
        ];
    }

    /**
     * Gera o script Bash para instalação rápida no Ubuntu/Debian (VPS e Dedicados)
     */
    public static function gerarScriptInstalacaoVps(Licenca $licenca): string
    {
        $chave = $licenca->chave_licenca;
        $empresa = $licenca->nome_empresa ?: 'Foco 7 Rastreamento';
        $dominio = $licenca->dominio_autorizado ?: 'seusite.com.br';
        $ano = date('Y');

        return <<<BASH
#!/usr/bin/env bash
# ==============================================================================
# SCRIPT OFICIAL DE INSTALAÇÃO RÁPIDA - SISTEMA FOCO 7 (LICENCIADO)
# Compatível com Ubuntu 22.04 LTS / 24.04 LTS e Debian 11/12
# Licença: {$chave} | Empresa: {$empresa}
# ==============================================================================

set -e

RED='\\033[0;31m'
GREEN='\\033[0;32m'
YELLOW='\\033[1;33m'
BLUE='\\033[0;34m'
NC='\\033[0m'

echo -e "\${BLUE}=================================================================\${NC}"
echo -e "\${GREEN}   INICIANDO INSTALADOR AUTOMATIZADO - SISTEMA FOCO 7 (\${NC}VPS/Cloud\${GREEN})\${NC}"
echo -e "   Empresa Licenciada: \${YELLOW}{$empresa}\${NC}"
echo -e "   Chave de Licença:   \${YELLOW}{$chave}\${NC}"
echo -e "\${BLUE}=================================================================\${NC}"

if [ "\$EUID" -ne 0 ]; then
    echo -e "\${RED}[ERRO] Por favor, execute este script como root (sudo bash install-vps.sh)\${NC}"
    exit 1
fi

echo -e "\${BLUE}[1/6] Atualizando pacotes do sistema...\${NC}"
apt-get update -y && apt-get upgrade -y
apt-get install -y software-properties-common curl git unzip zip certbot python3-certbot-nginx ufw

echo -e "\${BLUE}[2/6] Instalando Nginx e MariaDB Server...\${NC}"
apt-get install -y nginx mariadb-server mariadb-client
systemctl enable nginx
systemctl enable mariadb

echo -e "\${BLUE}[3/6] Instalando PHP e Extensões Necessárias...\${NC}"
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-curl php8.3-gd php8.3-mbstring php8.3-xml php8.3-zip php8.3-bcmath php8.3-intl php8.3-soap php8.3-redis
systemctl enable php8.3-fpm

echo -e "\${BLUE}[4/6] Instalando Composer Oficial...\${NC}"
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm -f /tmp/composer-setup.php
fi

echo -e "\${BLUE}[5/6] Preparando diretório e permissões em /var/www/foco7...\${NC}"
mkdir -p /var/www/foco7
cd /var/www/foco7

# Gravando arquivo da licença ativada
cat <<EOF > /var/www/foco7/license.key
{
    "licenca": "{$chave}",
    "empresa": "{$empresa}",
    "dominio": "{$dominio}",
    "gerado_em": "$(date -u +"%Y-%m-%dT%H:%M:%SZ")"
}
EOF

# Ajustando proprietário para www-data
chown -R www-data:www-data /var/www/foco7
chmod -R 775 /var/www/foco7

echo -e "\${BLUE}[6/6] Configurando Virtual Host Nginx...\${NC}"
cat <<EOF > /etc/nginx/sites-available/foco7
server {
    listen 80;
    listen [::]:80;
    server_name {$dominio} www.{$dominio};
    root /var/www/foco7/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    location / {
        try_files \\\$uri \\\$uri/ /index.php?\\\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \\.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \\\$realpath_root\\\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/foco7 /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

echo -e "\${GREEN}=================================================================\${NC}"
echo -e "\${GREEN}🎉 AMBIENTE VPS PREPARADO COM SUCESSO!\${NC}"
echo -e "\${YELLOW}Próximos passos simples para finalizar:\${NC}"
echo -e "1. Extraia os arquivos do pacote .ZIP para: \${BLUE}/var/www/foco7\${NC}"
echo -e "2. Ajuste as permissões: \${BLUE}chown -R www-data:www-data /var/www/foco7 && chmod -R 775 /var/www/foco7/storage\${NC}"
echo -e "3. Crie o banco MySQL no MariaDB: \${BLUE}CREATE DATABASE foco7_db; GRANT ALL ON foco7_db.* TO 'foco7_user'@'localhost' IDENTIFIED BY 'SUA_SENHA';\${NC}"
echo -e "4. Acesse pelo navegador: \${GREEN}http://{$dominio}/instalar\${NC} e siga o assistente web gráfico!"
echo -e "5. Para ativar SSL gratuito: \${BLUE}certbot --nginx -d {$dominio}\${NC}"
echo -e "\${GREEN}=================================================================\${NC}"
BASH;
    }

    /**
     * Gera o manual completo de instalação em Markdown
     */
    public static function gerarGuiaInstalacaoMarkdown(Licenca $licenca): string
    {
        $chave = $licenca->chave_licenca;
        $empresa = $licenca->nome_empresa ?: 'Foco 7 Rastreamento';
        $dominio = $licenca->dominio_autorizado ?: 'seusite.com.br';

        return <<<MD
# Guia de Instalação do Sistema FOCO 7 (Licenciado Oficial)

Parabéns pela aquisição da sua licença do **Sistema FOCO 7**!
Este guia detalha o passo a passo para instalar e rodar a plataforma completa em seu próprio servidor.

---

## 🔑 Dados da Sua Licença
- **Chave de Ativação:** `{$chave}`
- **Empresa Licenciada:** {$empresa}
- **Domínio Autorizado:** {$dominio}
- **Suporte Oficial WhatsApp:** +55 (91) 8051-0736

---

## 🌐 Opção 1: Instalação em Hospedagem Compartilhada cPanel (Recomendado / Fácil)

1. **Acesse o cPanel da sua hospedagem:**
   - Crie um banco de dados MySQL (ex: `seuuser_foco7`) e anote o nome do banco, usuário e senha.
2. **Envie os Arquivos:**
   - Acesse o **Gerenciador de Arquivos** do cPanel.
   - Entre na pasta `public_html`.
   - Envie o arquivo `FOCO7_DISTRIBUICAO.zip` e clique em **Extrair**.
3. **Execute o Instalador Web Gráfico:**
   - Abra o navegador e acesse: `https://seusite.com.br/instalar`
   - O assistente verificará as extensões do PHP (8.2+), testará a conexão MySQL e executará as migrações automaticamente.
   - Digite a sua chave de licença `{$chave}` e defina os dados do seu Administrador Master.
4. **Pronto!**
   - O instalador bloqueará o arquivo de trava de segurança (`installed.lock`) e redirecionará para a tela de login.

---

## 🖥️ Opção 2: Instalação em Servidor VPS Linux (Ubuntu 22.04 / 24.04 LTS)

1. Conecte ao seu VPS via SSH como root:
   ```bash
   ssh root@seu_ip_vps
   ```
2. Baixe e execute o script de instalação rápida incluído no pacote:
   ```bash
   bash install-vps.sh
   ```
3. O script instalará Nginx, PHP 8.3-FPM, MariaDB, Composer e criará o diretório `/var/www/foco7`.
4. Crie o banco de dados no MySQL:
   ```sql
   mysql -u root -p
   CREATE DATABASE foco7_sistema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'foco7_user'@'localhost' IDENTIFIED BY 'SenhaForte123@';
   GRANT ALL PRIVILEGES ON foco7_sistema.* TO 'foco7_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```
5. Acesse no navegador `http://seu_ip_ou_dominio/instalar` e conclua a instalação!
6. Para ativar SSL HTTPS grátis com Let's Encrypt:
   ```bash
   certbot --nginx -d {$dominio}
   ```

---

## ⏰ Tarefas Agendadas (Cron Job Obrigatório)
Para que o envio de faturas, avisos de vencimento no WhatsApp e a régua de cobrança funcionem automaticamente, configure o Cron do servidor para rodar a cada minuto:

```bash
* * * * * cd /caminho/do/sistema && php artisan schedule:run >> /dev/null 2>&1
```

*No cPanel:* Vá em **Tarefas Cron**, selecione "Uma vez por minuto (`* * * * *`)" e adicione:
`php /home/seu_usuario/public_html/artisan schedule:run >> /dev/null 2>&1`
MD;
    }

    /**
     * Gera o arquivo ZIP oficial de distribuição do sistema para download do licenciado
     */
    public static function gerarPacoteZipParaDownload(Licenca $licenca): string
    {
        $dirExport = storage_path('app/licencas/pacotes');
        if (! File::isDirectory($dirExport)) {
            File::makeDirectory($dirExport, 0755, true, true);
        }

        $zipPath = $dirExport . "/FOCO7_DISTRIBUICAO_{$licenca->id}.zip";

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Não foi possível criar o arquivo ZIP de distribuição.");
        }

        // Injeta os arquivos exclusivos de licença e instalação
        $zip->addFromString('license.key', json_encode([
            'chave_licenca' => $licenca->chave_licenca,
            'empresa' => $licenca->nome_empresa,
            'dominio' => $licenca->dominio_autorizado,
            'tipo' => $licenca->tipo_licenca,
            'versao' => $licenca->versao_liberada,
            'data_emissao' => now()->toDateTimeString(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $zip->addFromString('install-vps.sh', self::gerarScriptInstalacaoVps($licenca));
        $zip->addFromString('GUIA_INSTALACAO_SERVIDOR.md', self::gerarGuiaInstalacaoMarkdown($licenca));

        // Empacota diretórios essenciais do sistema de forma segura
        $baseDir = base_path();
        $diretorios = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes'];
        $arquivosRaiz = [
            'artisan',
            'composer.json',
            'package.json',
            'vite.config.js',
            '.env.example',
            '.htaccess',
        ];

        foreach ($arquivosRaiz as $arq) {
            $fullPath = $baseDir . DIRECTORY_SEPARATOR . $arq;
            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, $arq);
            }
        }

        foreach ($diretorios as $dir) {
            $dirPath = $baseDir . DIRECTORY_SEPARATOR . $dir;
            if (File::isDirectory($dirPath)) {
                self::adicionarDiretorioAoZip($zip, $dirPath, $dir);
            }
        }

        $zip->close();

        // Incrementa métricas de download
        $licenca->increment('total_downloads');
        $licenca->update(['ultimo_download_em' => now()]);

        return $zipPath;
    }

    /**
     * Adiciona arquivos recursivamente ao arquivo ZIP filtrando arquivos sensíveis
     */
    protected static function adicionarDiretorioAoZip(ZipArchive $zip, string $caminhoAbsoluto, string $caminhoInterno): void
    {
        $arquivos = File::allFiles($caminhoAbsoluto);

        foreach ($arquivos as $arquivo) {
            $caminhoRelativo = $arquivo->getRelativePathname();
            $extensao = strtolower($arquivo->getExtension());

            // Filtra arquivos sensíveis que não devem ir no pacote de distribuição
            if ($extensao === 'lock' && $arquivo->getFilename() === 'installed.lock') {
                continue; // Garante que o instalador abra no novo servidor
            }
            if (str_contains($caminhoRelativo, '.git') || str_contains($caminhoRelativo, 'node_modules')) {
                continue;
            }

            $zip->addFile($arquivo->getRealPath(), $caminhoInterno . '/' . str_replace('\\', '/', $caminhoRelativo));
        }
    }
}

