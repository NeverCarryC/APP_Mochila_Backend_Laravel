<?php

// database/seeders/TemplateBackpackSeeder.php

namespace Database\Seeders;

use App\Models\TemplateBackpack;
use App\Models\TemplateItem;
use App\Models\TripCategory;
use Illuminate\Database\Seeder;

class TemplateBackpackSeeder extends Seeder
{
    
    public function run()
    {
        $calor = TemplateBackpack::create([
            'name' => 'Calor',
        ]);

        $tripCategory = TripCategory::create([
            'name' => $calor->name,
            'description' => 'Categoría creada desde el template ' . $calor->name,
            'user_id' => 1,
        ]);


        $calor->items()->createMany([
            ['name' => 'Protector solar', 'quantity' => 1],
            ['name' => 'Gorra', 'quantity' => 1],
            ['name' => 'Gafas de sol', 'quantity' => 1],
        ]);

        $frio = TemplateBackpack::create([
            'name' => 'Frío',
        ]);

        $tripCategory = TripCategory::create([
            'name' => $frio->name,
            'description' => 'Categoría creada desde el template ' . $frio->name,
            'user_id' => 1,
        ]);

        $frio->items()->createMany([
            ['name' => 'Ropa termica', 'quantity' => 1],
            ['name' => 'Sudadera', 'quantity' => 2],
            ['name' => 'Guantes', 'quantity' => 1],
        ]);
    }
}
