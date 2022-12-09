<?php

namespace App\Implementations;

use App\Interfaces\ContainerException;
use App\Interfaces\ContainerFullException;
use App\Interfaces\WaterContainer;

class WaterContainerImplementation implements WaterContainer
{
  /**
   * Store the water in litres
   * @var float
   */
  protected $waterVolume;

  /**
   * Litres in the container
   * @var float
   */
  protected $litres = 0;

  /**
   * Define the properties
   * @param  float   $waterVolume The capacity of the container in litres
   * @return void
   */
  public function __construct($waterVolume = 2)
  {

    $this->waterVolume = $waterVolume;
  }

  /**
   * Add more water to the water container
   *
   * @param float $litres
   * @throws ContainerFullException
   *
   * @return void
   */
  public function addWater($litres): void
  {
    if ($litres > ($this->waterVolume - $this->litres)) {
      throw new ContainerFullException("Not enough volume left in the water container");
    }

    $this->litres = (float)$this->litres + (float)$litres;

  }

  /**
   * Use $litres from the container
   *
   * @throws EspressoMachineContainerException
   * @param float $litres
   * @return integer
   */
  public function useWater($litres): float
  {
    // Check there's enough water in the container
    if ($litres > (float) $this->litres) {
      throw new ContainerException("Not enough water in the container");
    }

    // Reduce the water by the amount requested
    $this->litres = (float) $this->litres - (float)$litres;
    return $this->litres;
  }

  /**
   * Returns the volume of water left in the container
   *
   * @return float number of litres
   */
  public function getWater(): float
  {
    return $this->litres;
  }
}
