<?php
error_reporting(-1);
ini_set('display_errors', 1);
include('vkpub.class.php');

$vkpub = new Vkpub();
$vkpub->setAccessToken($config['VK_ACCESS_TOKEN']);
$vkpub->setSecret($config['VK_SECRET']);

if (isset($_POST['run'])) {
	if (isset($_POST['group']) && !empty($_POST['group'])) {
		$vkpub->setGroup($_POST['group']);
	}
	$vkpub->getGroupInfo();
	$output = $vkpub->PrintReport();
} else {
	$output = file_get_contents('assets/templates/run.tpl');
}

$layout = file_get_contents('assets/templates/layout.tpl');
$layout = str_replace('{$output}', $output, $layout);

echo $layout;