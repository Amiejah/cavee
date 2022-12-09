<?php

namespace App\Implementations;

use App\Interfaces\BeansContainer;
use App\Interfaces\ContainerException;
use App\Interfaces\EspressoMachineException;
use App\Interfaces\EspressoMachineInterface;
use App\Interfaces\NoBeansException;
use App\Interfaces\NoWaterException;
use App\Interfaces\WaterContainer;

class EspressoMachineImplementation implements EspressoMachineInterface
{
    /**
     * The container to hold the beans
     * @var BeansContainer
     */
    protected $beansContainer;

    /**
     * The container to hold the water
     * @var WaterContainer
     */
    protected $waterContainer;

    /**
     *
     * @var boolean
     */
    protected $waterSupplyIsMains;

    /**
     * The number of litres used to make espresson since the machine was last descaled
     * @var integer
     */
    protected $litresSinceLastDescale = 0;


    /**
     * Set the properties of the machine
     * @param  boolean $waterSupplyIsMains   Is the machine connected to mains water?
     * @return void
     */
    public function __construct($waterSupplyIsMains = false)
    {
        $this->waterSupplyIsMains = $waterSupplyIsMains;
    }

    public function addBeans($numspoons)
    {
        if ( ! $this->hasBeansContainer() ) {
            throw new EspressoMachineException("No container");
        }
        $this->beansContainer->addBeans($numspoons);
    }

    /**
     * Get $numSpoons from the container
     *
     * @throws EspressoMachineException
     * @param integer $numSpoons number of spoons of beans
     * @return integer
     */
    public function useBeans($numSpoons)
    {
        if (!$this->hasBeansContainer()) {
            throw new EspressoMachineException("The machine hasn't got a bean container");
        }
        $this->beansContainer->useBeans($numSpoons);
    }


    /**
     * Returns the number of spoons of beans left in the container
     *
     * @return integer
     */
    public function getBeans()
    {
        return $this->beansContainer->getBeans();
    }


    /**
     * Adds water to the coffee machine's water tank
     *
     * @param float $litres
     * @throws ContainerException
     *
     * @return void
     */
    public function addWater($litres)
    {
        if ($this->waterSupplyIsMains) {
            throw new ContainerException("Water cannot be added as the machine is on mains supply");
        }

        $this->waterContainer->addWater($litres);
    }


    /**
     * Use $litres from the container
     *
     * @throws ContainerException
     * @param float $litres
     * @return integer
     */
    public function useWater($litres)
    {
        return $this->waterContainer->useWater($litres);
    }

    /**
     * Returns the volume of water left in the container
     *
     * @return float number of litres
     */
    public function getWater()
    {

        if ( ! $this->hasWaterContainer()) {
            throw new ContainerException("The machine hasn't got a water container");
        }

        return $this->waterContainer->getWater();
    }

    /**
     * Runs the process to descale the machine
     * so the machine can be used make coffee
     * uses 1 litre of water
     *
     * @throws NoWaterException
     *
     * @return void
     */
    public function descale()
    {
        try {
            $this->useWater(config('espresso.litres_used_per_descale'));
            $this->litresSinceLastDescale = 0;
        } catch (ContainerException $e) {
            throw new NoWaterException("Not enough water to descale. {$this->getWater()} litres remaining.");
        }
    }


    public function makeEspressos( $quantity ): float
    {
        // Check the machine doesn't need a descale
        if ($this->litresSinceLastDescale > config('espresso.litres_per_descale')) {
            throw new EspressoMachineException("The machine needs descaling. {$this->litresSinceLastDescale} litres since last descale.");
        }
        try {
            $this->useBeans(config('espresso.beans_per_espresso') * $quantity);
        } catch (ContainerException $e) {
            throw new NoBeansException("Not enough beans to make an espresso. {$this->getBeans()} spoons remaining.");
        }

        try {
            $this->useWater((float) config('espresso.litres_used_per_espresso') * $quantity);
        } catch (NoBeansException $e) {
            throw new NoWaterException("Not enough water to make an espresso. {$this->getWater()} litres remaining.");
        }


        $litres_of_coffee_made = config('espresso.litres_used_per_espresso') * $quantity;
        $this->litresSinceLastDescale = (float) $this->litresSinceLastDescale - (float) $litres_of_coffee_made;

    return $litres_of_coffee_made;
    }

    /**
     * Runs the process for making Espresso
     *
     * @throws NoBeansException, NoWaterException
     *
     * @return float of litres of coffee made
     */
    public function makeEspresso(): float
    {
        return $this->makeEspressos(1);
    }

    /**
     * @see makeEspresso
     * @throws NoBeansException, NoWaterException
     *
     * @return float of litres of coffee made
     */
    public function makeDoubleEspresso(): float
    {
        return $this->makeEspressos(2);
    }

    /**
     * This method controls what is displayed on the screen of the machine
     * Returns ONE of the following human readable statuses in the following preference order:
     *
     * Descale needed
     * Add beans and water
     * Add beans
     * Add water
     * {Integer} Espressos left
     *
     * @return string
     */
    public function getStatus(): string
    {
        if ($this->litresSinceLastDescale >= config('espresso.litres_per_descale')) {
            if (!$this->waterSupplyIsMains && $this->getWater() < config('espresso.litres_used_per_descale')) {
                return "Add water";
            }
            return "Descale needed";
        }

        if ($this->getBeans() < config('espresso.beans_per_espresso') && $this->getWater() < config('espresso.litres_used_per_espresso') ) {
            return "Add beans and water";
        }

        if ($this->getBeans() < config('espresso.beans_per_espresso') ) {
            return "Add beans";
        }

        if (!$this->waterSupplyIsMains && $this->getWater() < config('espresso.litres_used_per_espresso') ) {
            return "Add water";
        }

        $espressos_worth_of_beans = intval(bcdiv($this->getBeans(), config('espresso.beans_per_espresso')));
        $espressos_left = $espressos_worth_of_beans;

        $espressos_worth_of_water = intval(bcdiv($this->getWater(), config('espresso.litres_used_per_espresso')));
        $espressos_left = min($espressos_worth_of_beans, $espressos_worth_of_water);

        return "$espressos_left Espressos Left";
    }
	/**
	 * @param BeansContainer $container
	 */
	public function setBeansContainer(BeansContainer $container)
	{
		$this->beansContainer = $container;
	}

    /**
     * @return BeansContainer
     */
    public function getBeansContainer()
    {
        return $this->beansContainer;
    }

    /**
     * @param WaterContainer $container
     */
    public function setWaterContainer(WaterContainer $container)
    {
        $this->waterContainer = $container;
    }

    /**
     * Says if the machine has a water container attached
     * @return boolean
     */
    public function hasWaterContainer()
    {
        return $this->waterContainer instanceof WaterContainer;
    }

    /**
     * @return WaterContainer
     */
    public function getWaterContainer()
    {
        return $this->waterContainer;
    }

    /**
     * Says if the machine has a beans container attached
     * @return boolean
     */
    public function hasBeansContainer()
    {
        return $this->beansContainer instanceof BeansContainer;
    }
}
