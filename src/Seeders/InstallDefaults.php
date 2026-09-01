<?php

namespace HiddenCMS\News\Seeders;

use HB\HiddenCMS\Addons\Seeder;

class InstallDefaults implements Seeder
{
	public function run($db)
	{
		if (!$db->from('settings')->where('name', 'news_per_page')->count())
		{
			$db->insert_checked('settings', [
				'name'  => 'news_per_page',
				'site'  => '',
				'lang'  => '',
				'value' => '5',
				'type'  => 'int'
			]);
		}

		if (!$db->from('news_categories')->count())
		{
			$category_id = $db->insert_checked('news_categories', [
				'image_id' => NULL,
				'icon_id'  => NULL,
				'name'     => 'general'
			]);

			$db->insert_checked('news_categories_lang', [
				'category_id' => $category_id,
				'lang'        => 'fr',
				'title'       => 'Général'
			]);
		}

		$menu_id = $db->select('menu_id')->from('menus')->where('name', 'sidebar')->row();

		if ($menu_id && !$db->from('menus_items')->where('menu_id', $menu_id)->where('url', 'news')->count())
		{
			$position = (int)$db->select('MAX(position)')->from('menus_items')->where('menu_id', $menu_id)->row();
			$db->insert_checked('menus_items', [
				'menu_id'   => $menu_id,
				'parent_id' => NULL,
				'title'     => 'Actualités',
				'url'       => 'news',
				'target'    => '_parent',
				'position'  => $position + 1,
				'enabled'   => TRUE
			]);
		}
	}
}
