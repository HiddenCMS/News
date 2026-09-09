<?php

if (PHP_SAPI !== 'cli' || empty($argv[1]))
{
	fwrite(STDERR, "Usage: php tests/privacy-contract.php <hiddencms-root>\n");
	exit(1);
}

$root = realpath($argv[1]);
if (!$root || !is_file($root.DIRECTORY_SEPARATOR.'index.php'))
{
	fwrite(STDERR, "Invalid HiddenCMS root.\n");
	exit(1);
}

chdir($root);
define('HIDDENCMS_CLI', TRUE);
$_SERVER['REQUEST_METHOD'] = 'GET';
require 'index.php';

$assert = function($condition, $message){
	if (!$condition) throw new RuntimeException($message);
	echo 'PASS '.$message.PHP_EOL;
};

$module = HB()->module('news');
$db = HB()->db;
$row = $db->select('id as user_id')->from('user')->order_by('id')->row(FALSE);
if (!$row) throw new RuntimeException('No user is available for the privacy contract test.');

$db->begin_transaction();
try
{
	$category = $db->select('category_id')->from('news_categories')->order_by('category_id')->row(FALSE);
	if (!$category) throw new RuntimeException('No news category is available for the privacy contract test.');

	$user = HB()->module('user')->model2('user', (int)$row['user_id']);
	$news_id = $db->insert_checked('news', [
		'category_id' => (int)$category['category_id'],
		'user_id'     => (int)$user->id,
		'image_id'    => NULL,
		'date'        => date('Y-m-d H:i:s'),
		'published'   => TRUE,
		'views'       => 0,
		'vote'        => FALSE
	]);
	$db->insert_checked('news_lang', [
		'news_id'      => $news_id,
		'lang'         => HB()->config->lang->info()->name,
		'title'        => 'Privacy contract test',
		'slug'         => 'privacy-contract-test-'.$news_id,
		'introduction' => 'Temporary introduction',
		'content'      => 'Temporary content',
		'tags'         => 'privacy,test'
	]);

	$expected = $db->from('news')->where('user_id', (int)$user->id)->count();
	$export = $module->personal_data_export($user);
	$erase = $module->personal_data_erase($user);

	$assert(isset($export['authored_news']) && count($export['authored_news']) === $expected, 'News export contains every authored article');
	$assert(isset($erase['authored_news_preserved']) && $erase['authored_news_preserved'] === $expected, 'News erasure reports preserved editorial contributions');
	$assert(($erase['attribution'] ?? '') === 'anonymized_core_account', 'News erasure delegates identity anonymization to the core');

	$temporary = array_values(array_filter($export['authored_news'], function($item) use ($news_id){
		return (int)$item['news_id'] === (int)$news_id;
	}));
	$assert(count($temporary) === 1, 'News export contains the temporary article');
	$assert(count($temporary[0]['translations']) === 1 && $temporary[0]['translations'][0]['content'] === 'Temporary content', 'News export contains localized editorial content');

	$db->where('id', (int)$user->id)->update('user', ['username' => 'Utilisateur supprimé', 'deleted' => TRUE]);
	$listed = array_values(array_filter($module->model()->get_news(), function($item) use ($news_id){
		return (int)$item['news_id'] === (int)$news_id;
	}));
	$assert(count($listed) === 1, 'Anonymized author does not hide the published article');
	$assert(empty($listed[0]['user_id']) && $listed[0]['username'] === 'Utilisateur supprimé', 'Anonymized author is displayed without a profile link');
}
finally
{
	$db->rollback();
}
