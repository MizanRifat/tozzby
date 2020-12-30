<?php

use Illuminate\Database\Seeder;
use Webkul\Velocity\Database\Seeders\VelocityMetaDataSeeder;
use Webkul\Admin\Database\Seeders\DatabaseSeeder as BagistoDatabaseSeeder;
use Webkul\Velocity\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BagistoDatabaseSeeder::class);
        $this->call(VelocityMetaDataSeeder::class);
        
    }
}
