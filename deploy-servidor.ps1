# Script de deploy direto para o servidor remoto de producao via SSH/SCP
$ErrorActionPreference = "Stop"

$SSH_KEY = "$env:USERPROFILE\.ssh\foco7_cpanel_ed25519"
$HOST_REMOTO = "15.235.57.3"
$PORTA = 1979
$USER_REMOTO = "fococomb"
$DESTINO_APP = "/home/fococomb/public_html"

Write-Host "Iniciando empacotamento do repositorio local..." -ForegroundColor Cyan
git archive -o deploy_pack.tar.gz HEAD

try {
    Write-Host "Enviando pacote para o servidor remoto ($HOST_REMOTO)..." -ForegroundColor Cyan
    scp -P $PORTA -o StrictHostKeyChecking=no -i $SSH_KEY deploy_pack.tar.gz "${USER_REMOTO}@${HOST_REMOTO}:/home/${USER_REMOTO}/deploy_pack.tar.gz"

    Write-Host "Extraindo arquivos e limpando caches no servidor..." -ForegroundColor Cyan
    ssh -i $SSH_KEY -p $PORTA -o StrictHostKeyChecking=no "${USER_REMOTO}@${HOST_REMOTO}" "tar -xzf /home/${USER_REMOTO}/deploy_pack.tar.gz -C $DESTINO_APP && rm -f /home/${USER_REMOTO}/deploy_pack.tar.gz && cd $DESTINO_APP && php artisan optimize:clear && php artisan migrate --force --no-interaction && touch /home/${USER_REMOTO}/mod_lsapi_reset_me && echo 'DEPLOY_CONCLUIDO_COM_SUCESSO'"
    
    Write-Host "Deploy remoto concluido com sucesso!" -ForegroundColor Green
}
finally {
    if (Test-Path deploy_pack.tar.gz) {
        Remove-Item deploy_pack.tar.gz -Force -ErrorAction SilentlyContinue
    }
}
