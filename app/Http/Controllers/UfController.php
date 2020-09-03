<?php

namespace App\Http\Controllers;

use App\Uf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UfController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $fechas = Uf::select('fecha')->orderBy('fecha', 'ASC')->get();
            $valores = Uf::select('valor')->orderBy('fecha', 'ASC')->get();


            return response()->json([
                'fechas' => $fechas,
                'valores' => $valores
            ], 200);
        }
        return view('grafico.uf.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $uf = Uf::where('fecha', $request->fecha)->first();

        if ($uf) {
            return response()->json([
                'msg' => 'ERROR, Ya existe un valor en esta fecha'
            ], 200);
        }
        Uf::create([
            'fecha' => $request->fecha,
            'valor' => $request->valor
        ]);


        return response()->json([
            'msg' => 'Valor Ingresado Correctamente!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Uf  $uf
     * @return \Illuminate\Http\Response
     */
    public function show(Uf $uf)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Uf  $uf
     * @return \Illuminate\Http\Response
     */
    public function edit(Uf $uf)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Uf  $uf
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $fecha)
    {
        $uf = Uf::where('fecha', $fecha)->first();

        if ($uf) {
            $uf->valor = $request->valor;

            $uf->save();

            return response()->json([
                'msg' => 'Valor Actualizado'
            ], 200);
        }

        return response()->json([
            'msg' => 'ERROR!, Fecha no encontrada'
        ], 465);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($fecha)
    {
        $uf = Uf::where('fecha', '=', $fecha)->first();

        if ($uf->delete()) {
            return response()->json([
                'msg' => 'Valor Eliminado Correctamente!'
            ], 200);
        }

        return response()->json([
            'msg' => 'ERROR, No se pudo eliminar'
        ], 465);
    }


    public function resetChart(Request $request)
    {
        if ($request->ajax()) {

            $apiUrl = 'https://mindicador.cl/api/uf';
            //Es necesario tener habilitada la directiva allow_url_fopen para usar file_get_contents
            if (ini_get('allow_url_fopen')) {
                $json = file_get_contents($apiUrl);
            } else {
                //De otra forma utilizamos cURL
                $curl = curl_init($apiUrl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $json = curl_exec($curl);
                curl_close($curl);
            }

            $dailyIndicators = (array) json_decode($json, true);
            $indicadores = array();
            foreach ($dailyIndicators as $key => $indicator) {
                if ($key == 'serie') {
                    Uf::truncate();
                    $indicadores = $indicator;
                }
            }

            foreach ($indicadores as $key => $indi) {

                $date = Carbon::parse($indi['fecha'], 'UTC');

                $uf = Uf::where('fecha', $date->isoFormat('YYYY-MM-DD'))->first();

                if (!is_null($uf)) {
                    $uf->valor = $indi['valor'];
                    $uf->save();
                } else {

                    $uf = Uf::create([
                        'fecha' => $date->isoFormat('YYYY-MM-DD'),
                        'valor' => $indi['valor']
                    ]);
                }
            }

            return response()->json([
                'msg' => 'Grafico Reseteado!'
            ], 200);
        }
    }
}
