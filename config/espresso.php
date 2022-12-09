<?php
/**
 * Environments variables for the Espresso Machine
 */
return [
  'beans_per_espresso' => env('BEANS_USED_PER_ESPRESSO', 1),
  'litres_used_per_espresso' => env('LITRES_USED_PER_ESPRESSO', 0.05),
  'litres_used_per_descale' => env('LITRES_USED_PER_DESCALE', 1),
  'litres_per_descale' => env('LITRES_PER_DESCALE', 5),
  'beans_container' => (int) env('BEANS_CONTAINER', 50),
  'water_container' => (int) env('WATER_CONTAINER', 2),
];
