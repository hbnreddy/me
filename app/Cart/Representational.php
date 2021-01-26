<?php


namespace App\Cart;


interface Representational
{
    /**
     * Returns short array representation of the item.
     */
    public function toShort();

    /**
     * Returns array representation of the item.
     */
    public function toArray();

    /**
     * Returns database representation of the item.
     */
    public function toDatabase();
}
