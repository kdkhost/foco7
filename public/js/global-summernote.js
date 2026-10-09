/**
 * ==============================================================================
 * FOCO 7 - INICIALIZADOR GLOBAL DO SUMMERNOTE WYSIWYG (BOOTSTRAP 5)
 * Padronização de Editor Rico em Todo o Painel Administrativo CMS
 * ==============================================================================
 */

(function ($) {
    'use strict';

    if (typeof $ === 'undefined') {
        console.warn('[Foco 7]: jQuery é obrigatório para carregar o Summernote.');
        return;
    }

    /**
     * Inicializa instâncias do Summernote em qualquer textarea com .summernote
     * @param {string|HTMLElement|jQuery} container Contexto ou seletor opcional
     */
    window.initSummernote = function (container) {
        if (!$.fn.summernote) {
            console.warn('[Foco 7]: Plugin Summernote não está carregado.');
            return;
        }

        var $target = container ? $(container).find('.summernote') : $('.summernote');

        $target.each(function () {
            var $textarea = $(this);

            // Evita reinicialização duplicada
            if ($textarea.next('.note-editor').length > 0) {
                return;
            }

            var altura = parseInt($textarea.data('height')) || 180;
            var placeholder = $textarea.attr('placeholder') || 'Digite e formate o conteúdo visual aqui...';

            $textarea.summernote({
                lang: 'pt-BR',
                height: altura,
                minHeight: 100,
                placeholder: placeholder,
                dialogsInBody: true,
                dialogsFade: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                styleTags: [
                    'p',
                    { title: 'Título 2', tag: 'h2', className: 'fw-bold', value: 'h2' },
                    { title: 'Título 3', tag: 'h3', className: 'fw-bold', value: 'h3' },
                    { title: 'Título 4', tag: 'h4', className: 'fw-semibold', value: 'h4' },
                    'blockquote'
                ],
                callbacks: {
                    onChange: function (contents) {
                        $textarea.val(contents);
                        $textarea.trigger('change').trigger('input').trigger('summernote.change', [contents]);
                    },
                    onInit: function () {
                        // Aplica classes do bootstrap para harmonia estética
                        $textarea.next('.note-editor').addClass('foco-summernote-editor shadow-sm');
                    }
                }
            });
        });
    };

    /**
     * Define o conteúdo HTML no Summernote de forma segura
     */
    window.setSummernoteContent = function (seletor, html) {
        var $el = $(seletor);
        if ($el.length && $.fn.summernote) {
            $el.summernote('code', html || '');
            $el.val(html || '');
        }
    };

    /**
     * Obtém o conteúdo HTML do Summernote
     */
    window.getSummernoteContent = function (seletor) {
        var $el = $(seletor);
        if ($el.length && $.fn.summernote) {
            return $el.summernote('code');
        }
        return '';
    };

    /**
     * Limpa o conteúdo do Summernote
     */
    window.clearSummernote = function (seletor) {
        window.setSummernoteContent(seletor, '');
    };

    $(function () {
        // 1. Inicializa todos os Summernotes na carga da página
        window.initSummernote();

        // 2. Sincronização automática e mandatória antes do submit de qualquer form
        $(document).on('submit', 'form', function () {
            var $form = $(this);
            $form.find('.summernote').each(function () {
                var $t = $(this);
                if ($.fn.summernote && $t.next('.note-editor').length) {
                    $t.val($t.summernote('code'));
                }
            });
        });

        // 3. Atualiza visibilidade ao alternar abas Bootstrap 5
        $(document).on('shown.bs.tab', 'button[data-bs-toggle="tab"], a[data-bs-toggle="tab"]', function (e) {
            var target = $(e.target).attr('data-bs-target') || $(e.target).attr('href');
            if (target) {
                window.initSummernote(target);
            }
        });

        // 4. Garante inicialização ao abrir modais Bootstrap 5
        $(document).on('shown.bs.modal', '.modal', function () {
            window.initSummernote(this);
        });
    });

})(window.jQuery);
