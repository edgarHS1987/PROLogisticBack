<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Clients;
use App\Models\ClientsUsers;
use App\Models\ZipCodes;
use App\Models\Zones;
use App\Models\ZonesZipCodes;
use App\Models\ZonesDrivers;
use App\Models\Driver;
use App\Models\DriverAddress;
use App\Models\DriversSchedule;

class ZonesController extends Controller
{

    public function assignDriver(Request $request){  
        try{
            \DB::beginTransaction();

            //obtener id de cliente        
            $user = $request->user()->id;
            $clients = ClientsUsers::where('users_id', $user)->select('clients_id')->get();
            $arrayClients = array();
            foreach($clients as $client){
                array_push($arrayClients, $client->clients_id);
            }

            //verifica si existen zonas condiguradas
            $zones = Zones::join('clients', 'clients.id', 'zones.clients_id')
                        ->whereIn('zones.clients_id', $arrayClients)
                        ->get();
            
            if(count($zones) == 0){
                return response()->json([
                    'error'=>'No se han configurado las zonas'
                ]);
            }

            //obtener zip_code de driver
            $today = date('Y-m-d');
            $datetime = new \DateTime("now", new \DateTimeZone('America/Mexico_City'));
            $time = $datetime->format('H:i');

            if($time > '12:00'){
                $operator = '>';
            }else{
                $operator = '>=';
            }
            $drivers = Driver::join('drivers_address', 'drivers_address.drivers_id', 'drivers.id')
                            ->join('drivers_schedule', 'drivers_schedule.drivers_id', 'drivers.id')
                            ->whereIn('drivers.id', $request)
                            ->where('date', $operator, $today)
                            ->select('drivers.id', 'drivers_address.zip_code', 'drivers_schedule.date')
                            ->get();

            foreach($drivers as $driver){
                $zone = ZipCodes::join('zones_zip_codes', 'zones_zip_codes.zip_codes_id', 'zip_codes.id')
                                ->where('zip_codes.zip_code', $driver->zip_code)
                                ->select('zones_zip_codes.zones_id')
                                ->first();

                $zoneDriver = new ZonesDrivers();
                $zoneDriver->zones_id = $zone->zones_id;
                $zoneDriver->drivers_id = $driver->id;
                $zoneDriver->date = $driver->date;
                $zoneDriver->save();

            }

            \DB::commit();

            return response()->json([
                'message'=>'Los conductores se asignaron correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine()]);
        }
        
    }

    public function byClient($id){
        $zones = Zones::select('id', 'name')->where('clients_id', $id)->where('isLast', true)->get();

        $zones->each(function($z){
            $z['zip_codes'] = ZipCodes::join('zones_zip_codes', 'zones_zip_codes.zip_codes_id', 'zip_codes.id')
                                    ->join('zones', 'zones.id', 'zones_zip_codes.zones_id')
                                    ->join('colonies', 'colonies.zip_codes_id', 'zip_codes.id')
                                    ->select('zip_codes.id', 'zip_codes.zip_code', 'colonies.name as colony')
                                    ->where('zones_zip_codes.zones_id', $z->id)
                                    ->get();
        });

        return response()->json($zones);
    }

    public function configuring(Request $request){
        try{
            \DB::beginTransaction();

            $zones = Zones::where('clients_id', $request->clients_id)->where('isLast', true)->get();
            $zones->each(function($z){
                $z->isLast = false;
                $z->save();
            });

            $zip_codes = ZipCodes::join('municipalities', 'municipalities.id', 'zip_codes.municipalities_id')
                            ->where('municipalities.name', 'San Luis Potosí')
                            ->orWhere('municipalities.name', 'SSoledad de Graciano Sánchez')
                            ->select('zip_codes.id', 'zip_codes.zip_code')
                            ->get();
            
            $totalZone = round(count($zip_codes) / $request->numberZones);
            
            $countZone = 0;
            for($i = 0; $i < $request->numberZones; $i++){
                $zone = new Zones();
                $zone->clients_id = $request->clients_id;
                $zone->name = 'Zona '.($i + 1);
                $zone->isLast = true;
                $zone->save();

                for($j = 0; $j < $totalZone; $j++){
                    if($countZone <= count($zip_codes) - 1){
                        $zoneZipCode = new ZonesZipCodes();
                        $zoneZipCode->zones_id = $zone->id;
                        $zoneZipCode->zip_codes_id = $zip_codes[$countZone]['id'];
                        $zoneZipCode->save();                        
                    }

                    $countZone++;
                }
            }

            \DB::commit();
            
            return response()->json([
                'message'=>'Se realizo la configuración de zonas correctamente'
            ]);

        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine()]);
        }
    }

    /**
     * Obtiene el numero de driver que no estan asignados a una zona
     */
    public function unsignedDriver(){
        $today = date('Y-m-d');
        $datetime = new \DateTime("now", new \DateTimeZone('America/Mexico_City'));
        $time = $datetime->format('H:i');

        if($time > '12:00'){
            $operator = '>';
        }else{
            $operator = '>=';
        }

        $drivers = DriversSchedule::select('drivers_id')
                        ->where('date', $operator, $today)
                        ->groupBy('drivers_id')
                        ->get();

        $array = Array();       
                  
        foreach($drivers as $driver){
            $driversZone = ZonesDrivers::where('drivers_id', $driver->drivers_id)
                                ->where('date', $operator, $today)
                                ->get();

            if(count($driversZone) === 0){
                array_push($array, $driver->drivers_id);
            }
        }

        return response()->json($array);
    }

    public function verifyIfExist(Request $request){
        $zones = Zones::where('clients_id', $request->clients_id)->where('isLast', true)->get();

        if(count($zones) > 0){
            return response()->json([
                'error'=>'Ya existen zonas configuradas para el cliente seleccionado, ¿Desea realizar una nueva configuración?'
            ]);
        }

        return response()->json([
            'ok'=>true
        ]);
    }
}
