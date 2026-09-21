<?php
/**
 * https://neofr.ag
 * @author: Michaël BILCOT <michael.bilcot@neofr.ag>
 */

namespace HB\Widgets\News;

use HB\HiddenCMS\Addons\Widget;

class News extends Widget
{
	protected function __info()
	{
		return [
			'title'       => $this->lang('News'),
			'icon'        => 'fas fa-newspaper',
			'description' => '',
			'link'        => 'https://neofr.ag',
			'author'      => 'Michaël BILCOT & Jérémy VALENTIN <contact@HiddenCMS.com>',
			'license'     => 'LGPLv3 <https://neofr.ag/license>',
			'version'     => '0.2.1',
			'depends'     => [
				'HiddenCMS' => 'Alpha 0.2'
			],
			'types'       => [
				'index'      => $this->lang('Latest news'),
				'categories' => $this->lang('Categories'),
				'tags'       => $this->lang('Tags')
			]
		];
	}
}


