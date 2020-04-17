<?php

namespace App\Http\Controllers\API\Paciente;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MV\Paciente;

class ClassificacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Paciente $paciente)
    {
        // pega SOMENTE os query params abaixo
        $params = $request->only(['cd_atendimento', 'cd_tip_esq', 'cd_tip_presc']);

        if (!isset($params['cd_atendimento'])) {
            return response()->json(['message' => 'Parâmetro de atendimento obrigatório']);
        }

        $cdAtendimento  = isset($params['cd_atendimento']) ? $params['cd_atendimento'] : null;
        $filterTipEsq   = null;
        $filterTipPresc = null;

        // $pacientes = $paciente->join('atendime', 'atendime.cd_paciente', '=', 'paciente.cd_paciente')
        //                         ->join('categoria_paciente', 'categoria_paciente.cd_paciente', '=', 'paciente.cd_paciente')
        //                         ->join('categoria', 'categoria.cd_categoria', '=', 'categoria_paciente.cd_categoria')
        //                         ->join('pre_med', 'pre_med.cd_atendimento', '=', 'atendime.cd_atendimento')
        //                         ->join('itpre_med', 'itpre_med.cd_pre_med', '=', 'pre_med.cd_pre_med')
        //                         ->where('atendime.cd_atendimento', $cdAtendimento)
        //                         ->whereRaw('');

        // Adiciona o filtro do código do tip_esq
        if (isset($params['cd_tip_esq'])) {
            $inTipEsq = "'" . explode(',', $params['cd_tip_esq']) . "'";
            $filterTipEsq = "AND IPMD.CD_TIP_ESQ IN ($inTipEsq)";
        }

        // Adiciona o filtro dos códigos do tip_presc
        if (isset($params['cd_tip_presc'])) {
            $inTipPresc = "'" . explode(',', $params['cd_tip_presc']) . "'";
            $filterTipPresc = "AND IPMD.CD_TIP_PRESC IN ($inTipPresc)";
        }

        $pacientes = DB::connection('oracle')
                        ->select("  SELECT  DH_PRE_MED,
                                            CD_CATEGORIA,
                                            DS_CATEGORIA
                                    FROM    (
                                                SELECT  CAT.CD_CATEGORIA,
                                                        CAT.DS_CATEGORIA,
                                                        DENSE_RANK() OVER (PARTITION BY ATE.CD_ATENDIMENTO ORDER BY PMD.CD_PRE_MED DESC) AS SEQ_PRE,
                                                        FNC_MV_RECUPERA_DATA_HORA(PMD.DT_PRE_MED,PMD.HR_PRE_MED) AS DH_PRE_MED
                                                FROM    DBAMV.ATENDIME ATE,
                                                        DBAMV.PACIENTE PAC,
                                                        DBAMV.CATEGORIA_PACIENTE CP, 
                                                        DBAMV.CATEGORIA CAT,
                                                        DBAMV.PRE_MED PMD,
                                                        DBAMV.ITPRE_MED IPMD
                                                WHERE   ATE.CD_PACIENTE     =  PAC.CD_PACIENTE
                                                AND     ATE.CD_PACIENTE     = CP.CD_PACIENTE
                                                AND     CP.CD_CATEGORIA     =  CAT.CD_CATEGORIA   
                                                AND     ATE.CD_ATENDIMENTO  = PMD.CD_ATENDIMENTO
                                                AND     PMD.CD_PRE_MED      = IPMD.CD_PRE_MED
                                                $filterTipEsq
                                                $filterTipPresc
                                                AND     ATE.CD_ATENDIMENTO  = $cdAtendimento
                                            )
                                    WHERE   (SEQ_PRE = 1)
                                ");

        // Retorna o JSON dos resultados
        return response()->json($pacientes);
    }
}
