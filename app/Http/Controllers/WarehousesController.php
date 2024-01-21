<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Clients;
use Exception;

class WarehousesController extends Controller
{
    public function show($id){
        try {
            $warehouses = Clients::join('clients_warehouses', 'clients_warehouses.clients_id', 'clients.id')
                        ->join('warehouses', 'warehouses.id', 'clients_warehouses.warehouses_id')
                        ->select('warehouses.id as value', \DB::raw('CONCAT(warehouses.name, " - ", clients.name) as label'))
                        ->where('clients.id', $id)
                        ->get();
        } catch (\Exception $e) {
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage()]);
        }
        return response()->json($warehouses);
    }

    public function test($id){
        try {
            $warehouses = Clients::join('clients_warehouses', 'clients_warehouses.clients_id', 'clients.id')
                        ->join('warehouses', 'warehouses.id', 'clients_warehouses.warehouses_id')
                        ->selectRaw('warehouses.id as value, CONCAT(warehouses.name, " - ", clients.name) as label')
                        ->where('clients.id', $id)
                        ->get();
        } catch (\Exception $e) {
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage()]);
        }
        

        return response()->json($warehouses);
    }
}
