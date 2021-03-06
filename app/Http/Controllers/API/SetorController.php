<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MV\Setor;

class SetorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Setor $setor)
    {
        // pega SOMENTE os query params abaixo
        $params = $request->only(['cd_setor', 'company', 'active', 'order_by', 'order_field']);

        // Coloquei uma condição que todos os registros irão ter para poder montar o objeto do banco pra conseguir incrementar as condições
        $setores = $setor->whereNotNull('cd_setor');

        // Adiciona o filtro do código do setor
        if (isset($params['cd_setor'])) {
            $inSetor = explode(',', $params['cd_setor']);
            $setores->whereIn('cd_setor', $inSetor);
        }

        // Adiciona o filtro dos códigos da empresa
        if (isset($params['company'])) {
            $inCompany = explode(',', $params['company']);
            $setores->whereIn('cd_multi_empresa', $inCompany);
        }

        // Adiciona o filtro de ativo
        if (isset($params['active'])) {
            if ($params['active'] === 'true') {
                $active = 'S';
                $setores->where('sn_ativo', $active);
            } else if ($params['active'] === 'false') {
                $active = 'N';
                $setores->where('sn_ativo', $active);
            }
        }

        // Adiciona a ordenação dos registros
        if (isset($params['order_by']) && isset($params['order_field'])) {
            // Verifica se os paramestros passados são 'asc' ou 'desc', se não for ignora o filtro
            if (strtolower($params['order_by']) !== 'asc' && strtolower($params['order_by']) !== 'desc') {

            } else {
                $orderBy = $params['order_by'];
                $fields = explode(',', $params['order_field']);

                // Percorre os campos passados na URL
                foreach ($fields as $key => $field) {
                    // Seta a ordenção na ordem que foram passados
                    $setores->orderBy($field, $orderBy);
                }
            }
        }

        // Pega todos os registros com os filtros
        $setores = $setores->select('cd_setor', 'nm_setor', 'tp_grupo_setor')->get();

        // Retorna o JSON dos resultados
        return response()->json($setores);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
