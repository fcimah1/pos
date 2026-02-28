<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	public function run(): void
	{
		$this->call([
			RolesSeeder::class,
			BranchesSeeder::class,
			UsersSeeder::class,
			CategoriesSeeder::class,
			ProductsSeeder::class,
			DeliveryPersonsSeeder::class,
		]);
	}
}
