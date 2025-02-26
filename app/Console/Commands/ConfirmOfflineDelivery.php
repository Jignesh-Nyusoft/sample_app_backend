<?php

namespace App\Console\Commands;

use App\Helpers\StripeHelper;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ConfirmOfflineDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:confirm-offline-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
          
        $start = Carbon::now()->subDays(2)->startOfDay(); 
        $end = Carbon::now()->subDays(2)->endOfDay();     

        $orders = Order::where([
            'is_offline_delivery' => 1,
            'mark_as_delivered__seller' => 1,
            'mark_as_delivered_customer' => 0,
        ])
        ->whereBetween('created_at', [$start, $end]) 
        ->get();
       
         
        if(isset($orders)){

            foreach ($orders as $order) {

             Order::where('id',$order->id)->update([
              'mark_as_delivered_customer' => 1,
              'delivery_status'   => 'delivered',
            ]);                
                
           $seller = User::find($order->seller_id);

           if(isset($seller) && $seller->Stripe_connect_ac_id != null){
            $totalAmount = $order->net_amount - $order->seller_tax_amount;
            StripeHelper::payConnectedAccount(
              $seller->Stripe_connect_ac_id,
                $totalAmount,
                $seller->first_name
            );
           }


            }

        }

    return true;
    }
}
