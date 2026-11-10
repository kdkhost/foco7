# Script de deploy direto para o servidor remoto de producao via SSH/SCP
$ErrorActionPreference = "Stop"

$SSH_KEY = "$env:USERPROFILE\.ssh\foco7_cpanel_ed25519"
$HOST_REMOTO = "15.235.57.3"
$PORTA = 1979
$USER_REMOTO = "fococomb"
$DESTINO_APP = "/home/fococomb/public_html"
$DESTINO_SCP = $USER_REMOTO + "@" + $HOST_REMOTO + ":/home/" + $USER_REMOTO + "/deploy_pack.tar.gz"
$ALVO_SSH = $USER_REMOTO + "@" + $HOST_REMOTO

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "   DEPLOY AUTOMATIZADO FOCO 7 - SERVIDORES DE PRODUÇÃO   " -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan

# 1. Adicionar todas as alterações no Git
Write-Host "[1/5] Preparando arquivos no Git..." -ForegroundColor Yellow
git add -A

# 2. Commit e Push
Write-Host "[2/5] Realizando commit humanizado em portugues..." -ForegroundColor Yellow
$status = git status --porcelain
if ($status) {
    git commit -m "feat(licenciamento): modulo Seja um Licenciado FOCO 7 White Label para servidor proprio cPanel e VPS, gerador de pacote ZIP e painel administrativo"
}

Write-Host "[3/5] Publicando no repositorio remoto (GitHub origin/master)..." -ForegroundColor Yellow
git push origin master

# 3. Gerar pacote do código atualizado
Write-Host "[4/5] Gerando pacote compactado para producao..." -ForegroundColor Yellow
git archive -o deploy_pack.tar.gz HEAD

try {
    Write-Host ("[5/5] Enviando pacote para o servidor remoto (" + $HOST_REMOTO + ":" + $PORTA + ")...") -ForegroundColor Yellow
    scp -P $PORTA -o StrictHostKeyChecking=no -i $SSH_KEY deploy_pack.tar.gz $DESTINO_SCP

    Write-Host "Extraindo arquivos, executando migracoes do banco e limpando caches..." -ForegroundColor Cyan
    $comandoRemoto = "tar -xzf /home/" + $USER_REMOTO + "/deploy_pack.tar.gz -C " + $DESTINO_APP + " && rm -f /home/" + $USER_REMOTO + "/deploy_pack.tar.gz && cd " + $DESTINO_APP + " && php artisan optimize:clear && php artisan migrate --force --no-interaction && touch /home/" + $USER_REMOTO + "/mod_lsapi_reset_me && echo 'DEPLOY_CONCLUIDO_COM_SUCESSO'"
    ssh -i $SSH_KEY -p $PORTA -o StrictHostKeyChecking=no $ALVO_SSH $comandoRemoto
    
    Write-Host "==========================================================" -ForegroundColor Green
    Write-Host "  DEPLOY REMOTO E MIGRACOES FINALIZADOS COM SUCESSO!     " -ForegroundColor Green
    Write-Host "==========================================================" -ForegroundColor Green
}
finally {
    if (Test-Path deploy_pack.tar.gz) {
        Remove-Item deploy_pack.tar.gz -Force -ErrorAction SilentlyContinue
    }
}
