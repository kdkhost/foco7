/**
 * ==============================================================================
 * FOCO 7 - COMPONENTE GLOBAL DE UPLOAD COM ARRASTA E SOLTA (DRAG & DROP)
 * Suporte Universal a Imagens, Certificados .PEM, PDFs, Previews e Remoção
 * ==============================================================================
 */

(function ($) {
    'use strict';

    window.FocoDropzone = {
        init: function (selector) {
            var $elements = $(selector || '.foco-dropzone');

            $elements.each(function () {
                var $container = $(this);
                if ($container.data('foco-dropzone-initialized')) return;
                $container.data('foco-dropzone-initialized', true);

                var inputName = $container.data('input-name') || 'arquivo';
                var accept = $container.data('accept') || 'image/png,image/jpeg,image/webp';
                var ratioText = $container.data('ratio') || 'Proporção livre';
                var maxSizeText = $container.data('max-size') || '5MB';
                var currentPreview = $container.data('current-preview') || '';
                var isCert = accept.includes('.pem');
                var isPdf = accept.includes('application/pdf');
                var isImage = !isCert && !isPdf;

                var uniqueId = 'dropzone_input_' + Math.random().toString(36).substring(2, 9);
                var currentFileName = currentPreview ? currentPreview.split('/').pop() : '';

                var templateHtml = `
                    <div class="foco-dropzone-wrapper">
                        <input type="file" id="${uniqueId}" name="${inputName}" accept="${accept}" class="foco-dropzone-input d-none">
                        
                        <div class="foco-dropzone-box text-center p-4">
                            <!-- Estado Vazio / Inicial -->
                            <div class="foco-dropzone-empty ${currentPreview ? 'd-none' : ''}">
                                <div class="foco-dropzone-icon mb-2">
                                    <i class="fas ${isCert ? 'fa-certificate text-success' : (isPdf ? 'fa-file-pdf text-danger' : 'fa-cloud-upload-alt text-danger')} fs-1"></i>
                                </div>
                                <h6 class="fw-bold foco-dropzone-title mb-1">Arraste e solte o arquivo aqui</h6>
                                <p class="text-secondary small mb-3">ou <span class="text-danger fw-bold text-decoration-underline">clique para procurar no computador</span></p>
                                
                                <div class="foco-dropzone-specs d-inline-flex flex-wrap gap-2 justify-content-center">
                                    <span class="badge bg-dark-subtle border text-body py-1 px-2 font-monospace">
                                        <i class="fas ${isCert ? 'fa-shield-alt text-success' : 'fa-crop-alt text-danger'} me-1"></i> ${ratioText}
                                    </span>
                                    <span class="badge bg-dark-subtle border text-body py-1 px-2 font-monospace">
                                        <i class="fas fa-hdd text-info me-1"></i> Máx: ${maxSizeText}
                                    </span>
                                </div>
                            </div>

                            <!-- Estado com Preview / Arquivo Carregado -->
                            <div class="foco-dropzone-preview ${currentPreview ? '' : 'd-none'}">
                                <div class="preview-media-container position-relative d-inline-block mb-3">
                                    ${isImage 
                                        ? `<img src="${currentPreview}" alt="Preview" class="img-fluid rounded border border-secondary shadow-sm foco-preview-img" style="max-height: 180px; object-fit: contain;">`
                                        : `<div class="p-3 bg-dark-subtle rounded border border-secondary text-body font-monospace text-center">
                                             <i class="fas ${isCert ? 'fa-certificate text-success' : 'fa-file-alt text-warning'} fs-1 mb-2 d-block"></i>
                                             <div class="small fw-bold preview-file-name">${currentFileName || 'Arquivo anexado'}</div>
                                           </div>`
                                    }
                                </div>
                                
                                <div class="preview-info-box small text-secondary mb-2">
                                    <span class="fw-semibold preview-title">${currentFileName ? 'Arquivo: ' + currentFileName : ''}</span>
                                    <span class="preview-specs-detected ms-2 badge bg-danger"></span>
                                </div>

                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-change-file">
                                        <i class="fas fa-sync-alt me-1"></i> Trocar Arquivo
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-file">
                                        <i class="fas fa-trash-alt me-1"></i> Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $container.html(templateHtml);

                var $box = $container.find('.foco-dropzone-box');
                var $input = $container.find('.foco-dropzone-input');
                var $empty = $container.find('.foco-dropzone-empty');
                var $preview = $container.find('.foco-dropzone-preview');
                var $img = $container.find('.foco-preview-img');
                var $fileNameEl = $container.find('.preview-file-name');
                var $specsBadge = $container.find('.preview-specs-detected');
                var $title = $container.find('.preview-title');
                var $btnChange = $container.find('.btn-change-file');
                var $btnRemove = $container.find('.btn-remove-file');

                // Clique para abrir seletor
                $box.on('click', function (e) {
                    if ($(e.target).closest('button').length) return;
                    $input.trigger('click');
                });

                $btnChange.on('click', function (e) {
                    e.stopPropagation();
                    $input.trigger('click');
                });

                $btnRemove.on('click', function (e) {
                    e.stopPropagation();
                    $input.val('');
                    if (isImage) $img.attr('src', '');
                    $preview.addClass('d-none');
                    $empty.removeClass('d-none');

                    // Sincroniza campos hidden de remoção para o backend
                    $container.find('input[name^="remover_"]').remove();
                    $container.append(`<input type="hidden" name="remover_${inputName}" value="1">`);

                    if (inputName.startsWith('efi_certificado_')) {
                        var sufixo = inputName.replace('efi_certificado_', '');
                        $container.append(`<input type="hidden" name="remover_efi_cert_${sufixo}" value="1">`);
                    }
                    if (inputName === 'app_mockup_upload') {
                        $container.append(`<input type="hidden" name="remover_app_mockup" value="1">`);
                    }
                    if (inputName === 'og_image_upload') {
                        $container.append(`<input type="hidden" name="remover_og_image" value="1">`);
                    }
                    if (inputName === 'hero_bg_upload') {
                        $container.append(`<input type="hidden" name="remover_hero_bg" value="1">`);
                    }
                });

                // Drag & Drop visual feedback
                $box.on('dragover dragenter', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $box.addClass('is-dragover');
                });

                $box.on('dragleave dragend drop', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $box.removeClass('is-dragover');
                });

                $box.on('drop', function (e) {
                    var droppedFiles = e.originalEvent.dataTransfer.files;
                    if (droppedFiles && droppedFiles.length > 0) {
                        $input[0].files = droppedFiles;
                        processFile(droppedFiles[0]);
                    }
                });

                $input.on('change', function () {
                    if (this.files && this.files.length > 0) {
                        processFile(this.files[0]);
                    }
                });

                function processFile(file) {
                    $container.find('input[name^="remover_"]').remove();

                    // Validação de tamanho (padrão 10MB)
                    var maxBytes = 10 * 1024 * 1024;
                    if (file.size > maxBytes) {
                        if (window.foco7Notify) {
                            window.foco7Notify.error('O arquivo excede o limite permitido de ' + maxSizeText);
                        }
                        $input.val('');
                        return;
                    }

                    var sizeFormatted = (file.size / 1024).toFixed(1) + ' KB';
                    if (file.size > 1024 * 1024) {
                        sizeFormatted = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                    }

                    $title.text(file.name + ' (' + sizeFormatted + ')');
                    if ($fileNameEl.length) {
                        $fileNameEl.text(file.name + ' (' + sizeFormatted + ')');
                    }

                    if (file.type.startsWith('image/')) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var imgObj = new Image();
                            imgObj.onload = function () {
                                $specsBadge.text(this.width + ' × ' + this.height + ' px');
                            };
                            imgObj.src = e.target.result;

                            $img.attr('src', e.target.result);
                            $empty.addClass('d-none');
                            $preview.removeClass('d-none');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // Não é imagem (ex: .pem certificado ou .pdf)
                        var ext = file.name.split('.').pop().toUpperCase();
                        $specsBadge.text(ext);
                        $empty.addClass('d-none');
                        $preview.removeClass('d-none');
                    }

                    if (window.foco7Notify) {
                        window.foco7Notify.info('Arquivo anexado com sucesso. Clique em salvar para confirmar.');
                    }
                }
            });
        },

        // Helper para definir preview dinamicamente (ex: modais de edição)
        setPreview: function (selector, fileUrl, displayName) {
            var $container = $(selector);
            if (!$container.length) return;

            var $empty = $container.find('.foco-dropzone-empty');
            var $preview = $container.find('.foco-dropzone-preview');
            var $img = $container.find('.foco-preview-img');
            var $fileNameEl = $container.find('.preview-file-name');
            var $title = $container.find('.preview-title');
            var $specsBadge = $container.find('.preview-specs-detected');

            if (fileUrl) {
                var name = displayName || fileUrl.split('/').pop();
                if ($img.length) {
                    $img.attr('src', fileUrl);
                }
                if ($fileNameEl.length) {
                    $fileNameEl.text(name);
                }
                $title.text(name);
                $specsBadge.text('');
                $empty.addClass('d-none');
                $preview.removeClass('d-none');
            } else {
                this.reset(selector);
            }
        },

        // Helper para resetar o dropzone (ex: ao fechar ou abrir novo modal)
        reset: function (selector) {
            var $container = $(selector);
            if (!$container.length) return;

            var $input = $container.find('.foco-dropzone-input');
            var $empty = $container.find('.foco-dropzone-empty');
            var $preview = $container.find('.foco-dropzone-preview');
            var $img = $container.find('.foco-preview-img');
            var $fileNameEl = $container.find('.preview-file-name');

            $input.val('');
            if ($img.length) $img.attr('src', '');
            if ($fileNameEl.length) $fileNameEl.text('Arquivo anexado');
            $preview.addClass('d-none');
            $empty.removeClass('d-none');
        }
    };

    // Auto-inicialização quando o DOM carregar
    $(function () {
        window.FocoDropzone.init();
    });

})(jQuery);
