<?php

namespace App\Cart;

use App\Traits\HasKey;
use Illuminate\Support\Str;

class Traveller implements Representational
{
    use HasKey;

    private static $counts = [
        TravellerType::ADULTS => 0,
        TravellerType::CHILDREN => 0,
        TravellerType::INFANTS => 0,
    ];

    private $id;
    protected $type = TravellerType::ADULTS; // Default - adults
    protected $reference_key;

    protected $first_name;
    protected $last_name;
    protected $middle_name;
    protected $email;

    protected $birthdate;
    protected $gender;

    protected $passport_start_date;
    protected $passport_end_date;
    protected $nationality;
    protected $mobile_number;

    protected $house_number;
    protected $street_number;
    protected $area;
    protected $city;
    protected $country;
    protected $pincode;

    public $planItems = [];

    public const REQUIRED_FIELDS = [
        'first_name',
        'last_name',
        'email',
        'birthdate',
        'gender',
        'passport_start_date',
        'passport_end_date',
        'nationality',
        'mobile_number',
        'city',
        'country',
    ];

    public function __construct($type = TravellerType::ADULTS)
    {
        $this->id = Str::random();
        $this->type = $type;

        self::$counts[$type]++;
        $this->reference_key = strtoupper($type[0]).self::$counts[$type];
    }

    public function setAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function referenceKey()
    {
//        if ($this->first_name && $this->last_name) {
//            return $this->first_name[0].$this->last_name;
//        }

        return $this->reference_key;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function toShort()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'reference_key' => $this->reference_key,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'reference_key' => $this->reference_key,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'passport_start_date' => $this->passport_start_date,
            'passport_end_date' => $this->passport_end_date,
            'nationality' => $this->nationality,
            'mobile_number' => $this->mobile_number,
            'house_number' => $this->house_number,
            'street_number' => $this->street_number,
            'area' => $this->area,
            'city' => $this->city,
            'country' => $this->country,
            'pincode' => $this->pincode,
            'planItems' => $this->planItems,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toDatabase()
    {
        // TODO: Implement toDatabase() method.
    }
}
