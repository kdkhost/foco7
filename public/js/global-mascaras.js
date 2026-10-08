/**
 * ==============================================================================
 * FOCO 7 - MÁSCARAS BRASILEIRAS E AUTOCOMPLETE DE CEP (VIACEP)
 * ==============================================================================
 */

(function () {
    'use strict';

    // Máscara de Telefone (fixo e celular dinâmico)
    function maskTelefone(value) {
        if (!value) return '';
        var v = value.replace(/\D/g, '');
        if (v.length > 11) v = v.substring(0, 11);
        if (v.length > 10) {
            return v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
        } else if (v.length > 5) {
            return v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
        } else if (v.length > 2) {
            return v.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
        } else {
            return v;
        }
    }

    // Máscara de CPF / CNPJ
    function maskCpfCnpj(value) {
        if (!value) return '';
        var v = value.replace(/\D/g, '');
        if (v.length <= 11) {
            // CPF
            return v.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
                    .replace(/(\d{3})(\d{3})(\d{3})/, '$1.$2.$3')
                    .replace(/(\d{3})(\d{3})/, '$1.$2');
        } else {
            // CNPJ
            if (v.length > 14) v = v.substring(0, 14);
            return v.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
                    .replace(/(\d{2})(\d{3})(\d{3})(\d{4})/, '$1.$2.$3/$4')
                    .replace(/(\d{2})(\d{3})(\d{3})/, '$1.$2.$3')
                    .replace(/(\d{2})(\d{3})/, '$1.$2');
        }
    }

    // Máscara de CEP
    function maskCep(value) {
        if (!value) return '';
        var v = value.replace(/\D/g, '');
        if (v.length > 8) v = v.substring(0, 8);
        return v.replace(/(\d{5})(\d{1,3})/, '$1-$2');
    }

    // Máscara de Moeda (R$)
    function maskMoeda(value) {
        if (!value) return '';
        var v = value.replace(/\D/g, '');
        if (v === '') return '';
        var num = (parseInt(v, 10) / 100).toFixed(2);
        var partes = num.split('.');
        var inteira = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'R$ ' + inteira + ',' + partes[1];
    }

    // Máscara de Placa de Veículo
    function maskPlaca(value) {
        if (!value) return '';
        var v = value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (v.length > 7) v = v.substring(0, 7);
        if (v.length > 3) {
            return v.substring(0, 3) + '-' + v.substring(3);
        }
        return v;
    }

    // Busca de CEP via ViaCEP
    function buscarViaCep(cep, form) {
        var cleanCep = cep.replace(/\D/g, '');
        if (cleanCep.length !== 8) return;

        var url = 'https://viacep.com.br/ws/' + cleanCep + '/json/';
        fetch(url)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.erro) {
                    if (window.foco7Notify) window.foco7Notify.warning('CEP não encontrado.');
                    return;
                }

                if (form) {
                    var $form = typeof jQuery !== 'undefined' ? jQuery(form) : null;
                    if ($form) {
                        $form.find('[name="endereco"], [name="logradouro"]').val(data.logradouro);
                        $form.find('[name="bairro"]').val(data.bairro);
                        $form.find('[name="cidade"]').val(data.localidade);
                        $form.find('[name="estado"], [name="uf"]').val(data.uf);
                        $form.find('[name="numero"]').focus();
                    }
                }
            })
            .catch(function () {
                console.warn('Erro ao consultar ViaCEP');
            });
    }

    // Anexar aos inputs no carregamento
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.mask-telefone').forEach(function (input) {
            input.addEventListener('input', function (e) {
                e.target.value = maskTelefone(e.target.value);
            });
        });

        document.querySelectorAll('.mask-cpf-cnpj').forEach(function (input) {
            input.addEventListener('input', function (e) {
                e.target.value = maskCpfCnpj(e.target.value);
            });
        });

        document.querySelectorAll('.mask-cep').forEach(function (input) {
            input.addEventListener('input', function (e) {
                e.target.value = maskCep(e.target.value);
            });
            input.addEventListener('blur', function (e) {
                buscarViaCep(e.target.value, e.target.closest('form'));
            });
        });

        document.querySelectorAll('.mask-moeda').forEach(function (input) {
            input.addEventListener('input', function (e) {
                e.target.value = maskMoeda(e.target.value);
            });
        });

        document.querySelectorAll('.mask-placa').forEach(function (input) {
            input.addEventListener('input', function (e) {
                e.target.value = maskPlaca(e.target.value);
            });
        });
    });

    window.foco7Mask = {
        telefone: maskTelefone,
        cpfCnpj: maskCpfCnpj,
        cep: maskCep,
        moeda: maskMoeda,
        placa: maskPlaca,
        buscarCep: buscarViaCep
    };
})();
