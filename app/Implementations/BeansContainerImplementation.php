<?php

namespace App\Implementations;

use App\Interfaces\BeansContainer;
use App\Interfaces\ContainerFullException;

class BeansContainerImplementation implements BeansContainer
{

  /**
   * The amount of beans stored in the container
   * @var float
   */
  protected $beansAmount;

  /**
   * The amount of spoons
   * @var integer
   */
  protected $numSpoons = 0;

  /**
   * Set the properties of the machine
   * @param  integer $beansAmount   The capacity of the beans container
   * @return void
   */
  public function __construct($beansAmount = 50)
  {
    $this->beansAmount = $beansAmount;
  }

  public function addBeans($numSpoons) : void
  {
    $spaceForBeans = $this->beansAmount - $this->numSpoons;

    if ($numSpoons > $spaceForBeans) {
      throw new ContainerFullException("Trying to add {$numSpoons} spoons to {$spaceForBeans} spoons space. Not enough capacity.");
    } else {
      $this->numSpoons += $numSpoons;
    }
  }

  /**
   * Get $numSpoons from the container
   *
   * @throws ContainerFullException
   * @param integer $numSpoons number of spoons of beans
   * @return integer
   */
  public function useBeans($numSpoons): int
  {
    // Check there's enough beans in the container
    if ($numSpoons > $this->numSpoons) {
      throw new ContainerFullException("Not enough beans in the container");
      // Get all the beans from the container
      $availableSpoons = $this->numSpoons;
      $this->numSpoons = 0;
      return $availableSpoons;
    }

    // Reduce the beans by the amount requested
    $this->numSpoons -= $numSpoons;
    return $numSpoons;
  }

  /**
   * Returns the number of spoons of beans left in the container
   *
   * @return integer
   */
  public function getBeans(): int
  {
    return $this->numSpoons;
  }

}
