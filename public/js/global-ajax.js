/**
 * ==============================================================================
 * FOCO 7 - INTERCEPTADOR GLOBAL AJAX, TOASTR E SWEETALERT2
 * Regra do Sistema: Nenhum alert() nativo permitido. SweetAlert2 e Toastr globais.
 * ==============================================================================
 */

(function () {
    'use strict';

    // 1. BLINDAGEM CONTRA ALERT NATIVO (Regra Global Estrita)
    window.alert = function (message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Atenção',
                html: message,
                icon: 'info',
                confirmButtonColor: '#d90000',
                confirmButtonText: 'OK'
            });
        } else {
            console.warn('[Foco 7 Alerta]:', message);
        }
    };

    // 2. CONFIGURAÇÃO PADRÃO DO TOASTR
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            preventDuplicates: false,
            showDuration: '300',
            hideDuration: '1000',
            timeOut: '5000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };
    }

    // 3. FUNÇÕES GLOBAIS DE NOTIFICAÇÃO E CONFIRMAÇÃO
    window.foco7Notify = {
        success: function (msg, title) {
            if (typeof toastr !== 'undefined') toastr.success(msg, title || 'Sucesso');
            else console.log('SUCCESS:', msg);
        },
        error: function (msg, title) {
            if (typeof toastr !== 'undefined') toastr.error(msg, title || 'Erro');
            else console.error('ERROR:', msg);
        },
        warning: function (msg, title) {
            if (typeof toastr !== 'undefined') toastr.warning(msg, title || 'Atenção');
            else console.warn('WARNING:', msg);
        },
        info: function (msg, title) {
            if (typeof toastr !== 'undefined') toastr.info(msg, title || 'Informação');
            else console.info('INFO:', msg);
        }
    };

    /**
     * Modal de confirmação padrão com SweetAlert2
     * @param {string} titulo
     * @param {string} texto
     * @param {string} icone 'warning' | 'question' | 'danger'
     * @param {function} onConfirm Callback executado se confirmado
     */
    window.confirmarAcao = function (titulo, texto, icone, onConfirm) {
        if (typeof Swal === 'undefined') {
            if (confirm(texto || titulo)) onConfirm();
            return;
        }

        Swal.fire({
            title: titulo || 'Tem certeza?',
            text: texto || 'Esta ação não poderá ser desfeita.',
            icon: icone || 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, confirmar!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    };

    window.foco7Confirm = function (titulo, texto, onConfirm) {
        window.confirmarAcao(titulo, texto, 'question', onConfirm);
    };

    // 4. SISTEMA GLOBAL DE TOOLTIPS BOOTSTRAP 5
    // Substitui títulos nativos estáticos (title) por tooltips visuais modernos e elegantes
    window.initTooltips = function (container) {
        if (typeof bootstrap === 'undefined' || typeof bootstrap.Tooltip === 'undefined') return;

        var root = container || document;
        if (typeof jQuery !== 'undefined' && container instanceof jQuery) {
            root = container[0] || document;
        }
        if (!root || !root.querySelectorAll) return;

        var selector = '[data-bs-toggle="tooltip"], [title]:not(iframe):not(script):not(style):not(meta):not(link):not(head):not(title)';
        var elements = root.querySelectorAll(selector);

        Array.prototype.forEach.call(elements, function (el) {
            if (el.tagName === 'BODY' || el.tagName === 'HTML') return;

            var titleVal = el.getAttribute('title') || el.getAttribute('data-bs-original-title');
            if (!titleVal || !titleVal.trim()) return;

            var existing = bootstrap.Tooltip.getInstance(el);
            if (!existing) {
                try {
                    new bootstrap.Tooltip(el, {
                        trigger: 'hover',
                        boundary: 'clippingParents',
                        container: 'body',
                        animation: true
                    });
                } catch (e) {
                    // Silencia caso o elemento não suporte Popper
                }
            }
        });
    };

    // Oculta tooltip ao clicar no elemento para evitar persistência visual indesejada
    document.addEventListener('click', function (e) {
        var trigger = e.target ? e.target.closest('[data-bs-toggle="tooltip"], [data-bs-original-title], [title]') : null;
        if (trigger && typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var inst = bootstrap.Tooltip.getInstance(trigger);
            if (inst) inst.hide();
        }
    }, true);

    // Auto-inicialização na carga do documento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            window.initTooltips();
            iniciarObserverTooltips();
        });
    } else {
        window.initTooltips();
        iniciarObserverTooltips();
    }

    function iniciarObserverTooltips() {
        if (typeof MutationObserver !== 'undefined' && document.body) {
            var observerTimer = null;
            var observer = new MutationObserver(function () {
                if (observerTimer) clearTimeout(observerTimer);
                observerTimer = setTimeout(function () {
                    window.initTooltips();
                }, 200);
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
    }

    // 5. CONFIGURAÇÃO DO TOKEN CSRF PARA AJAX JQUERY (se presente)
    if (typeof jQuery !== 'undefined') {
        jQuery(function ($) {
            var token = $('meta[name="csrf-token"]').attr('content');
            if (token) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            }

            // Reinicialização de tooltips após cada requisição AJAX
            $(document).ajaxComplete(function () {
                setTimeout(function () {
                    window.initTooltips();
                }, 100);
            });

            // Manipulador global para submissão de formulários com classe .ajax-form
            $(document).on('submit', 'form.ajax-form', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $form.find('[type="submit"]');
                var originalHtml = $btn.html();
                var url = $form.attr('action');
                var method = ($form.attr('method') || 'POST').toUpperCase();
                var isFormData = $form.attr('enctype') === 'multipart/form-data' || $form.find('input[type="file"]').length > 0;

                // Sincronização explícita de campos Summernote WYSIWYG
                if (typeof $.fn.summernote !== 'undefined') {
                    $form.find('.summernote').each(function () {
                        var $s = $(this);
                        if ($s.next('.note-editor').length) {
                            $s.val($s.summernote('code'));
                        }
                    });
                }

                var data = isFormData ? new FormData(this) : $form.serialize();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processando...');

                var ajaxOptions = {
                    url: url,
                    type: method,
                    data: data,
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(originalHtml);

                        if (res.success) {
                            window.foco7Notify.success(res.message || 'Operação realizada com sucesso!');

                            var $modal = $form.closest('.modal');

                            // Limpeza universal do formulário (exceto formulários de configurações com .keep-data)
                            if (!$form.hasClass('keep-data')) {
                                $form[0].reset();
                                $form.find('input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]), textarea').val('');
                                $form.find('select').each(function () {
                                    $(this).prop('selectedIndex', 0);
                                });
                                $form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
                                $form.find('input[type="hidden"]:not([name="_token"]):not([name="_method"])').val('');
                                
                                if (typeof $.fn.summernote !== 'undefined') {
                                    $form.find('.summernote').each(function () {
                                        $(this).summernote('reset');
                                    });
                                }
                                if (window.FocoDropzone) {
                                    $form.find('.foco-dropzone').each(function () {
                                        window.FocoDropzone.reset(this);
                                    });
                                }
                                $form.find('.focodropzone-preview').empty();
                                $form.find('.focodropzone-file-input').val('');
                                $form.trigger('form:cleared');
                            }

                            if (res.redirect) {
                                setTimeout(function () {
                                    window.location.href = res.redirect;
                                }, 1000);
                            } else if (res.reload) {
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            } else if ($modal.length) {
                                var modalInst = bootstrap.Modal.getInstance($modal[0]);
                                if (modalInst) {
                                    modalInst.hide();
                                } else {
                                    $modal.modal('hide');
                                }
                                setTimeout(function () {
                                    window.location.reload();
                                }, 600);
                            }
                        } else {
                            window.foco7Notify.error(res.message || 'Ocorreu um erro.');
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        var errMsg = 'Ocorreu um erro inesperado ao processar sua solicitação.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            }
                            if (xhr.responseJSON.errors) {
                                var errList = Object.values(xhr.responseJSON.errors);
                                if (errList.length > 0 && errList[0][0]) {
                                    errMsg = errList[0][0];
                                }
                            }
                        }

                        window.foco7Notify.error(errMsg);
                    }
                };

                if (isFormData) {
                    ajaxOptions.processData = false;
                    ajaxOptions.contentType = false;
                }

                $.ajax(ajaxOptions);
            });

            // Garante que todo formulário dentro de modal seja mantido 100% limpo e vazio ao fechar
            $(document).on('hidden.bs.modal', '.modal', function () {
                var $modal = $(this);
                var $f = $modal.find('form.ajax-form');
                if ($f.length && !$f.hasClass('keep-data')) {
                    $f[0].reset();
                    $f.find('input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]), textarea').val('');
                    $f.find('select').each(function () {
                        $(this).prop('selectedIndex', 0);
                    });
                    $f.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
                    $f.find('input[type="hidden"]:not([name="_token"]):not([name="_method"])').val('');
                    if (typeof $.fn.summernote !== 'undefined') {
                        $f.find('.summernote').each(function () {
                            $(this).summernote('reset');
                        });
                    }
                    if (window.FocoDropzone) {
                        $f.find('.foco-dropzone').each(function () {
                            window.FocoDropzone.reset(this);
                        });
                    }
                    $f.find('.focodropzone-preview').empty();
                    $f.find('.focodropzone-file-input').val('');
                    $f.trigger('form:cleared');
                }
            });

            // Alternador Universal de Visualização de Senha (eye / eye-slash)
            $(document).on('click', '.btn-toggle-password', function (e) {
                e.preventDefault();
                var target = $(this).data('target');
                var $input = target ? $(target) : $(this).closest('.input-group').find('input[type="password"], input[type="text"]');
                var $icon = $(this).find('i');
                if ($input.length && $input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $icon.removeClass('fa-eye').addClass('fa-eye-slash text-danger');
                } else if ($input.length) {
                    $input.attr('type', 'password');
                    $icon.removeClass('fa-eye-slash text-danger').addClass('fa-eye text-secondary');
                }
            });
        });
    }
})();
