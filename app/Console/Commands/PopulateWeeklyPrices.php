<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\PopulateItemsWeeklyPrices;
use App\Models\Item;
use App\Models\ItemsWeeklyPrice;

class PopulateWeeklyPrices extends Command
{
    protected $signature = 'populate:weekly-prices';
    protected $description = 'Popula a tabela items_weekly_prices com dados da última semana';

    public function handle(): void
    {
        $countEndpoints = 0;
        $city = 'Caerleon,Bridgewatch,Thetford,Lymhurst,Martlock,Fort Sterling,Black Market,Brecilien';
        $cityArray = explode(',', $city);
        $endDate = date('j-n-Y');
        $startDate = date('j-n-Y', strtotime('-7 days'));

        $totalItems = Item::count();
        $processedItems = 0;
        $bar = $this->output->createProgressBar($totalItems);
        $bar->start();

        Item::chunk(100, function ($items) use ($startDate, $endDate, $city, &$processedItems, $bar, &$countEndpoints) {
            $dataInsert = [];
            $itemsExternalIdConcate = implode(',', $items->pluck('external_id')->toArray());
            $url = "https://west.albion-online-data.com/api/v2/stats/history/{$itemsExternalIdConcate}?date={$startDate}&end_date={$endDate}&locations=" . urlencode($city) . "&time-scale=24";
            $json = @file_get_contents($url);

            // Check for HTTP 429 Too Many Requests
            if ($http_response_header && strpos($http_response_header[0], '429') !== false) {
                $countEndpoints = 0;
                $this->warn("Recebeu HTTP 429 Too Many Requests! Esperando 1 minuto...");
                sleep(60); // espera 1 minuto
                // Tenta novamente após esperar
                $json = @file_get_contents($url);
                if ($http_response_header && strpos($http_response_header[0], '429') !== false) {
                    $this->warn("Ainda recebendo HTTP 429. Esperando 5 minutos...");
                    sleep(300); // espera 5 minutos
                    $json = @file_get_contents($url);
                    if ($http_response_header && strpos($http_response_header[0], '429') !== false) {
                        $this->error("Ainda recebendo HTTP 429 após esperar 5 minutos. Pulando este chunk.");
                        throw new \Exception("HTTP 429 Too Many Requests");
                    }
                }
            }
            $data = json_decode($json, true);
            $countEndpoints++;
            foreach ($data as $entry) {
                if (!isset($dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']])) {
                        $dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']] = [
                            'item_id'    => null,
                            'date' => null,
                            'city'       => null,
                            'item_count' => null,
                            'avg_price'  => null,
                        ];

                        $count = 0;
                        foreach ($entry["data"] as $dataEntry) {
                            if (!isset($dataEntry['item_count']) || !isset($dataEntry['avg_price'])) {
                                continue;
                            }
                            $dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['item_id'] = $entry['item_id'];
                            $dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['date'] = $startDate;
                            $dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['city'] = $entry['location'];
                            $dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['item_count'] = ($dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['item_count'] ?? 0) + ($dataEntry['item_count'] ?? 0);
                            $dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['avg_price'] = ($dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['avg_price'] ?? 0) + ($dataEntry['avg_price'] ?? 0);
                            $dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['quality'] = $entry['quality'];
                            $count++;
                        }
                        
                        // Verificar se $count é maior que 0 antes de dividir para evitar divisão por zero
                        $dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['item_count'] = round($dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['item_count'] / $count);
                        $dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['avg_price'] = round($dataInsert[$entry['item_id'] . $entry['location'] . $entry['quality']]['avg_price'] / $count, 2);
                }
            }
            foreach ($dataInsert as $data) {
                $item = Item::where('external_id', $data['item_id'])->first();
                // conver data['date']  j-n-Y to Y-m-d $table->date('query_date');
                if (!$item) {
                    continue;
                }
                $data['date'] = date('Y-m-d', strtotime($data['date']));
                ItemsWeeklyPrice::updateOrCreate(
                    [
                        'item_id'    => $item->id,
                        'city'       => $data['city'],
                        'quality'    => $data['quality'],
                    ],
                    [
                        'query_date' => $data['date'],
                        'item_count' => $data['item_count'],
                        'price'  => $data['avg_price'],
                        'quality'    => $data['quality'],
                    ]
                );
            }
            $processedItems += $items->count();
            $bar->advance($items->count());
            sleep(3);
            if ($countEndpoints >= 170) {
                $this->warn("Atingiu 170 endpoints. Esperando 5 minutos para evitar HTTP 429...");
                sleep(300);
                $countEndpoints = 0;
            }
        });
        $bar->finish();
        $this->info("\nProcessamento concluído!");
    }
}
