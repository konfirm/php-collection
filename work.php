<?php

require('vendor/autoload.php');

use Konfirm\Collection\Provider;
use Konfirm\Collection\Comparable;

class Item implements Comparable {
	protected $foo;
	protected $bar;

	public function __construct($foo, $bar=null) {
		$this->foo = $foo;
		$this->bar = $bar;
	}

	public function getComparison(): string {
		return json_encode([$this->foo, $this->bar]);
	}
}

$specialA = new Item('one', 'two');
$specialB = (object) ['hello' => 'world'];
$specialC = (object) ['hello' => 'world'];

$providerA = new Provider(
	new Item('one'),
	$specialA,
	new Item('two', 'three'),
	new Item('two', 'four'),
	$specialB,
	new Item('three', 'four'),
	$specialC
);

$providerB = new Provider(
	new Item('foo'),
	new Item('bar'),
	new Item('one'),
	new Item('two', 'four'),
	$specialA,
	$specialA,
	$specialB,
	$specialC
);

$providerC = $providerA->intersect($providerB);
var_dump($providerC);
var_dump($providerC->unique());

