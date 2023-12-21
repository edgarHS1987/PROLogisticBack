<?php

namespace App\Http\Controllers;
use App\Models\Driver;
use App\Models\User;
use App\Models\DriverDocument;
use App\Models\DriverDocumentImage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DriversDocumentsController extends Controller
{


    /**
     * Upload Document
     */
    public function subir( Request $request ){

        try{
            \DB::beginTransaction();
            // Get the file from the request
            $file = $request->file('file');

            $idDriver = $request->idDriver;
            $tipoDoc = $request->tipo;

            if ($tipoDoc == 'Identificacion Oficial') {

                $nameFile = $file->getClientOriginalName();
                $file->storeAs('drivers-doc\driver_'. $idDriver . '\INE', $nameFile ,'local');
            }
            if ($tipoDoc == 'Licencia Conducir') {

                $nameFile = $file->getClientOriginalName();
                $file->storeAs('drivers-doc\driver_'. $idDriver . '\Licencia', $nameFile ,'local');

            }
            if ($tipoDoc == 'Comprobante Domicilio') {

                $nameFile = $file->getClientOriginalName();
                $file->storeAs('drivers-doc\driver_'. $idDriver . '\Comprobante', $nameFile ,'local');

            }
            if ($tipoDoc == 'Constancia Situacion Fiscal') {

                $nameFile = $file->getClientOriginalName();
                $file->storeAs('drivers-doc\driver_'. $idDriver . '\SAT', $nameFile ,'local');

            }

            return response()->json([
                'ok' => true,
                'mensaje' => 'Archivo guardado correctamente'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();
            return response()->json([
                'ok' => false,
                'mensaje' => $e.getMessage()
            ]);
        }
        
    }


    public function deleteFolder( Request $request ){
        
        $idDriver = $request->idDriver;
        $tipoDoc = $request->tipo;

        try{
            // Ruta de la carpeta que deseas eliminar
            $carpetaAEliminar = 'ruta/a/la/carpeta';

            // Eliminar la carpeta
            Storage::deleteDirectory($carpetaAEliminar);
            
        }catch(\Exception $e){
            return response()->json([
                'ok' => false,
                'mensaje' => $e.getMessage()
            ]);
        }
        
    }


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
        $image = new DriversDocumentsImagesController();
        $DocObj = new DriversDocumentsController();

        try {
            $request->validate([
                'drivers_id' => 'required',
                'expirationIdenti' => 'required',
                'expirationLicenc' => 'required',
            ]);

            //Exatraer todos los documentos con get
            $docsExistentes = DriverDocument::where('drivers_id', $request->drivers_id)->get();
            
            //si ya existen los documentos del driver actualizar
            if ( $docsExistentes->count() > 0 ) {

                foreach ($docsExistentes as $documento) {
                    // Accede a las propiedades del documento
                    
                    //if ( $documento->type == 'INE' || $documento->type == 'Licencia' ) {
                        
                        $id = $documento->id;
                        $updateRes = $DocObj->update( $request,$id );
                    //}
                    
                }

                $statusCode = 201;
                $datos = [
                    'message' => 'Documentos Actualizados',
                    'statusCode' => $statusCode,
                    'error' => false
                ];
                return response()->json([$datos]);

            }

            $data = [
                'drivers_id' => $request->drivers_id,
                'type' => 'ConstanciaSAT',
                'number' => '1',
            ];
            $docSAT = DriverDocument::create($data);
            $path = 'drivers-doc\driver_'. $request->drivers_id . '\SAT';
            $image->readFilesinPath( $path,$docSAT->id );

            $data = [
                'drivers_id' => $request->drivers_id,
                'type' => 'Comprobante',
                'number' => '1',
            ];
            $docComprobante = DriverDocument::create($data);
            $path = 'drivers-doc\driver_'. $request->drivers_id . '\Comprobante';
            $image->readFilesinPath( $path,$docComprobante->id );

            $data = [
                'drivers_id' => $request->drivers_id,
                'type' => 'INE',
                'expiration_date' => $request->expirationIdenti,
                'number' => '1',
            ];
            $docIdentificacion = DriverDocument::create($data);
            $path = 'drivers-doc\driver_'. $request->drivers_id . '\INE';
            $image->readFilesinPath( $path,$docComprobante->id );

            $data = [
                'drivers_id' => $request->drivers_id,
                'type' => 'Licencia',
                'expiration_date' => $request->expirationLicenc,
                'number' => '1',
            ];
            $docLicencia = DriverDocument::create($data);
            $path = 'drivers-doc\driver_'. $request->drivers_id . '\Licencia';
            $image->readFilesinPath( $path,$docComprobante->id );

            $statusCode = 201;
            $datos = [
                'message' => 'Documentos Agregados',
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
        $driver = Driver::join('drivers_document', 'drivers.id', 'drivers_document.drivers_id')
                    ->where('drivers.id', $id)
                    ->select('drivers_document.expiration_date', 'drivers_document.type')->get();

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
    public function update( Request $request,string $id )
    {
        $image = new DriversDocumentsImagesController();

        try{
            \DB::beginTransaction();
            
            $dataLicencia = [
                'drivers_id' => $request->drivers_id,
                'type' => 'Licencia',
                'expiration_date' => $request->expirationLicenc,
                'number' => '1',
            ];
            $dataINE = [
                'drivers_id' => $request->drivers_id,
                'type' => 'INE',
                'expiration_date' => $request->expirationIdenti,
                'number' => '1',
            ];
            
            $doc = DriverDocument::find($id);
            if ( $doc->type == 'INE' ) {

                $doc->update($dataINE);

                //update files
                $path = 'drivers-doc\driver_'. $request->drivers_id . '\INE';
                $image->destroy( $doc->id );
                $image->readFilesinPath( $path,$doc->id );

            }elseif ( $doc->type == 'Licencia' ){

                $doc->update($dataLicencia);

                //update files
                $path = 'drivers-doc\driver_'. $request->drivers_id . '\Licencia';
                $image->destroy( $doc->id );
                $image->readFilesinPath( $path,$doc->id );

            }elseif ( $doc->type == 'Comprobante' ){

                //update files
                $path = 'drivers-doc\driver_'. $request->drivers_id . '\Comprobante';
                $image->destroy( $doc->id );
                $image->readFilesinPath( $path,$doc->id );

            }elseif ( $doc->type == 'ConstanciaSAT' ){

                //update files
                $path = 'drivers-doc\driver_'. $request->drivers_id . '\SAT';
                $image->destroy( $doc->id );
                $image->readFilesinPath( $path,$doc->id );

            }
            

            \DB::commit();
            
            return response()->json([
                'error' => false,
                'mensaje' => 'El documento se Actualizo Correctamente'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(
                [
                'mensaje'=>'ERROR ('.$e->getCode().'): '.$e->getMessage(),
                'error' => true]
            );
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
