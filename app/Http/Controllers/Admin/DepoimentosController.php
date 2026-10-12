<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Depoimento;
use Illuminate\Support\Facades\File;

class DepoimentosController extends Controller
{
    /**
     * Exibe a listagem completa da Central de Depoimentos & Moderação
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'todos');
        $busca = $request->get('busca', '');

        $query = Depoimento::query();

        if ($status === 'pendentes') {
            $query->where('ativo', false);
        } elseif ($status === 'aprovados') {
            $query->where('ativo', true);
        }

        if (!empty($busca)) {
            $query->where(function ($q) use ($busca) {
                $q->where('nome_cliente', 'like', "%{$busca}%")
                  ->orWhere('veiculo', 'like', "%{$busca}%")
                  ->orWhere('cidade_uf', 'like', "%{$busca}%")
                  ->orWhere('texto', 'like', "%{$busca}%");
            });
        }

        // Ordena pendentes primeiro e mais recentes no topo
        $depoimentos = $query->orderBy('ativo', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->appends($request->all());

        // Métricas para os cards no topo da página
        $totalGeral = Depoimento::count();
        $totalPendentes = Depoimento::where('ativo', false)->count();
        $totalAprovados = Depoimento::where('ativo', true)->count();
        $mediaEstrelas = Depoimento::where('ativo', true)->avg('estrelas') ?: 5.0;

        return view('admin.depoimentos.index', compact(
            'depoimentos',
            'status',
            'busca',
            'totalGeral',
            'totalPendentes',
            'totalAprovados',
            'mediaEstrelas'
        ));
    }

    /**
     * Retorna os dados de um depoimento específico via JSON para o modal de edição
     */
    public function obter($id)
    {
        $depoimento = Depoimento::findOrFail($id);

        return response()->json([
            'success' => true,
            'depoimento' => $depoimento,
            'foto_url' => $depoimento->foto ? asset($depoimento->foto) : '',
        ]);
    }

    /**
     * Salva ou atualiza um depoimento
     */
    public function salvar(Request $request)
    {
        $validated = $request->validate([
            'nome_cliente' => 'required|string|max:150',
            'cidade_uf' => 'nullable|string|max:100',
            'veiculo' => 'nullable|string|max:100',
            'texto' => 'required|string|min:5|max:1500',
            'estrelas' => 'required|integer|min:1|max:5',
            'ordem' => 'nullable|numeric',
            'ativo' => 'nullable|in:0,1',
        ]);

        $id = $request->input('depoimento_id') ?: $request->input('id');
        $validated['ativo'] = $request->has('ativo') ? true : false;
        $validated['ordem'] = $request->input('ordem', 0) ?: 0;

        if ($request->hasFile('foto_upload')) {
            $uploadPath = public_path('uploads/depoimentos');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }

            $file = $request->file('foto_upload');
            $fileName = 'depoimento_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);

            $storageDir = storage_path('app/public/uploads/depoimentos');
            if (!File::isDirectory($storageDir)) {
                File::makeDirectory($storageDir, 0755, true, true);
            }
            @copy($uploadPath . '/' . $fileName, $storageDir . '/' . $fileName);

            $validated['foto'] = '/uploads/depoimentos/' . $fileName;
        } elseif ($request->boolean('remover_foto_upload') || $request->boolean('remover_foto')) {
            $validated['foto'] = null;
        }

        if ($id) {
            $depoimento = Depoimento::findOrFail($id);
            $depoimento->update($validated);
            $msg = 'Depoimento atualizado com sucesso!';
        } else {
            $depoimento = Depoimento::create($validated);
            $msg = 'Novo depoimento cadastrado com sucesso!';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'depoimento' => $depoimento,
            'reload' => true,
        ]);
    }

    /**
     * Alterna rapidamente a aprovação/moderação do depoimento via AJAX
     */
    public function toggleAprovacao($id)
    {
        $depoimento = Depoimento::findOrFail($id);
        $depoimento->ativo = !$depoimento->ativo;
        $depoimento->save();

        $statusNome = $depoimento->ativo ? 'Aprovado e publicado' : 'Colocado em moderação';

        return response()->json([
            'success' => true,
            'message' => "Depoimento de {$depoimento->nome_cliente}: {$statusNome}!",
            'ativo' => $depoimento->ativo,
            'pendentes_count' => Depoimento::where('ativo', false)->count(),
            'aprovados_count' => Depoimento::where('ativo', true)->count(),
        ]);
    }

    /**
     * Exclui um depoimento com confirmação
     */
    public function excluir($id)
    {
        $depoimento = Depoimento::findOrFail($id);

        if ($depoimento->foto && File::exists(public_path(ltrim($depoimento->foto, '/')))) {
            File::delete(public_path(ltrim($depoimento->foto, '/')));
        }

        $depoimento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Depoimento removido com sucesso!',
            'pendentes_count' => Depoimento::where('ativo', false)->count(),
            'aprovados_count' => Depoimento::where('ativo', true)->count(),
        ]);
    }
}
