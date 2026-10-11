<?php

namespace App\Services;

use App\Models\CmsSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    const API_URL = 'https://onesignal.com/api/v1/notifications';

    /**
     * Retorna se a integração com OneSignal está ativa e configurada
     */
    public static function isAtivo(): bool
    {
        $ativo = CmsSetting::get('onesignal_ativo', '0');
        $appId = CmsSetting::get('onesignal_app_id');

        return ($ativo === '1' || $ativo === 1 || $ativo === true) && !empty($appId);
    }

    /**
     * Retorna as configurações do OneSignal cadastradas
     */
    public static function getConfig(): array
    {
        return [
            'app_id' => CmsSetting::get('onesignal_app_id', ''),
            'rest_api_key' => CmsSetting::get('onesignal_rest_api_key', ''),
            'ativo' => CmsSetting::get('onesignal_ativo', '0') == '1',
            'imagem_padrao' => CmsSetting::get('onesignal_imagem_padrao', asset('img/og-foco7.jpg')),
        ];
    }

    /**
     * Dispara uma notificação Push via OneSignal com imagem para todos os dispositivos inscritos
     *
     * @param string $titulo
     * @param string $mensagem
     * @param string|null $linkAcao
     * @param string|null $imagemUrl
     * @param array $dadosExtras
     * @return array
     */
    public static function enviarNotificacao(
        string $titulo,
        string $mensagem,
        ?string $linkAcao = null,
        ?string $imagemUrl = null,
        array $dadosExtras = []
    ): array {
        $config = self::getConfig();

        if (empty($config['app_id'])) {
            return [
                'success' => false,
                'message' => 'OneSignal App ID não está configurado.',
            ];
        }

        if (empty($config['rest_api_key'])) {
            return [
                'success' => false,
                'message' => 'OneSignal REST API Key não está configurada.',
            ];
        }

        // Se nenhuma imagem foi fornecida, usa a imagem padrão configurada
        $imagemFinal = $imagemUrl ?: $config['imagem_padrao'];
        if (empty($imagemFinal)) {
            $imagemFinal = asset('img/og-foco7.jpg');
        }

        // Ícone padrão da Foco 7
        $iconeFinal = asset('img/favicon.png');

        $urlDestino = $linkAcao ?: route('admin.notificacoes.index');

        $payload = [
            'app_id' => $config['app_id'],
            'included_segments' => ['Subscribed Users', 'Total Subscriptions'],
            'headings' => [
                'en' => $titulo,
                'pt' => $titulo,
            ],
            'contents' => [
                'en' => $mensagem,
                'pt' => $mensagem,
            ],
            'url' => $urlDestino,
            'web_url' => $urlDestino,
            // Imagens do Push (Banner e Ícone)
            'chrome_web_image' => $imagemFinal,
            'big_picture' => $imagemFinal,
            'chrome_web_icon' => $iconeFinal,
            'firefox_icon' => $iconeFinal,
            'small_icon' => $iconeFinal,
            'data' => array_merge([
                'url' => $urlDestino,
                'sistema' => 'Foco 7',
            ], $dadosExtras),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . trim($config['rest_api_key']),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(8)->post(self::API_URL, $payload);

            $body = $response->json();

            if ($response->successful()) {
                Log::info('OneSignal Push enviado com sucesso:', [
                    'titulo' => $titulo,
                    'imagem' => $imagemFinal,
                    'resposta' => $body,
                ]);

                return [
                    'success' => true,
                    'message' => 'Notificação Push OneSignal enviada com sucesso!',
                    'data' => $body,
                    'imagem' => $imagemFinal,
                ];
            }

            Log::warning('OneSignal retornou erro ao enviar notificação:', [
                'status' => $response->status(),
                'body' => $body,
            ]);

            return [
                'success' => false,
                'message' => 'OneSignal erro: ' . ($body['errors'][0] ?? 'Falha na requisição'),
                'errors' => $body['errors'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('Exceção ao disparar Push no OneSignal: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Exceção ao conectar com a API do OneSignal: ' . $e->getMessage(),
            ];
        }
    }
}
