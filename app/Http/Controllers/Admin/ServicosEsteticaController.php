<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServicoEstetica;
use Illuminate\Support\Str;

class ServicosEsteticaController extends Controller
{
    /**
     * Listagem de serviços de Estética Automotiva
     */
    public function index()
    {
        $servicos = ServicoEstetica::orderBy('ordem', 'asc')->orderBy('id', 'asc')->get();
        return view('admin.estetica.index', compact('servicos'));
    }

    /**
     * Retorna dados de um serviço para edição no modal via AJAX
     */
    public function obter($id)
    {
        $servico = ServicoEstetica::findOrFail($id);
        return response()->json([
            'success' => true,
            'servico' => $servico,
        ]);
    }

    /**
     * Cria ou atualiza um serviço de Estética Automotiva
     */
    public function salvar(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:150',
            'subtitulo' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'icone' => 'nullable|string|max:60',
            'preco' => 'nullable',
            'tempo_estimado' => 'nullable|string|max:100',
            'recursos' => 'nullable|string',
            'ordem' => 'nullable|integer',
            'imagem_upload' => 'nullable|image|max:5120',
        ]);

        $id = $request->input('servico_id');
        $servico = $id ? ServicoEstetica::findOrFail($id) : new ServicoEstetica();

        $servico->nome = $request->input('nome');
        $servico->slug = Str::slug($request->input('nome'));
        $servico->subtitulo = $request->input('subtitulo');
        $servico->descricao = $request->input('descricao');
        $servico->icone = $request->input('icone') ?: 'fa-car-side';
        $servico->tempo_estimado = $request->input('tempo_estimado');
        $servico->ordem = $request->input('ordem', 0);
        $servico->destaque = $request->has('destaque');
        $servico->ativo = $request->has('ativo');

        // Tratamento do Preço
        $precoRaw = $request->input('preco');
        if (!empty($precoRaw)) {
            $precoLimpo = str_replace(['R$', ' ', '.'], '', $precoRaw);
            $precoLimpo = str_replace(',', '.', $precoLimpo);
            $servico->preco = is_numeric($precoLimpo) ? (float) $precoLimpo : null;
        } else {
            $servico->preco = null;
        }

        // Recursos (separados por linha no textarea)
        if ($request->filled('recursos')) {
            $linhas = array_filter(array_map('trim', explode("\n", $request->input('recursos'))));
            $servico->recursos = array_values($linhas);
        } else {
            $servico->recursos = [];
        }

        // Upload de Imagem de Demonstração
        if ($request->hasFile('imagem_upload')) {
            $file = $request->file('imagem_upload');
            $filename = 'estetica_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/estetica'), $filename);
            $servico->imagem = '/uploads/estetica/' . $filename;
        }

        $servico->save();

        return response()->json([
            'success' => true,
            'message' => 'Serviço de Estética Automotiva gravado com sucesso!',
        ]);
    }

    /**
     * Alterna status Ativo/Inativo
     */
    public function toggleStatus($id)
    {
        $servico = ServicoEstetica::findOrFail($id);
        $servico->ativo = !$servico->ativo;
        $servico->save();

        return response()->json([
            'success' => true,
            'ativo' => $servico->ativo,
            'message' => 'Status do serviço atualizado com sucesso!',
        ]);
    }

    /**
     * Exclui um serviço
     */
    public function excluir($id)
    {
        $servico = ServicoEstetica::findOrFail($id);
        $servico->delete();

        return response()->json([
            'success' => true,
            'message' => 'Serviço excluído com sucesso!',
        ]);
    }
}
