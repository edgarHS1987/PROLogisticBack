<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Clients;
use App\Models\Warehouses;
use App\Models\Services;

class ServicesController extends Controller
{

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
        $services = Services::where('clients_id', $id)->get();

        return response()->json(count($services));
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
