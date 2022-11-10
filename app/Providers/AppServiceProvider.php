<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $locations = \App\Location::latest('id', 'user_id', 'desc', 'value', 'created_at')/*->paginate(8)*/->get();
        view()->share('locations', $locations);

        $services = \App\Service::latest('id', 'user_id', 'desc', 'value', 'created_at')/*->paginate(8)*/->get();
        view()->share('services', $services);

        $locationsNServices = array();

        //FAZER UNIFICAÇÃO DOS DADOS
        foreach ($locations as $location){
            $location->type='location';

            //Recuperação de fotos da locação
            $location->pics = \App\Location_pic::where('location_id', $location->id)->get();
            
            array_push($locationsNServices, $location);
            
        }
        
        foreach ($services as $service){
            $service->type='service';

            //Recuperação de fotos do serviço
            $service->pics = \App\Service_pic::where('service_id', $service->id)->get();

            array_push($locationsNServices, $service);
        }

        usort($locationsNServices,
            function($a, $b){ 
                if ($a->created_at == $b->created_at) {
                    return 0;
                }
                return ($a->created_at < $b->created_at) ? -1 : 1;
            }
        );

        view()->share('locationsNServices', $locationsNServices);
    }
}
