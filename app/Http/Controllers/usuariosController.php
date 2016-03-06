<?php

namespace App\Http\Controllers;

use App\Http\Requests\preparacionSectorRequest;
use App\Http\Requests\usuarioAdministracionRequest;
use Bican\Roles\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\maquinaria;
use App\preparacionSector;
use App\sector;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class usuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*
     * Devuelve la pagina de buscar y automaticamente llena la tabla con la busqueda de en un intervalo de fecha de hoy a hace 6 meses
     */
    public function index() {
        $now= Carbon::now()->format('Y/m/d');
        $now2 =Carbon::now()->subMonth(6)->format('Y/m/d');
        $preparaciones = preparacionSector::whereBetween('fecha', array($now2,$now))->orderBy('fecha', 'desc')->paginate(15);
        $this->adaptaFechas($preparaciones);


        $sectores= Sector::select('id','nombre')->orderBy('nombre', 'asc')->get();
        $maquinarias= Maquinaria::select('id','nombre')->orderBy('nombre', 'asc')->get();
        return view('Sector/Preparacion/buscar')->with([
            'sectores' => $sectores,
            'maquinarias' => $maquinarias,
            'preparaciones'=>$preparaciones

        ]);
    }

    /*
     * Devuelve la vista de crear con los valores de los combobox
     * */
    public function pagCrear() {
        $roles= Role::select('id','name')->get();
        return view('Administracion/Usuarios/crear')->with([
            'roles' => $roles
        ]);
    }

    /*
     * Devuelve vista modificar con los valores del registro que se manda como parametro ($id)
     */
    public function pagModificar($id) {
        /*Validar que no sea el mismo usuario*/
        if(Auth::user()->id==$id){
            return redirect ('404');
        }

        $usuario= User::findOrFail($id);
        $roles= Role::select('id','name')->get();
        $usuarioRol=$usuario->getRoles();

        return view('Administracion/Usuarios/modificar')->with([
            'usuario'=>$usuario,
            'roles' => $roles,
            'usuarioRol'=>$usuarioRol[0]
        ]);
    }

    /*
     * Devuelve vista consultar con los valores del registro que se manda como parametro ($id)
     */

    public function pagConsultar($id) {
        $preparacion= preparacionSector::findOrFail($id);
        $fecha=Carbon::createFromFormat('Y-m-d H:i:s', $preparacion->fecha);
        $preparacion->fecha=$fecha->format('d/m/Y');


        return view('Sector/Preparacion/consultar')->with([
            'preparacion'=>$preparacion
        ]);
    }



    /*
     * Recibe la informacion del formulario de crear y la almacena en la base de datos
     */

    public function crear(usuarioAdministracionRequest $request){
        $usuario=$this->adaptarRequest($request);
        $usuario->save();
        $usuario->attachRole($request->tipoUsuario);

        Session::flash('message', 'El usuario ha sido registrado');
        return redirect('administracion/usuarios/crear');
    }


    /*
     * Recibe la informacion del formulario de modificary la actualiza en la base de datos
     */
    public function modificar(preparacionSectorRequest $request){
        $preparacion=$this->adaptarRequest($request);
        $preparacion->save();
        $preparacion->push();
        Session::flash('message', 'La preparacion ha sido modificada');
        return redirect('sector/preparacion/modificar/'.$preparacion->id);
    }

    /*
     * Elimina un registro de la base de datos
     */
    public function eliminar(Request $request){
        $preparacion= preparacionSector::findOrFail($request->id);
        $preparacion->delete();

        Session::flash('message','La preparacion ha sido eliminada');
        return redirect('sector/preparacion');
    }

    /*
     * Realiza una busqueda de informacion con los valores enviados desde la vista de busqueda
     */

    public function buscar(Request $request){

        /*Listados de combobox*/
        $sectores= Sector::select('id','nombre')->orderBy('nombre', 'asc')->get();
        $maquinarias= Maquinaria::select('id','nombre')->orderBy('nombre', 'asc')->get();

        /*Ahi se guardaran los resultados de la busqueda*/
        $preparaciones=null;


        $validator = Validator::make($request->all(), [
            'fechaInicio' => 'date_format:d/m/Y',
            'fechaFin' => 'date_format:d/m/Y',
            'sector' => 'exists:sector,id',
            'maquinaria' => 'exists:maquinaria,id'
        ]);

        /*Si validador no falla se pueden realizar busquedas*/
        if ($validator->fails()) {
        }
        else{

            /*Busqueda sin parametros*/
            if($request->fechaFin == "" && $request->fechaInicio =="" && $request->sector == "" && $request->maquinaria =="") {
                $preparaciones = preparacionSector::orderBy('fecha', 'desc')->paginate(15);;

            }

            /*Busqueda solo con sector*/
            if($request->fechaFin == "" && $request->fechaInicio =="" && $request->sector != "" && $request->maquinaria =="") {
                $preparaciones = preparacionSector::where('id_sector', $request->sector)->orderBy('fecha', 'desc')->paginate(15);;

            }

            /*Busqueda solo con maquinaria*/
            if($request->fechaFin == "" && $request->fechaInicio =="" && $request->sector == "" && $request->maquinaria !="") {
                $preparaciones = preparacionSector::where('id_maquinaria', $request->maquinaria)->orderBy('fecha', 'desc')->paginate(15);;
            }

            /*Busqueda solo con maquinaria y sector*/
            if($request->fechaFin == "" && $request->fechaInicio =="" && $request->sector != "" && $request->maquinaria !="") {
                $preparaciones = preparacionSector::where('id_sector', $request->sector)->where('id_maquinaria', $request->maquinaria)->orderBy('fecha', 'desc')->paginate(15);
            }

            /*Pregunta si se mandaron fechas, para calcular busquedas con fechas*/
            if ( $request->fechaFin != "" && $request->fechaInicio !="") {

                /*Transforma fechas en formato adecuado*/

                $fecha = $request->fechaInicio . " 00:00:00";
                $fechaInf = Carbon::createFromFormat("d/m/Y H:i:s", $fecha);
                $fecha = $request->fechaFin . " 23:59:59";
                $fechaSup = Carbon::createFromFormat("d/m/Y H:i:s", $fecha);

                /*Hay cuatro posibles casos de busqueda con fechas, cada if se basa en un caso */

                /*Solo con fechas*/
                if ($request->sector == "" && $request->maquinaria == "") {
                    $preparaciones = preparacionSector::whereBetween('fecha', array($fechaInf, $fechaSup))->orderBy('fecha', 'desc')->paginate(15);;
                }
                /*Solo con fechas y sector*/
                if ($request->sector != "" && $request->maquinaria == "") {
                    $preparaciones = preparacionSector::where('id_sector', $request->sector)->whereBetween('fecha', array($fechaInf, $fechaSup))->orderBy('fecha', 'desc')->paginate(15);;
                }

                /*Solo con fechas y maquinaria*/
                if ($request->sector == "" && $request->maquinaria !== "") {
                    $preparaciones = preparacionSector::where('id_maquinaria', $request->maquinaria)->whereBetween('fecha', array($fechaInf, $fechaSup))->orderBy('fecha', 'desc')->paginate(15);;
                }

                /*Fechas, maquinaria y sector, los tres parametros de filtro*/
                if ($request->sector != "" && $request->maquinaria !== "") {
                    $preparaciones = preparacionSector::where('id_sector', $request->sector)->where('id_maquinaria', $request->maquinaria)->whereBetween('fecha', array($fechaInf, $fechaSup))->orderBy('fecha', 'desc')->paginate(15);;
                }
            }
        }


            if($preparaciones!=null){
                /*Adapta el formato de fecha para poder imprimirlo en la vista adecuadamente*/
                $this->adaptaFechas($preparaciones);

                /*Si no es nulo puede contar los resultados*/
                $num = $preparaciones->total();
            }
            else{
                $num=0;
            }


            if($num<=0){
                Session::flash('error', 'No se encontraron resultados');
            }
            else{
                Session::flash('message', 'Se encontraron '.$num.' resultados');
            }
        /*Regresa la vista*/
            return view('Sector/Preparacion/buscar')->with([
                'preparaciones'=>$preparaciones,
                'sectores' => $sectores,
                'maquinarias' => $maquinarias
            ]);
    }






    /*
     * Recibe la informacion del formulario de crear y la adapta a los campos del modelo
     */
    public function adaptarRequest($request){
        $usuario=new User();
        $usuario->remember_token = str_random(10);
        if(isset($request->id)) {
            $usuario = User::findOrFail($request->id);
        }

        $usuario->email= $request->correo;
        $usuario->password=  Hash::make($request->password);

        return $usuario;
    }
    /*
     * Adapta fechas de resultado de busqueda a formato adecuado para imprimir en la vista de busqueda
     */
    public function adaptaFechas($resultados){

        foreach($resultados as $resultado  ){
            $fecha=Carbon::createFromFormat('Y-m-d H:i:s', $resultado->fecha);
            $resultado->fecha=$fecha->format('d/m/Y');
        }

    }


}
