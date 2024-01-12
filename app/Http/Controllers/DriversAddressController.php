<?php

namespace App\Http\Controllers;
use App\Models\Driver;
use App\Models\DriverAddress;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Exception;

class DriversAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $AddressObj = new DriversAddressController();
        $Driver = new DriversController();

        try {
            $request->validate([
                'drivers_id' => 'required',
                'street' => 'required',
                'ext_number' => 'required',
                'colony' => 'required',
                'state' => 'required',
                'municipality' => 'required',
                'zip_code' => 'required'
            ]);


            $addressExistente = DriverAddress::where('drivers_id', $request->drivers_id)->first();
            //si ya existe el direccion del driver, actualizar datos
            if ($addressExistente) {
                $updateRes = $AddressObj->update($addressExistente->id,$request);
                $Driver->updateRFCDriver($request->drivers_id,$request->RFC);

                //$updateRes = $Driver->update($request, $request->idDriver);
                return $updateRes;
            }


            $newAddress = DriverAddress::create($request->all());

            $statusCode = 201;
            $datos = [
                'message' => 'Driver Address Agregada',
                'statusCode' => $statusCode,
                'data' => $newAddress
            ];

            $Driver->updateRFCDriver($request->drivers_id,$request->RFC);

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


    public function subir(Request $request){

        try{
            \DB::beginTransaction();
            // Get the file from the request
            $file = $request->file('file');

            $nameFile = $file->getClientOriginalName();

            $file->storeAs('drivers-doc', $nameFile ,'local');
            return response()->json([
                'ok' => true,
                'mensaje' => 'Archivo subido correctamente'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();
            return response()->json([
                'ok' => false,
                'mensaje' => $e.getMessage()
            ]);
        }
        

        
    }


    public function edit($id){
        $driver = Driver::join('drivers_address', 'drivers.id', 'drivers_address.drivers_id')
                    ->where('drivers.id', $id)
                    ->select('drivers.rfc', 'drivers_address.id','drivers_address.street',
                                'drivers_address.int_number','drivers_address.colony',
                                'drivers_address.state','drivers_address.municipality',
                                'drivers_address.zip_code','drivers_address.isFiscal',
                                'drivers_address.ext_number')->first();

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
    public function update( string $id, Request $request )
    {
        try{
            \DB::beginTransaction();


            $address = DriverAddress::where('id', $id)->first();
            $address->fill($request->all());
            $address->save();
            \DB::commit();
            
            return response()->json([
                'ok' => true,
                'mensaje' => 'Datos fiscales actualizados correctamente.'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();
            //dd($e);
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage()]);
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
