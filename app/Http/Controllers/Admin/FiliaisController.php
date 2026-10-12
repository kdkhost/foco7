<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Filial;
use Illuminate\Support\Facades\File;

class FiliaisController extends Controller
{
    /**
     * Lista todas as filiais e unidades de atendimento
     */
    public function index(Request $request)
    {
        $query = Filial::query();

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('cidade', 'like', "%{$busca}%")
                  ->orWhere('estado', 'like', "%{$busca}%")
                  ->orWhere('endereco', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status') === 'ativo';
            $query->where('ativo', $status);
        }

        $filiais = $query->orderBy('destaque', 'desc')
                         ->orderBy('ordem', 'asc')
                         ->orderBy('id', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        $totalFiliais = Filial::count();
        $filiaisAtivas = Filial::where('ativo', true)->count();
        $filiaisInativas = Filial::where('ativo', false)->count();
        $totalCidades = Filial::where('ativo', true)->distinct('cidade')->count('cidade');

        return view('admin.filiais.index', compact(
            'filiais',
            'totalFiliais',
            'filiaisAtivas',
            'filiaisInativas',
            'totalCidades'
        ));
    }

    /**
     * Retorna os dados da filial em JSON para edição via modal AJAX
     */
    public function obter($id)
    {
        $filial = Filial::findOrFail($id);

        return response()->json([
            'success' => true,
            'filial' => $filial,
            'foto_url' => $filial->foto_url,
            'rota_url' => $filial->rota_url,
        ]);
    }

    /**
     * Salva ou atualiza uma filial via AJAX
     */
    public function salvar(Request $request)
    {
        $id = $request->input('filial_id') ?? $request->input('id');

        $validated = $request->validate([
            'nome' => 'required|string|max:150',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2',
            'endereco' => 'required|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'cep' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'horario_atendimento' => 'nullable|string|max:150',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'link_rota' => 'nullable|string|max:500',
            'destaque' => 'nullable',
            'ativo' => 'nullable',
            'ordem' => 'nullable|integer',
            'foto_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'nome.required' => 'O nome da filial ou unidade é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'estado.required' => 'O estado (UF) é obrigatório.',
            'endereco.required' => 'O endereço completo é obrigatório.',
            'foto_upload.image' => 'O arquivo da foto da fachada deve ser uma imagem válida.',
            'foto_upload.mimes' => 'Formatos permitidos para foto: JPEG, PNG, JPG, WEBP.',
            'foto_upload.max' => 'O tamanho máximo da foto é de 5MB.',
        ]);

        if ($id) {
            $filial = Filial::findOrFail($id);
        } else {
            $filial = new Filial();
        }

        $filial->nome = $validated['nome'];
        $filial->cidade = $validated['cidade'];
        $filial->estado = strtoupper(trim($validated['estado']));
        $filial->endereco = $validated['endereco'];
        $filial->bairro = $validated['bairro'] ?? null;
        $filial->cep = $validated['cep'] ?? null;
        $filial->telefone = $validated['telefone'] ?? null;
        $filial->whatsapp = $validated['whatsapp'] ?? null;
        $filial->email = $validated['email'] ?? null;
        $filial->horario_atendimento = $validated['horario_atendimento'] ?? 'Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00';
        $filial->latitude = !empty($validated['latitude']) ? (float)$validated['latitude'] : null;
        $filial->longitude = !empty($validated['longitude']) ? (float)$validated['longitude'] : null;
        $filial->link_rota = $validated['link_rota'] ?? null;
        $filial->destaque = $request->boolean('destaque') || $request->input('destaque') == '1';
        $filial->ordem = (int) ($validated['ordem'] ?? 0);

        if ($request->has('ativo')) {
            $ativoVal = $request->input('ativo');
            $filial->ativo = ($ativoVal === '1' || $ativoVal === 1 || $ativoVal === true || $ativoVal === 'true');
        } else {
            $filial->ativo = false;
        }

        // Upload de foto/fachada com FocoDropzone
        $file = $request->file('foto_upload') ?? $request->file('foto');
        if ($file && $file->isValid()) {
            $uploadDir = public_path('uploads/filiais');
            if (!File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true, true);
            }

            if ($filial->foto) {
                $cleanOld = public_path(ltrim($filial->foto, '/'));
                if (file_exists($cleanOld)) {
                    @unlink($cleanOld);
                }
            }

            $fileName = 'filial_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);

            // Espelha para storage/app/public/uploads/filiais
            $storageDir = storage_path('app/public/uploads/filiais');
            if (!File::isDirectory($storageDir)) {
                File::makeDirectory($storageDir, 0755, true, true);
            }
            @copy($uploadDir . '/' . $fileName, $storageDir . '/' . $fileName);

            $filial->foto = '/uploads/filiais/' . $fileName;
        } elseif ($request->boolean('remover_foto_upload') || $request->boolean('remover_foto') || $request->input('remover_foto_upload') == '1' || $request->input('remover_foto') == '1') {
            if ($filial->foto) {
                $cleanOld = public_path(ltrim($filial->foto, '/'));
                if (file_exists($cleanOld)) {
                    @unlink($cleanOld);
                }
            }
            $filial->foto = null;
        }

        $filial->save();

        return response()->json([
            'success' => true,
            'message' => $id ? 'Filial atualizada com sucesso!' : 'Nova filial cadastrada com sucesso!',
            'filial' => $filial,
        ]);
    }

    /**
     * Alterna o status ativo/inativo via AJAX
     */
    public function toggle($id)
    {
        $filial = Filial::findOrFail($id);
        $filial->ativo = !$filial->ativo;
        $filial->save();

        return response()->json([
            'success' => true,
            'ativo' => $filial->ativo,
            'message' => 'Status da filial ' . $filial->nome . ' alterado para ' . ($filial->ativo ? 'Ativo' : 'Inativo') . '!',
        ]);
    }

    /**
     * Exclui uma filial via AJAX
     */
    public function excluir($id)
    {
        $filial = Filial::findOrFail($id);
        $nome = $filial->nome;

        if ($filial->foto) {
            $cleanOld = public_path(ltrim($filial->foto, '/'));
            if (file_exists($cleanOld)) {
                @unlink($cleanOld);
            }
        }

        $filial->delete();

        return response()->json([
            'success' => true,
            'message' => "Filial '{$nome}' excluída com sucesso!",
        ]);
    }
}
