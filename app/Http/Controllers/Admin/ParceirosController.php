<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parceiro;
use Illuminate\Support\Facades\File;

class ParceirosController extends Controller
{
    /**
     * Lista todas as empresas parceiras
     */
    public function index(Request $request)
    {
        $query = Parceiro::query();

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where('nome', 'like', "%{$busca}%")
                  ->orWhere('descricao', 'like', "%{$busca}%");
        }

        if ($request->filled('status')) {
            $status = $request->input('status') === 'ativo';
            $query->where('ativo', $status);
        }

        $parceiros = $query->orderBy('ordem', 'asc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalParceiros = Parceiro::count();
        $parceirosAtivos = Parceiro::where('ativo', true)->count();
        $parceirosInativos = Parceiro::where('ativo', false)->count();

        return view('admin.parceiros.index', compact(
            'parceiros',
            'totalParceiros',
            'parceirosAtivos',
            'parceirosInativos'
        ));
    }

    /**
     * Retorna os dados do parceiro em JSON para edição via modal
     */
    public function obter($id)
    {
        $parceiro = Parceiro::findOrFail($id);

        return response()->json([
            'success' => true,
            'parceiro' => $parceiro,
            'logo_url' => $parceiro->logo_url,
        ]);
    }

    /**
     * Salva ou atualiza um parceiro via AJAX
     */
    public function salvar(Request $request)
    {
        $id = $request->input('parceiro_id') ?? $request->input('id');

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'link' => 'nullable|string|max:255',
            'descricao' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'ordem' => 'nullable|integer',
            'ativo' => 'nullable',
            'logo' => 'nullable',
            'logo_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ], [
            'nome.required' => 'O nome da empresa parceira é obrigatório.',
            'logo_upload.image' => 'O arquivo de logotipo deve ser uma imagem válida.',
            'logo_upload.mimes' => 'Formatos permitidos para logotipo: SVG, PNG, WEBP, JPG.',
            'logo_upload.max' => 'O tamanho máximo da logo é de 5MB.',
        ]);

        if ($id) {
            $parceiro = Parceiro::findOrFail($id);
        } else {
            $parceiro = new Parceiro();
        }

        $parceiro->nome = $validated['nome'];
        $parceiro->link = $validated['link'] ?? null;
        $parceiro->descricao = $validated['categoria'] ?? $validated['descricao'] ?? null;
        $parceiro->ordem = (int) ($validated['ordem'] ?? 0);
        
        if ($request->has('ativo')) {
            $ativoVal = $request->input('ativo');
            $parceiro->ativo = ($ativoVal === '1' || $ativoVal === 1 || $ativoVal === true || $ativoVal === 'true');
        } else {
            $parceiro->ativo = false;
        }

        // Upload da Logomarca (SVG / PNG / WEBP)
        $file = $request->file('logo_upload') ?? $request->file('logo');
        if ($file && $file->isValid()) {
            $uploadDir = public_path('uploads/parceiros');
            if (!File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true, true);
            }

            // Remove logo antiga se não for SVG padrão de sistema
            if ($parceiro->logo && !str_starts_with($parceiro->logo, '/img/parceiros/')) {
                $cleanOld = public_path(ltrim($parceiro->logo, '/'));
                if (file_exists($cleanOld)) {
                    @unlink($cleanOld);
                }
            }

            $fileName = 'parceiro_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);
            $parceiro->logo = '/uploads/parceiros/' . $fileName;
        } elseif ($request->input('remover_logo_upload') == '1' || $request->input('remover_logo') == '1') {
            if ($parceiro->logo && !str_starts_with($parceiro->logo, '/img/parceiros/')) {
                $cleanOld = public_path(ltrim($parceiro->logo, '/'));
                if (file_exists($cleanOld)) {
                    @unlink($cleanOld);
                }
            }
            $parceiro->logo = '/img/favicon.png';
        } elseif (!$id && empty($parceiro->logo)) {
            $parceiro->logo = '/img/favicon.png';
        }

        $parceiro->save();

        return response()->json([
            'success' => true,
            'message' => $id ? 'Parceiro atualizado com sucesso!' : 'Parceiro cadastrado com sucesso!',
        ]);
    }

    /**
     * Alterna o status ativo/inativo via AJAX
     */
    public function toggle($id)
    {
        $parceiro = Parceiro::findOrFail($id);
        $parceiro->ativo = !$parceiro->ativo;
        $parceiro->save();

        return response()->json([
            'success' => true,
            'ativo' => $parceiro->ativo,
            'message' => $parceiro->ativo
                ? "Parceiro {$parceiro->nome} ativado com sucesso!"
                : "Parceiro {$parceiro->nome} desativado com sucesso!",
        ]);
    }

    /**
     * Exclui o parceiro com SweetAlert2
     */
    public function excluir($id)
    {
        $parceiro = Parceiro::findOrFail($id);

        if ($parceiro->logo && !str_starts_with($parceiro->logo, '/img/parceiros/')) {
            $cleanOld = public_path(ltrim($parceiro->logo, '/'));
            if (file_exists($cleanOld)) {
                @unlink($cleanOld);
            }
        }

        $parceiro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Parceiro removido com sucesso!',
        ]);
    }
}
