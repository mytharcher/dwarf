<?php
$cfg = array(
	'vars' => array(
		'version' => '0.3.1',
		'version.date' => '2011-12-27',
		'version.file.url' => 'http://elfjs.googlecode.com/files/',
		'images.logo.src' => '/assets/img/logo.png'
	),
	'group' => array()
);

$cfg['group']['/wrap/page.tpl'] = array(
	'/targets/index.html',
	'/wrap/content.tpl'
);

$cfg['group']['/wrap/content.tpl'] = array(
	'/targets/downloads/*.html',
	'/wrap/doc.tpl'
);

$cfg['group']['/wrap/doc.tpl'] = array(
	'/targets/docs/*.html',
	'/targets/docs/*.text',
	'/wrap/doc-advance.tpl',
	'/wrap/doc-support.tpl',
	'/wrap/doc-develop.tpl'
);

$cfg['group']['/wrap/doc-advance.tpl'] = array(
	'/targets/docs/advance/*.text',
	'/targets/docs/advance/*.html'
);

$cfg['group']['/wrap/doc-support.tpl'] = array(
	'/targets/docs/support/*.text',
	'/targets/docs/support/*.html'
);

$cfg['group']['/wrap/doc-develop.tpl'] = array(
	'/targets/docs/develop/*.text',
	'/targets/docs/develop/*.html'
);
?>