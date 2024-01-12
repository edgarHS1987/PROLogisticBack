<?php

namespace App\Http\Controllers;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DriversController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        // try{
        //     \DB::beginTransaction();



        //     \DB::commit();

        //     return response()->json([
        //         'ok' => 'registro correcto',                
        //         'sales_id' => $sales->id
        //     ]);


        // }catch(\Exception $e){
        //     \DB::rollback();
        //     //dd($e);
        //     return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine()]);
        // }

        $allDrivers = Driver::all();
        try {
            $statusCode = 200;
            return response()->json([
                'message' => 'Lista de Drivers',
                'statusCode' => $statusCode,
                'error' => true,
                'data' => $allDrivers
            ],$statusCode);
            
        } catch(Exception $e){

            $statusCode = 500;
            return response()->json([
                'message' => 'Lista de Drivers - Error ' . $e,
                'statusCode' => $statusCode,
                'error' => false,
                'data' => []
            ],$statusCode);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        //Crea objeto del tipo user para poder invocar el metodo store y se agregan campos necesarios al request
        $user = new UsersController();

        $Driver = new DriversController();

        $request->merge(['email' => $request->email]);
        $request->merge(['role' => 'administrador_de_sistema']);

        $driverExistente = Driver::where('id', $request->idDriver)->first();
        //si ya existe el driver, entonses actualizar datos
        if ($driverExistente) {
            $user->update($driverExistente->users_id,$request);

            $updateRes = $Driver->update($request, $request->idDriver);
            return $updateRes;
            // $driverExistente = Driver::where('id', $request->idDriver)->first();
            // $driverExistente->fill($request->all());
            // $driverExistente->save();
        }
 

        
        
        $respuesta = $user->store($request);

        $responseContent = json_decode($respuesta->getContent(), true);

        if ( isset( $responseContent["ok"] ) ) {
            $respuestaDriver = $Driver->creaDriver($request);
            
            return response()->json([$respuestaDriver]);

        }else{
            //Elimina el usuario en caso de que se haya creado
            //User::where('email', $request->email)->delete();
            $statusCode = 500;
            return response()->json([
                'message' => 'Error al crear User',
                'statusCode' => $statusCode,
                'error' => true,
                'data' => []
            ],$statusCode);
        }   
    }


    public function creaDriver(Request $request){
        try {
            $user = User::where('email', $request->email)->first();
            $request->merge(['users_id' => $user->id]);

            $request->validate([
                'names' => 'required',
                'lastname1' => 'required',
                'status' => 'required',
                'users_id' => 'required',
            ]);

            $newDriver = Driver::create($request->all());

            $statusCode = 201;
            $datos = [
                'message' => 'Driver Agregado',
                'statusCode' => $statusCode,
                'error' => true,
                'data' => $newDriver
            ];

            return $datos;


        } catch (Exception $e) {
            $statusCode = 500;
            $datos = [
                'message' => 'Error Validacion ' . $e->getMessage(),
                'statusCode' => $statusCode,
                'error' => true,
                'data' => []
            ];

            $json = json_encode($datos);
            return $datos;
        }
    }


    public function edit($id){
        $driver = Driver::join('users', 'users.id', 'drivers.users_id')
                    ->where('drivers.id', $id)
                    ->select('drivers.id', 'drivers.names', 'drivers.lastname1','drivers.lastname2',
                                'drivers.status','users.email')->first();
        return response()->json($driver);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            \DB::beginTransaction();


            $driver = Driver::where('id', $id)->first();
            $driver->fill($request->all());
            $driver->save();
            \DB::commit();
            
            return response()->json([
                'ok' => true,
                'mensaje' => 'El driver '.$driver->names.' se actualizo correctamente.'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();
            //dd($e);
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage()]);
        }
    }


    /**
     * Update RFC Driver
     */
    public function updateRFCDriver(string $id,string $RFC)
    {
        Driver::where('id', $id)->update(['rfc' => $RFC]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
