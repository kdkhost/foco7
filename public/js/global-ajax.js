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

    // 4. CONFIGURAÇÃO DO TOKEN CSRF PARA AJAX JQUERY (se presente)
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

            // Manipulador global para submissão de formulários com classe .ajax-form
            $(document).on('submit', 'form.ajax-form', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $form.find('[type="submit"]');
                var originalHtml = $btn.html();
                var url = $form.attr('action');
                var method = ($form.attr('method') || 'POST').toUpperCase();
                var isFormData = $form.attr('enctype') === 'multipart/form-data';

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
                            if (res.redirect) {
                                setTimeout(function () {
                                    window.location.href = res.redirect;
                                }, 1200);
                            } else if (res.reload) {
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1200);
                            } else if ($form.hasClass('reset-on-success')) {
                                $form[0].reset();
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
        });
    }
})();
