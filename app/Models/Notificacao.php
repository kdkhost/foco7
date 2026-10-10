<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Services\EmailTemplateService;

class Notificacao extends Model
{
    use HasFactory;

    protected $table = 'notificacoes';

    protected $fillable = [
        'tipo',
        'titulo',
        'mensagem',
        'icone',
        'cor',
        'link_acao',
        'dados',
        'lida',
        'lida_em',
        'notificado_email',
        'notificado_push',
    ];

    protected $casts = [
        'dados' => 'array',
        'lida' => 'boolean',
        'lida_em' => 'datetime',
        'notificado_email' => 'boolean',
        'notificado_push' => 'boolean',
    ];

    /**
     * Scope para notificações não lidas
     */
    public function scopeNaoLidas($query)
    {
        return $query->where('lida', false);
    }

    /**
     * Scope para ordenar por mais recentes
     */
    public function scopeRecentes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Marca a notificação como lida
     */
    public function marcarComoLida(): bool
    {
        return $this->update([
            'lida' => true,
            'lida_em' => now(),
        ]);
    }

    /**
     * Registra uma nova notificação no sistema, despacha e-mail para o administrador
     * e prepara o evento de Web Push
     */
    public static function registrar(
        string $tipo,
        string $titulo,
        string $mensagem,
        ?string $linkAcao = null,
        array $dados = [],
        bool $notificarEmail = true
    ): self {
        $icones = [
            'contato' => 'fas fa-envelope-open-text',
            'agendamento' => 'fas fa-calendar-check',
            'depoimento' => 'fas fa-star',
            'lead' => 'fas fa-user-plus',
            'sistema' => 'fas fa-bell',
        ];

        $cores = [
            'contato' => 'warning',
            'agendamento' => 'danger',
            'depoimento' => 'primary',
            'lead' => 'success',
            'sistema' => 'info',
        ];

        $notificacao = self::create([
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'icone' => $icones[$tipo] ?? 'fas fa-bell',
            'cor' => $cores[$tipo] ?? 'danger',
            'link_acao' => $linkAcao,
            'dados' => $dados,
            'lida' => false,
            'notificado_push' => false,
            'notificado_email' => false,
        ]);

        if ($notificarEmail) {
            self::enviarEmailAdmin($notificacao);
        }

        return $notificacao;
    }

    /**
     * Dispara e-mail de notificação para o administrador da Foco 7
     */
    public static function enviarEmailAdmin(self $notificacao): void
    {
        try {
            // E-mail do administrador configurado em CMS ou padrão
            $adminEmail = CmsSetting::get('admin_email_notificacoes')
                ?: CmsSetting::get('contato_email')
                ?: env('MAIL_ADMIN_NOTIF', 'contato@foco7.com.br');

            if (empty($adminEmail)) {
                return;
            }

            $assunto = "🔔 [Foco 7] " . $notificacao->titulo;

            $dadosHtml = '';
            if (!empty($notificacao->dados) && is_array($notificacao->dados)) {
                $dadosHtml .= '<table style="width:100%; border-collapse:collapse; margin-top:15px; font-size:14px; color:#334155;">';
                foreach ($notificacao->dados as $chave => $valor) {
                    if (is_scalar($valor)) {
                        $rotulo = ucfirst(str_replace('_', ' ', $chave));
                        $dadosHtml .= '<tr style="border-bottom:1px solid #e2e8f0;">';
                        $dadosHtml .= '<td style="padding:8px 0; font-weight:bold; color:#0f172a; width:35%;">' . e($rotulo) . ':</td>';
                        $dadosHtml .= '<td style="padding:8px 0;">' . e($valor) . '</td>';
                        $dadosHtml .= '</tr>';
                    }
                }
                $dadosHtml .= '</table>';
            }

            $linkBotao = $notificacao->link_acao ?: url('/admin/notificacoes');

            $corpoHtml = "
                <div style='font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,sans-serif; color:#334155;'>
                    <div style='background:rgba(217,0,0,0.06); border-left:4px solid #d90000; padding:15px; border-radius:6px; margin-bottom:20px;'>
                        <h2 style='color:#d90000; margin:0 0 8px 0; font-size:18px;'>{$notificacao->titulo}</h2>
                        <p style='margin:0; font-size:15px; line-height:1.5;'>{$notificacao->mensagem}</p>
                    </div>
                    {$dadosHtml}
                    <div style='margin-top:25px; text-align:center;'>
                        <a href='{$linkBotao}' style='display:inline-block; background:#d90000; color:#ffffff; font-weight:bold; text-decoration:none; padding:12px 28px; border-radius:8px; font-size:14px;'>
                            Acessar Painel / Ver Detalhes
                        </a>
                    </div>
                    <p style='margin-top:25px; font-size:12px; color:#94a3b8; text-align:center;'>
                        Mensagem automática gerada pela Central de Notificações da Foco 7.
                    </p>
                </div>
            ";

            $enviado = EmailTemplateService::enviarEmailCustomizado(
                $adminEmail,
                'Administrador Foco 7',
                $assunto,
                $corpoHtml
            );

            if ($enviado) {
                $notificacao->update(['notificado_email' => true]);
            }
        } catch (\Throwable $e) {
            Log::warning("Falha ao enviar e-mail de notificação ao admin: " . $e->getMessage());
        }
    }
}
