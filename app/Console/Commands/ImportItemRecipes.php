<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;
use App\Models\ItemRecipe;

class ImportItemRecipes extends Command
{
    /**
     * Popula os ingredientes de uma receita para um item.
     */
    private function populateRecipeIngredients($item, $ingredients, $ingredientIndex, $recipeValue, &$duplicate)
    {
        foreach ($ingredients as $ingredientName => $amount) {
            $ingredientOptions = explode('::::', $ingredientName);
            $duplicate = max($duplicate, count($ingredientOptions));
            $nameToSearch = $ingredientOptions[0];
            if (count($ingredientOptions) > 1 && isset($ingredientOptions[$ingredientIndex])) {
                $nameToSearch = $ingredientOptions[$ingredientIndex];
            }
            $parts = explode('@', $nameToSearch);
            $name = $parts[0];
            $quality = $parts[1] ?? null;
            $itemIngredient = Item::where('name_en', $name);
            if ($quality) {
                $itemIngredient = $itemIngredient->where('external_id', 'LIKE', '%@' . $quality);
            }
            $itemIngredient = $itemIngredient->first();
            if (!$itemIngredient) {
                $this->warn("Ingrediente não encontrado: $nameToSearch. Pulando...");
                continue;
            }
            ItemRecipe::updateOrCreate(
                [
                    'item_id' => $item->id,
                    'item_ingrediente_id' => $itemIngredient->id,
                    'recipe' => $recipeValue,
                ],
                [
                    'amount' => $amount,
                ]
            );
        }
    }
    protected $signature = 'import:item-recipes {jsonFile}';
    protected $description = 'Importa receitas de itens de um arquivo JSON para a tabela item_recipes';

    public function handle()
    {
        $jsonFile = $this->argument('jsonFile');
        if (!file_exists($jsonFile)) {
            $this->error("Arquivo não encontrado: $jsonFile");
            return 1;
        }

        $data = json_decode(file_get_contents($jsonFile), true);
        if (!is_array($data)) {
            $this->error('JSON inválido ou vazio.');
            return 1;
        }

        try {
            foreach ($data as $key => $ingredients) {
                //divide a  key em um array pelo @
                $parts = explode('@', $key);
                $name = $parts[0];
                $quality = $parts[1] ?? null;


                $item = Item::where('name_en', $name);
                if ($quality) {
                    $item = $item->where('external_id', 'LIKE', '%@' . $quality);
                }
                $item = $item->first();
                if (!$item) {
                    $this->warn("Item não encontrado: $name com qualidade $quality. Pulando...");
                    continue;
                }
                $duplicate = 1;
                // receita principal
                $this->populateRecipeIngredients($item, $ingredients, 0, 1, $duplicate);
                // duplicatas
                if ($duplicate > 1) {
                    $this->populateRecipeIngredients($item, $ingredients, 1, 2, $duplicate);
                }
                if ($duplicate > 2) {
                    $this->populateRecipeIngredients($item, $ingredients, 2, 3, $duplicate);
                }
                $duplicate = 1;
            }
            $this->info('Importação concluída com sucesso.');
        } catch (\Exception $e) {
            $this->error('Erro ao importar: ' . $e->getMessage());
            return 1;
        }
        return 0;
    }
}
