<?php
/**
 * https://neofr.ag
 * @author: Michaël BILCOT <michael.bilcot@neofr.ag>
 */

$rules = [
	'title' => [
		'label'         => $this->lang('Title'),
		'value'         => $this->form()->value('title'),
		'type'          => 'text',
		'rules'			=> 'required'
	],
	'image' => [
		'label'       => $this->lang('Image'),
		'value'       => $this->form()->value('image'),
		'upload'      => 'news/categories',
		'type'        => 'file',
		'info'        => $this->lang(' image (max. %d MB)', file_upload_max_size() / 1024 / 1024),
		'check'       => function($filename, $ext){
			if (!in_array($ext, ['gif', 'jpeg', 'jpg', 'png']))
			{
				return $this->lang('Please choose an image file');
			}
		}
	],
	'icon' => [
		'label'       => $this->lang('Icon'),
		'value'       => $this->form()->value('icon'),
		'upload'      => 'news/categories',
		'type'        => 'file',
		'info'        => $this->lang(' image (square, min. %dpx and max. %d MB)', 16, file_upload_max_size() / 1024 / 1024),
		'check'       => function($filename, $ext){
			if (!in_array($ext, ['gif', 'jpeg', 'jpg', 'png']))
			{
				return $this->lang('Please choose an image file');
			}

			list($w, $h) = getimagesize($filename);

			if ($w != $h)
			{
				return $this->lang('The icon must be square');
			}
			else if ($w < 16)
			{
				return $this->lang('The icon must be at least %dpx', 16);
			}
		}
	]
];
