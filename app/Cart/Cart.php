<?php

namespace App\Cart;

use Illuminate\Support\Facades\Session;

class Cart
{
    private static $key = 'CART';
    private static $tempKey = 'TEMP_CART';

    public static function init($temp = false)
    {
        $key = $temp ? static::$tempKey : static::$key;

        if (Session::has($key)) {
            return Session::get($key);
        }

        Session::put($key, static::model());

        return self::get($temp);
    }

    public static function exists($temp = false)
    {
        return !!self::get($temp);
    }

    public static function get($temp = false)
    {
        $key = $temp ? static::$tempKey : static::$key;

        return Session::get($key);
    }

    public static function hasCurrentPlan()
    {
        $cart = static::get();

        return isset($cart['current_plan_id']) && $cart['current_plan_id'] !== false;
    }

    public static function countPlans()
    {
        $cart = self::get();

        if (! $cart) {
            return 0;
        }

        if (! isset($cart['plans'])) {
            $cart = self::get();

            $cart = array_merge($cart, [
                'plans' => [
                    //
                ],
                'current_plan_id' => false,
            ]);

            self::update($cart);
        }

        return count($cart['plans']);
    }

    public static function planExists(Plan $plan)
    {
        return false;
    }

    public static function addPlan(Plan $plan)
    {
        if (self::planExists($plan)) {
            return false;
        }

        $cart = static::get();

        if (! isset($cart)) {
            self::init();

            $cart = static::get();
        }

        $cart['plans'][] = $plan;

        $plan->setName('Plan '.count($cart['plans']));


        if (! self::hasCurrentPlan()) {
            $cart['current_plan_id'] = $plan->getKey();
        }

        return static::update($cart);
    }

    public static function getPlan($planId)
    {
        $cart = static::get();


        if (!$cart || !isset($cart['plans'])) {
            return false;
        }

        $plans = $cart['plans'];

        foreach ($plans as $plan) {
            if ($plan->getKey() === $planId) {
                return $plan;
            }
        }

        return false;
    }

    public static function updatePlan(Plan $newPlan)
    {
        $cart = static::get();

        if (! $cart) {
            return false;
        }

        $found = false;
        $plans = $cart['plans'];

        foreach ($plans as $index => $plan) {
            if ($plan->getKey() === $newPlan->getKey()) {
                $found = true;
                $plans[$index] = $newPlan;
            }
        }

        $cart['plans'] = $plans;

        return $found && static::update($cart);
    }

    public static function removePlan($planId)
    {
        $cart = static::get();

        if (! $cart) {
            return false;
        }

        $found = false;
        $plans = $cart['plans'];
        foreach ($plans as $index => $plan) {
            if ($plan->getKey() === $planId) {
                $found = true;
                unset($plans[$index]);
            }
        }

        $cart['plans'] = $plans;
        $cart['current_plan_id'] = false;

        return $found && static::update($cart);
    }

    public static function update($value)
    {
        Session::put(static::$key, $value);

        return true;
    }

    public static function currentPlan()
    {
        $cart = Cart::get();

        if (!$cart || !isset($cart['current_plan_id'])) {
            return false;
        }

        return Cart::getPlan($cart['current_plan_id']);
    }

    public static function destroy($temp = false)
    {
        $key = $temp ? static::$tempKey : static::$key;

        Session::forget($key);

        return true;
    }

    public static function toDatabase()
    {
        //
    }

    public static function model()
    {
        return [
            'plans' => [
                //
            ],
            'current_plan_id' => false,
        ];
    }
}
