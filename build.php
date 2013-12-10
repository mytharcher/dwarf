<?php
if ( !defined('ABSPATH') ) {
	define('ABSPATH', dirname(__FILE__) . '/');
}

require_once('filters/php-markdown/markdown.php');



class LogBuffer {
	static private $instance = null;
	
	private $buffer = array();
	
	private function getInstance() {
		if (self::$instance == null) {
			self::$instance = new LogBuffer();
		}
		return self::$instance;
	}
	
	private function __construct() {}
	private function __clone() {}
	
	static public function push($content) {
		array_push(self::getInstance()->buffer, $content);
	}
	
	static public function output() {
		echo join("\n", self::getInstance()->buffer);
	}
	
	static public function clean() {
		self::getInstance()->buffer = array();
	}
	
	static public function clean_out() {
		self::output();
		self::clean();
	}
}

function parse_param() {
	global $argv;
	return isset($argv) ? parse_cli_param() : parse_http_param();
}

function parse_http_param() {
	$param = array();
	foreach ($_GET as $key => $value) {
		$param[$key] = $value;
	}
	return $param;
}

function parse_cli_param() {
	global $argv;
	$param = array();
	for ($i = 1, $len = count($argv); $i < $len; $i += 2) {
		$key = $argv[$i];
		if (strpos($key, '-') === 0) {
			$key = substr($key, 1);
		}
		$param[$key] = $argv[$i + 1];
	}
	
	return $param;
}

function parse_param_path(&$options, $keys) {
	global $argv;

	foreach ($keys as $key) {
		if (isset($argv)) {
			$options[$key] = dirname(__FILE__) . '/' . $options[$key];
		} else {
			$options[$key] = $_SERVER['DOCUMENT_ROOT'] . '/' . $options[$key];
		}
	}
}

/**
 * 获取某个文件夹下的所有文件列表，以“.”开头的除外
 * @param {String} $path 要计算的文件(列表/文件夹)路径，可以是文件路径，文件夹路径，或者文件路径数组
 * @param {String} $baseDir 基于查询的目录
 * @param {Boolean} $deep 是否递归获取深层的文件
 */
function get_dir_list($path = './', $baseDir = './', $deep = false) {
	$list = array();
	
	if (is_array($path)) {
		foreach ($path as $item) {
			$list = array_merge($list, get_dir_list($item, $baseDir, $deep));
		}
	} else {
		$curPath = $baseDir . $path;
		if (is_dir($curPath)) {
			$dir = opendir($curPath);
			$fileList = array();
			while (($file = readdir($dir)) !== false) {
				if (($pos = strpos($file, '.')) === false || $pos > 0) {
					array_push($fileList, $file);
				}
			}
			closedir($dir);
			
			sort($fileList, SORT_STRING);
			
			foreach($fileList as $file) {
				$curFile = $curPath . $file;
				if (is_dir($curFile)) {
					$list = array_merge($list, get_dir_list("$file/", $curPath, $deep));
				} else {
					array_push($list, $curFile);
				}
			}
		} else {
			$list[] = $curPath;
		}
	}
	
	return array_unique($list);
}

function dir_dfs($path, $func, $filter = null) {
	$cur = $path;
	if (file_exists($cur) && (!$filter || $filter($cur))) {
		if ($func($cur) === false) {
			return false;
		}
		if (is_dir($cur)) {
			$dir = opendir($cur);
			$fileList = array();
			while (($f = readdir($dir)) !== false) {
				if ($f != '.' && $f != '..') {
					array_push($fileList, $f);
				}
			}
			closedir($dir);
			
			sort($fileList, SORT_STRING);
			
			foreach($fileList as $file) {
				if (dir_dfs("$cur/$file", $func, $filter) === false) {
					break;
				}
			}
		}
	}
}

function get_targets_list ($base) {
	$ret = array();
	
	dir_dfs($base, function ($file) use(&$ret) {
		$ret[] = $file;
	});
	
	return $ret;
}

function parse_includes (&$content, $base) {
	if ($content) {
		while (preg_match("/<!--\s*include\(\s*([_A-Za-z0-9\.\-\/]+)\s*\)\s*-->/", $content, $matcher)) {
			$content = str_replace($matcher[0], parse_includes(file_get_contents($base . $matcher[1]), $base), $content);
		}
	}
	return $content;
}

function wrap_content ($content, $wrap_file) {
	
}

function find_wrap ($file, $base) {
	global $cfg;
	$ret = array();
	foreach ($cfg['group'] as $wrap => $group) {
		foreach ($group as $pattern) {
			foreach (glob($base . $pattern) as $f) {
				if ($f == $file) {
					array_push($ret, $wrap);
					$outer = find_wrap($base . $wrap, $base);
					if (count($outer)) {
						$ret = array_merge($ret, $outer);
					}
				}
			}
		}
	}
	return $ret;
}

function parse_wrap (&$content, $file, $base) {
	$wraps = find_wrap($file, $base);
	foreach ($wraps as $wrap) {
		$buffer = array();
		$lines = split("[\r\n]+", $content);
		$current = '';
		foreach ($lines as $line) {
			if ($line) {
				if (preg_match("/<!--\s*block\s*\(\s*(\w+)\s*\)\s*-->/", $line, $matcher)) {
					$current = $matcher[1];
					$buffer[$current] = array();
				} else if (isset($buffer[$current])){
					array_push($buffer[$current], $line);
				}
			}
		}
		
		if ($wrapContent = file_get_contents($base . $wrap)) {
			while (preg_match("/<!--\s*placeholder\s*\(\s*(\w+)\s*\)\s*-->/", $wrapContent, $matcher)) {
				$replacer = isset($buffer[$matcher[1]]) ? join("\r\n", $buffer[$matcher[1]]) : '';
				$wrapContent = str_replace($matcher[0], $replacer, $wrapContent);
			}
			$content = $wrapContent;
		}
	}
	return $content;
}

function parse_variables (&$content, $vars) {
	while (preg_match("/<!--\s*vars\s*\(([^)]+)\)\s*-->/", $content, $matcher)) {
		$varLines = split("[\r\n]+", $matcher[1]);
		foreach($varLines as $varLine) {
			if ($varLine) {
				$pair = split("=", $varLine);
				$vars[trim($pair[0])] = trim(replace_vars($pair[1], $vars));
			}
		}
		$content = str_replace($matcher[0], '', $content);
	}
	replace_vars($content, $vars);
}

function replace_vars (&$content, $vars) {
	while (preg_match("/\\$\{([\w\.]+)\}/", $content, $replacer)) {
		$content = str_replace($replacer[0], isset($vars[$replacer[1]]) ? $vars[$replacer[1]] : '', $content);
	}
	return $content;
}

function dir_handler ($file, $base, $dest) {
	LogBuffer::push("[directory] ");
	if (!file_exists($dest)) {
		mkdir($dest);
		LogBuffer::push("made a directory at '$dest'");
	} else {
		LogBuffer::push("directory '$dest' exists");
	}
	
	LogBuffer::push("<br />");
}

function copy_handler ($file, $base, $dest) {
	LogBuffer::push("[common file] ");
	if (copy($file, $dest)) {
		LogBuffer::push("copied '$file' to directory '$dest' successfully<br />");
	} else {
		LogBuffer::push("copy '$file' <strong>failed</strong>");
	}
}

function site_decorator (&$content, $targetPath, $base) {
	global $cfg;
	parse_wrap($content, $targetPath, $base);
	parse_includes($content, $base);
	parse_variables($content, $cfg['vars']);
}

function output_text_content ($dest, &$content) {
	file_put_contents($dest, preg_replace("/>\s+</", ">\n<", $content));
}

function html_handler ($file, $base, $dest) {
	LogBuffer::push("[html] ");
	$content = file_get_contents($file);
	site_decorator($content, $file, $base);
	
	output_text_content($dest, $content);
	
	LogBuffer::push("parsed the target file '$file' with including and wrapping, wrote file content to '$dest' successfully<br />");
}

function markdown_handler ($file, $base, $dest) {
	LogBuffer::push("[markdown] ");
	$content = Markdown(file_get_contents($file));
	site_decorator($content, $file, $base);
	
	$d = substr($dest, 0, -4) . 'html';
	output_text_content($d, $content);
	
	LogBuffer::push("parsed the target file '$file' as markdown syntax with including and wrapping, wrote file content to '$d' successfully<br />");
}

function build() {
	$options = parse_param();
	parse_param_path($options, array('o', 'p'));

	header('Content-type:text/html; charset=utf-8');
	//$options = parse_param();
	
	LogBuffer::push('Build start<br />');
	
	$base = $options['p'];
	$dest = $options['o'];

	require_once($base . '/config.inc');
	
	LogBuffer::push("Project path: $base<br />");
	LogBuffer::push("Build destination: $dest<br />");
	
	$handlerMap = array(
		'~' => 'dir_handler',
		'html' => 'html_handler',
		'text' => 'markdown_handler'
	);
	
	$targets = get_targets_list($base . '/targets');
	
	foreach ($targets as $file) {
		$output = str_replace($base . '/targets', $dest, $file);
		
		$type = '';
		if (is_dir($file)) {
			$type = '~';
		} else {
			if (preg_match("/\.(\w+)$/", $file, $matcher)) {
				$type = $matcher[1];
			}
		}
		
		if (isset($handlerMap[$type])) {
			$handlerMap[$type]($file, $base, $output);
		} else {
			copy_handler($file, $base, $output);
		}
	}
	
	LogBuffer::push('Build end<br />');
	
	LogBuffer::clean_out();
}

build();
?>