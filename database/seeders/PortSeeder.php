<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;
use App\Models\Country;

class PortSeeder extends Seeder
{
    public function run(): void
    {

        $ports = [

            [
                'country'=>'France',
                'port_name'=>'Port of Le Havre',
                'city'=>'Le Havre',
                'latitude'=>49.4938,
                'longitude'=>0.1077,
                'type'=>'Seaport',
                'status'=>'Normal'
            ],

            [
                'country'=>'France',
                'port_name'=>'Port of Marseille',
                'city'=>'Marseille',
                'latitude'=>43.2965,
                'longitude'=>5.3698,
                'type'=>'Seaport',
                'status'=>'Busy'
            ],


            [
                'country'=>'Indonesia',
                'port_name'=>'Port of Tanjung Priok',
                'city'=>'Jakarta',
                'latitude'=>-6.104,
                'longitude'=>106.885,
                'type'=>'Container Port',
                'status'=>'Busy'
            ],


            [
                'country'=>'Singapore',
                'port_name'=>'Port of Singapore',
                'city'=>'Singapore',
                'latitude'=>1.264,
                'longitude'=>103.84,
                'type'=>'Container Port',
                'status'=>'Normal'
            ],


            [
                'country'=>'United States',
                'port_name'=>'Port of Los Angeles',
                'city'=>'Los Angeles',
                'latitude'=>33.736,
                'longitude'=>-118.262,
                'type'=>'Container Port',
                'status'=>'Busy'
            ],


            [
                'country'=>'China',
                'port_name'=>'Port of Shanghai',
                'city'=>'Shanghai',
                'latitude'=>31.230,
                'longitude'=>121.490,
                'type'=>'Container Port',
                'status'=>'Busy'
            ],

        ];


        foreach($ports as $item){


            $country = Country::where(
                'country_name',
                $item['country']
            )->first();


            if($country){


                Port::create([

                    'country_id'=>$country->id,

                    'port_name'=>$item['port_name'],

                    'city'=>$item['city'],

                    'latitude'=>$item['latitude'],

                    'longitude'=>$item['longitude'],

                    'type'=>$item['type'],

                    'status'=>$item['status']

                ]);


            }


        }


    }
}