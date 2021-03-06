--TEST--
Test for tracing property assignments in user-readable function traces (>= PHP 7.2, opcache)
--SKIPIF--
<?php
require __DIR__ . '/utils.inc';
check_reqs('PHP >= 7.2; opcache');
?>
--INI--
xdebug.default_enable=1
xdebug.profiler_enable=0
xdebug.auto_trace=0
xdebug.trace_format=0
xdebug.collect_vars=1
xdebug.collect_params=4
xdebug.collect_return=0
xdebug.collect_assignments=1
--FILE--
<?php
$tf = xdebug_start_trace(sys_get_temp_dir() . '/'. uniqid('xdt', TRUE));

function test($a, $b, $c)
{
	$d = 89;
	$a += $b;
	$c /= 7;
	$b *= 9;
}

class testClass
{
	public $a;
	private $b;
	protected $c;

	function __construct( $obj )
	{
		$obj->a = 98;
		$obj->b = 4;
		$obj->b -= 8;
		$obj->b *= -0.5;
		$obj->b <<= 1;
		$obj->c = $obj->b / 32;
	}
}

test(1, 2, 3);
$a = new testClass( new StdClass );

xdebug_stop_trace();
echo file_get_contents($tf);
unlink($tf);
?>
--EXPECTF--
TRACE START [%d-%d-%d %d:%d:%d]
                           => $tf = '%s.xt' %sassignment-trace7-php72-opcache.php:2
%w%f %w%d     -> test($a = 1, $b = 2, $c = 3) %sassignment-trace7-php72-opcache.php:29
                             => $a += 2 %sassignment-trace7-php72-opcache.php:7
                             => $c /= 7 %sassignment-trace7-php72-opcache.php:8
                             => $b *= 9 %sassignment-trace7-php72-opcache.php:9
%w%f %w%d     -> testClass->__construct($obj = class stdClass {  }) %sassignment-trace7-php72-opcache.php:30
                             => $obj->a = 98 %sassignment-trace7-php72-opcache.php:20
                             => $obj->b = 4 %sassignment-trace7-php72-opcache.php:21
                             => $obj->b -= 8 %sassignment-trace7-php72-opcache.php:22
                             => $obj->b *= -0.5 %sassignment-trace7-php72-opcache.php:23
                             => $obj->b <<= 1 %sassignment-trace7-php72-opcache.php:24
                             => $obj->c = 0.125 %sassignment-trace7-php72-opcache.php:25
                           => $a = class testClass { public $a = NULL; private $b = NULL; protected $c = NULL } %sassignment-trace7-php72-opcache.php:30
%w%f %w%d     -> xdebug_stop_trace() %sassignment-trace7-php72-opcache.php:32
%w%f %w%d
TRACE END   [%d-%d-%d %d:%d:%d]
