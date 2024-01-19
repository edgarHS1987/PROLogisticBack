<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\UsePlatform;

class UsePlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $last = UsePlatform::select('platform', 'total')->get();
        
        \DB::table('use_maps_api')->delete();

        if(count($last) > 0){
            foreach($last as $l){
                $use_maps = UsePlatform::create([
                    'platform'=>$l->name,
                    'total'=>$l->total
                ]);
            }
        }else{            
            $use_maps = UsePlatform::create([
                'platform'=>'mapbox',
                'total'=>0
            ]);

            $use_maps = UsePlatform::create([
                'platform'=>'gmaps',
                'total'=>0
            ]);            
        }
        
        
    }
}
