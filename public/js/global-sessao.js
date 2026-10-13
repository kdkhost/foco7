/**
 * ==============================================================================
 * FOCO 7 - MONITOR DE SESSÃO & DESCONEXÃO AUTOMÁTICA POR INATIVIDADE
 * Proteção contra sequestro de sessão e acesso não autorizado.
 * ==============================================================================
 */

(function () {
    'use strict';

    // Obtém o tempo configurado (em minutos) injetado pelo Blade ou valor padrão 30
    var timeoutMinutos = window.foco7SessionTimeoutMinutes !== undefined 
        ? parseInt(window.foco7SessionTimeoutMinutes, 10) 
        : 30;

    // Se o valor for menor que 1, define 30 minutos por segurança
    if (isNaN(timeoutMinutos) || timeoutMinutos < 1) {
        timeoutMinutos = 30;
    }

    var totalTimeoutMs = timeoutMinutos * 60 * 1000;
    var avisoPrevioMs = totalTimeoutMs > 120000 ? 60000 : 15000; // 60s ou 15s de aviso antes
    var tempoAteAvisoMs = Math.max(10000, totalTimeoutMs - avisoPrevioMs);

    var timerAviso = null;
    var timerLogout = null;
    var timerRegressivoInterval = null;
    var swalAberto = false;
    var ultimaInteracao = Date.now();
    var ultimoPingServidor = Date.now();

    function registrarAtividade() {
        ultimaInteracao = Date.now();

        // Se o modal de aviso não estiver aberto, renova os timers locais
        if (!swalAberto) {
            reiniciarTimers();

            // Heartbeat de segurança no backend a cada 5 minutos de atividade contínua
            if (Date.now() - ultimoPingServidor > 300000) {
                ultimoPingServidor = Date.now();
                pingServidor();
            }
        }
    }

    function pingServidor(callback) {
        if (typeof jQuery !== 'undefined') {
            jQuery.ajax({
                url: '/auth/ping-sessao',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (callback) callback(true);
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        redirecionarParaLogin();
                    }
                    if (callback) callback(false);
                }
            });
        } else {
            fetch('/auth/ping-sessao', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            }).then(function (res) {
                if (res.status === 401) redirecionarParaLogin();
                if (callback) callback(res.ok);
            }).catch(function () {
                if (callback) callback(false);
            });
        }
    }

    function redirecionarParaLogin() {
        window.location.href = '/login?motivo=inatividade';
    }

    function efetuarLogoutInatividade() {
        if (typeof Swal !== 'undefined' && Swal.isVisible()) {
            Swal.close();
        }

        // Envia logout formal ao servidor
        if (typeof jQuery !== 'undefined') {
            jQuery.ajax({
                url: '/logout',
                type: 'POST',
                data: {
                    _token: jQuery('meta[name="csrf-token"]').attr('content'),
                    motivo: 'inatividade'
                },
                complete: function () {
                    redirecionarParaLogin();
                }
            });
        } else {
            redirecionarParaLogin();
        }
    }

    function isDarkTheme() {
        return (document.documentElement.getAttribute('data-bs-theme') === 'dark') ||
               (document.body && document.body.classList.contains('dark-mode')) ||
               (localStorage.getItem('foco7_admin_theme') === 'dark');
    }

    function dispararAvisoInatividade() {
        swalAberto = true;
        var segundosRestantes = Math.round(avisoPrevioMs / 1000);
        var isDark = isDarkTheme();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '<span class="text-danger"><i class="fas fa-user-clock me-2"></i>Sessão Expirando</span>',
                html: `
                    <div class="text-start p-2">
                        <p class="mb-2" style="color: ${isDark ? '#dee2e6' : '#212529'}; font-size: 0.95rem;">
                            Por segurança contra acesso não autorizado e roubo de sessão, sua conta será desconectada automaticamente em:
                        </p>
                        <div class="text-center my-3">
                            <span class="badge bg-danger fs-3 px-3 py-2 shadow" id="swalContadorRegressivo">${segundosRestantes}</span>
                            <span class="d-block small mt-1" style="color: ${isDark ? '#adb5bd' : '#6c757d'};">segundos</span>
                        </div>
                        <p class="small mb-0" style="color: ${isDark ? '#adb5bd' : '#495057'}; font-weight: 500;">
                            Deseja continuar conectado ao sistema?
                        </p>
                    </div>
                `,
                icon: 'warning',
                background: isDark ? '#212529' : '#ffffff',
                color: isDark ? '#f8f9fa' : '#212529',
                showCancelButton: true,
                confirmButtonColor: '#d90000',
                cancelButtonColor: isDark ? '#495057' : '#6c757d',
                confirmButtonText: '<i class="fas fa-check-circle me-1"></i> Continuar Conectado',
                cancelButtonText: '<i class="fas fa-sign-out-alt me-1"></i> Desconectar Agora',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function () {
                    var el = document.getElementById('swalContadorRegressivo');
                    timerRegressivoInterval = setInterval(function () {
                        segundosRestantes--;
                        if (el) el.textContent = segundosRestantes;
                        if (segundosRestantes <= 0) {
                            clearInterval(timerRegressivoInterval);
                            efetuarLogoutInatividade();
                        }
                    }, 1000);
                },
                willClose: function () {
                    if (timerRegressivoInterval) {
                        clearInterval(timerRegressivoInterval);
                    }
                    swalAberto = false;
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    // Usuário escolheu continuar: renova sessão no servidor
                    pingServidor(function (sucesso) {
                        if (sucesso) {
                            if (window.foco7Notify) {
                                window.foco7Notify.success('Sessão renovada com sucesso!');
                            }
                            registrarAtividade();
                        } else {
                            redirecionarParaLogin();
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    efetuarLogoutInatividade();
                }
            });
        } else {
            // Fallback caso SweetAlert2 não esteja carregado
            timerLogout = setTimeout(efetuarLogoutInatividade, avisoPrevioMs);
        }
    }

    function reiniciarTimers() {
        if (timerAviso) clearTimeout(timerAviso);
        if (timerLogout) clearTimeout(timerLogout);

        timerAviso = setTimeout(dispararAvisoInatividade, tempoAteAvisoMs);
    }

    // Monitora interações ativas do usuário com debounce
    var throttleTimeout = null;
    function aoInteragir() {
        if (!throttleTimeout) {
            throttleTimeout = setTimeout(function () {
                registrarAtividade();
                throttleTimeout = null;
            }, 1000);
        }
    }

    ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach(function (evento) {
        document.addEventListener(evento, aoInteragir, { passive: true });
    });

    // Inicia os temporizadores na inicialização
    reiniciarTimers();

    // Expõe utilitário para depuração ou controle do painel
    window.foco7Session = {
        timeoutMinutos: timeoutMinutos,
        ping: pingServidor,
        reset: registrarAtividade,
        logout: efetuarLogoutInatividade
    };
})();
