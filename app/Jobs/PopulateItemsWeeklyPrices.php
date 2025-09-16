<?php

namespace App\Jobs;

use App\Models\Item;
use App\Models\ItemsWeeklyPrice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PopulateItemsWeeklyPrices implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $city = 'Caerleon,Bridgewatch,Thetford,Lymhurst,Martlock,Fort Sterling,Black Market,Brecilien';
        $endDate = date('j-n-Y');
        $startDate = date('j-n-Y', strtotime('-7 days'));

        $totalItems = Item::count();
        $bar = app('console')->output()->createProgressBar($totalItems);
        $bar->start();

        Item::chunk(100, function ($items) use ($startDate, $endDate, $city, $bar) {
            foreach ($items as $item) {
                $url = "https://west.albion-online-data.com/api/v2/stats/history/{$item->external_id}?date={$startDate}&end_date={$endDate}&locations=" . urlencode($city) . "&time-scale=24";
                $json = file_get_contents($url);
                $data = json_decode($json, true);
                foreach ($data as $entry) {
                    ItemsWeeklyPrice::create([
                        'item_id'    => $entry['item_id'] ?? null,
                        'value'      => $entry['data'][0]['price'] ?? null,
                        'query_date' => $entry['data'][0]['timestamp'] ?? null,
                        'city'       => $city,
                        'item_count' => $entry['data'][0]['item_count'] ?? null,
                        'price'  => $entry['data'][0]['price'] ?? null,
                    ]);
                }
                $bar->advance();
            }
        });
        $bar->finish();
    }
}
