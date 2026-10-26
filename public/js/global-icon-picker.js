/**
 * Global Icon Picker - Foco 7
 * Seletor Visual de Ícones FontAwesome para todo o Painel Administrativo
 */
(function ($) {
    'use strict';

    // Catálogo selecionado de ícones FontAwesome com nomes em português e palavras-chave de busca
    var CATALOGO_ICONES = [
        // === VEÍCULOS & TRANSPORTE ===
        { icone: 'fa-car', nome: 'Carro Passeio', cat: 'veiculos', tags: 'carro automovel veiculo passeio sedan hatch' },
        { icone: 'fa-motorcycle', nome: 'Moto / Duas Rodas', cat: 'veiculos', tags: 'moto motocicleta duas rodas scooter motoboy entrega' },
        { icone: 'fa-truck', nome: 'Caminhão Pesado', cat: 'veiculos', tags: 'caminhao truck pesado frete carga cavalo carreta' },
        { icone: 'fa-truck-pickup', nome: 'Caminhonete / Picape', cat: 'veiculos', tags: 'caminhonete picape pickup utilitario 4x4 hilux toro ranger' },
        { icone: 'fa-van-shuttle', nome: 'Van / Utilitário', cat: 'veiculos', tags: 'van utilitario escolar transporte fiorino master van-shuttle' },
        { icone: 'fa-bus', nome: 'Ônibus / Coletivo', cat: 'veiculos', tags: 'onibus coletivo viagem turismo passageiros' },
        { icone: 'fa-cubes', nome: 'Frotas / Logística', cat: 'veiculos', tags: 'frotas frota empresa logística caixas frotistas cubos' },
        { icone: 'fa-boxes-stacked', nome: 'Cargas & Encomendas', cat: 'veiculos', tags: 'cargas caixas paletes encomendas logistica mercadorias' },
        { icone: 'fa-tractor', nome: 'Trator / Agrícola', cat: 'veiculos', tags: 'trator maquina agricola fazenda campo colheitadeira' },
        { icone: 'fa-truck-monster', nome: 'SUV / 4x4 Pesado', cat: 'veiculos', tags: 'suv 4x4 monster picape grande tracao offroad' },
        { icone: 'fa-gas-pump', nome: 'Posto / Combustível', cat: 'veiculos', tags: 'combustivel gasolina etanol diesel bomba abastecimento' },
        { icone: 'fa-gauge-high', nome: 'Velocímetro / Telemetria', cat: 'veiculos', tags: 'velocimetro velocidade telemetria odometro medidor' },
        { icone: 'fa-car-side', nome: 'Perfil do Veículo', cat: 'veiculos', tags: 'carro lateral perfil silhueta automovel' },
        { icone: 'fa-car-battery', nome: 'Bateria Veicular', cat: 'veiculos', tags: 'bateria carga eletrica partida energia voltagem' },
        { icone: 'fa-oil-can', nome: 'Troca de Óleo', cat: 'veiculos', tags: 'oleo motor lubrificante revisao manutencao' },
        { icone: 'fa-car-burst', nome: 'Colisão / Sinistro', cat: 'veiculos', tags: 'batida colisao acidente sinistro quebra crash' },
        { icone: 'fa-road', nome: 'Rodovia / Estrada', cat: 'veiculos', tags: 'estrada rodovia asfalto pista trajeto pedagio' },
        { icone: 'fa-truck-ramp-box', nome: 'Carga & Descarga', cat: 'veiculos', tags: 'entrega frete descarga doca caminhao' },
        { icone: 'fa-trailer', nome: 'Reboque / Carretinha', cat: 'veiculos', tags: 'reboque carretinha engate trailer' },

        // === SEGURANÇA & PROTEÇÃO ===
        { icone: 'fa-shield-halved', nome: 'Escudo de Proteção', cat: 'seguranca', tags: 'escudo protecao defesa seguranca blindagem ativo' },
        { icone: 'fa-shield-virus', nome: 'Prevenção de Riscos', cat: 'seguranca', tags: 'escudo risco ameaca prevencao seguranca virus' },
        { icone: 'fa-shield-cat', nome: 'Proteção Integral', cat: 'seguranca', tags: 'escudo protecao total garantia' },
        { icone: 'fa-shield-heart', nome: 'Proteção à Família', cat: 'seguranca', tags: 'escudo coracao vida familia protecao pessoal' },
        { icone: 'fa-lock', nome: 'Bloqueio Remoto', cat: 'seguranca', tags: 'cadeado bloqueio trava fecho tranca seguranca celular' },
        { icone: 'fa-lock-open', nome: 'Desbloqueio Seguro', cat: 'seguranca', tags: 'desbloqueio cadeado aberto liberado' },
        { icone: 'fa-key', nome: 'Chave / Ignição', cat: 'seguranca', tags: 'chave ignicao partida virada segredo abertura' },
        { icone: 'fa-user-shield', nome: 'Condutor Protegido', cat: 'seguranca', tags: 'usuario motorista pessoa escudo condutor seguranca' },
        { icone: 'fa-building-shield', nome: 'Base / Central Blindada', cat: 'seguranca', tags: 'predio base central blindada empresa matriz' },
        { icone: 'fa-mask', nome: 'Prevenção a Furtos', cat: 'seguranca', tags: 'mascara ladrao roubo furto sinistro marginal' },
        { icone: 'fa-bell', nome: 'Alarme / Notificação', cat: 'seguranca', tags: 'sino alarme alerta aviso sirene disparo' },
        { icone: 'fa-triangle-exclamation', nome: 'Alerta de Emergência', cat: 'seguranca', tags: 'triangulo perigo atencao alerta panico socorro' },
        { icone: 'fa-handcuffs', nome: 'Apoio Policial', cat: 'seguranca', tags: 'algemas policia prisao recuperacao flagrante resgate' },
        { icone: 'fa-fingerprint', nome: 'Biometria / Autenticação', cat: 'seguranca', tags: 'biometria digital identificacao senha segredo' },
        { icone: 'fa-eye', nome: 'Monitoramento 24h', cat: 'seguranca', tags: 'olho visao vigilancia monitoramento camera acompanhamento' },
        { icone: 'fa-bullhorn', nome: 'Alerta Sonoro', cat: 'seguranca', tags: 'megafone aviso comunicacao alerta sirene chamada' },

        // === GPS, RASTREAMENTO & TECNOLOGIA ===
        { icone: 'fa-satellite-dish', nome: 'Antena Satelital GPS', cat: 'tecnologia', tags: 'antena satelite satelital prato sinal telemetria rastreador' },
        { icone: 'fa-satellite', nome: 'Satélite em Órbita', cat: 'tecnologia', tags: 'satelite orbita espaco sinal precisao gps' },
        { icone: 'fa-map-location-dot', nome: 'Localização no Mapa', cat: 'tecnologia', tags: 'mapa pino pin gps localizacao posicao endereco' },
        { icone: 'fa-location-dot', nome: 'Ponto GPS (Pin)', cat: 'tecnologia', tags: 'pino pin ponto localizacao marcador destino' },
        { icone: 'fa-route', nome: 'Histórico de Rotas', cat: 'tecnologia', tags: 'rota percurso trajeto caminho linha historico viagem' },
        { icone: 'fa-crosshairs', nome: 'Precisão / Mira', cat: 'tecnologia', tags: 'mira precisao exatidao foco alvo metros' },
        { icone: 'fa-compass', nome: 'Bússola / Direção', cat: 'tecnologia', tags: 'bussola direcao orientacao norte navegacao' },
        { icone: 'fa-draw-polygon', nome: 'Cerca Virtual (Geofence)', cat: 'tecnologia', tags: 'cerca poligono perimetro area delimitacao geofence' },
        { icone: 'fa-mobile-screen-button', nome: 'Aplicativo Mobile', cat: 'tecnologia', tags: 'celular smartphone app aplicativo tela botao iphone android' },
        { icone: 'fa-signal', nome: 'Sinal 4G / Telemetria', cat: 'tecnologia', tags: 'sinal barras rede 4g 5g transmissao torre' },
        { icone: 'fa-wifi', nome: 'Conectividade Wi-Fi', cat: 'tecnologia', tags: 'wifi conexao internet rede sem fio link' },
        { icone: 'fa-microchip', nome: 'Chip Inteligente / Módulo', cat: 'tecnologia', tags: 'microchip chip processador modulo anatel hardware' },
        { icone: 'fa-sim-card', nome: 'Chip Multioperadora', cat: 'tecnologia', tags: 'sim card chip multioperadora telemetria m2m dados' },
        { icone: 'fa-tower-broadcast', nome: 'Torre de Transmissão', cat: 'tecnologia', tags: 'torre antena transmissao broadcast comunicacao' },
        { icone: 'fa-cloud', nome: 'Nuvem / Servidor Seguro', cat: 'tecnologia', tags: 'nuvem cloud servidor armazenamento backup online' },
        { icone: 'fa-laptop-code', nome: 'Plataforma Web', cat: 'tecnologia', tags: 'computador notebook tela software plataforma sistema' },

        // === MÉTRICAS, CONQUISTAS & NEGÓCIOS ===
        { icone: 'fa-award', nome: 'Prêmio de Qualidade', cat: 'negocios', tags: 'premio medalha fita qualidade certificacao destaque' },
        { icone: 'fa-trophy', nome: 'Troféu de Liderança', cat: 'negocios', tags: 'trofeu lider campeao primeiro lugar conquista sucesso' },
        { icone: 'fa-medal', nome: 'Medalha de Honra', cat: 'negocios', tags: 'medalha condecoracao merito confianca excelencia' },
        { icone: 'fa-certificate', nome: 'Certificado de Garantia', cat: 'negocios', tags: 'certificado garantia laudo homologado selo anatel' },
        { icone: 'fa-handshake', nome: 'Parcerias Estratégicas', cat: 'negocios', tags: 'aperto de maos parceria convenio acordo contrato socio' },
        { icone: 'fa-building', nome: 'Unidades & Filiais', cat: 'negocios', tags: 'empresa predio filial sede matriz franquia polo' },
        { icone: 'fa-star', nome: 'Avaliação 5 Estrelas', cat: 'negocios', tags: 'estrela destaque melhor top avaliacao satisfacao' },
        { icone: 'fa-coins', nome: 'Economia Financeira', cat: 'negocios', tags: 'moedas dinheiro economia custo beneficio barato lucro' },
        { icone: 'fa-money-bill-wave', nome: 'Preço Justo / Mensalidade', cat: 'negocios', tags: 'dinheiro cedula pagamento nota valor plano' },
        { icone: 'fa-chart-line', nome: 'Resultados Crescentes', cat: 'negocios', tags: 'grafico alta crescimento metas numeros estatistica' },
        { icone: 'fa-chart-pie', nome: 'Gráfico / Estatísticas', cat: 'negocios', tags: 'pizza grafico porcentagem taxa indice metricas' },
        { icone: 'fa-users', nome: 'Clientes Atendidos', cat: 'negocios', tags: 'clientes pessoas usuarios equipe comunidade frota' },
        { icone: 'fa-user-tie', nome: 'Consultores Especialistas', cat: 'negocios', tags: 'consultor gravata executivo vendedor especialista' },
        { icone: 'fa-briefcase', nome: 'Corporativo / Empresas', cat: 'negocios', tags: 'maleta corporativo empresas b2b negocios contrato' },
        { icone: 'fa-calculator', nome: 'Cálculo de Economia', cat: 'negocios', tags: 'calculadora contas numeros matematica simulacao' },

        // === ESTÉTICA AUTOMOTIVA & CUIDADOS ===
        { icone: 'fa-spray-can-sparkles', nome: 'Vitrificação & Brilho', cat: 'estetica', tags: 'spray lata brilho vitrificacao ceramica polimento verniz' },
        { icone: 'fa-spray-can', nome: 'Higienização Interna', cat: 'estetica', tags: 'spray higienizacao limpeza bancos teto sanitizacao' },
        { icone: 'fa-wand-magic-sparkles', nome: 'Detalhamento Mágico', cat: 'estetica', tags: 'varinha magica brilho detalhamento impecavel novo' },
        { icone: 'fa-soap', nome: 'Lavagem Técnica', cat: 'estetica', tags: 'sabao espuma lavagem detalhada detalhamento limpeza' },
        { icone: 'fa-brush', nome: 'Pincelamento Técnico', cat: 'estetica', tags: 'pincel escova grelhas emblemas limpeza cantos' },
        { icone: 'fa-droplet', nome: 'Hidrorrepelência (Água)', cat: 'estetica', tags: 'gota agua chuva repelencia protecao pintura' },
        { icone: 'fa-warehouse', nome: 'Box / Estúdio Próprio', cat: 'estetica', tags: 'galpao box estudio climatizado iluminado instalacao' },
        { icone: 'fa-screwdriver-wrench', nome: 'Instalação & Ferramental', cat: 'estetica', tags: 'chave de fenda ferramentas instalacao sigilosa chicote' },
        { icone: 'fa-wrench', nome: 'Manutenção Preventiva', cat: 'estetica', tags: 'chave inglesa ajuste revisao conserto oficina' },
        { icone: 'fa-shield-heart', nome: 'Cuidado & Proteção', cat: 'estetica', tags: 'cuidado zelo carinho estetica conservacao' },

        // === ATENDIMENTO, SUPORTE & GERAL ===
        { icone: 'fa-headset', nome: 'Central de Atendimento 24h', cat: 'atendimento', tags: 'headset fone central atendimento operador suporte 24h plantao' },
        { icone: 'fa-phone-volume', nome: 'Ligação / Plantão Urgente', cat: 'atendimento', tags: 'telefone chamada ligacao voz urgente contato' },
        { icone: 'fa-comments', nome: 'Chat / WhatsApp', cat: 'atendimento', tags: 'conversa balao chat mensagens suporte humano' },
        { icone: 'fa-clock', nome: 'Pronta Resposta Imediata', cat: 'atendimento', tags: 'relogio tempo rapidez imediata agilidade minutos' },
        { icone: 'fa-history', nome: 'Tradição / Anos de Mercado', cat: 'atendimento', tags: 'relogio volta historia passado experiencia anos solidez' },
        { icone: 'fa-calendar-check', nome: 'Agendamento Confirmado', cat: 'atendimento', tags: 'calendario data agendamento horario visita instalacao' },
        { icone: 'fa-circle-check', nome: 'Aprovado / Sucesso', cat: 'atendimento', tags: 'check correto sucesso verificado aprovado sim' },
        { icone: 'fa-check-double', nome: 'Garantido / Concluído', cat: 'atendimento', tags: 'dois checks dupla checagem confirmado certeza 100%' },
        { icone: 'fa-hourglass-half', nome: 'Tempo Decisivo / Espera', cat: 'atendimento', tags: 'ampulheta tempo minutos contagem regressiva' },
        { icone: 'fa-circle-question', nome: 'Dúvidas / FAQ', cat: 'atendimento', tags: 'interrogacao duvida pergunta faq ajuda suporte' },
        { icone: 'fa-circle-info', nome: 'Informação Importante', cat: 'atendimento', tags: 'informacao aviso detalhe saber mais' },
        { icone: 'fa-bolt', nome: 'Ação Rápida / Instantânea', cat: 'atendimento', tags: 'raio agilidade velocidade choque potencia' },
        { icone: 'fa-thumbs-up', nome: 'Recomendado / Positivo', cat: 'atendimento', tags: 'joinha like positivo aprovado confianca' }
    ];

    // Variável para armazenar o input que disparou o modal
    var $inputAlvoAtual = null;

    // Normaliza nome do ícone: garante que comece sem 'fa-' repetido e retorna classe completa
    function normalizarClasseIcone(val) {
        if (!val) return 'fa-icons';
        val = $.trim(val);
        // Remove fas, far, fa-solid se existirem para pegar o nome puro
        var limpo = val.replace(/^(fas|far|fal|fad|fat|fa-solid|fa-regular|fa-brands)\s+/g, '');
        if (!limpo.startsWith('fa-')) {
            limpo = 'fa-' + limpo;
        }
        return limpo;
    }

    // Injeta o HTML do Modal Global no final do body
    function injetarModalGlobal() {
        if ($('#modalGlobalIconPicker').length > 0) return;

        var modalHtml = '' +
            '<div class="modal fade" id="modalGlobalIconPicker" tabindex="-1" aria-labelledby="modalGlobalIconPickerLabel" aria-hidden="true" style="z-index: 10800;">' +
            '    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">' +
            '        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">' +
            '            <!-- HEADER -->' +
            '            <div class="modal-header bg-danger text-white py-3 px-4">' +
            '                <div class="d-flex align-items-center gap-2">' +
            '                    <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">' +
            '                        <i class="fas fa-icons fs-5"></i>' +
            '                    </div>' +
            '                    <div>' +
            '                        <h5 class="modal-title fw-bold mb-0 text-white" id="modalGlobalIconPickerLabel">Escolher Ícone Visual</h5>' +
            '                        <small class="text-white-50">Clique em qualquer ícone da grade para selecionar instantaneamente</small>' +
            '                    </div>' +
            '                </div>' +
            '                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>' +
            '            </div>' +
            '            <!-- BODY -->' +
            '            <div class="modal-body p-4 bg-body-tertiary">' +
            '                <!-- BARRA DE PESQUISA -->' +
            '                <div class="mb-3">' +
            '                    <div class="input-group shadow-sm">' +
            '                        <span class="input-group-text bg-white border-end-0 text-danger"><i class="fas fa-search"></i></span>' +
            '                        <input type="text" id="iconPickerInputBusca" class="form-control border-start-0 py-2" placeholder="Buscar ícone por nome em português ou inglês... (ex: carro, moto, escudo, satélite, chave, mapa...)" autocomplete="off">' +
            '                        <button class="btn btn-outline-secondary border-start-0" type="button" id="btnLimparBuscaIcones" title="Limpar busca"><i class="fas fa-times"></i></button>' +
            '                    </div>' +
            '                </div>' +
            '                <!-- FILTRO POR CATEGORIAS -->' +
            '                <div class="d-flex gap-1 flex-wrap mb-3" id="iconPickerCategorias">' +
            '                    <button type="button" class="btn btn-sm btn-danger active btn-cat-filter" data-cat="todas">Todos (<span id="countTotalIcones">0</span>)</button>' +
            '                    <button type="button" class="btn btn-sm btn-outline-secondary btn-cat-filter" data-cat="veiculos"><i class="fas fa-car me-1"></i> Veículos</button>' +
            '                    <button type="button" class="btn btn-sm btn-outline-secondary btn-cat-filter" data-cat="seguranca"><i class="fas fa-shield-halved me-1"></i> Segurança</button>' +
            '                    <button type="button" class="btn btn-sm btn-outline-secondary btn-cat-filter" data-cat="tecnologia"><i class="fas fa-satellite-dish me-1"></i> GPS & Tecnologia</button>' +
            '                    <button type="button" class="btn btn-sm btn-outline-secondary btn-cat-filter" data-cat="negocios"><i class="fas fa-chart-line me-1"></i> Métricas & Negócios</button>' +
            '                    <button type="button" class="btn btn-sm btn-outline-secondary btn-cat-filter" data-cat="estetica"><i class="fas fa-spray-can-sparkles me-1"></i> Estética</button>' +
            '                    <button type="button" class="btn btn-sm btn-outline-secondary btn-cat-filter" data-cat="atendimento"><i class="fas fa-headset me-1"></i> Atendimento</button>' +
            '                </div>' +
            '                <!-- GRADE DE ÍCONES -->' +
            '                <div class="icon-picker-grid-wrapper bg-white p-3 rounded-3 border" style="max-height: 380px; overflow-y: auto;">' +
            '                    <div class="icon-picker-grid" id="iconPickerGrid"></div>' +
            '                    <div id="iconPickerVazio" class="text-center py-5 d-none text-muted">' +
            '                        <i class="fas fa-search-minus fa-3x mb-2 text-danger opacity-50"></i>' +
            '                        <p class="mb-0 fw-semibold">Nenhum ícone encontrado com esse termo.</p>' +
            '                        <small>Tente buscar por palavras sinônimas ou navegue pelas categorias acima.</small>' +
            '                    </div>' +
            '                </div>' +
            '            </div>' +
            '            <!-- FOOTER COM CAMPO MANUAL CASO DESEJE DIGITAR -->' +
            '            <div class="modal-footer bg-white border-top justify-content-between py-2 px-4">' +
            '                <div class="d-flex align-items-center gap-2">' +
            '                    <span class="small text-muted">Ícone atual selecionado:</span>' +
            '                    <span id="iconPickerAtualBadge" class="badge bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1 px-2 py-1 fs-6">' +
            '                        <i class="fas fa-icons" id="iconPickerAtualIcone"></i> <code id="iconPickerAtualTexto" class="text-danger fw-bold">fa-car</code>' +
            '                    </span>' +
            '                </div>' +
            '                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '</div>';

        $('body').append(modalHtml);
        $('#countTotalIcones').text(CATALOGO_ICONES.length);
        renderizarGradeIcones();
        registrarEventosModal();
    }

    // Renderiza a grade de ícones no modal
    function renderizarGradeIcones(termoBusca, categoriaAtiva) {
        var $grid = $('#iconPickerGrid');
        $grid.empty();

        termoBusca = termoBusca ? termoBusca.toLowerCase().trim() : '';
        categoriaAtiva = categoriaAtiva || 'todas';

        var valorAtual = $inputAlvoAtual ? normalizarClasseIcone($inputAlvoAtual.val()) : '';
        var contador = 0;

        $.each(CATALOGO_ICONES, function (index, item) {
            // Filtro por Categoria
            if (categoriaAtiva !== 'todas' && item.cat !== categoriaAtiva) {
                return;
            }

            // Filtro por Termo de Busca
            if (termoBusca) {
                var searchBlob = (item.icone + ' ' + item.nome + ' ' + item.tags).toLowerCase();
                if (searchBlob.indexOf(termoBusca) === -1) {
                    return;
                }
            }

            contador++;
            var isSelected = (normalizarClasseIcone(item.icone) === valorAtual);
            var activeClass = isSelected ? ' active' : '';

            var itemHtml = '' +
                '<button type="button" class="icon-picker-item' + activeClass + '" data-icone="' + item.icone + '" data-nome="' + item.nome + '" title="' + item.nome + ' (' + item.icone + ')">' +
                '    <div class="icon-picker-icon-box">' +
                '        <i class="fas ' + item.icone + '"></i>' +
                '    </div>' +
                '    <span class="icon-picker-name" title="' + item.nome + '">' + item.nome + '</span>' +
                '    <span class="icon-picker-code">' + item.icone + '</span>' +
                '</button>';

            $grid.append(itemHtml);
        });

        if (contador === 0) {
            $('#iconPickerVazio').removeClass('d-none');
        } else {
            $('#iconPickerVazio').addClass('d-none');
        }
    }

    // Registra os eventos do modal
    function registrarEventosModal() {
        // Busca em tempo real
        $(document).on('input', '#iconPickerInputBusca', function () {
            var termo = $(this).val();
            var cat = $('#iconPickerCategorias .btn-cat-filter.active').data('cat');
            renderizarGradeIcones(termo, cat);
        });

        // Limpar busca
        $(document).on('click', '#btnLimparBuscaIcones', function () {
            $('#iconPickerInputBusca').val('').focus();
            var cat = $('#iconPickerCategorias .btn-cat-filter.active').data('cat');
            renderizarGradeIcones('', cat);
        });

        // Filtro por categorias
        $(document).on('click', '.btn-cat-filter', function () {
            $('.btn-cat-filter').removeClass('active btn-danger').addClass('btn-outline-secondary');
            $(this).addClass('active btn-danger').removeClass('btn-outline-secondary');

            var cat = $(this).data('cat');
            var termo = $('#iconPickerInputBusca').val();
            renderizarGradeIcones(termo, cat);
        });

        // Clique num ícone da grade
        $(document).on('click', '.icon-picker-item', function () {
            var iconeEscolhido = $(this).data('icone');
            var nomeEscolhido = $(this).data('nome');

            if ($inputAlvoAtual && $inputAlvoAtual.length > 0) {
                // Atualiza o valor do input alvo
                $inputAlvoAtual.val(iconeEscolhido);

                // Dispara os eventos change e input no campo
                $inputAlvoAtual.trigger('change').trigger('input');

                // Atualiza o preview visual do campo
                atualizarPreviewDoCampo($inputAlvoAtual, iconeEscolhido);

                // Notificação suave com Toastr
                if (typeof toastr !== 'undefined') {
                    toastr.success('Ícone selecionado: <strong>' + nomeEscolhido + '</strong> (' + iconeEscolhido + ')', 'Ícone Atualizado', {
                        timeOut: 2500,
                        progressBar: true
                    });
                }
            }

            // Fecha o modal
            var modalEl = document.getElementById('modalGlobalIconPicker');
            if (modalEl && typeof bootstrap !== 'undefined') {
                var modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        });
    }

    // Atualiza o preview visual anexado ao input
    function atualizarPreviewDoCampo($input, icone) {
        var classePura = normalizarClasseIcone(icone);
        var $group = $input.closest('.foco-icon-picker-group');

        if ($group.length > 0) {
            var $iconElement = $group.find('.foco-icon-preview-tag');
            if ($iconElement.length > 0) {
                $iconElement.attr('class', 'fas ' + classePura + ' text-danger fa-fw foco-icon-preview-tag');
            }
        }
    }

    // Abre o modal de seleção apontando para um input
    window.abrirIconPickerParaInput = function (elementoInput) {
        $inputAlvoAtual = $(elementoInput);
        injetarModalGlobal();

        var valorAtual = normalizarClasseIcone($inputAlvoAtual.val());
        $('#iconPickerAtualIcone').attr('class', 'fas ' + valorAtual);
        $('#iconPickerAtualTexto').text(valorAtual);

        // Reseta filtros
        $('#iconPickerInputBusca').val('');
        $('.btn-cat-filter').removeClass('active btn-danger').addClass('btn-outline-secondary');
        $('.btn-cat-filter[data-cat="todas"]').addClass('active btn-danger').removeClass('btn-outline-secondary');

        renderizarGradeIcones('', 'todas');

        // Exibe o modal
        var modalEl = document.getElementById('modalGlobalIconPicker');
        if (modalEl && typeof bootstrap !== 'undefined') {
            var modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();

            // Foco na busca após abrir
            setTimeout(function () {
                $('#iconPickerInputBusca').focus();
            }, 350);
        }
    };

    // Inicializador Global que transforma campos de ícone em componentes ricos com preview e botão
    window.inicializarIconPickers = function (contexto) {
        var $ctx = contexto ? $(contexto) : $(document);

        // Procura inputs de ícone
        var $inputs = $ctx.find('input[name*="icone"], input[id*="icone"], input.foco-icon-picker');

        $inputs.each(function () {
            var $input = $(this);

            // Ignora se já estiver encapsulado ou tiver marcação especial
            if ($input.hasClass('foco-icon-picker-ready') || $input.data('no-picker')) {
                return;
            }

            $input.addClass('foco-icon-picker-ready');

            var valAtual = $input.val() || 'fa-car';
            var classePura = normalizarClasseIcone(valAtual);

            // Se o input já estiver dentro de um grupo customizado que criamos
            if ($input.closest('.foco-icon-picker-group').length > 0) {
                return;
            }

            // Encapsula o input em um grupo visual elegante
            var isSmall = $input.hasClass('form-control-sm');
            var groupSizeClass = isSmall ? ' input-group-sm' : '';

            var $wrapper = $('<div class="input-group' + groupSizeClass + ' foco-icon-picker-group shadow-sm"></div>');
            $input.before($wrapper);

            // Botão/Preview do Ícone à esquerda
            var $btnPreview = $(
                '<button type="button" class="btn btn-light border foco-icon-preview-btn d-inline-flex align-items-center justify-content-center px-3" title="Clique para escolher outro ícone" style="min-width: 44px;">' +
                '    <i class="fas ' + classePura + ' text-danger fa-fw foco-icon-preview-tag" style="font-size: 1.15rem;"></i>' +
                '</button>'
            );

            // Botão Escolher à direita
            var $btnEscolher = $(
                '<button type="button" class="btn btn-outline-danger fw-semibold btn-abrir-icon-picker d-inline-flex align-items-center gap-1" title="Abrir Seletor Visual de Ícones">' +
                '    <i class="fas fa-icons"></i> <span class="d-none d-sm-inline">Escolher</span>' +
                '</button>'
            );

            $wrapper.append($btnPreview);
            $wrapper.append($input);
            $wrapper.append($btnEscolher);

            // Estiliza o input para ser clicável
            $input.attr('title', 'Clique para abrir o seletor visual de ícones')
                  .css('cursor', 'pointer');
        });
    };

    // Delegação de cliques global para abrir o picker
    $(document).on('click', '.btn-abrir-icon-picker, .foco-icon-preview-btn', function (e) {
        e.preventDefault();
        var $group = $(this).closest('.foco-icon-picker-group');
        var $input = $group.find('input[name*="icone"], input[id*="icone"], input.foco-icon-picker-ready, input');
        if ($input.length > 0) {
            window.abrirIconPickerParaInput($input[0]);
        }
    });

    // Clicar no próprio input de ícone também abre o seletor visual
    $(document).on('click', 'input.foco-icon-picker-ready', function () {
        window.abrirIconPickerParaInput(this);
    });

    // Se o usuário digitar algo manualmente no campo, atualiza o preview em tempo real
    $(document).on('input change', 'input.foco-icon-picker-ready, input[name*="icone"], input[id*="icone"]', function () {
        atualizarPreviewDoCampo($(this), $(this).val());
    });

    // Inicialização ao carregar a página
    $(document).ready(function () {
        injetarModalGlobal();
        window.inicializarIconPickers();

        // Reinicializa ao abrir modais Bootstrap (ex: modal de planos ou estética)
        $(document).on('shown.bs.modal', function () {
            window.inicializarIconPickers(this);
        });

        // Reinicializa ao trocar de abas Bootstrap
        $(document).on('shown.bs.tab', function () {
            window.inicializarIconPickers();
        });
    });

})(jQuery);
