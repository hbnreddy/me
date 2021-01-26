<?php

namespace App\Console\Commands;

use App\Eloquent\Customer;
use App\Eloquent\Order;
use App\Eloquent\OrderItem;
use App\Eloquent\OrderItemCustomer;
use App\Eloquent\OrderItemProduct;
use App\Eloquent\OrderItemProductCustomer;
use Illuminate\Console\Command;

class ClearOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all orders in database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Order::query()->delete();
        Customer::query()->delete();
        OrderItem::query()->delete();
        OrderItemProduct::query()->delete();
        OrderItemProductCustomer::query()->delete();
        OrderItemCustomer::query()->delete();
    }
}
