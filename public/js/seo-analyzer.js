/**
 * ==============================================================================
 * FOCO 7 - ANALISADOR DE SEO EM TEMPO REAL
 * Atualiza Score de SEO, barra de progresso e checklist interativo
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

        var scoreBar = document.getElementById('seo-score-bar');
        var scoreValue = document.getElementById('seo-score-value');
        var scoreStatus = document.getElementById('seo-score-status');
        var checklistContainer = document.getElementById('seo-checklist');

        var previewGoogleTitle = document.getElementById('preview-google-title');
        var previewGoogleDesc = document.getElementById('preview-google-desc');

        if (!titleInput || !descInput) return;

        function calcularScoreLocal() {
            var title = titleInput.value.trim();
            var desc = descInput.value.trim();
            var keywords = keywordsInput ? keywordsInput.value.trim() : '';
            var ogTitle = ogTitleInput ? ogTitleInput.value.trim() : '';
            var ogDesc = ogDescInput ? ogDescInput.value.trim() : '';

            // Atualizar Google Snippet Preview
            if (previewGoogleTitle) previewGoogleTitle.innerText = title || 'Título da Página no Google';
            if (previewGoogleDesc) previewGoogleDesc.innerText = desc || 'Descrição da página exibida nos resultados de busca do Google...';

            var pontos = 0;
            var itens = [];

            // 1. Título (40 a 65 chars)
            var lenT = title.length;
            if (lenT >= 40 && lenT <= 65) {
                pontos += 25;
                itens.push({ ok: true, text: 'Tamanho do Título ideal: ' + lenT + ' caracteres (Recomendado: 40-65)' });
            } else if (lenT > 0) {
                pontos += 12;
                itens.push({ ok: false, text: 'Tamanho do Título: ' + lenT + ' caracteres (Ajuste para entre 40 e 65)' });
            } else {
                itens.push({ ok: false, text: 'Título ausente' });
            }

            // 2. Descrição (120 a 160 chars)
            var lenD = desc.length;
            if (lenD >= 120 && lenD <= 160) {
                pontos += 25;
                itens.push({ ok: true, text: 'Meta Description perfeita: ' + lenD + ' caracteres (Recomendado: 120-160)' });
            } else if (lenD > 0) {
                pontos += 14;
                itens.push({ ok: false, text: 'Meta Description: ' + lenD + ' caracteres (Ideal entre 120 e 160)' });
            } else {
                itens.push({ ok: false, text: 'Meta Description ausente' });
            }

            // 3. Palavras-chave
            if (keywords.length >= 10) {
                pontos += 15;
                itens.push({ ok: true, text: 'Palavras-chave configuradas e com boa densidade' });
            } else {
                itens.push({ ok: false, text: 'Adicione pelo menos 3 palavras-chave separadas por vírgula' });
            }

            // 4. Open Graph Tags
            if (ogTitle.length > 5 && ogDesc.length > 10) {
                pontos += 20;
                itens.push({ ok: true, text: 'Tags Open Graph para redes sociais preenchidas' });
            } else {
                itens.push({ ok: false, text: 'Preencha Título e Descrição para compartilhamento no WhatsApp/Facebook' });
            }

            // 5. Presença da marca Foco 7 no título
            if (/foco\s*7/i.test(title)) {
                pontos += 15;
                itens.push({ ok: true, text: 'Nome da marca "Foco 7" presente no título principal' });
            } else {
                itens.push({ ok: false, text: 'Inclua a marca "Foco 7" no título para autoridade de marca' });
            }

            pontos = Math.min(100, pontos);

            // Atualizar UI
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

        [titleInput, descInput, keywordsInput, ogTitleInput, ogDescInput].forEach(function (el) {
            if (el) {
                el.addEventListener('input', calcularScoreLocal);
            }
        });

        calcularScoreLocal();
    }

    document.addEventListener('DOMContentLoaded', initSeoAnalyzer);
})();
