/**
 * ==============================================================================
 * FOCO 7 - MOTOR GLOBAL DE MÁSCARAS BRASILEIRAS EM TEMPO REAL & AUTOCOMPLETE VIACEP
 * Suporta Digitação (input), Colagem (paste) e Elementos Dinâmicos (Modais/AJAX)
 * Padrões: CPF/CNPJ, Moeda (R$), Datas, Horas, CEP, Telefones e Placas
 * ==============================================================================
 */

(function () {
    'use strict';

    // =========================================================================
    // FUNÇÕES DE FORMATAÇÃO PURA (PADRÃO BRASILEIRO)
    // =========================================================================

    // 1. Telefone & Celular Dinâmico: (99) 9999-9999 ou (99) 99999-9999
    function maskTelefone(value) {
        if (!value) return '';
        var v = String(value).replace(/\D/g, '');
        if (v.length > 11) v = v.substring(0, 11);
        if (v.length > 10) {
            return v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
        } else if (v.length > 6) {
            return v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
        } else if (v.length > 2) {
            return v.replace(/^(\d{2})(\d{0,4})$/, '($1) $2');
        } else if (v.length > 0) {
            return v.replace(/^(\d*)/, '($1');
        }
        return '';
    }

    // 2. CPF e CNPJ Dinâmico: 000.000.000-00 ou 00.000.000/0001-00
    function maskCpfCnpj(value) {
        if (!value) return '';
        var v = String(value).replace(/\D/g, '');
        if (v.length > 14) v = v.substring(0, 14);

        if (v.length <= 11) {
            // Padrão CPF
            if (v.length > 9) {
                return v.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
            } else if (v.length > 6) {
                return v.replace(/^(\d{3})(\d{3})(\d{1,3})$/, '$1.$2.$3');
            } else if (v.length > 3) {
                return v.replace(/^(\d{3})(\d{1,3})$/, '$1.$2');
            }
            return v;
        } else {
            // Padrão CNPJ
            if (v.length > 12) {
                return v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})$/, '$1.$2.$3/$4-$5');
            } else if (v.length > 8) {
                return v.replace(/^(\d{2})(\d{3})(\d{3})(\d{1,4})$/, '$1.$2.$3/$4');
            } else if (v.length > 5) {
                return v.replace(/^(\d{2})(\d{3})(\d{1,3})$/, '$1.$2.$3');
            } else if (v.length > 2) {
                return v.replace(/^(\d{2})(\d{1,3})$/, '$1.$2');
            }
            return v;
        }
    }

    // 3. CEP Brasileiro: 00000-000
    function maskCep(value) {
        if (!value) return '';
        var v = String(value).replace(/\D/g, '');
        if (v.length > 8) v = v.substring(0, 8);
        if (v.length > 5) {
            return v.replace(/^(\d{5})(\d{1,3})$/, '$1-$2');
        }
        return v;
    }

    // 4. Moeda Brasileira: R$ 0,00 até R$ 999.999.999,99
    function maskMoeda(value) {
        if (value === null || value === undefined || value === '') return '';
        var str = String(value);

        // Se veio de colagem com vírgula/ponto decimal direto (ex: "150.50" ou "150,50")
        var v = str.replace(/\D/g, '');
        if (v === '') return '';

        var num = (parseInt(v, 10) / 100).toFixed(2);
        var partes = num.split('.');
        var inteira = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'R$ ' + inteira + ',' + partes[1];
    }

    // 5. Data Brasileira: DD/MM/AAAA
    function maskData(value) {
        if (!value) return '';
        var v = String(value).replace(/\D/g, '');
        if (v.length > 8) v = v.substring(0, 8);
        if (v.length > 4) {
            return v.replace(/^(\d{2})(\d{2})(\d{1,4})$/, '$1/$2/$3');
        } else if (v.length > 2) {
            return v.replace(/^(\d{2})(\d{1,2})$/, '$1/$2');
        }
        return v;
    }

    // 6. Horário Brasileiro: HH:MM ou HH:MM:SS
    function maskHora(value) {
        if (!value) return '';
        var v = String(value).replace(/\D/g, '');
        if (v.length > 6) v = v.substring(0, 6);
        if (v.length > 4) {
            return v.replace(/^(\d{2})(\d{2})(\d{1,2})$/, '$1:$2:$3');
        } else if (v.length > 2) {
            return v.replace(/^(\d{2})(\d{1,2})$/, '$1:$2');
        }
        return v;
    }

    // 7. Data e Horário Combinados: DD/MM/AAAA HH:MM ou DD/MM/AAAA HH:MM:SS
    function maskDataHora(value) {
        if (!value) return '';
        var v = String(value).replace(/\D/g, '');
        if (v.length > 14) v = v.substring(0, 14);

        if (v.length > 12) {
            return v.replace(/^(\d{2})(\d{2})(\d{4})(\d{2})(\d{2})(\d{1,2})$/, '$1/$2/$3 $4:$5:$6');
        } else if (v.length > 10) {
            return v.replace(/^(\d{2})(\d{2})(\d{4})(\d{2})(\d{1,2})$/, '$1/$2/$3 $4:$5');
        } else if (v.length > 8) {
            return v.replace(/^(\d{2})(\d{2})(\d{4})(\d{1,2})$/, '$1/$2/$3 $4');
        } else if (v.length > 4) {
            return v.replace(/^(\d{2})(\d{2})(\d{1,4})$/, '$1/$2/$3');
        } else if (v.length > 2) {
            return v.replace(/^(\d{2})(\d{1,2})$/, '$1/$2');
        }
        return v;
    }

    // 8. Placa de Veículo (Antiga ABC-1234 e Mercosul ABC1D23)
    function maskPlaca(value) {
        if (!value) return '';
        var v = String(value).toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (v.length > 7) v = v.substring(0, 7);
        if (v.length > 3) {
            return v.substring(0, 3) + '-' + v.substring(3);
        }
        return v;
    }

    // =========================================================================
    // IDENTIFICADORES INTELIGENTES DE TIPO DE CAMPO
    // =========================================================================
    function obterTipoMascara(input) {
        if (!input || input.type === 'hidden' || input.type === 'file' || input.type === 'checkbox' || input.type === 'radio') {
            return null;
        }

        var cls = input.className || '';
        var name = (input.name || '').toLowerCase();
        var dataMask = (input.getAttribute('data-mask') || '').toLowerCase();
        var id = (input.id || '').toLowerCase();

        // 1. CEP
        if (cls.indexOf('mask-cep') !== -1 || dataMask === 'cep' || name === 'cep' || name === 'endereco_cep' || id === 'cep') {
            return 'cep';
        }

        // 2. CPF / CNPJ / Documento
        if (cls.indexOf('mask-cpf-cnpj') !== -1 || cls.indexOf('mask-cpf') !== -1 || cls.indexOf('mask-cnpj') !== -1 || cls.indexOf('mask-documento') !== -1 ||
            dataMask === 'cpf' || dataMask === 'cnpj' || dataMask === 'cpf-cnpj' || dataMask === 'documento' ||
            name === 'cpf' || name === 'cnpj' || name === 'cpf_cnpj' || name === 'documento') {
            return 'cpfCnpj';
        }

        // 3. Moeda / Dinheiro / Valor
        if (cls.indexOf('mask-moeda') !== -1 || cls.indexOf('mask-dinheiro') !== -1 || cls.indexOf('mask-valor') !== -1 ||
            dataMask === 'moeda' || dataMask === 'dinheiro' || dataMask === 'valor' ||
            name === 'preco' || name === 'preco_mensal' || name === 'valor_multa' || name === 'valor_juros' || name === 'taxa' || name === 'desconto') {
            return 'moeda';
        }

        // 4. Data e Hora combinadas
        if (cls.indexOf('mask-data-hora') !== -1 || dataMask === 'data-hora') {
            return 'dataHora';
        }

        // 5. Data (DD/MM/AAAA) - Apenas se não for input nativo type="date"
        if (input.type !== 'date') {
            if (cls.indexOf('mask-data') !== -1 || cls.indexOf('mask-date') !== -1 || dataMask === 'data' || dataMask === 'date' ||
                name === 'data' || name === 'data_nascimento' || name === 'data_vencimento' || name === 'data_agendamento' || id === 'data_vencimento') {
                return 'data';
            }
        }

        // 6. Horário (HH:MM / HH:MM:SS) - Apenas se não for input nativo type="time"
        if (input.type !== 'time') {
            if (cls.indexOf('mask-hora') !== -1 || cls.indexOf('mask-horario') !== -1 || dataMask === 'hora' || dataMask === 'horario' ||
                name === 'hora' || name === 'horario') {
                return 'hora';
            }
        }

        // 7. Telefone / Celular / WhatsApp
        if (cls.indexOf('mask-telefone') !== -1 || cls.indexOf('mask-celular') !== -1 || cls.indexOf('mask-fone') !== -1 ||
            dataMask === 'telefone' || dataMask === 'celular' ||
            name === 'telefone' || name === 'whatsapp' || name === 'celular' || name === 'contato_telefone' || name === 'contato_whatsapp_formatado') {
            return 'telefone';
        }

        // 8. Placa
        if (cls.indexOf('mask-placa') !== -1 || dataMask === 'placa' || name === 'placa') {
            return 'placa';
        }

        return null;
    }

    // =========================================================================
    // CONSULTA AUTOMÁTICA VIACEP DURANTE A DIGITAÇÃO (TEMPO REAL)
    // =========================================================================
    var ultimoCepBuscado = '';
    var requisicaoCepEmAndamento = false;

    function consultarViaCepEmTempoReal(cepDigitado, inputCep) {
        var cleanCep = String(cepDigitado).replace(/\D/g, '');

        if (cleanCep.length !== 8) {
            return;
        }

        if (cleanCep === ultimoCepBuscado && !requisicaoCepEmAndamento) {
            return;
        }

        ultimoCepBuscado = cleanCep;
        requisicaoCepEmAndamento = true;

        // Container contextual (Modal, Card, Tab ou Form mais próximo)
        var container = inputCep ? inputCep.closest('form, .modal-content, .card-body, .tab-pane, body') : document;

        // Feedback visual no campo de CEP
        if (inputCep) {
            inputCep.classList.add('is-validating-cep');
        }

        var url = 'https://viacep.com.br/ws/' + cleanCep + '/json/';
        fetch(url)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                requisicaoCepEmAndamento = false;
                if (inputCep) inputCep.classList.remove('is-validating-cep');

                if (data.erro) {
                    if (window.foco7Notify) {
                        window.foco7Notify.warning('CEP não localizado nos Correios. Preencha os campos manualmente.');
                    }
                    return;
                }

                if (container) {
                    var $c = typeof jQuery !== 'undefined' ? jQuery(container) : null;
                    if ($c) {
                        // Preenche Logradouro / Rua
                        var $logradouro = $c.find('[name="endereco_logradouro"], [name="logradouro"], [name="rua"], [name="endereco"]');
                        if ($logradouro.length && data.logradouro) {
                            $logradouro.val(data.logradouro).trigger('input').trigger('change');
                        }

                        // Preenche Bairro
                        var $bairro = $c.find('[name="endereco_bairro"], [name="bairro"]');
                        if ($bairro.length && data.bairro) {
                            $bairro.val(data.bairro).trigger('input').trigger('change');
                        }

                        // Preenche Cidade / Localidade
                        var $cidade = $c.find('[name="endereco_cidade"], [name="cidade"], [name="localidade"]');
                        if ($cidade.length && data.localidade) {
                            $cidade.val(data.localidade).trigger('input').trigger('change');
                        }

                        // Preenche Estado / UF
                        var $uf = $c.find('[name="endereco_estado"], [name="estado"], [name="uf"]');
                        if ($uf.length && data.uf) {
                            $uf.val(data.uf.toUpperCase()).trigger('input').trigger('change');
                        }

                        // Foco inteligente e automático no campo Número
                        var $numero = $c.find('[name="endereco_numero"], [name="numero"]');
                        if ($numero.length) {
                            setTimeout(function () {
                                $numero.focus().select();
                            }, 50);
                        }
                    } else {
                        // Fallback puro vanilla JS
                        var setVal = function(sel, val) {
                            var el = container.querySelector(sel);
                            if (el && val) {
                                el.value = val;
                                el.dispatchEvent(new Event('input', { bubbles: true }));
                                el.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        };
                        setVal('[name="endereco_logradouro"], [name="logradouro"], [name="rua"], [name="endereco"]', data.logradouro);
                        setVal('[name="endereco_bairro"], [name="bairro"]', data.bairro);
                        setVal('[name="endereco_cidade"], [name="cidade"], [name="localidade"]', data.localidade);
                        setVal('[name="endereco_estado"], [name="estado"], [name="uf"]', data.uf ? data.uf.toUpperCase() : '');

                        var numInput = container.querySelector('[name="endereco_numero"], [name="numero"]');
                        if (numInput) {
                            setTimeout(function () { numInput.focus(); }, 50);
                        }
                    }

                    if (window.foco7Notify) {
                        window.foco7Notify.success('Endereço preenchido automaticamente para o CEP ' + maskCep(cleanCep) + '!');
                    }
                }
            })
            .catch(function () {
                requisicaoCepEmAndamento = false;
                if (inputCep) inputCep.classList.remove('is-validating-cep');
            });
    }

    // =========================================================================
    // APLICADOR CENTRAL DE MÁSCARA POR TIPO
    // =========================================================================
    function aplicarMascara(input, tipo, valorOriginal, dispararBuscaCep) {
        var valor = valorOriginal !== undefined ? valorOriginal : input.value;
        var formatado = valor;

        switch (tipo) {
            case 'cep':
                formatado = maskCep(valor);
                break;
            case 'cpfCnpj':
                formatado = maskCpfCnpj(valor);
                break;
            case 'moeda':
                formatado = maskMoeda(valor);
                break;
            case 'data':
                formatado = maskData(valor);
                break;
            case 'hora':
                formatado = maskHora(valor);
                break;
            case 'dataHora':
                formatado = maskDataHora(valor);
                break;
            case 'telefone':
                formatado = maskTelefone(valor);
                break;
            case 'placa':
                formatado = maskPlaca(valor);
                break;
        }

        if (input.value !== formatado) {
            input.value = formatado;
        }

        // Se for CEP e tiver completado 8 dígitos, busca imediatamente APENAS se for digitação/interação ativa do usuário
        if (tipo === 'cep' && dispararBuscaCep === true) {
            var limpo = formatado.replace(/\D/g, '');
            if (limpo.length === 8) {
                consultarViaCepEmTempoReal(limpo, input);
            }
        }
    }

    // =========================================================================
    // DELEGAÇÃO GLOBAL DE EVENTOS (INPUT & PASTE EM TEMPO REAL)
    // =========================================================================

    // Evento INPUT (digitação contínua em tempo real pelo usuário)
    document.addEventListener('input', function (e) {
        var input = e.target;
        if (!input || input.tagName !== 'INPUT') return;

        var tipo = obterTipoMascara(input);
        if (tipo) {
            // Passa dispararBuscaCep = true porque o usuário está ativamente interagindo
            aplicarMascara(input, tipo, undefined, true);
        }
    }, true);

    // Evento PASTE (colagem de texto com formatação imediata pelo usuário)
    document.addEventListener('paste', function (e) {
        var input = e.target;
        if (!input || input.tagName !== 'INPUT') return;

        var tipo = obterTipoMascara(input);
        if (tipo) {
            var textoColado = (e.clipboardData || window.clipboardData)?.getData('text');
            if (textoColado !== undefined && textoColado !== null) {
                e.preventDefault();
                // Passa dispararBuscaCep = true porque é ação explícita do usuário
                aplicarMascara(input, tipo, textoColado, true);
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }, true);

    // Formata campos pré-preenchidos no carregamento do DOM (SEM disparar requisições ou alertas)
    function inicializarCamposExistentes() {
        var inputs = document.querySelectorAll('input');
        inputs.forEach(function (input) {
            var tipo = obterTipoMascara(input);
            if (tipo && input.value) {
                // Se for CEP já preenchido na inicialização, memoriza o valor para evitar re-busca automática
                if (tipo === 'cep') {
                    ultimoCepBuscado = String(input.value).replace(/\D/g, '');
                }
                // dispararBuscaCep = false (apenas formata a máscara sem consultar API externa)
                aplicarMascara(input, tipo, undefined, false);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarCamposExistentes);
    } else {
        inicializarCamposExistentes();
    }

    // Observa injeção de novos elementos dinâmicos no DOM (Modais, AJAX)
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'INPUT') {
                            var tipo = obterTipoMascara(node);
                            if (tipo && node.value) aplicarMascara(node, tipo, undefined, false);
                        } else if (node.querySelectorAll) {
                            node.querySelectorAll('input').forEach(function (inp) {
                                var tipo = obterTipoMascara(inp);
                                if (tipo && inp.value) aplicarMascara(inp, tipo, undefined, false);
                            });
                        }
                    }
                });
            });
        });

        observer.observe(document.body || document.documentElement, {
            childList: true,
            subtree: true
        });
    }

    // =========================================================================
    // EXPORTAÇÃO GLOBAL DE MÁSCARAS
    // =========================================================================
    window.foco7Mask = {
        telefone: maskTelefone,
        cpfCnpj: maskCpfCnpj,
        cep: maskCep,
        moeda: maskMoeda,
        data: maskData,
        hora: maskHora,
        dataHora: maskDataHora,
        placa: maskPlaca,
        buscarCep: consultarViaCepEmTempoReal,
        aplicar: aplicarMascara,
        inicializar: inicializarCamposExistentes
    };
})();
