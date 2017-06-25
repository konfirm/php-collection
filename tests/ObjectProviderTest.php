<?php

use Konfirm\Collection\Provider;
use Konfirm\Collection\Comparable;

/**
 *  Test Provider class
 */
class ObjectProviderTest extends PHPUnit\Framework\TestCase {
	public function testProviderIterable() {
		$same = (object) ['hello' => 'world'];
		$other = (object) ['foo' => 'bar'];
		$provider = new Provider($same, $same, $other);

		$item = $provider->rewind();
		$this->assertEquals($same, $item);
		$this->assertEquals(0, $provider->key());
		$this->assertEquals($same, $provider->current());

		$item = $provider->next();
		$this->assertEquals($same, $item);
		$this->assertEquals(1, $provider->key());
		$this->assertEquals($same, $provider->current());

		$item = $provider->next();
		$this->assertEquals($other, $item);
		$this->assertEquals(2, $provider->key());
		$this->assertEquals($other, $provider->current());

		$item = $provider->next();
		$this->assertEquals(null, $item);
		$this->assertEquals(null, $provider->key());
		$this->assertEquals(null, $provider->current());

		unset($provider);
		unset($same);
		unset($other);
		unset($item);
	}

	public function testProviderIntersect() {
		$same = (object) ['hello' => 'world'];
		$other = (object) ['foo' => 'bar'];
		$providerA = new Provider($same, $same, $other);
		$providerB = new Provider($same);
		$providerC = $providerA->intersect($providerB);

		$item = $providerC->rewind();
		$this->assertEquals($same, $item);
		$this->assertEquals(0, $providerC->key());
		$this->assertEquals($same, $providerC->current());

		$item = $providerC->next();
		$this->assertEquals($same, $item);
		$this->assertEquals(1, $providerC->key());
		$this->assertEquals($same, $providerC->current());

		$item = $providerC->next();
		$this->assertEquals(null, $item);
		$this->assertEquals(null, $providerC->key());
		$this->assertEquals(null, $providerC->current());

		//  intersection the other way around produce the same result
		$providerC = $providerB->intersect($providerA);
		$item = $providerC->rewind();
		$this->assertEquals($same, $item);
		$this->assertEquals(0, $providerC->key());
		$this->assertEquals($same, $providerC->current());

		$item = $providerC->next();
		$this->assertEquals($same, $item);
		$this->assertEquals(1, $providerC->key());
		$this->assertEquals($same, $providerC->current());

		$item = $providerC->next();
		$this->assertEquals(null, $item);
		$this->assertEquals(null, $providerC->key());
		$this->assertEquals(null, $providerC->current());

		unset($providerA);
		unset($providerB);
		unset($providerC);
	}

	public function testProviderUnique() {
		$same = (object) ['hello' => 'world'];
		$other = (object) ['foo' => 'bar'];
		$provider = new Provider($same, $same, $same, $same, $other, $same, $other);
		$unique = $provider->unique();

		$this->assertEquals(7, count($provider));
		$this->assertEquals(2, count($unique));

		$item = $unique->rewind();
		$this->assertEquals($same, $item);
		$this->assertEquals(0, $unique->key());
		$this->assertEquals($same, $unique->current());

		$item = $unique->next();
		$this->assertEquals($other, $item);
		$this->assertEquals(1, $unique->key());
		$this->assertEquals($other, $unique->current());

		$item = $unique->next();
		$this->assertEquals(null, $item);
		$this->assertEquals(null, $unique->key());
		$this->assertEquals(null, $unique->current());

		unset($same);
		unset($other);
		unset($provider);
		unset($unique);
		unset($item);
	}
}
