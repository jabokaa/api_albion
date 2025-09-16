<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class FetchWikiData extends Command
{
    protected $signature = 'fetch:wiki-data';
    protected $description = 'Busca e extrai dados de uma página HTML';

    public function handle()
    {
        $response = Http::get('https://api.scraperapi.com', [
            'api_key' => 'b8e3580a16366e3df92b0b44b817b4a0',
            'url' => 'https://wiki.albiononline.com/wiki/Basic_Fish_Sauce'
        ]);
        $html = $response->body();

        dd($html);

        $crawler = new Crawler($html);

        // Exemplo: extrair o título da página
        $title = $crawler->filter('h1')->text();

        // Exemplo: extrair o primeiro parágrafo
        $paragraph = $crawler->filter('p')->first()->text();

        $this->info("Título: $title");
        $this->info("Parágrafo: $paragraph");
    }
}
