<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\PopulateItemsFromJson;
use App\Models\Item;

class PopulateItems extends Command
{
    protected $signature = 'populate:items';
    protected $description = 'Popula a tabela items a partir de um arquivo JSON';

    public function handle()
    {
        $jsonPath = 'https://raw.githubusercontent.com/ao-data/ao-bin-dumps/refs/heads/master/formatted/items.json';

        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);
        $total = count($data);
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        foreach ($data as $item) {
            Item::updateOrCreate(
                [
                    'external_id' => $item['UniqueName'] ?? null,
                ],
                [
                    'index' => $item['Index'] ?? '-',
                    'name_en' => $item['LocalizedNames']['EN-US'] ?? '-',
                    'name_sp' => $item['LocalizedNames']['ES-ES'] ?? '-',
                    'name_pt' => $item['LocalizedNames']['PT-BR'] ?? '-',
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->info("\nTabela items populada com JSON!");
    }
}
