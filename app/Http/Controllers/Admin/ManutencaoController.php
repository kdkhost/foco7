<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsSetting;
use Illuminate\Support\Facades\File;

class ManutencaoController extends Controller
{
    /**
     * Retorna a URL válida do wallpaper ativo com fallback garantido
     */
    protected function getWallpaperUrl(): string
    {
        $bg = CmsSetting::get('manutencao_bg');
        if (!empty($bg)) {
            $cleanPath = parse_url($bg, PHP_URL_PATH) ?: $bg;
            if (file_exists(public_path(ltrim($cleanPath, '/\\')))) {
                return $bg;
            }
        }
        return '/img/manutencao-bg.jpg';
    }

    /**
     * Exibe o painel de controle do Modo de Manutenção
     */
    public function index(Request $request)
    {
        $statusAtivo = (string) CmsSetting::get('manutencao_ativa', '0') === '1';
        $titulo = CmsSetting::get('manutencao_titulo', 'SITE EM MANUTENÇÃO PROGRAMADA');
        $mensagem = CmsSetting::get('manutencao_mensagem', 'Estamos realizando melhorias em nossos servidores de monitoramento.');
        $previsao = CmsSetting::get('manutencao_previsao', date('Y-m-d H:i', strtotime('+24 hours')));
        $ips = CmsSetting::get('manutencao_ips_liberados', '');
        $token = CmsSetting::get('manutencao_token', 'foco7_vip_bypass_2026');
        $mostrarRedes = CmsSetting::get('manutencao_mostrar_redes', '1') == '1';
        $mostrarWhatsApp = CmsSetting::get('manutencao_mostrar_whatsapp', '1') == '1';
        $bgImagem = $this->getWallpaperUrl();
        $meuIp = $request->ip();

        return view('admin.manutencao.index', compact(
            'statusAtivo',
            'titulo',
            'mensagem',
            'previsao',
            'ips',
            'token',
            'mostrarRedes',
            'mostrarWhatsApp',
            'bgImagem',
            'meuIp'
        ));
    }

    /**
     * Alterna o status do modo de manutenção com 1 clique via AJAX
     */
    public function toggle(Request $request)
    {
        $statusAtual = (string) CmsSetting::get('manutencao_ativa', '0');
        $novoStatus = ($statusAtual === '1' || $statusAtual === 'true') ? '0' : '1';

        CmsSetting::set('manutencao_ativa', $novoStatus, 'manutencao', 'boolean');

        $mensagem = $novoStatus === '1'
            ? 'Modo de Manutenção ATIVADO com sucesso! Visitantes normais verão a página de aviso.'
            : 'Modo de Manutenção DESATIVADO com sucesso! Landing Page online para todo o público.';

        return response()->json([
            'success' => true,
            'ativo' => $novoStatus === '1',
            'message' => $mensagem,
        ]);
    }

    /**
     * Salva as configurações detalhadas do modo de manutenção com upload de wallpaper e sem perda de dados
     */
    public function salvar(Request $request)
    {
        $validated = $request->validate([
            'manutencao_ativa' => 'nullable',
            'manutencao_titulo' => 'required|string|max:200',
            'manutencao_mensagem' => 'required|string',
            'manutencao_previsao' => 'nullable|string',
            'manutencao_ips_liberados' => 'nullable|string',
            'manutencao_token' => 'required|string|max:100',
            'manutencao_mostrar_redes' => 'nullable',
            'manutencao_mostrar_whatsapp' => 'nullable',
            'manutencao_bg' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'remover_manutencao_bg' => 'nullable',
        ], [
            'manutencao_titulo.required' => 'O título da página de manutenção é obrigatório.',
            'manutencao_mensagem.required' => 'A mensagem explicativa ao visitante é obrigatória.',
            'manutencao_token.required' => 'O token de bypass é obrigatório.',
            'manutencao_bg.image' => 'A imagem de fundo deve ser um arquivo de imagem válido.',
            'manutencao_bg.mimes' => 'Formatos permitidos para o fundo: JPG, JPEG, PNG, WEBP.',
            'manutencao_bg.max' => 'O tamanho máximo da imagem de fundo é de 5MB.',
        ]);

        // Sincroniza o status da manutenção (trata switch e input hidden de forma estrita)
        $isAtivo = ($request->input('manutencao_ativa') === '1' || $request->boolean('manutencao_ativa')) ? '1' : '0';
        CmsSetting::set('manutencao_ativa', $isAtivo, 'manutencao', 'boolean');

        CmsSetting::set('manutencao_titulo', $validated['manutencao_titulo'], 'manutencao');
        CmsSetting::set('manutencao_mensagem', $validated['manutencao_mensagem'], 'manutencao');
        CmsSetting::set('manutencao_previsao', $validated['manutencao_previsao'] ?? '', 'manutencao');
        CmsSetting::set('manutencao_ips_liberados', $validated['manutencao_ips_liberados'] ?? '', 'manutencao');
        CmsSetting::set('manutencao_token', $validated['manutencao_token'], 'manutencao');
        CmsSetting::set('manutencao_mostrar_redes', $request->boolean('manutencao_mostrar_redes') ? '1' : '0', 'manutencao');
        CmsSetting::set('manutencao_mostrar_whatsapp', $request->boolean('manutencao_mostrar_whatsapp') ? '1' : '0', 'manutencao');

        // Upload da Imagem de Fundo (Drag & Drop via FocoDropzone com AJAX e Preview)
        if ($request->hasFile('manutencao_bg')) {
            $file = $request->file('manutencao_bg');
            $uploadDir = public_path('uploads/manutencao');
            if (!File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true, true);
            }

            // Exclui wallpaper anterior customizado se existir
            $bgAntigo = CmsSetting::get('manutencao_bg');
            if ($bgAntigo && str_starts_with($bgAntigo, '/uploads/manutencao/')) {
                $caminhoAntigo = public_path(ltrim($bgAntigo, '/\\'));
                if (file_exists($caminhoAntigo)) {
                    @unlink($caminhoAntigo);
                }
            }

            $fileName = 'manutencao_bg_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);
            $bgUrl = '/uploads/manutencao/' . $fileName;
            CmsSetting::set('manutencao_bg', $bgUrl, 'manutencao', 'image');
        } elseif ($request->boolean('remover_manutencao_bg')) {
            $bgAntigo = CmsSetting::get('manutencao_bg');
            if ($bgAntigo && str_starts_with($bgAntigo, '/uploads/manutencao/')) {
                $caminhoAntigo = public_path(ltrim($bgAntigo, '/\\'));
                if (file_exists($caminhoAntigo)) {
                    @unlink($caminhoAntigo);
                }
            }
            CmsSetting::set('manutencao_bg', '/img/manutencao-bg.jpg', 'manutencao', 'image');
        }

        $bgAtual = $this->getWallpaperUrl();

        return response()->json([
            'success' => true,
            'message' => 'Configurações salvas com sucesso! Status: ' . ($isAtivo === '1' ? 'MANUTENÇÃO ATIVADA' : 'SITE ONLINE'),
            'bg_imagem' => $bgAtual,
            'status_ativo' => $isAtivo === '1',
            'reload' => true,
        ]);
    }

    /**
     * Limpa a lista de IPs liberados para permitir teste imediato em navegador anônimo
     */
    public function limparIps(Request $request)
    {
        CmsSetting::set('manutencao_ips_liberados', '', 'manutencao');

        return response()->json([
            'success' => true,
            'message' => 'Lista de IPs liberados zerada com sucesso! Testes em aba anônima agora serão bloqueados.',
            'reload' => true,
        ]);
    }

    /**
     * Pré-visualização da página de manutenção em tempo real pelo administrador
     */
    public function preview()
    {
        $dadosManutencao = [
            'titulo' => CmsSetting::get('manutencao_titulo', 'SITE EM MANUTENÇÃO PROGRAMADA'),
            'mensagem' => CmsSetting::get('manutencao_mensagem', 'Estamos realizando melhorias em nossos servidores de monitoramento.'),
            'previsao' => CmsSetting::get('manutencao_previsao', date('Y-m-d H:i:s', strtotime('+24 hours'))),
            'mostrar_redes' => CmsSetting::get('manutencao_mostrar_redes', '1') == '1',
            'mostrar_whatsapp' => CmsSetting::get('manutencao_mostrar_whatsapp', '1') == '1',
            'whatsapp' => CmsSetting::get('contato_whatsapp', '559180510736'),
            'telefone' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'instagram' => CmsSetting::get('social_instagram', ''),
            'facebook' => CmsSetting::get('social_facebook', ''),
            'youtube' => CmsSetting::get('social_youtube', ''),
            'logo' => CmsSetting::getLogo('escuro'),
            'bg_imagem' => $this->getWallpaperUrl(),
            'client_ip' => request()->ip(),
            'is_preview' => true,
        ];

        return response()->view('site.manutencao', $dadosManutencao);
    }
}
