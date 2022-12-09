<?php

namespace App\Http\Controllers;

use App\Implementations\BeansContainerImplementation;
use App\Implementations\EspressoMachineImplementation;
use App\Implementations\WaterContainerImplementation;
use Illuminate\Http\Request;
use App\Interfaces\EspressoMachineInterface;


class EspressoController extends Controller
{

    public $espresso;
    /**
     * Initialize all espresso and it's containers
     */
    public function __construct(EspressoMachineImplementation $espresso)
    {
        $this->espresso = $espresso;
        $this->beansContainer = config('espresso.beans_container');
        $this->waterContainer = config('espresso.water_container');

        $this->espresso->setBeansContainer(new BeansContainerImplementation($this->beansContainer));
        $this->espresso->setWaterContainer(new WaterContainerImplementation($this->waterContainer));

        $this->espresso->addBeans(config('espresso.beans_container'));
        $this->espresso->addWater(config('espresso.water_container'));
    }

    /**
     * Returns the status of the "Cave" machine
     *
     * This endpoint allows you to return some informatino about the "Cave" Machine
     *
     * <aside class="notice">Not using an actual database for this excercise</aside>
     */
    public function index(): object
    {
        return response()->json([
            'status' => $this->espresso->getStatus(),
        ], 200);
    }


    /**
     * Order1 espresso
     *
     * This endpoint will place an order for 1 espresso
     *
     * @response {
     *  "status": "10 Espressos Left",
     *  "remaining_water": "1 litres",
     *  "remaining_beans": "50 beans"
     * }
     */
    public function orderEspresso()
    {

        $this->espresso->makeEspresso();


        return response()->json([
            'data' => [
                'status' => $this->espresso->getStatus(),
                'remaining_water' => "{$this->espresso->getWater()} litres",
                'remaining_beans' => "{$this->espresso->getBeans()} beans"
            ]
        ], 200);
    }


    /**
     * Order more than 1 espresso
     *
     * This endpoint will place an order for x amount
     *
     * @bodyParam quantity int required The quantity needed to order. Example: 3
     *
     * @response {
     *  "status": "10 Espressos Left",
     *  "remaining_water": "1 litres",
     *  "remaining_beans": "50 beans"
     * }
     */
    public function orderEspressos(Request $request)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer'
        ]);

        $this->espresso->makeEspressos( (int) $request->input('quantity') );

        return response()->json([
            'data' => [
                'status' => $this->espresso->getStatus(),
                'remaining_water' => "{$this->espresso->getWater()} litres",
                'remaining_beans' => "{$this->espresso->getBeans()} beans"
            ]
        ], 200);
    }

}
