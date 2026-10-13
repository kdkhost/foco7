/**
 * ==============================================================================
 * FOCO 7 - ANALISADOR E PREVIEW DE SEO EM TEMPO REAL (GOOGLE & SOCIAL CARDS)
 * ==============================================================================
 */

(function () {
    'use strict';

    function initSeoAnalyzer() {
        var titleInput = document.getElementById('seo_meta_title');
        var descInput = document.getElementById('seo_meta_description');
        var keywordsInput = document.getElementById('seo_meta_keywords');
        var ogTitleInput = document.getElementById('seo_og_title');
        var ogDescInput = document.getElementById('seo_og_description');
        var canonicalInput = document.getElementById('seo_canonical_url');

        var charCountTitle = document.getElementById('char-count-title');
        var charCountDesc = document.getElementById('char-count-desc');

        var scoreBar = document.getElementById('seo-score-bar');
        var scoreValue = document.getElementById('seo-score-value');
        var scoreStatus = document.getElementById('seo-score-status');
        var checklistContainer = document.getElementById('seo-checklist');
        var keywordsTagsContainer = document.getElementById('keywords-tags-container');

        var previewGoogleTitle = document.getElementById('preview-google-title');
        var previewGoogleDesc = document.getElementById('preview-google-desc');
        var previewGoogleUrl = document.getElementById('preview-google-url');

        var previewSocialTitle = document.getElementById('preview-social-title');
        var previewSocialDesc = document.getElementById('preview-social-desc');
        var previewSocialImg = document.getElementById('preview-social-img');
        var previewSocialDomain = document.getElementById('preview-social-domain');

        if (!titleInput || !descInput) return;

        function atualizarPreviews() {
            var title = titleInput.value.trim();
            var desc = descInput.value.trim();
            var keywords = keywordsInput ? keywordsInput.value.trim() : '';
            var ogTitle = ogTitleInput ? ogTitleInput.value.trim() : '';
            var ogDesc = ogDescInput ? ogDescInput.value.trim() : '';
            var canonical = canonicalInput ? canonicalInput.value.trim() : '';

            // 1. Contadores de Caracteres
            var lenT = title.length;
            var lenD = desc.length;

            if (charCountTitle) {
                charCountTitle.innerText = lenT + ' / 65 caracteres';
                if (lenT >= 40 && lenT <= 65) {
                    charCountTitle.className = 'small font-monospace text-success fw-bold';
                } else if (lenT > 0 && lenT < 40) {
                    charCountTitle.className = 'small font-monospace text-warning';
                } else if (lenT > 65) {
                    charCountTitle.className = 'small font-monospace text-danger fw-bold';
                } else {
                    charCountTitle.className = 'small font-monospace text-secondary';
                }
            }

            if (charCountDesc) {
                charCountDesc.innerText = lenD + ' / 160 caracteres';
                if (lenD >= 120 && lenD <= 160) {
                    charCountDesc.className = 'small font-monospace text-success fw-bold';
                } else if (lenD > 0 && lenD < 120) {
                    charCountDesc.className = 'small font-monospace text-warning';
                } else if (lenD > 160) {
                    charCountDesc.className = 'small font-monospace text-danger fw-bold';
                } else {
                    charCountDesc.className = 'small font-monospace text-secondary';
                }
            }

            // 2. Atualizar Prévia no Google (SERP Preview)
            if (previewGoogleTitle) previewGoogleTitle.innerText = title || 'Título da Página no Google';
            if (previewGoogleDesc) previewGoogleDesc.innerText = desc || 'Descrição da página exibida nos resultados de busca do Google...';
            if (previewGoogleUrl && canonical) {
                previewGoogleUrl.innerText = canonical;
            }

            // 3. Atualizar Prévia nas Redes Sociais (WhatsApp / Facebook Card)
            var finalSocialTitle = ogTitle || title || 'Título de Compartilhamento Foco 7';
            var finalSocialDesc = ogDesc || desc || 'Descrição da página para compartilhamento social...';

            if (previewSocialTitle) previewSocialTitle.innerText = finalSocialTitle;
            if (previewSocialDesc) previewSocialDesc.innerText = finalSocialDesc;

            if (previewSocialDomain && canonical) {
                try {
                    var parsed = new URL(canonical);
                    previewSocialDomain.innerText = parsed.hostname;
                } catch (e) {
                    previewSocialDomain.innerText = 'foco7.com.br';
                }
            }

            // 4. Nuvem dinâmica de Palavras-Chave
            if (keywordsTagsContainer) {
                if (keywords) {
                    var kArray = keywords.split(',').map(function (k) { return k.trim(); }).filter(function (k) { return k.length > 0; });
                    var kHtml = '';
                    kArray.forEach(function (k) {
                        kHtml += '<span class="badge bg-secondary bg-opacity-10 text-body border border-secondary border-opacity-25 px-2 py-1"><i class="fas fa-tag text-danger me-1 small"></i>' + k + '</span>';
                    });
                    keywordsTagsContainer.innerHTML = kHtml;
                } else {
                    keywordsTagsContainer.innerHTML = '<span class="small text-muted fst-italic">Nenhuma palavra-chave informada.</span>';
                }
            }

            // 5. Cálculo do Score e Checklist
            var pontos = 0;
            var itens = [];

            // Título (40 a 65 chars)
            if (lenT >= 40 && lenT <= 65) {
                pontos += 25;
                itens.push({ ok: true, text: 'Tamanho do Título ideal: ' + lenT + ' caracteres (Recomendado: 40-65)' });
            } else if (lenT > 0) {
                pontos += 12;
                itens.push({ ok: false, text: 'Tamanho do Título: ' + lenT + ' caracteres (Ideal entre 40 e 65)' });
            } else {
                itens.push({ ok: false, text: 'Título ausente' });
            }

            // Descrição (120 a 160 chars)
            if (lenD >= 120 && lenD <= 160) {
                pontos += 25;
                itens.push({ ok: true, text: 'Meta Description perfeita: ' + lenD + ' caracteres (Recomendado: 120-160)' });
            } else if (lenD > 0) {
                pontos += 14;
                itens.push({ ok: false, text: 'Meta Description: ' + lenD + ' caracteres (Ideal entre 120 e 160)' });
            } else {
                itens.push({ ok: false, text: 'Meta Description não preenchida' });
            }

            // Palavras-chave
            if (keywords.length >= 10) {
                pontos += 15;
                itens.push({ ok: true, text: 'Palavras-chave configuradas e com boa densidade' });
            } else {
                itens.push({ ok: false, text: 'Adicione pelo menos 3 palavras-chave separadas por vírgula' });
            }

            // Open Graph
            if (ogTitle.length > 5 && ogDesc.length > 10) {
                pontos += 20;
                itens.push({ ok: true, text: 'Tags Open Graph para redes sociais preenchidas' });
            } else {
                itens.push({ ok: false, text: 'Preencha Título e Descrição para compartilhamento no WhatsApp/Facebook' });
            }

            // Presença da marca Foco 7 no título
            if (/foco\s*7/i.test(title)) {
                pontos += 15;
                itens.push({ ok: true, text: 'Nome da marca "Foco 7" presente no título principal' });
            } else {
                itens.push({ ok: false, text: 'Inclua a marca "Foco 7" no título para autoridade de marca' });
            }

            pontos = Math.min(100, pontos);

            // Atualizar UI do Score
            if (scoreBar) {
                scoreBar.style.width = pontos + '%';
                scoreBar.className = 'progress-bar progress-bar-striped progress-bar-animated ' +
                    (pontos >= 80 ? 'bg-success' : (pontos >= 50 ? 'bg-warning' : 'bg-danger'));
            }

            if (scoreValue) scoreValue.innerText = pontos + '/100';

            if (scoreStatus) {
                scoreStatus.innerText = pontos >= 80 ? 'Excelente (Otimizado)' : (pontos >= 50 ? 'Bom (Pode Melhorar)' : 'Crítico (Precisa de Atenção)');
                scoreStatus.className = 'badge ' + (pontos >= 80 ? 'bg-success' : (pontos >= 50 ? 'bg-warning text-dark' : 'bg-danger'));
            }

            if (checklistContainer) {
                var html = '<ul class="list-group list-group-flush small">';
                itens.forEach(function (it) {
                    var icon = it.ok
                        ? '<i class="fas fa-check-circle text-success me-2"></i>'
                        : '<i class="fas fa-exclamation-triangle text-warning me-2"></i>';
                    html += '<li class="list-group-item bg-transparent d-flex align-items-center">' + icon + it.text + '</li>';
                });
                html += '</ul>';
                checklistContainer.innerHTML = html;
            }
        }

        // Observar mudanças no preview do Dropzone para a imagem OG
        var dropzonePreviewImg = document.querySelector('.foco-dropzone img.preview-img');
        if (dropzonePreviewImg) {
            var observer = new MutationObserver(function () {
                if (dropzonePreviewImg.src && previewSocialImg) {
                    previewSocialImg.src = dropzonePreviewImg.src;
                }
            });
            observer.observe(dropzonePreviewImg, { attributes: true, attributeFilter: ['src'] });
        }

        // Listener de arquivo selecionado no dropzone
        document.addEventListener('change', function (e) {
            if (e.target && e.target.name === 'og_image_upload' && e.target.files && e.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function (evt) {
                    if (previewSocialImg) {
                        previewSocialImg.src = evt.target.result;
                    }
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        [titleInput, descInput, keywordsInput, ogTitleInput, ogDescInput, canonicalInput].forEach(function (el) {
            if (el) {
                el.addEventListener('input', atualizarPreviews);
                el.addEventListener('change', atualizarPreviews);
            }
        });

        atualizarPreviews();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSeoAnalyzer);
    } else {
        initSeoAnalyzer();
    }
})();
