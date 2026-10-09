<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsSetting;
use App\Models\BannerSlide;
use App\Models\Faq;
use App\Models\Depoimento;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    /**
     * Exibe o gerenciador do CMS com abas visuais
     */
    public function index()
    {
        $settings = CmsSetting::all()->pluck('valor', 'chave')->toArray();
        $slides = BannerSlide::orderBy('ordem', 'asc')->get();
        $faqs = Faq::orderBy('ordem', 'asc')->get();
        $depoimentos = Depoimento::orderBy('ordem', 'asc')->get();

        return view('admin.cms.index', compact('settings', 'slides', 'faqs', 'depoimentos'));
    }

    /**
     * Salva as configurações de textos, logos e contatos do CMS via AJAX
     */
    public function salvarConfiguracoes(Request $request)
    {
        $dados = $request->except(['_token', 'logo_upload', 'favicon_upload', 'hero_bg_upload', 'remover_hero_bg']);

        // Upload de Logo se enviado
        if ($request->hasFile('logo_upload')) {
            $path = $request->file('logo_upload')->store('uploads/logos', 'public');
            $dados['site_logo'] = '/storage/' . $path;
        }

        // Upload de Favicon se enviado
        if ($request->hasFile('favicon_upload')) {
            $path = $request->file('favicon_upload')->store('uploads/logos', 'public');
            $dados['site_favicon'] = '/storage/' . $path;
        }

        // Upload de Imagem de Fundo do Hero se enviada
        if ($request->hasFile('hero_bg_upload')) {
            $path = $request->file('hero_bg_upload')->store('uploads/banners', 'public');
            $dados['hero_bg_imagem'] = '/storage/' . $path;
        } elseif ($request->boolean('remover_hero_bg')) {
            $dados['hero_bg_imagem'] = '';
        }

        // Validação e garantia de valor numérico para opacidade (0 a 100)
        if ($request->has('hero_bg_opacidade')) {
            $opacidade = (int) $request->input('hero_bg_opacidade', 85);
            $dados['hero_bg_opacidade'] = max(0, min(100, $opacidade));
        }

        foreach ($dados as $chave => $valor) {
            CmsSetting::set($chave, $valor);
        }

        return response()->json([
            'success' => true,
            'message' => 'Configurações do CMS atualizadas com sucesso!',
        ]);
    }

    /**
     * Cria ou atualiza um slide do Hero Carousel
     */
    public function salvarBanner(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|string',
            'titulo' => 'required|string|max:255',
            'subtitulo' => 'nullable|string',
            'badge' => 'nullable|string|max:100',
            'texto_botao_1' => 'nullable|string|max:100',
            'link_botao_1' => 'nullable|string|max:255',
            'texto_botao_2' => 'nullable|string|max:100',
            'link_botao_2' => 'nullable|string|max:255',
            'ordem' => 'nullable|numeric',
            'ativo' => 'nullable|boolean',
        ]);

        if ($request->hasFile('imagem_upload')) {
            $path = $request->file('imagem_upload')->store('uploads/banners', 'public');
            $validated['imagem'] = '/storage/' . $path;
        }

        $id = $request->input('banner_id');

        if ($id) {
            $banner = BannerSlide::findOrFail($id);
            $banner->update($validated);
        } else {
            BannerSlide::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Banner/Slide salvo com sucesso!',
        ]);
    }

    /**
     * Exclui um banner
     */
    public function excluirBanner($id)
    {
        $banner = BannerSlide::findOrFail($id);
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner removido com sucesso!',
        ]);
    }

    /**
     * Salva ou atualiza uma pergunta frequente (FAQ)
     */
    public function salvarFaq(Request $request)
    {
        $validated = $request->validate([
            'pergunta' => 'required|string|max:255',
            'resposta' => 'required|string',
            'categoria' => 'required|string',
            'ordem' => 'nullable|numeric',
        ]);

        $id = $request->input('faq_id');

        if ($id) {
            $faq = Faq::findOrFail($id);
            $faq->update($validated);
        } else {
            Faq::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pergunta frequente salva com sucesso!',
        ]);
    }

    /**
     * Exclui uma pergunta frequente
     */
    public function excluirFaq($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pergunta removida com sucesso!',
        ]);
    }

    /**
     * Salva ou atualiza um depoimento de cliente
     */
    public function salvarDepoimento(Request $request)
    {
        $validated = $request->validate([
            'nome_cliente' => 'required|string|max:150',
            'cidade_uf' => 'nullable|string|max:100',
            'veiculo' => 'nullable|string|max:100',
            'texto' => 'required|string',
            'estrelas' => 'required|integer|min:1|max:5',
            'ordem' => 'nullable|numeric',
        ]);

        if ($request->hasFile('foto_upload')) {
            $path = $request->file('foto_upload')->store('uploads/depoimentos', 'public');
            $validated['foto'] = '/storage/' . $path;
        }

        $id = $request->input('depoimento_id');

        if ($id) {
            $depoimento = Depoimento::findOrFail($id);
            $depoimento->update($validated);
        } else {
            Depoimento::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Depoimento salvo com sucesso!',
        ]);
    }

    /**
     * Exclui um depoimento
     */
    public function excluirDepoimento($id)
    {
        $depoimento = Depoimento::findOrFail($id);
        $depoimento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Depoimento removido com sucesso!',
        ]);
    }
}
