<?php

namespace App\Http\Controllers;
use App\Models\DriverVehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DriversVehiclesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   

        $allVehicles = DriverVehicle::all();
        try {
            $statusCode = 200;
            return response()->json([
                'message' => 'Lista de Driver-vehiculos',
                'statusCode' => $statusCode,
                'error' => true,
                'data' => $allVehicles
            ],$statusCode);
            
        } catch(Exception $e){

            $statusCode = 500;
            return response()->json([
                'message' => 'Lista de Vehiculos - Error ' . $e,
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
        try {
            
            $data = [
                'drivers_id' => $request->drivers_id,
                'brand' => $request->marca,
                'model' => $request->modelo,
                'color' => $request->color,
                'plate' => $request->placas,
                'year' => $request->year,
            ];
            $vehicle = DriverVehicle::create($data);

            $statusCode = 201;
            $datos = [
                'message' => 'Vehiculo Agregado',
                'statusCode' => $statusCode,
                'error' => false
            ];
            return response()->json([$datos]);

        } catch (Exception $e) {
            $statusCode = 500;
            $datos = [
                'message' => 'Error Validacion ' . $e->getMessage(),
                'statusCode' => $statusCode,
                'error' => true,
                'data' => []
            ];

            return response()->json([$datos]);
        }

    }


    public function edit($id){

        $vehicle = DriverVehicle::where('drivers_id', $id)->first();

        return response()->json($vehicle);
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
    public function update( Request $request )
    {
        try{
            \DB::beginTransaction();

            $data = [
                'drivers_id' => $request->drivers_id,
                'brand' => $request->marca,
                'model' => $request->modelo,
                'color' => $request->color,
                'plate' => $request->placas,
                'year' => $request->year
            ];

            $vehicle = DriverVehicle::where( 'drivers_id', $request->drivers_id )->first();
            $vehicle->fill( $data );
            $vehicle->save();
            \DB::commit();  
            
            return response()->json([
                'ok' => true,
                'error' => false,
                'mensaje' => 'El vehiculo se actualizo correctamente.'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();
            //dd($e);
            return response()->json([
                'error' => true,
                'message'=>'ERROR ('.$e->getCode().'): '.$e->getMessage()
            ]);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
