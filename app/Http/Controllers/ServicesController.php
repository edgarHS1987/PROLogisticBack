<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Clients;
use App\Models\Warehouses;
use App\Models\Services;
use App\Models\ZipCodes;
use App\Models\ZonesDrivers;
use App\Models\ServicesDrivers;
use App\Models\DriverAddress;

class ServicesController extends Controller
{

    public function assignToDriver(Request $request){
        try{
            \DB::beginTransaction();

            $today = date('Y-m-d');

            //verifica drivers dsponibles
            $drivers = ZonesDrivers::where('date', $today)->select('drivers_id')->get();            

            if(count($drivers) === 0){
                return response()->json([
                    'error'=>'No se encontraron drivers disponibles, no es posible asignar los servicios'
                ]);
            }

            //asignar
            $clients_id = $request->clients_id;

            $services = Services::where('clients_id', $clients_id)->select('id', 'zip_code', 'assigned')->get();

            foreach($services as $service){
                $service->assigned = true;
                $service->save();

                //buscar driver de zona por codigo postal
                $driver = ZipCodes::join('zones_zip_codes', 'zones_zip_codes.zip_codes_id', 'zip_codes.id')
                                ->join('zones', 'zones.id', 'zones_zip_codes.zones_id')
                                ->join('zones_drivers', 'zones_drivers.zones_id', 'zones.id')
                                ->where('zip_codes.zip_code', $service->zip_code)
                                ->where('zones_drivers.date',  $today)
                                ->select('zones_drivers.drivers_id')
                                ->first();

                
                //asigna id de driver
                if(isset($driver)){
                    $driverId = $driverId;
                }else{
                    //obtiene el driver mas cercano
                    $zipCode = ZipCodes::where('zip_code', $service->zip_code)->select('id')->first();
                    
                    $positionZipCode = $zipCode->id;
                    $distance = -1;
                    $driverid = -1;

                    foreach($drivers as $d){
                        $driversAddress = DriverAddress::where('drivers_id', $d->drivers_id)
                                                    ->select('zip_code')->first();

                        $zipCode = ZipCodes::where('zip_code', $driversAddress->zip_code)->select('id')->first();
                        $positionDriver = $zipCode->id;

                        if($positionZipCode > $positionDriver){
                            $position = $positionZipCode - $positionDriver;
                
                            if($distance == -1){
                                $distance = $position;
                                $driverid = $d->drivers_id;
                            }else if($distance > $position){
                                $distance = $position;
                                $driverid = $d->drivers_id;
                            }
                        }else{
                            $position = $positionDriver - $positionZipCode;
                
                            if($distance == -1){
                                $distance = $position;
                                $driverid = $d->drivers_id;
                            }else if($distance > $position){
                                $distance = $position;
                                $driverid = $d->drivers_id;
                            }
                        }                                               
                    }

                    $driverId = $driverid;

                }

                $datetime = new \DateTime("now", new \DateTimeZone('America/Mexico_City'));
                $time = $datetime->format('H:i');

                //creacion de registro services_drivers
                $serviceDriver = new ServicesDrivers();
                $serviceDriver->services_id = $service->id;
                $serviceDriver->drivers_id = $driverId;
                $serviceDriver->date = $today;
                $serviceDriver->time = $time;
                $serviceDriver->status = 'En sitio';
                $serviceDriver->save();                
            }

            \DB::commit();

            return response()->json([
                'message'=>'Los servicios se asignaron correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine()]);
        }
    }

    public function delete($id){
        try{
            \DB::beginTransaction();

            $service = Services::where('id', $id)->first();
            $service->delete();

            \DB::commit();

            return response()->json([
                'message'=>'Se elimino el servicio correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine()]);
        }
    }

    public function unsignedByClient(Request $request){
        $services = Services::where('clients_id', $request->clients_id)
                            ->where('assigned', false)
                            ->get();

        foreach($services as $service){
            $client = Clients::where('id', $service->clients_id)->select('name')->first();
            $service->client = $client->name;

            $warehouse = Warehouses::where('id', $service->warehouses_id)->select('name')->first();
            $service->warehouse = $warehouse->name;
        }

        return response()->json($services);
    }


    public function totalUnsignedByClient($id){
        $services = Services::where('clients_id', $id)->where('assigned', false)->get();

        return response()->json([
            'services'=>count($services)
        ]);
    }

    public function store(Request $request){
        try{
            \DB::beginTransaction();

            $service = Services::where('confirmation', $request->confirmation)
                            ->where('contact_name', $request->contact_name)
                            ->select('id')
                            ->get();
            
            if(count($service) > 0){
                return response()->json([
                    'error'=>'El servicio ya se encuentra registrado'
                ]);
            }

            $service = new Services();
            $service->warehouses_id = $request->warehouses_id;
            $service->clients_id = $request->clients_id;
            $service->date = $request->date;
            $service->time = $request->time;
            $service->confirmation = $request->confirmation;
            $service->contact_name = $request->contact_name;
            $service->address = $request->address;
            $service->zip_code = $request->zip_code;
            $service->colony = $request->colony;
            $service->state = $request->state;
            $service->municipality = $request->municipality;
            $service->phones = $request->phone;
            $service->guide_number = $request->guide_number;
            $service->route_number = $request->route_number;
            $service->save();

            $client = Clients::where('id', $service->clients_id)->select('name')->first();
            $service->client = $client->name;

            $warehouse = Warehouses::where('id', $service->warehouses_id)->select('name')->first();
            $service->warehouse = $warehouse->name;

            \DB::commit();

            return response()->json($service);

        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine()]);
        }
    }
}
