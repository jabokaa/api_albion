<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\ItemsDayPrice;

class PopulateDayPrices extends Command
{
    protected $signature = 'populate:day-prices';
    protected $description = 'Popula a tabela items_day_prices a partir da API Albion Online Data';

    public function handle(): void
    {
        $countEndpoints = 0;
        $city = 'Caerleon,Bridgewatch,Thetford,Lymhurst,Martlock,Fort Sterling,Black Market,Brecilien';

        $totalItems = Item::count();
        $processedItems = 0;
        $bar = $this->output->createProgressBar($totalItems);
        $bar->start();

        Item::chunk(100, function ($items) use ($city, &$processedItems, $bar, &$countEndpoints) {
            $dataInsert = [];
            $itemsExternalIdConcate = implode(',', $items->pluck('external_id')->toArray());
            $url = "https://west.albion-online-data.com/api/v2/stats/prices/{$itemsExternalIdConcate}?locations=" . urlencode($city);
            $json = @file_get_contents($url);

            // Check for HTTP 429 Too Many Requests
            if (
                ($http_response_header && strpos($http_response_header[0], '429') !== false) ||
                empty($json)
            ) {
                $countEndpoints = 0;
                $this->warn("Recebeu HTTP 429 Too Many Requests ou resposta vazia! Esperando 1 minuto...");
                sleep(60); // espera 1 minuto
                // Tenta novamente após esperar
                $json = @file_get_contents($url);
                if (
                    ($http_response_header && strpos($http_response_header[0], '429') !== false) ||
                    empty($json)
                ) {
                    $this->warn("Ainda recebendo HTTP 429 ou resposta vazia. Esperando 5 minutos...");
                    sleep(300); // espera 5 minutos
                    $json = @file_get_contents($url);
                    if (
                        ($http_response_header && strpos($http_response_header[0], '429') !== false) ||
                        empty($json)
                    ) {
                        $this->error("Ainda recebendo HTTP 429 ou resposta vazia após esperar 5 minutos. Pulando este chunk.");
                        throw new \Exception("HTTP 429 Too Many Requests ou resposta vazia");
                    }
                }
            }
            $data = json_decode($json, true);
            $countEndpoints++;
            foreach ($data as $entry) {
                if($entry['sell_price_min'] <= 0){
                    continue;
                }
                if (!isset($dataInsert[$entry['item_id'] . $entry['city'] . $entry['quality']])) {
                        $dataInsert[$entry['item_id'] . $entry['city'] . $entry['quality']] = [
                            'item_id'    => null,
                            'date' => null,
                            'city'       => null,
                            'item_count' => null,
                            'price'  => null,
                        ];

                        // converte 0001-01-01T00:00:00 em Y-m-d de $entry['sell_price_min_date'];
                        $dataInsert[$entry['item_id'] . $entry['city'] . $entry['quality']]['date'] = date('Y-m-d', strtotime($entry['sell_price_min_date']));
                        $dataInsert[$entry['item_id'] . $entry['city'] . $entry['quality']]['item_id'] = $entry['item_id'];
                        $dataInsert[$entry['item_id'] . $entry['city'] . $entry['quality']]['city'] = $entry['city'];
                        $dataInsert[$entry['item_id'] . $entry['city'] . $entry['quality']]['price'] = $entry['sell_price_min'];
                        $dataInsert[$entry['item_id'] . $entry['city'] . $entry['quality']]['quality'] = $entry['quality'];
                }
            }
            foreach ($dataInsert as $data) {
                $item = Item::where('external_id', $data['item_id'])->first();
                // conver data['date']  j-n-Y to Y-m-d $table->date('query_date');
                if (!$item) {
                    continue;
                }
                $data['date'] = date('Y-m-d', strtotime($data['date']));
                ItemsDayPrice::updateOrCreate(
                    [
                        'item_id'    => $item->id,
                        'city'       => $data['city'],
                        'quality'    => $data['quality'],
                    ],
                    [
                        'query_date' => $data['date'],
                        'item_count' => $data['item_count'],
                        'price'  => $data['price'],
                        'quality'    => $data['quality'],
                    ]
                );
            }
            $processedItems += $items->count();
            $bar->advance($items->count());
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
