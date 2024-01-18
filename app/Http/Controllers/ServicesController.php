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
use App\Models\Driver;

class ServicesController extends Controller
{

    /**
     * Servicios asignados a driver (app)
     */
    public function assigned(Request $request){
        $users_id = $request->user()->id;
        $driver = Driver::where('users_id', $users_id)->select('id')->first();
        
        $services = Services::join('services_drivers', 'services_drivers.services_id', 'services.id')                            
                            ->where('services_drivers.drivers_id', $driver->id)
                            ->where('services_drivers.isLast', true)
                            ->select(
                                'services.id', 'services.contact_name', 'services.address', 'services.colony',
                                'services.zip_code', 'services.phones', 'services.municipality', "services.status", 
                                'services.confirmation', 'services_drivers.status as status_service_driver'
                            )
                            ->get();

        return response()->json($services);
    }

    /**
     * Asignar servicios a driver
     */
    public function assignToDriver(Request $request){
        try{
            \DB::beginTransaction();

            $datetime = new \DateTime("now", new \DateTimeZone('America/Mexico_City'));
            $today = $datetime->format('Y-m-d');

            //verifica drivers dsponibles
            $drivers = ZonesDrivers::where('date', $today)->select('drivers_id')->get();            

            if(count($drivers) === 0){
                return response()->json([
                    'error'=>'No se encontraron drivers disponibles, no es posible asignar los servicios'
                ]);
            }

            //asignar
            $clients_id = $request->clients_id;

            $services = Services::where('clients_id', $clients_id)->select('id', 'zip_code', 'assigned', 'status')->get();

            foreach($services as $service){
                $status->status = 'En sitio';
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
                    $driverId = $driver->drivers_id;
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

    /**
     * Elimina servicio
     */
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

    /**
     * Muestra detalles de servicio
     */
    public function details($id){
        $service = Services::where('id', $id)
                        ->select(
                            'id', 'date', 'guide_number', 'route_number', 'contact_name', 'confirmation',
                            'address', 'zip_code', 'colony', 'state', 'municipality', 'phones'
                        )->first();
        
        $service['details'] = ServicesDrivers::join('drivers', 'drivers.id', 'services_drivers.drivers_id')
                                    ->where('services_drivers.services_id', $service->id)
                                    ->select(
                                        'services_drivers.id', 'services_drivers.date', 'services_drivers.time', 'services_drivers.status',
                                        'services_drivers.observations', 'services_drivers.finish_location', 'drivers.names', 'drivers.lastname1', 'drivers.lastname2'
                                    )->get();
        
        return response()->json($service);
    }

    /**
     * Carga listado de servicios por fecha
     */
    public function list($id, $date){
        $services = Services::where('clients_id', $id)
                            ->where('date', $date)
                            ->where('assigned', true)
                            ->select(
                                'id', 'date', 'contact_name', 'address', 'zip_code',
                                'colony', 'municipality', 'phones', 'status', 'confirmation'
                            )->get();

        foreach($services as $service){
            $driver = ServicesDrivers::join('drivers', 'drivers.id', 'services_drivers.drivers_id')
                                ->where('services_drivers.services_id', $service->id)
                                ->selectRaw('CONCAT(drivers.names," ",drivers.lastname1," ",drivers.lastname2) as name')
                                ->first();
            
            $service['driver'] = $driver->name;
        }

        return response()->json($services);
        
    }

    /**
     * Iniciar carga de servicios (app)
     */
    public function startCharge(Request $request){
        try{
            \DB::beginTransaction();

            $servicesIds = $request->all();
            $services = ServicesDrivers::whereIn('services_id', $servicesIds)->get();

            foreach($services as $service){
                $service->isLast = false;
                $service->save();

                $serviceItem = Services::where('id', $service->services_id)->select('id', 'status')->first();
                $serviceItem->status = 'Cargando';
                $serviceItem->save();

                $datetime = new \DateTime("now", new \DateTimeZone('America/Mexico_City'));
                $today = $datetime->format('Y-m-d');
                $time = $datetime->format('H:i');
                
                $serviceDriver = new ServicesDrivers();
                $serviceDriver->services_id = $service->services_id;
                $serviceDriver->drivers_id = $service->drivers_id;
                $serviceDriver->date = $today;
                $serviceDriver->time = $time;
                $serviceDriver->status = 'Cargando';
                $serviceDriver->save();  
            }

            \DB::commit();

            return response()->json([
                'message'=>'Iniciando carga'
            ]);
        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine()]);
        }
    }

    /**
     * finalizar carga de servicios (app)
     */
    public function endCharge(Request $request){
        try{
            \DB::beginTransaction();

            $servicesIds = $request->all();
            $services = ServicesDrivers::whereIn('services_id', $servicesIds)->where('isLast', true)->get();

            foreach($services as $service){
                $service->isLast = false;
                $service->save();

                $serviceItem = Services::where('id', $service->services_id)->select('id', 'status')->first();
                $serviceItem->status = 'Listo para entrega';
                $serviceItem->save();

                $datetime = new \DateTime("now", new \DateTimeZone('America/Mexico_City'));
                $today = $datetime->format('Y-m-d');
                $time = $datetime->format('H:i');
                
                $serviceDriver = new ServicesDrivers();
                $serviceDriver->services_id = $service->services_id;
                $serviceDriver->drivers_id = $service->drivers_id;
                $serviceDriver->date = $today;
                $serviceDriver->time = $time;
                $serviceDriver->status = 'Listo para entrega';
                $serviceDriver->save();  
            }

            \DB::commit();

            return response()->json([
                'message'=>'Carga finalizada'
            ]);
        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine()]);
        }
    }

    /**
     * iniciar entrega (app)
     */
    public function startDeliver($id){
        try{
            \DB::beginTransaction();

            $service = Services::where('id', $id)->first();
            $service->status = 'En ruta';
            $service->save();

            $service = ServicesDrivers::where('services_id', $id)->where('isLast', true)->first();
            $service->isLast = false;
            $service->save();

            $datetime = new \DateTime("now", new \DateTimeZone('America/Mexico_City'));
            $today = $datetime->format('Y-m-d');
            $time = $datetime->format('H:i');

            $serviceDriver = new ServicesDrivers();
            $serviceDriver->services_id = $service->services_id;
            $serviceDriver->drivers_id = $service->drivers_id;
            $serviceDriver->date = $today;
            $serviceDriver->time = $time;
            $serviceDriver->status = 'En ruta';
            $serviceDriver->save(); 

            \DB::commit();

            return response()->json([
                'message'=>'Inicia ruta'
            ]);

        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine()]);
        }
    } 

    /**
     * Guarda servicio
     */
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

    public function totalUnsignedByClient($id){
        $services = Services::where('clients_id', $id)->where('assigned', false)->get();

        return response()->json([
            'services'=>count($services)
        ]);
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
    
}
