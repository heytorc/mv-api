<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MV\Localidade;

class LocalidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Localidade $localidade)
    {
        // pega SOMENTE os query params abaixo
        $params = $request->only(['cd_localidade', 'cd_setor', 'company', 'order_by', 'order_field']);

        // Coloquei uma condição que todos os registros irão ter para poder montar o objeto do banco pra conseguir incrementar as condições
        $localidades = $localidade->whereNotNull('cd_localidade');

        // Adiciona o filtro do código do localidade
        if (isset($params['cd_localidade'])) {
            $inLocalidade = explode(',', $params['cd_localidade']);
            $localidades->whereIn('cd_localidade', $inLocalidade);
        }

        // Adiciona o filtro dos códigos da empresa
        if (isset($params['company'])) {
            $inCompany = explode(',', $params['company']);
            $localidades->whereIn('cd_multi_empresa', $inCompany);
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
                    $localidades->orderBy($field, $orderBy);
                }
            }
        }

        // Pega todos os registros com os filtros
        $localidades = $localidades->select('cd_localidade', 'ds_localidade', 'cd_setor')->get();

        // Retorna o JSON dos resultados
        return response()->json($localidades);
    }
}
