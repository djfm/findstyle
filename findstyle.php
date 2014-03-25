#!/usr/bin/php
<?php

ini_set('display_errors', 'on');

$dir = isset($argv[1]) ? $argv[1] : '.';

$exp = '/\bstyle\s*=\s*(["\'])(?:(?!\1).)*\1/';

$found = 0;
$files = array();

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $path => $info)
{
	if (preg_match('/\.tpl\.php$/', $path))
		continue;

	if (!preg_match('/\.(php|tpl)$/', $path))
		continue;

	$contents = file_get_contents($path);

	$m = array();
	$n = preg_match_all($exp, $contents, $m, PREG_OFFSET_CAPTURE);

	for ($i = 0; $i < $n; $i++)
	{
		$at = $m[0][$i][1];

		$match = $m[0][$i][0];
		$line = 1 + preg_match_all('/\n/', substr($contents, 0, $at));

		if (preg_match('/\b(?:left|right)\b/', $match))
		{
			echo "Match in file $path at line $line: $match\n";
			$found += 1;
			$files[$path] = true;
		}
	}
}

echo "\n\nFound $found matches in ".count($files)." distinct files.\n"; 