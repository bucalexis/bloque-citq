<?php

namespace App\Http\Controllers;

use App\cultivo;
use App\fertilizacion;
use App\fuente;
use App\Http\Requests\fertilizacionSectorRequest;
use App\sector;
use App\siembraSector;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class fertilizacionSectorController extends Controller
{
    /**
     * Metodo para ver la pagina inicial de fertilizacion sector
     *
     *
     */
    public function index()
    {
        //
        $now= Carbon::now()->format('Y/m/d');
        $now2 =Carbon::now()->subMonth(6)->format('Y/m/d');
        $fertilizaciones = fertilizacion::whereBetween('fecha', array($now2,$now))->orderBy('fecha', 'desc')->paginate(15);
        $this->adaptaFechas($fertilizaciones);



        $sectores= sector::select('id','nombre')->orderBy('nombre', 'asc')->get();
        $fuentes= fuente::select('id','nombre')->orderBy('nombre', 'asc')->get();
        return view('Sector/Fertilizacion/buscar')->with([
            'sectores' => $sectores,
            'fuentes' => $fuentes,
            'fertilizaciones'=>$fertilizaciones

        ]);
    }

    public function buscar(Request $request)
    {
        $sectores= Sector::select('id','nombre')->orderBy('nombre', 'asc')->get();
        $fuentes= fuente::select('id','nombre')->orderBy('nombre', 'asc')->get();

        /*Pregunta si se mandaron fechas, en caso contrario manda error 404*/
        if ( $request->fechaFin != "" && $request->fechaInicio !="") {

            /*Transforma fechas en formato adecuado*/
            $fecha = $request->fechaInicio . " 00:00:00";
            $fechaInf = Carbon::createFromFormat("d/m/Y H:i:s", $fecha);
            $fecha = $request->fechaFin . " 23:59:59";
            $fechaSup = Carbon::createFromFormat("d/m/Y H:i:s", $fecha);

            /*Hay cuatro posibles casos de busqueda, cada if se basa en un caso */
            if($request->sector==""&&$request->fuente=="") {
                $fertilizaciones= fertilizacion::whereBetween('fecha', array($fechaInf, $fechaSup))->orderBy('fecha', 'desc')->paginate(15);;
            }
            if($request->sector!=""&&$request->fuente=="") {
                $fertilizaciones= fertilizacion::where('id_sector',$request->sector)->whereBetween('fecha', array($fechaInf, $fechaSup))->orderBy('fecha', 'desc')->paginate(15);;
            }
            if($request->sector==""&&$request->fuente!=="") {
                $fertilizaciones= fertilizacion::where('id_fuente',$request->fuente)->whereBetween('fecha', array($fechaInf, $fechaSup))->orderBy('fecha', 'desc')->paginate(15);;
            }
            if($request->sector!=""&&$request->fuente!=="") {
                $fertilizaciones= fertilizacion::where('id_sector',$request->sector)->where('id_fuente',$request->fuente)->whereBetween('fecha', array($fechaInf, $fechaSup))->orderBy('fecha', 'desc')->paginate(15);;
            }

            /*Adapta el formato de fecha para poder imprimirlo en la vista adecuadamente*/
            $this->adaptaFechas($fertilizaciones);
            $num = $fertilizaciones->total();

            if($num<=0){
                Session::flash('error', 'No se encontraron resultados');

            }
            else{
                Session::flash('message', 'Se encontraron '.$num.' resultados');
            }

            return view('Sector/Fertilizacion/buscar')->with([
                'fertilizaciones'=>$fertilizaciones,
                'sectores' => $sectores,
                'fuentes' => $fuentes
            ]);
        }
        else
        {
            return redirect('errors/404');
        }


    }

    /*Devuelve la vista de crear con los valores de los combobox*/
    public function pagCrear()
    {
        $sectores= Sector::select('id','nombre')->orderBy('nombre', 'asc')->get();

        $siembras = DB::table('siembraSector')->join('cultivo','cultivo.id','=','siembraSector.id_cultivo')
            ->select('siembraSector.id','siembraSector.id_cultivo','cultivo.nombre','siembraSector.variedad')
            ->get();


        $fuentes = fuente::select('id','nombre')->orderBy('nombre', 'asc')->get();

        $tipoFertilizaciones = ['Riego','Aplicacion dirigida'];


        return view('Sector/Fertilizacion/crear')->with([
            'sectores' => $sectores,
            'siembras' => $siembras,
            'tipoFertilizaciones'=>$tipoFertilizaciones,
            'fuentes' => $fuentes

        ]);
    }

    /*
     * Crear pagina de modificar
     *
     * */
    public function pagModificar($id)
    {
        $fertilizacionSector= fertilizacion::findOrFail($id);

        $sectores= Sector::select('id','nombre')->orderBy('nombre', 'asc')->get();
        $siembras = DB::table('siembraSector')->join('cultivo','cultivo.id','=','siembraSector.id_cultivo')
            ->select('siembraSector.id','siembraSector.id_cultivo','cultivo.nombre','siembraSector.variedad')
            ->get();

        $fuentes = fuente::select('id','nombre')->orderBy('nombre', 'asc')->get();
        $tipoFertilizaciones = ['Riego','Aplicacion dirigida'];

        $fecha=Carbon::createFromFormat('Y-m-d H:i:s', $fertilizacionSector->fecha);
        $fertilizacionSector->fecha=$fecha->format('d/m/Y');



        return view('Sector/Fertilizacion/modificar')->with([
            'sectores' => $sectores,
            'siembras' => $siembras,
            'tipoFertilizaciones'=>$tipoFertilizaciones,
            'fuentes' => $fuentes,
            'fertilizacionSector' => $fertilizacionSector

        ]);
    }


    /*Recibe la informacion del formulario de crear y la almacena en la base de datos*/
    public function crear(fertilizacionSectorRequest $request)
    {

        $fertilizacion=$this->adaptarRequest($request);
        $fertilizacion->save();

        Session::flash('message', 'La fertilizacion ha sido agregada');
        return redirect('sector/fertilizacion/crear');
    }



    /*Modificar registro*/
    public function modificar(fertilizacionSectorRequest $request)
    {
        $fertilizacion=$this->adaptarRequest($request);
        $fertilizacion->save();
        $fertilizacion->push();
        Session::flash('message', 'La preparacion ha sido modificada');
        return redirect('sector/fertilizacion/modificar/'.$fertilizacion->id);
    }



    /*Recibe la informacion del formulario de crear y la adapta a los campos del modelo*/
    public function adaptarRequest($request){
        $fertilizacion = new fertilizacion();
        if(isset($request->id)) {
            $fertilizacion = fertilizacion::findOrFail($request->id);
        }


        $fertilizacion->programaNPK = $request->programaNPK;
        $fertilizacion->cantidad= $request->cantidad;

        $fertilizacion->id_siembra = $request->siembra;
        $fertilizacion->id_fuente= $request->fuente;
        $fertilizacion->id_sector= $request->sector;
        $fertilizacion->fecha = Carbon::createFromFormat('d/m/Y', $request->fecha)->toDateTimeString();



        return $fertilizacion;
    }


    public function pagConsultar($id)
    {
        $fertilizacion= fertilizacion::findOrFail($id);
        $fecha=Carbon::createFromFormat('Y-m-d H:i:s', $fertilizacion->fecha);
        $fertilizacion->fecha=$fecha->format('d/m/Y');


        $siembras = DB::table('siembraSector')
            ->join('cultivo','cultivo.id','=','siembraSector.id_cultivo')
            ->join('fertilizacion','fertilizacion.id_siembra','=','siembraSector.id')
            ->select('siembraSector.id','siembraSector.id_cultivo','cultivo.nombre','siembraSector.variedad')
            ->where('fertilizacion.id','=',$id)->first();




        return view('Sector/Fertilizacion/consultar')->with([
            'fertilizacion'=>$fertilizacion,
            'siembras' => $siembras
        ]);
    }


    /*Eliminar registro*/
    public function eliminar(Request $request)
    {
        $fertilizacion= fertilizacion::findOrFail($request->id);
        $fertilizacion->delete();

        Session::flash('message','La fertilizacion ha sido eliminada');
        return redirect('sector/fertilizacion');
    }


    /*Adapta fechas a formato adecuado para imprimir en la vista*/
    public function adaptaFechas($resultados){

        foreach($resultados as $resultado  ){
            $fecha=Carbon::createFromFormat('Y-m-d H:i:s', $resultado->fecha);
            $resultado->fecha=$fecha->format('d/m/Y');
        }

    }

    public function carga($id)
    {
       return $id;
    }

}
