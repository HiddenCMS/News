<?php
/**
 * https://neofr.ag
 * @author: Michaël BILCOT <michael.bilcot@neofr.ag>
 */

namespace HB\Modules\News;

use HB\HiddenCMS\Addons\Module;

class News extends Module
{
	protected function __info()
	{
		return [
			'title'       => $this->lang('Actualités'),
			'description' => '',
			'icon'        => 'far fa-file-alt',
			'link'        => 'https://neofr.ag',
			'author'      => 'Michaël BILCOT & Jérémy VALENTIN <contact@HiddenCMS.com>',
			'license'     => 'LGPLv3 <https://neofr.ag/license>',
			'admin'       => TRUE,
			'front'       => TRUE,
			'page_blocks' => TRUE,
			'version'     => '1.0',
			'reserved_route' => 'news',
			'depends'     => [
				'HiddenCMS' => 'Alpha 0.2'
			],
			'routes'      => [
				//Index
				'{page}'                                   => 'index',
				'{url_title}/{url_title}'                  => '_news',
				'tag/{url_title}{pages}'                   => '_tag',
				'{url_title}{pages}'                       => '_category',

				//Admin
				'admin{pages}'                             => 'index',
				'admin/{id}/{url_title}'                   => '_edit',
				'admin/categories/add'                     => '_categories_add',
				'admin/categories/{id}/{url_title}'        => '_categories_edit',
				'admin/categories/delete/{id}/{url_title}' => '_categories_delete'
			],
			'settings'    => function(){
				return $this->form2()
							->rule($this->form_number('news_per_page')
										->title('Actualités par page')
										->value($this->config->news_per_page)
							)
							->success(function($data){
								$this->config('news_per_page', $data['news_per_page']);
								notify('Configuration modifiée');
								refresh();
							});
			}
		];
	}

	public function permissions()
	{
		return [
			'default' => [
				'access'  => [
					[
						'title'  => 'Actualités',
						'icon'   => 'far fa-file-alt',
						'access' => [
							'add_news' => [
								'title' => 'Ajouter',
								'icon'  => 'fas fa-plus',
								'admin' => TRUE
							],
							'modify_news' => [
								'title' => 'Modifier',
								'icon'  => 'fas fa-edit',
								'admin' => TRUE
							],
							'delete_news' => [
								'title' => 'Supprimer',
								'icon'  => 'far fa-trash-alt',
								'admin' => TRUE
							]
						]
					],
					[
						'title'  => 'Catégories',
						'icon'   => 'fas fa-align-left',
						'access' => [
							'add_news_category' => [
								'title' => 'Ajouter une catégorie',
								'icon'  => 'fas fa-plus',
								'admin' => TRUE
							],
							'modify_news_category' => [
								'title' => 'Modifier une catégorie',
								'icon'  => 'fas fa-edit',
								'admin' => TRUE
							],
							'delete_news_category' => [
								'title' => 'Supprimer une catégorie',
								'icon'  => 'far fa-trash-alt',
								'admin' => TRUE
							]
						]
					]
				]
			]
		];
	}

	public function page_blocks()
	{
		$blocks = [
			'index' => [
				'title'  => (string)$this->lang('Toutes les actualités'),
				'icon'   => 'fas fa-stream'
			]
		];

		foreach ($this->page_block_categories() as $category_id => $title)
		{
			$blocks['category:'.$category_id] = [
				'title' => $title,
				'icon'  => 'far fa-folder-open'
			];
		}

		foreach ($blocks as &$block)
		{
			$block['displays'] = [
				'cards' => [
					'title' => (string)$this->lang('Cartes sur 3 colonnes'),
					'icon'  => 'fas fa-th-large'
				],
				'list' => [
					'title' => (string)$this->lang('Liste verticale'),
					'icon'  => 'fas fa-list'
				]
			];
			$block['fields'] = [
				'limit' => [
					'label'   => (string)$this->lang('Nombre d\'actualités visibles'),
					'type'    => 'number',
					'default' => 6,
					'min'     => 1,
					'max'     => 24,
					'step'    => 1
				]
			];
		}
		unset($block);

		return $blocks;
	}

	public function page_block($block = 'index', $settings = [])
	{
		$category_id = strpos($block, 'category:') === 0 ? (int)substr($block, 9) : (!empty($settings['category_id']) ? (int)$settings['category_id'] : 0);
		$display = in_array(isset($settings['display']) ? $settings['display'] : '', ['cards', 'list']) ? $settings['display'] : 'cards';
		$limit = min(24, max(1, (int)(isset($settings['limit']) ? $settings['limit'] : 6)));

		if ($category_id)
		{
			$category = $this->db	->select('category_id', 'name')
									->from('news_categories')
									->where('category_id', $category_id)
									->row();

			if ($category)
			{
				return [
					'route'    => $category['name'],
					'settings' => [
						'block'       => 'category:'.$category['category_id'],
						'category_id' => $category['category_id'],
						'display'     => $display,
						'limit'       => $limit
					]
				];
			}
		}

		return [
			'route'    => '',
			'settings' => [
				'block'   => 'index',
				'display' => $display,
				'limit'   => $limit
			]
		];
	}

	public function page_block_form_value($block)
	{
		$settings = !empty($block['settings']) && is_array($block['settings']) ? $block['settings'] : [];
		$type = !empty($settings['block']) ? $settings['block'] : (!empty($settings['category_id']) ? 'category:'.$settings['category_id'] : 'index');

		if ($type == 'category' && !empty($settings['category_id']))
		{
			$type = 'category:'.$settings['category_id'];
		}

		return [
			'type'     => 'module',
			'module'   => $this->info()->name,
			'block'    => $type,
			'settings' => [
				'category_id' => isset($settings['category_id']) ? $settings['category_id'] : '',
				'display'     => isset($settings['display']) ? $settings['display'] : 'cards',
				'limit'       => isset($settings['limit']) ? $settings['limit'] : 6
			]
		];
	}

	public function page_block_content($block = 'index', $settings = [])
	{
		return $this->controller('index')->page_block($block, $settings);
	}

	private function page_block_categories()
	{
		$categories = [];

		foreach ($this->db	->select('c.category_id', 'cl.title')
							->from('news_categories c')
							->join('news_categories_lang cl', 'c.category_id = cl.category_id')
							->where('cl.lang', $this->config->lang->info()->name)
							->order_by('cl.title')
							->get() as $category)
		{
			$categories[$category['category_id']] = utf8_html_entity_decode((string)$category['title'], ENT_QUOTES);
		}

		return $categories;
	}

	public function comments($news_id)
	{
		$news = $this->db	->select('nl.title', 'nl.slug', 'c.name as category_name')
							->from('news n')
							->join('news_lang nl', 'n.news_id = nl.news_id')
							->join('news_categories c', 'n.category_id = c.category_id')
							->where('n.news_id', $news_id)
							->where('nl.lang', $this->config->lang->info()->name)
							->row();

		if ($news)
		{
			return [
				'title' => $news['title'],
				'url'   => $this->news_path($news['category_name'], $news['title'], $news['slug'])
			];
		}
	}

	public function news_path($category_name, $title, $slug = '')
	{
		return $this->base_path().trim($category_name, '/').'/'.($slug ?: url_title($title));
	}

	public function category_path($category_name)
	{
		return $this->base_path().trim($category_name, '/');
	}

	public function tag_path($tag)
	{
		return $this->base_path().'tag/'.url_title($tag);
	}

	public function index_path()
	{
		return rtrim($this->base_path(), '/');
	}

	private function base_path()
	{
		if (!$this->url->admin && !$this->url->ajax && !empty($this->info()->reserved_route))
		{
			return trim($this->info()->reserved_route, '/').'/';
		}

		if (!$this->url->admin && !$this->url->ajax && ($page = $this->output->data->get('page', 'name')))
		{
			return trim($page, '/').'/';
		}

		return $this->info()->name.'/';
	}
}


