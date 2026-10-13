<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoConfig;

class SeoController extends Controller
{
    /**
     * Exibe o painel de gerenciamento de SEO com Score em tempo real
     */
    public function index(Request $request)
    {
        $pagina = $request->query('pagina', 'home');
        if (!in_array($pagina, ['home', 'login', 'cadastro', 'reset'])) {
            $pagina = 'home';
        }

        $seo = SeoConfig::paraPagina($pagina);

        $paginas = [
            'home' => [
                'chave' => 'home',
                'nome' => 'Página Inicial (Landing Page)',
                'icone' => 'fas fa-home',
                'url' => url('/'),
                'badge' => 'Principal',
            ],
            'login' => [
                'chave' => 'login',
                'nome' => 'Página de Login & Acesso',
                'icone' => 'fas fa-sign-in-alt',
                'url' => route('login'),
                'badge' => 'Autenticação',
            ],
            'cadastro' => [
                'chave' => 'cadastro',
                'nome' => 'Página de Cadastro & Registro',
                'icone' => 'fas fa-user-plus',
                'url' => route('register'),
                'badge' => 'Novos Clientes',
            ],
            'reset' => [
                'chave' => 'reset',
                'nome' => 'Recuperação de Senha',
                'icone' => 'fas fa-key',
                'url' => route('password.request'),
                'badge' => 'Segurança',
            ],
        ];

        return view('admin.seo.index', compact('seo', 'pagina', 'paginas'));
    }

    /**
     * Salva as configurações de SEO e recalcula a pontuação
     */
    public function salvar(Request $request)
    {
        $pagina = $request->input('pagina', 'home');
        if (!in_array($pagina, ['home', 'login', 'cadastro', 'reset'])) {
            $pagina = 'home';
        }

        $validated = $request->validate([
            'pagina' => 'nullable|string|in:home,login,cadastro,reset',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
            'robots' => 'required|string|max:50',
        ]);

        $seoAtual = SeoConfig::paraPagina($pagina);
        $validated['pagina'] = $pagina;

        if ($request->hasFile('og_image_upload')) {
            $file = $request->file('og_image_upload');
            $fileName = 'og_' . $pagina . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/seo', $fileName, 'public');
            $validated['og_image'] = '/storage/' . $path;
        } elseif ($request->boolean('remover_og_image_upload') || $request->boolean('remover_og_image')) {
            $validated['og_image'] = '';
        } else {
            $validated['og_image'] = $seoAtual->og_image ?? '/img/og-foco7.jpg';
        }

        $score = $this->calcularScore(
            $validated['meta_title'],
            $validated['meta_description'],
            $validated['meta_keywords'] ?? '',
            $validated['og_title'] ?? '',
            $validated['og_description'] ?? '',
            $validated['og_image'] ?? ''
        );

        $validated['seo_score'] = $score['pontos'];

        $seo = SeoConfig::updateOrCreate(['pagina' => $pagina], $validated);

        return response()->json([
            'success' => true,
            'message' => 'Configurações de SEO da página ' . strtoupper($pagina) . ' salvas com sucesso!',
            'score' => $score,
            'seo' => $seo,
        ]);
    }

    /**
     * Algoritmo de cálculo de pontuação SEO
     */
    public function analisar(Request $request)
    {
        $score = $this->calcularScore(
            $request->input('meta_title', ''),
            $request->input('meta_description', ''),
            $request->input('meta_keywords', ''),
            $request->input('og_title', ''),
            $request->input('og_description', ''),
            $request->input('og_image', '')
        );

        return response()->json($score);
    }

    /**
     * Motor interno de auditoria de SEO
     */
    protected function calcularScore(string $title, string $desc, string $keywords, string $ogTitle, string $ogDesc, string $ogImage): array
    {
        $pontos = 0;
        $checklist = [];

        // 1. Título (ideal entre 40 e 65 caracteres)
        $lenTitle = mb_strlen($title);
        if ($lenTitle >= 40 && $lenTitle <= 65) {
            $pontos += 25;
            $checklist[] = ['item' => 'Tamanho do Título ideal (40-65 caracteres)', 'aprovado' => true, 'dica' => "Perfeito! Seu título possui {$lenTitle} caracteres."];
        } elseif ($lenTitle > 0) {
            $pontos += 12;
            $checklist[] = ['item' => 'Tamanho do Título', 'aprovado' => false, 'dica' => "Seu título possui {$lenTitle} caracteres. Recomendamos entre 40 e 65."];
        } else {
            $checklist[] = ['item' => 'Título não preenchido', 'aprovado' => false, 'dica' => 'Insira um título otimizado para o Google.'];
        }

        // 2. Meta Description (ideal entre 120 e 160 caracteres)
        $lenDesc = mb_strlen($desc);
        if ($lenDesc >= 120 && $lenDesc <= 160) {
            $pontos += 25;
            $checklist[] = ['item' => 'Meta Description perfeita (120-160 caracteres)', 'aprovado' => true, 'dica' => "Excelente! Sua descrição possui {$lenDesc} caracteres."];
        } elseif ($lenDesc > 0) {
            $pontos += 14;
            $checklist[] = ['item' => 'Meta Description', 'aprovado' => false, 'dica' => "Sua descrição possui {$lenDesc} caracteres. O recomendado é entre 120 e 160."];
        } else {
            $checklist[] = ['item' => 'Meta Description não preenchida', 'aprovado' => false, 'dica' => 'A meta description é o resumo que atrai cliques nos resultados de busca.'];
        }

        // 3. Palavras-chave
        if (mb_strlen($keywords) > 10) {
            $pontos += 15;
            $checklist[] = ['item' => 'Palavras-chave configuradas', 'aprovado' => true, 'dica' => 'Palavras-chave definidas para o nicho de rastreamento.'];
        } else {
            $checklist[] = ['item' => 'Palavras-chave ausentes ou curtas', 'aprovado' => false, 'dica' => 'Adicione termos relevantes separados por vírgula.'];
        }

        // 4. Open Graph para Redes Sociais
        if (!empty($ogTitle) && !empty($ogDesc)) {
            $pontos += 20;
            $checklist[] = ['item' => 'Tags Open Graph (Facebook / WhatsApp)', 'aprovado' => true, 'dica' => 'Pré-visualização garantida ao compartilhar links.'];
        } else {
            $pontos += 5;
            $checklist[] = ['item' => 'Tags Open Graph incompletas', 'aprovado' => false, 'dica' => 'Preencha o título e descrição das redes sociais.'];
        }

        // 5. Imagem de Compartilhamento
        if (!empty($ogImage)) {
            $pontos += 15;
            $checklist[] = ['item' => 'Imagem de compartilhamento (OG Image)', 'aprovado' => true, 'dica' => 'Card visual atrativo para WhatsApp e redes sociais.'];
        } else {
            $checklist[] = ['item' => 'Sem imagem de compartilhamento', 'aprovado' => false, 'dica' => 'Faça upload de uma imagem 1200x630 para banners no WhatsApp.'];
        }

        return [
            'pontos' => min(100, $pontos),
            'checklist' => $checklist,
        ];
    }
}
