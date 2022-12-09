<?php

namespace App\Interfaces;


class EspressoMachineException extends \Exception {}
class NoWaterException extends EspressoMachineException {}
class NoBeansException extends EspressoMachineException { }
class ContainerException extends \Exception {}
class ContainerFullException extends ContainerException {}

interface BeansContainer
{
    /**
     * Adds beans to the container
     *
     * @param int $numSpoons number of spoons of beans
     *
     * @return void
     * @throws ContainerFullException
     *
     */
    public function addBeans(int $numSpoons): void;

    /**
     * Use $numSpoons from the container
     *
     * @param int $numSpoons number of spoons of beans
     *
     * @return int number of bean spoons used
     */
    public function useBeans(int $numSpoons): int;

    /**
     * Returns the number of spoons of beans left in the container
     *
     * @return int
     */
    public function getBeans(): int;
}

interface WaterContainer
{
    /**
     * Adds water to the coffee machine's water tank
     *
     * @param float $litres number of litres of water
     *
     * @return void
     * @throws ContainerFullException
     *
     */
    public function addWater(float $litres): void;

    /**
     * Use $litres from the container
     *
     * @param float $litres float number of litres of water
     *
     * @return float number of litres used
     */
    public function useWater(float $litres): float;

    /**
     * Returns the volume of water left in the container
     *
     * @return float number of litres remaining
     */
    public function getWater(): float;
}

/**
 * A single espresso uses 1 spoon of beans and 0.05 litres of water
 * A double espresso uses 2 spoons of beans and 0.10 litres of water
 */
interface EspressoMachineInterface
{
    /**
     * Runs the process for making Espresso
     *
     * @return float amount of litres of coffee made
     *
     * @throws NoBeansException
     * @throws NoWaterException
     */
    public function makeEspresso(): float;

    /**
     * Runs the process for making Double Espresso
     *
     * @return float of litres of coffee made
     *
     * @throws NoBeansException
     * @throws NoWaterException
     */
    public function makeDoubleEspresso(): float;

    /**
     * This method controls what is displayed on the screen of the machine
     * Returns ONE of the following human readable statuses in the following preference order:
     *
     * - Add beans and water
     * - Add beans
     * - Add water
     * - {int} Espressos left
     *
     * @return string
     */
    public function getStatus(): string;
}
