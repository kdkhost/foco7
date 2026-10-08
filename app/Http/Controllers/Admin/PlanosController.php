<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plano;
use Illuminate\Support\Str;

class PlanosController extends Controller
{
    /**
     * Lista todos os planos de rastreamento
     */
    public function index()
    {
        $planos = Plano::orderBy('ordem', 'asc')->get();
        return view('admin.planos.index', compact('planos'));
    }

    /**
     * Salva ou atualiza um plano via AJAX
     */
    public function salvar(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'categoria' => 'required|string|max:50',
            'subtitulo' => 'nullable|string|max:255',
            'preco_mensal' => 'required',
            'taxa_adesao' => 'nullable',
            'icone' => 'nullable|string|max:100',
            'destaque' => 'nullable|boolean',
            'tag_destaque' => 'nullable|string|max:50',
            'ordem' => 'nullable|integer',
            'ativo' => 'nullable|boolean',
            'recursos' => 'nullable|string', // Linhas de texto separadas por quebra de linha
        ], [
            'nome.required' => 'Informe o nome do plano.',
            'preco_mensal.required' => 'Informe a mensalidade.',
        ]);

        // Limpeza de moeda brasileira (R$ 69,90 -> 69.90)
        $precoLimpo = str_replace(['R$', ' ', '.'], '', $validated['preco_mensal']);
        $validated['preco_mensal'] = (float) str_replace(',', '.', $precoLimpo);

        if (!empty($validated['taxa_adesao'])) {
            $taxaLimpa = str_replace(['R$', ' ', '.'], '', $validated['taxa_adesao']);
            $validated['taxa_adesao'] = (float) str_replace(',', '.', $taxaLimpa);
        } else {
            $validated['taxa_adesao'] = 0.00;
        }

        // Converter recursos de linhas para array
        if (!empty($validated['recursos'])) {
            $linhas = explode("\n", str_replace("\r", "", $validated['recursos']));
            $validated['recursos'] = array_values(array_filter(array_map('trim', $linhas)));
        } else {
            $validated['recursos'] = [];
        }

        $validated['destaque'] = $request->boolean('destaque');
        $validated['ativo'] = $request->boolean('ativo', true);
        $validated['slug'] = Str::slug($validated['nome']);

        $id = $request->input('plano_id');

        if ($id) {
            $plano = Plano::findOrFail($id);
            $plano->update($validated);
        } else {
            Plano::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plano gravado com sucesso!',
        ]);
    }

    /**
     * Retorna os dados de um plano para edição em modal
     */
    public function obter($id)
    {
        $plano = Plano::findOrFail($id);
        return response()->json([
            'success' => true,
            'plano' => $plano,
            'recursos_texto' => is_array($plano->recursos) ? implode("\n", $plano->recursos) : '',
        ]);
    }

    /**
     * Exclui um plano
     */
    public function excluir($id)
    {
        $plano = Plano::findOrFail($id);
        $plano->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plano excluído com sucesso!',
        ]);
    }
}
