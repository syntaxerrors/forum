<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('Forum_board_typesTableSeeder');
		$this->call('Forum_category_typesTableSeeder');
		$this->call('Forum_post_typesTableSeeder');
		$this->call('Forum_reply_typesTableSeeder');
		$this->call('Forum_support_statusTableSeeder');
	}

}