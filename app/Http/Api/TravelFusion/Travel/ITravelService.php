<?php

namespace App\Http\Api\TravelFusion\Travel;

interface ITravelService
{
    /**
     * @param array $parametersSets
     * @param array $commonParameters
     * @return mixed
     */
    function groups($parametersSets = [], $commonParameters = []);
}
