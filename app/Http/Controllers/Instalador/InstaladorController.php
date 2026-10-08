<?php

namespace App\Http\Controllers\Instalador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use PDO;
use Exception;

class InstaladorController extends Controller
{
    protected string $lockFile;

    public function __construct()
    {
        $this->lockFile = storage_path('installed.lock');
    }

    /**
     * Exibe o assistente de instalação caso o sistema ainda não tenha sido instalado
     */
    public function index()
    {
        if (file_exists($this->lockFile)) {
            return redirect()->route('site.home')->with('info', 'O sistema já se encontra devidamente instalado e configurado.');
        }

        $requisitos = [
            'PHP >= 8.2' => [
                'status' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'atual' => PHP_VERSION,
            ],
            'PDO MySQL' => [
                'status' => extension_loaded('pdo_mysql'),
                'atual' => extension_loaded('pdo_mysql') ? 'Instalado' : 'Ausente',
            ],
            'cURL' => [
                'status' => extension_loaded('curl'),
                'atual' => extension_loaded('curl') ? 'Instalado' : 'Ausente',
            ],
            'GD' => [
                'status' => extension_loaded('gd'),
                'atual' => extension_loaded('gd') ? 'Instalado' : 'Ausente',
            ],
            'Mbstring' => [
                'status' => extension_loaded('mbstring'),
                'atual' => extension_loaded('mbstring') ? 'Instalado' : 'Ausente',
            ],
            'OpenSSL' => [
                'status' => extension_loaded('openssl'),
                'atual' => extension_loaded('openssl') ? 'Instalado' : 'Ausente',
            ],
            'Tokenizer' => [
                'status' => extension_loaded('tokenizer'),
                'atual' => extension_loaded('tokenizer') ? 'Instalado' : 'Ausente',
            ],
            'XML' => [
                'status' => extension_loaded('xml'),
                'atual' => extension_loaded('xml') ? 'Instalado' : 'Ausente',
            ],
            'Permissão Storage' => [
                'status' => is_writable(storage_path()),
                'atual' => is_writable(storage_path()) ? 'Gravável' : 'Sem Permissão',
            ],
            'Permissão Bootstrap/Cache' => [
                'status' => is_writable(base_path('bootstrap/cache')),
                'atual' => is_writable(base_path('bootstrap/cache')) ? 'Gravável' : 'Sem Permissão',
            ],
        ];

        $todosAprovados = !in_array(false, array_column($requisitos, 'status'), true);

        return view('instalador.index', compact('requisitos', 'todosAprovados'));
    }

    /**
     * Testa a conexão MySQL via AJAX
     */
    public function testarBanco(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required|numeric',
            'db_name' => 'required',
            'db_user' => 'required',
        ]);

        $host = $request->input('db_host');
        $port = $request->input('db_port', 3306);
        $name = $request->input('db_name');
        $user = $request->input('db_user');
        $pass = $request->input('db_pass', '');

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conexão com o banco de dados MySQL estabelecida com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Falha na conexão: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Executa a instalação completa do banco MySQL e configuração
     */
    public function executar(Request $request)
    {
        if (file_exists($this->lockFile)) {
            return response()->json(['success' => false, 'message' => 'O sistema já está instalado.'], 403);
        }

        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required|numeric',
            'db_name' => 'required',
            'db_user' => 'required',
            'admin_nome' => 'required|string|max:100',
            'admin_email' => 'required|email|max:120',
            'admin_senha' => 'required|string|min:6',
        ]);

        try {
            // 1. Atualizar arquivo .env
            $this->atualizarEnv([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->input('db_host'),
                'DB_PORT' => $request->input('db_port', '3306'),
                'DB_DATABASE' => $request->input('db_name'),
                'DB_USERNAME' => $request->input('db_user'),
                'DB_PASSWORD' => $request->input('db_pass', ''),
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
            ]);

            // 2. Configurar conexão de tempo de execução
            config([
                'database.default' => 'mysql',
                'database.connections.mysql.host' => $request->input('db_host'),
                'database.connections.mysql.port' => $request->input('db_port', '3306'),
                'database.connections.mysql.database' => $request->input('db_name'),
                'database.connections.mysql.username' => $request->input('db_user'),
                'database.connections.mysql.password' => $request->input('db_pass', ''),
            ]);

            DB::purge('mysql');
            DB::reconnect('mysql');

            // 3. Executar Migrações e Seeders
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);

            // 4. Cadastrar / Atualizar Administrador Master
            User::updateOrCreate(
                ['email' => $request->input('admin_email')],
                [
                    'name' => $request->input('admin_nome'),
                    'password' => Hash::make($request->input('admin_senha')),
                    'role' => 'admin',
                    'status' => 'ativo',
                ]
            );

            // 5. Criar arquivo de trava para segurança contra reinstalações
            file_put_contents($this->lockFile, json_encode([
                'installed_at' => date('Y-m-d H:i:s'),
                'version' => '1.0.0',
                'admin_email' => $request->input('admin_email'),
            ], JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'Sistema Foco 7 instalado e configurado com sucesso!',
                'redirect' => route('login'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro durante a instalação: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atualiza pares chave=valor no arquivo .env
     */
    protected function atualizarEnv(array $dados): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }

        $envContent = file_get_contents($envPath);

        foreach ($dados as $key => $value) {
            // Se o valor contiver espaços ou caracteres especiais, encapsular em aspas
            $formattedValue = (str_contains($value, ' ') || str_contains($value, '#')) ? '"' . $value . '"' : $value;

            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $envContent);
            } else {
                $envContent .= "\n{$key}={$formattedValue}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
