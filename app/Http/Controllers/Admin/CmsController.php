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
        $dados = $request->except([
            '_token',
            'hero_bg_upload',
            'remover_hero_bg',
            'app_mockup_upload',
            'remover_app_mockup'
        ]);

        // Upload de Imagem de Fundo do Hero se enviada
        if ($request->hasFile('hero_bg_upload')) {
            $file = $request->file('hero_bg_upload');
            $uploadPath = public_path('uploads/banners');
            if (!\Illuminate\Support\Facades\File::isDirectory($uploadPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($uploadPath, 0755, true, true);
            }
            $fileName = 'hero_bg_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);

            // Espelha para storage/app/public/uploads/banners para garantir qualquer link existente
            $storageDir = storage_path('app/public/uploads/banners');
            if (!\Illuminate\Support\Facades\File::isDirectory($storageDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($storageDir, 0755, true, true);
            }
            @copy($uploadPath . '/' . $fileName, $storageDir . '/' . $fileName);

            $dados['hero_bg_imagem'] = '/uploads/banners/' . $fileName;
        } elseif ($request->boolean('remover_hero_bg') || $request->boolean('remover_hero_bg_upload') || $request->input('remover_hero_bg') == '1' || $request->input('remover_hero_bg_upload') == '1') {
            $dados['hero_bg_imagem'] = '';
        }

        // Upload de Mockup / Imagem da Seção do Aplicativo se enviado
        if ($request->hasFile('app_mockup_upload')) {
            $file = $request->file('app_mockup_upload');
            $uploadPath = public_path('uploads/app');
            if (!\Illuminate\Support\Facades\File::isDirectory($uploadPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($uploadPath, 0755, true, true);
            }
            $fileName = 'app_mockup_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);

            // Espelha para storage/app/public/uploads/app
            $storageDir = storage_path('app/public/uploads/app');
            if (!\Illuminate\Support\Facades\File::isDirectory($storageDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($storageDir, 0755, true, true);
            }
            @copy($uploadPath . '/' . $fileName, $storageDir . '/' . $fileName);

            $dados['secao_app_mockup_imagem'] = '/uploads/app/' . $fileName;
        } elseif ($request->boolean('remover_app_mockup') || $request->boolean('remover_app_mockup_upload') || $request->input('remover_app_mockup') == '1' || $request->input('remover_app_mockup_upload') == '1') {
            $dados['secao_app_mockup_imagem'] = '';
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
        // Garante o tipo padrão caso o campo oculto chegue vazio
        if (!$request->filled('tipo')) {
            $request->merge(['tipo' => 'hero_slide']);
        }

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
            $file = $request->file('imagem_upload');
            $uploadPath = public_path('uploads/banners');
            if (!\Illuminate\Support\Facades\File::isDirectory($uploadPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($uploadPath, 0755, true, true);
            }
            $fileName = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);

            // Espelha para storage/app/public/uploads/banners
            $storageDir = storage_path('app/public/uploads/banners');
            if (!\Illuminate\Support\Facades\File::isDirectory($storageDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($storageDir, 0755, true, true);
            }
            @copy($uploadPath . '/' . $fileName, $storageDir . '/' . $fileName);

            $validated['imagem'] = '/uploads/banners/' . $fileName;
        } elseif ($request->boolean('remover_imagem_upload') || $request->boolean('remover_imagem') || $request->input('remover_imagem_upload') == '1' || $request->input('remover_imagem') == '1') {
            $validated['imagem'] = null;
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
     * Retorna os dados de um banner para edição via modal AJAX
     */
    public function obterBanner($id)
    {
        $banner = BannerSlide::findOrFail($id);

        return response()->json([
            'success' => true,
            'banner' => $banner,
            'imagem_url' => $banner->imagem ? asset($banner->imagem) : '',
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
     * Retorna os dados de uma pergunta frequente para edição via modal AJAX
     */
    public function obterFaq($id)
    {
        $faq = Faq::findOrFail($id);

        return response()->json([
            'success' => true,
            'faq' => $faq,
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
     * Retorna os dados de um depoimento para edição via modal AJAX
     */
    public function obterDepoimento($id)
    {
        $depoimento = Depoimento::findOrFail($id);

        return response()->json([
            'success' => true,
            'depoimento' => $depoimento,
            'foto_url' => $depoimento->foto ? asset($depoimento->foto) : '',
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
            $uploadPath = public_path('uploads/depoimentos');
            if (!\Illuminate\Support\Facades\File::isDirectory($uploadPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file = $request->file('foto_upload');
            $fileName = 'depoimento_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $storageDir = storage_path('app/public/uploads/depoimentos');
            if (!\Illuminate\Support\Facades\File::isDirectory($storageDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($storageDir, 0755, true, true);
            }
            @copy($uploadPath . '/' . $fileName, $storageDir . '/' . $fileName);
            $validated['foto'] = '/uploads/depoimentos/' . $fileName;
        } elseif ($request->boolean('remover_foto_upload') || $request->boolean('remover_foto') || $request->input('remover_foto_upload') == '1' || $request->input('remover_foto') == '1') {
            $validated['foto'] = null;
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
