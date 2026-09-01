<?php

namespace HiddenCMS\News\Migrations;

use HB\HiddenCMS\Addons\Migration;

class CreateNewsTables implements Migration
{
	public function up($db)
	{
		$db->execute_checked('CREATE TABLE IF NOT EXISTS `news_categories` (
			`category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`image_id` int(11) unsigned DEFAULT NULL,
			`icon_id` int(11) unsigned DEFAULT NULL,
			`name` varchar(100) NOT NULL,
			PRIMARY KEY (`category_id`),
			KEY `image_id` (`image_id`),
			KEY `icon_id` (`icon_id`),
			CONSTRAINT `news_categories_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `file` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
			CONSTRAINT `news_categories_ibfk_2` FOREIGN KEY (`icon_id`) REFERENCES `file` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

		$db->execute_checked('CREATE TABLE IF NOT EXISTS `news_categories_lang` (
			`category_id` int(11) unsigned NOT NULL,
			`lang` varchar(5) NOT NULL,
			`title` varchar(100) NOT NULL,
			PRIMARY KEY (`category_id`, `lang`),
			KEY `lang` (`lang`),
			CONSTRAINT `news_categories_lang_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

		$db->execute_checked('CREATE TABLE IF NOT EXISTS `news` (
			`news_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`category_id` int(11) unsigned NOT NULL,
			`user_id` int(11) unsigned NOT NULL,
			`image_id` int(11) unsigned DEFAULT NULL,
			`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`published` enum("0", "1") NOT NULL DEFAULT "0",
			`views` int(11) unsigned NOT NULL DEFAULT "0",
			`vote` enum("0", "1") NOT NULL DEFAULT "0",
			PRIMARY KEY (`news_id`),
			KEY `category_id` (`category_id`),
			KEY `user_id` (`user_id`),
			KEY `image_id` (`image_id`),
			CONSTRAINT `news_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
			CONSTRAINT `news_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `file` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
			CONSTRAINT `news_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

		$db->execute_checked('CREATE TABLE IF NOT EXISTS `news_lang` (
			`news_id` int(11) unsigned NOT NULL,
			`lang` varchar(5) NOT NULL,
			`title` varchar(100) NOT NULL,
			`slug` varchar(150) NOT NULL,
			`introduction` text NOT NULL,
			`content` text NOT NULL,
			`tags` text NOT NULL,
			PRIMARY KEY (`news_id`, `lang`),
			KEY `lang` (`lang`),
			KEY `slug` (`slug`),
			CONSTRAINT `news_lang_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
	}

	public function down($db)
	{
		$db->where('widget', 'news')->delete('widgets');
		$db->where('module', 'news')->delete('pages_instances');
		$db->where('url', 'news')->delete('menus_items');
		$db->where('name', 'news_per_page')->delete('settings');
		$db->execute_checked('DROP TABLE IF EXISTS `news_lang`');
		$db->execute_checked('DROP TABLE IF EXISTS `news`');
		$db->execute_checked('DROP TABLE IF EXISTS `news_categories_lang`');
		$db->execute_checked('DROP TABLE IF EXISTS `news_categories`');
	}
}
