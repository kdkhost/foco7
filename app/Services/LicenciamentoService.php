<?php

namespace App\Services;

use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Models\Licenca;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

class LicenciamentoService
{
    /**
     * Retorna os parâmetros gerais do Programa de Licenciados FOCO 7
     */
    public static function obterParametrosPrograma(): array
    {
        return [
            'ativo' => CmsSetting::get('licenciamento_ativo', '1') == '1',
            'badge' => CmsSetting::get('licenciamento_badge', 'OPORTUNIDADE DE NEGÓCIO • WHITE LABEL'),
            'titulo' => CmsSetting::get('licenciamento_titulo', 'Tenha Sua Própria Empresa de Rastreamento Veicular'),
            'subtitulo' => CmsSetting::get('licenciamento_subtitulo', 'Adquira a licença oficial do Sistema FOCO 7 e instale no seu próprio servidor (VPS, cPanel, Cloud ou Dedicado) com 100% da sua marca e lucro.'),
            'preco_adesao' => (float) CmsSetting::get('licenciamento_preco_adesao', 1497.00),
            'preco_adesao_formatado' => 'R$ ' . number_format((float) CmsSetting::get('licenciamento_preco_adesao', 1497.00), 2, ',', '.'),
            'preco_mensal' => (float) CmsSetting::get('licenciamento_preco_mensal', 0.00),
            'preco_mensal_formatado' => 'R$ ' . number_format((float) CmsSetting::get('licenciamento_preco_mensal', 0.00), 2, ',', '.'),
            'versao_atual' => CmsSetting::get('licenciamento_versao_atual', 'v3.8.0'),
            'whatsapp_suporte' => CmsSetting::get('licenciamento_whatsapp_suporte', '559180510736'),
            'termo_licenca' => CmsSetting::get('licenciamento_termo_licenca', 'Ao adquirir a licença FOCO 7, o licenciado adquire o direito de instalar o sistema em seu próprio servidor, operando sob sua marca própria sem cobrança de taxas por placa rastreada.'),
            'permitir_auto_binding' => CmsSetting::get('licenciamento_permitir_auto_binding', '1') == '1',
        ];
    }

    /**
     * Cria ou localiza uma licença para um usuário
     */
    public static function criarLicenca(User $user, array $dados = [], ?Fatura $fatura = null): Licenca
    {
        $chave = Licenca::gerarChaveUnica();

        $licenca = Licenca::create([
            'user_id' => $user->id,
            'fatura_id' => $fatura?->id,
            'chave_licenca' => $chave,
            'tipo_licenca' => $dados['tipo_licenca'] ?? 'vitalicia',
            'nome_empresa' => $dados['nome_empresa'] ?? ($user->name . ' Rastreamento'),
            'dominio_autorizado' => $dados['dominio_autorizado'] ?? null,
            'ip_autorizado' => $dados['ip_autorizado'] ?? null,
            'status' => $dados['status'] ?? 'pendente',
            'versao_liberada' => $dados['versao_liberada'] ?? (self::obterParametrosPrograma()['versao_atual']),
            'limite_veiculos' => $dados['limite_veiculos'] ?? null,
            'limite_clientes' => $dados['limite_clientes'] ?? null,
            'data_ativacao' => ($dados['status'] ?? '') === 'ativa' ? now() : null,
            'data_expiracao' => $dados['data_expiracao'] ?? null,
            'observacoes' => $dados['observacoes'] ?? 'Licença gerada no sistema.',
        ]);

        return $licenca;
    }

    /**
     * Ativação automática da licença após confirmação do pagamento
     */
    public static function ativarLicencaPosPagamento(Fatura $fatura): ?Licenca
    {
        $user = $fatura->user;
        if (! $user) {
            return null;
        }

        // Localiza a licença vinculada à fatura ou busca licença pendente do usuário
        $licenca = Licenca::where('fatura_id', $fatura->id)->first();
        if (! $licenca) {
            $licenca = Licenca::where('user_id', $user->id)
                ->where('status', 'pendente')
                ->latest()
                ->first();
        }

        if (! $licenca) {
            // Se não encontrou, cria a licença ativa para o comprador
            $licenca = self::criarLicenca($user, [
                'status' => 'ativa',
                'tipo_licenca' => 'vitalicia',
                'nome_empresa' => $user->name . ' Rastreamento',
            ], $fatura);
        } else {
            $licenca->update([
                'status' => 'ativa',
                'data_ativacao' => now(),
                'fatura_id' => $fatura->id,
            ]);
        }

        // Atualiza a role do usuário para 'licenciado' mantendo acesso total
        if ($user->role !== 'admin') {
            $user->update(['role' => 'licenciado']);
        }

        Log::info("Licença FOCO 7 ativada com sucesso para {$user->name} (#{$licenca->chave_licenca}).");

        // Disparo de e-mail ao Licenciado
        try {
            EmailTemplateService::enviar($user->email, 'licenca_ativada', [
                'cliente_nome' => $user->name,
                'chave_licenca' => $licenca->chave_licenca,
                'link_portal' => url('/cliente/licenca'),
                'versao' => $licenca->versao_liberada,
                'nome_empresa' => $licenca->nome_empresa,
            ]);
        } catch (\Throwable $e) {
            Log::warning("Falha ao enviar e-mail de ativação de licença: " . $e->getMessage());
        }

        // Disparo de notificação WhatsApp se Evolution API estiver ativa
        if (EvolutionApiService::isAtivo() && ! empty($user->telefone)) {
            try {
                $msgWpp = "🎉 *PARABÉNS! SUA LICENÇA FOCO 7 ESTÁ ATIVA!*\n\n"
                    . "Olá *{$user->name}*, seu pagamento foi confirmado e sua licença exclusiva foi liberada com sucesso!\n\n"
                    . "🔑 *Sua Chave de Licença:* `{$licenca->chave_licenca}`\n"
                    . "🏢 *Empresa:* {$licenca->nome_empresa}\n"
                    . "📦 *Versão Liberada:* {$licenca->versao_liberada}\n\n"
                    . "Acesse agora seu portal para baixar o instalador oficial e o pacote completo para seu servidor (VPS ou cPanel):\n"
                    . "👉 " . url('/cliente/licenca') . "\n\n"
                    . "_Seja muito bem-vindo à rede de Licenciados FOCO 7!_";

                EvolutionApiService::enviarTexto($user->telefone, $msgWpp);
            } catch (\Throwable $e) {
                Log::warning("Falha ao enviar WhatsApp de licença: " . $e->getMessage());
            }
        }

        return $licenca;
    }

    /**
     * API: Valida a licença enviada por um servidor instalado externamente
     */
    public static function validarLicencaRemota(string $chave, ?string $dominio = null, ?string $ip = null, array $dadosServidor = []): array
    {
        $licenca = Licenca::where('chave_licenca', trim($chave))->first();

        if (! $licenca) {
            return [
                'valida' => false,
                'status' => 'inexistente',
                'mensagem' => 'Chave de licença inválida ou não encontrada no servidor central.',
            ];
        }

        if ($licenca->status === 'bloqueada') {
            return [
                'valida' => false,
                'status' => 'bloqueada',
                'mensagem' => 'Esta licença foi bloqueada pela administração do sistema.',
            ];
        }

        if ($licenca->status === 'suspensa') {
            return [
                'valida' => false,
                'status' => 'suspensa',
                'mensagem' => 'Esta licença está temporariamente suspensa por pendências financeiras.',
            ];
        }

        if ($licenca->data_expiracao && $licenca->data_expiracao->isPast()) {
            return [
                'valida' => false,
                'status' => 'expirada',
                'mensagem' => 'Esta licença expirou em ' . $licenca->data_expiracao->format('d/m/Y') . '.',
            ];
        }

        $params = self::obterParametrosPrograma();

        // Auto-Binding: Se o domínio da licença ainda estiver vazio, vincula ao primeiro domínio requisitante
        if (empty($licenca->dominio_autorizado) && ! empty($dominio) && $params['permitir_auto_binding']) {
            $licenca->dominio_autorizado = strtolower(trim($dominio));
            if (! empty($ip)) {
                $licenca->ip_autorizado = trim($ip);
            }
        }

        // Validação de Domínio (se configurado e diferente de wildcard '*')
        if (! empty($licenca->dominio_autorizado) && $licenca->dominio_autorizado !== '*') {
            $domReq = strtolower(trim((string) $dominio));
            $domAut = strtolower(trim($licenca->dominio_autorizado));

            // Permite subdomínio ou correspondência exata
            if (! empty($domReq) && ! str_contains($domReq, $domAut) && ! str_contains($domAut, $domReq)) {
                return [
                    'valida' => false,
                    'status' => 'dominio_invalido',
                    'mensagem' => "Domínio '{$domReq}' não está autorizado para esta chave. Domínio cadastrado: '{$domAut}'.",
                ];
            }
        }

        // Atualiza Check-in e dados técnicos da instalação do licenciado
        $licenca->update([
            'ultimo_check_in' => now(),
            'dados_servidor' => array_merge($licenca->dados_servidor ?? [], $dadosServidor, [
                'ultimo_ip' => $ip,
                'ultimo_dominio' => $dominio,
                'data_checkin' => now()->toDateTimeString(),
            ]),
        ]);

        return [
            'valida' => true,
            'status' => 'ativa',
            'empresa' => $licenca->nome_empresa,
            'tipo' => $licenca->tipo_licenca,
            'versao' => $licenca->versao_liberada,
            'dominio' => $licenca->dominio_autorizado,
            'limite_veiculos' => $licenca->limite_veiculos,
            'limite_clientes' => $licenca->limite_clientes,
            'mensagem' => 'Licença autêntica e ativa.',
        ];
    }

    /**
     * Gera o script Bash para instalação rápida no Ubuntu/Debian (VPS e Dedicados)
     */
    public static function gerarScriptInstalacaoVps(Licenca $licenca): string
    {
        $chave = $licenca->chave_licenca;
        $empresa = $licenca->nome_empresa ?: 'Foco 7 Rastreamento';
        $dominio = $licenca->dominio_autorizado ?: 'seusite.com.br';
        $ano = date('Y');

        return <<<BASH
#!/usr/bin/env bash
# ==============================================================================
# SCRIPT OFICIAL DE INSTALAÇÃO RÁPIDA - SISTEMA FOCO 7 (LICENCIADO)
# Compatível com Ubuntu 22.04 LTS / 24.04 LTS e Debian 11/12
# Licença: {$chave} | Empresa: {$empresa}
# ==============================================================================

set -e

RED='\\033[0;31m'
GREEN='\\033[0;32m'
YELLOW='\\033[1;33m'
BLUE='\\033[0;34m'
NC='\\033[0m'

echo -e "\${BLUE}=================================================================\${NC}"
echo -e "\${GREEN}   INICIANDO INSTALADOR AUTOMATIZADO - SISTEMA FOCO 7 (\${NC}VPS/Cloud\${GREEN})\${NC}"
echo -e "   Empresa Licenciada: \${YELLOW}{$empresa}\${NC}"
echo -e "   Chave de Licença:   \${YELLOW}{$chave}\${NC}"
echo -e "\${BLUE}=================================================================\${NC}"

if [ "\$EUID" -ne 0 ]; then
    echo -e "\${RED}[ERRO] Por favor, execute este script como root (sudo bash install-vps.sh)\${NC}"
    exit 1
fi

echo -e "\${BLUE}[1/6] Atualizando pacotes do sistema...\${NC}"
apt-get update -y && apt-get upgrade -y
apt-get install -y software-properties-common curl git unzip zip certbot python3-certbot-nginx ufw

echo -e "\${BLUE}[2/6] Instalando Nginx e MariaDB Server...\${NC}"
apt-get install -y nginx mariadb-server mariadb-client
systemctl enable nginx
systemctl enable mariadb

echo -e "\${BLUE}[3/6] Instalando PHP e Extensões Necessárias...\${NC}"
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-curl php8.3-gd php8.3-mbstring php8.3-xml php8.3-zip php8.3-bcmath php8.3-intl php8.3-soap php8.3-redis
systemctl enable php8.3-fpm

echo -e "\${BLUE}[4/6] Instalando Composer Oficial...\${NC}"
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm -f /tmp/composer-setup.php
fi

echo -e "\${BLUE}[5/6] Preparando diretório e permissões em /var/www/foco7...\${NC}"
mkdir -p /var/www/foco7
cd /var/www/foco7

# Gravando arquivo da licença ativada
cat <<EOF > /var/www/foco7/license.key
{
    "licenca": "{$chave}",
    "empresa": "{$empresa}",
    "dominio": "{$dominio}",
    "gerado_em": "$(date -u +"%Y-%m-%dT%H:%M:%SZ")"
}
EOF

# Ajustando proprietário para www-data
chown -R www-data:www-data /var/www/foco7
chmod -R 775 /var/www/foco7

echo -e "\${BLUE}[6/6] Configurando Virtual Host Nginx...\${NC}"
cat <<EOF > /etc/nginx/sites-available/foco7
server {
    listen 80;
    listen [::]:80;
    server_name {$dominio} www.{$dominio};
    root /var/www/foco7/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    location / {
        try_files \\\$uri \\\$uri/ /index.php?\\\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \\.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \\\$realpath_root\\\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/foco7 /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

echo -e "\${GREEN}=================================================================\${NC}"
echo -e "\${GREEN}🎉 AMBIENTE VPS PREPARADO COM SUCESSO!\${NC}"
echo -e "\${YELLOW}Próximos passos simples para finalizar:\${NC}"
echo -e "1. Extraia os arquivos do pacote .ZIP para: \${BLUE}/var/www/foco7\${NC}"
echo -e "2. Ajuste as permissões: \${BLUE}chown -R www-data:www-data /var/www/foco7 && chmod -R 775 /var/www/foco7/storage\${NC}"
echo -e "3. Crie o banco MySQL no MariaDB: \${BLUE}CREATE DATABASE foco7_db; GRANT ALL ON foco7_db.* TO 'foco7_user'@'localhost' IDENTIFIED BY 'SUA_SENHA';\${NC}"
echo -e "4. Acesse pelo navegador: \${GREEN}http://{$dominio}/instalar\${NC} e siga o assistente web gráfico!"
echo -e "5. Para ativar SSL gratuito: \${BLUE}certbot --nginx -d {$dominio}\${NC}"
echo -e "\${GREEN}=================================================================\${NC}"
BASH;
    }

    /**
     * Gera o manual completo de instalação em Markdown
     */
    public static function gerarGuiaInstalacaoMarkdown(Licenca $licenca): string
    {
        $chave = $licenca->chave_licenca;
        $empresa = $licenca->nome_empresa ?: 'Foco 7 Rastreamento';
        $dominio = $licenca->dominio_autorizado ?: 'seusite.com.br';

        return <<<MD
# Guia de Instalação do Sistema FOCO 7 (Licenciado Oficial)

Parabéns pela aquisição da sua licença do **Sistema FOCO 7**!
Este guia detalha o passo a passo para instalar e rodar a plataforma completa em seu próprio servidor.

---

## 🔑 Dados da Sua Licença
- **Chave de Ativação:** `{$chave}`
- **Empresa Licenciada:** {$empresa}
- **Domínio Autorizado:** {$dominio}
- **Suporte Oficial WhatsApp:** +55 (91) 8051-0736

---

## 🌐 Opção 1: Instalação em Hospedagem Compartilhada cPanel (Recomendado / Fácil)

1. **Acesse o cPanel da sua hospedagem:**
   - Crie um banco de dados MySQL (ex: `seuuser_foco7`) e anote o nome do banco, usuário e senha.
2. **Envie os Arquivos:**
   - Acesse o **Gerenciador de Arquivos** do cPanel.
   - Entre na pasta `public_html`.
   - Envie o arquivo `FOCO7_DISTRIBUICAO.zip` e clique em **Extrair**.
3. **Execute o Instalador Web Gráfico:**
   - Abra o navegador e acesse: `https://seusite.com.br/instalar`
   - O assistente verificará as extensões do PHP (8.2+), testará a conexão MySQL e executará as migrações automaticamente.
   - Digite a sua chave de licença `{$chave}` e defina os dados do seu Administrador Master.
4. **Pronto!**
   - O instalador bloqueará o arquivo de trava de segurança (`installed.lock`) e redirecionará para a tela de login.

---

## 🖥️ Opção 2: Instalação em Servidor VPS Linux (Ubuntu 22.04 / 24.04 LTS)

1. Conecte ao seu VPS via SSH como root:
   ```bash
   ssh root@seu_ip_vps
   ```
2. Baixe e execute o script de instalação rápida incluído no pacote:
   ```bash
   bash install-vps.sh
   ```
3. O script instalará Nginx, PHP 8.3-FPM, MariaDB, Composer e criará o diretório `/var/www/foco7`.
4. Crie o banco de dados no MySQL:
   ```sql
   mysql -u root -p
   CREATE DATABASE foco7_sistema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'foco7_user'@'localhost' IDENTIFIED BY 'SenhaForte123@';
   GRANT ALL PRIVILEGES ON foco7_sistema.* TO 'foco7_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```
5. Acesse no navegador `http://seu_ip_ou_dominio/instalar` e conclua a instalação!
6. Para ativar SSL HTTPS grátis com Let's Encrypt:
   ```bash
   certbot --nginx -d {$dominio}
   ```

---

## ⏰ Tarefas Agendadas (Cron Job Obrigatório)
Para que o envio de faturas, avisos de vencimento no WhatsApp e a régua de cobrança funcionem automaticamente, configure o Cron do servidor para rodar a cada minuto:

```bash
* * * * * cd /caminho/do/sistema && php artisan schedule:run >> /dev/null 2>&1
```

*No cPanel:* Vá em **Tarefas Cron**, selecione "Uma vez por minuto (`* * * * *`)" e adicione:
`php /home/seu_usuario/public_html/artisan schedule:run >> /dev/null 2>&1`
MD;
    }

    /**
     * Gera o arquivo ZIP oficial de distribuição do sistema para download do licenciado
     */
    public static function gerarPacoteZipParaDownload(Licenca $licenca): string
    {
        $dirExport = storage_path('app/licencas/pacotes');
        if (! File::isDirectory($dirExport)) {
            File::makeDirectory($dirExport, 0755, true, true);
        }

        $zipPath = $dirExport . "/FOCO7_DISTRIBUICAO_{$licenca->id}.zip";

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Não foi possível criar o arquivo ZIP de distribuição.");
        }

        // Injeta os arquivos exclusivos de licença e instalação
        $zip->addFromString('license.key', json_encode([
            'chave_licenca' => $licenca->chave_licenca,
            'empresa' => $licenca->nome_empresa,
            'dominio' => $licenca->dominio_autorizado,
            'tipo' => $licenca->tipo_licenca,
            'versao' => $licenca->versao_liberada,
            'data_emissao' => now()->toDateTimeString(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $zip->addFromString('install-vps.sh', self::gerarScriptInstalacaoVps($licenca));
        $zip->addFromString('GUIA_INSTALACAO_SERVIDOR.md', self::gerarGuiaInstalacaoMarkdown($licenca));

        // Empacota diretórios essenciais do sistema de forma segura
        $baseDir = base_path();
        $diretorios = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes'];
        $arquivosRaiz = [
            'artisan',
            'composer.json',
            'package.json',
            'vite.config.js',
            '.env.example',
            '.htaccess',
        ];

        foreach ($arquivosRaiz as $arq) {
            $fullPath = $baseDir . DIRECTORY_SEPARATOR . $arq;
            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, $arq);
            }
        }

        foreach ($diretorios as $dir) {
            $dirPath = $baseDir . DIRECTORY_SEPARATOR . $dir;
            if (File::isDirectory($dirPath)) {
                self::adicionarDiretorioAoZip($zip, $dirPath, $dir);
            }
        }

        $zip->close();

        // Incrementa métricas de download
        $licenca->increment('total_downloads');
        $licenca->update(['ultimo_download_em' => now()]);

        return $zipPath;
    }

    /**
     * Adiciona arquivos recursivamente ao arquivo ZIP filtrando arquivos sensíveis
     */
    protected static function adicionarDiretorioAoZip(ZipArchive $zip, string $caminhoAbsoluto, string $caminhoInterno): void
    {
        $arquivos = File::allFiles($caminhoAbsoluto);

        foreach ($arquivos as $arquivo) {
            $caminhoRelativo = $arquivo->getRelativePathname();
            $extensao = strtolower($arquivo->getExtension());

            // Filtra arquivos sensíveis que não devem ir no pacote de distribuição
            if ($extensao === 'lock' && $arquivo->getFilename() === 'installed.lock') {
                continue; // Garante que o instalador abra no novo servidor
            }
            if (str_contains($caminhoRelativo, '.git') || str_contains($caminhoRelativo, 'node_modules')) {
                continue;
            }

            $zip->addFile($arquivo->getRealPath(), $caminhoInterno . '/' . str_replace('\\', '/', $caminhoRelativo));
        }
    }
}

