<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MV\Leito;

class LeitoController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Leito $leito)
    {
        // pega SOMENTE os query params abaixo
        $params = $request->only(['cd_leito', 'cd_setor', 'company', 'active', 'order_by', 'order_field']);

        // Coloquei uma condição que todos os registros irão ter para poder montar o objeto do banco pra conseguir incrementar as condições
        $leitos = $leito->join('unid_int', 'unid_int.cd_unid_int', '=', 'leito.cd_unid_int')
                        ->join('setor', 'setor.cd_setor', '=', 'unid_int.cd_setor')
                        ->join('atendime', 'atendime.cd_leito', '=', 'leito.cd_leito')
                        ->join('paciente', 'paciente.cd_paciente', '=', 'atendime.cd_paciente')
                        ->whereNull('atendime.dt_alta')
                        ->whereNotNull('atendime.cd_leito');

        // Adiciona o filtro do código do leito
        if (isset($params['cd_leito'])) {
            $inLeito = explode(',', $params['cd_leito']);
            $leitos->whereIn('leito.cd_leito', $inLeito);
        }

        // Adiciona o filtro do código do setor
        if (isset($params['cd_setor'])) {
            $inSetor = explode(',', $params['cd_setor']);
            $leitos->whereIn('setor.cd_setor', $inSetor);
        }

        // Adiciona o filtro dos códigos da empresa
        if (isset($params['company'])) {
            $inCompany = explode(',', $params['company']);
            $leitos->whereIn('setor.cd_multi_empresa', $inCompany);
        }

        // Adiciona o filtro de ativo
        if (isset($params['active'])) {
            if ($params['active'] === 'true') {
                $active = 'A';
                $leitos->where('leito.tp_situacao', $active);
            } else if ($params['active'] === 'false') {
                $active = 'I';
                $leitos->where('leito.tp_situacao', $active);
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
                    $leitos->orderBy($field, $orderBy);
                }
            }
        }

        // Pega todos os registros com os filtros
        $leitos = $leitos->select('atendime.cd_atendimento', 'paciente.cd_paciente', 'paciente.nm_paciente', 'paciente.dt_nascimento', 'paciente.sn_vip', 'unid_int.cd_unid_int', 'leito.cd_leito', 'leito.ds_leito', 'leito.ds_resumo', 'setor.cd_setor')->get();

        // Retorna o JSON dos resultados
        return response()->json($leitos);
    }
}
