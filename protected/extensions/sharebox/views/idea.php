<?php
echo CHtml::openTag('div', $this->htmlOptions);
foreach ($this->shareDefinitions as $name => $def) {

	$url = strtr($def['url'], array(
		'{title}' => htmlentities(urlencode($this->title)),
		'{message}' => $this->message,
		'{url}' => htmlentities(urlencode($this->url)),
		'{imgUrl}' => $this->imgUrl,

	));

	$link = CHtml::link('', $url, array(
		'rel' => 'nofollow',
		'target' => '_blank',
		'title' => $def['title'],
		'class' => $classDefinition[$name],
	));
	echo $link.' ';
}
echo CHtml::closeTag('div');