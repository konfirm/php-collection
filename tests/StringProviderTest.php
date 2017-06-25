<?php

use Konfirm\Collection\Provider;

/**
 *  Test Provider class
 */
class StringProviderTest extends PHPUnit\Framework\TestCase {
	public function testProviderIterable() {
		$provider = new Provider('a', 'b', 'c', 'a');

		$item = $provider->rewind();
		$this->assertEquals('a', $item);
		$this->assertEquals(0, $provider->key());
		$this->assertEquals('a', $provider->current());

		$item = $provider->next();
		$this->assertEquals('b', $item);
		$this->assertEquals(1, $provider->key());
		$this->assertEquals('b', $provider->current());

		$item = $provider->next();
		$this->assertEquals('c', $item);
		$this->assertEquals(2, $provider->key());
		$this->assertEquals('c', $provider->current());

		$item = $provider->next();
		$this->assertEquals('a', $item);
		$this->assertEquals(3, $provider->key());
		$this->assertEquals('a', $provider->current());

		$item = $provider->next();
		$this->assertEquals(null, $item);
		$this->assertEquals(null, $provider->key());
		$this->assertEquals(null, $provider->current());

		unset($provider);
		unset($item);
	}

	public function testProviderArrayAccess() {
		$provider = new Provider();

		$this->assertEquals(0, count($provider));
		$this->assertFalse(isset($provider[0]));

		$provider[0] = 'foo';
		$this->assertEquals('foo', $provider[0]);
		$this->assertEquals(1, count($provider));
		$this->assertTrue(isset($provider[0]));
		$this->assertFalse(isset($provider[1]));

		$provider[1] = 'bar';
		$this->assertEquals('bar', $provider[1]);
		$this->assertEquals(2, count($provider));
		$this->assertTrue(isset($provider[1]));

		$provider[1] = 'baz';
		$this->assertEquals('baz', $provider[1]);
		$this->assertEquals(2, count($provider));
		$this->assertTrue(isset($provider[1]));

		unset($provider[1]);
		$this->assertEquals(null, $provider[1]);
		$this->assertEquals(1, count($provider));
		$this->assertTrue(isset($provider[0]));
		$this->assertFalse(isset($provider[1]));

		$provider[1] = 'bar';
		$this->assertEquals('bar', $provider[1]);
		$this->assertEquals(2, count($provider));
		$this->assertTrue(isset($provider[1]));


		$item = $provider->rewind();
		$this->assertEquals('foo', $item);
		$this->assertEquals(0, $provider->key());
		$this->assertEquals('foo', $provider->current());

		$item = $provider->next();
		$this->assertEquals('bar', $item);
		$this->assertEquals(1, $provider->key());
		$this->assertEquals('bar', $provider->current());

		$item = $provider->next();
		$this->assertEquals(null, $item);
		$this->assertEquals(null, $provider->key());
		$this->assertEquals(null, $provider->current());

		unset($provider);
		unset($item);
	}

	public function testProviderIntersect() {
		$providerA = new Provider('a', 'b', 'c');
		$providerB = new Provider('b', 'c', 'd');
		$providerC = $providerA->intersect($providerB);

		$item = $providerC->rewind();
		$this->assertEquals('b', $item);
		$this->assertEquals(0, $providerC->key());
		$this->assertEquals('b', $providerC->current());

		$item = $providerC->next();
		$this->assertEquals('c', $item);
		$this->assertEquals(1, $providerC->key());
		$this->assertEquals('c', $providerC->current());

		$item = $providerC->next();
		$this->assertEquals(null, $item);
		$this->assertEquals(null, $providerC->key());
		$this->assertEquals(null, $providerC->current());

		unset($providerA);
		unset($providerB);
		unset($providerC);
	}

	public function testProviderUnique() {
		$provider = new Provider('a', 'b', 'c', 'a');
		$unique = $provider->unique();

		$item = $unique->rewind();
		$this->assertEquals('a', $item);
		$this->assertEquals(0, $unique->key());
		$this->assertEquals('a', $unique->current());

		$item = $unique->next();
		$this->assertEquals('b', $item);
		$this->assertEquals(1, $unique->key());
		$this->assertEquals('b', $unique->current());

		$item = $unique->next();
		$this->assertEquals('c', $item);
		$this->assertEquals(2, $unique->key());
		$this->assertEquals('c', $unique->current());

		$item = $unique->next();
		$this->assertEquals(null, $item);
		$this->assertEquals(null, $unique->key());
		$this->assertEquals(null, $unique->current());

		unset($provider);
		unset($unique);
		unset($item);
	}

	public function testProviderFind() {
		$provider = new Provider('a', 'b', 'c', 'a');

		$finding = $provider->find('a');
		$this->assertInstanceOf(Provider::class, $finding);
		$this->assertEquals(2, count($finding));

		$finding = $provider->find('b');
		$this->assertInstanceOf(Provider::class, $finding);
		$this->assertEquals(1, count($finding));

		$finding = $provider->find('z');
		$this->assertInstanceOf(Provider::class, $finding);
		$this->assertEquals(0, count($finding));

		unset($provider);
		unset($finding);
	}

	public function testProviderContains() {
		$provider = new Provider('a', 'b', 'c', 'a');

		$contains = $provider->contains('a');
		$this->assertTrue($contains);

		$contains = $provider->contains('b');
		$this->assertTrue($contains);

		$contains = $provider->contains('c');
		$this->assertTrue($contains);

		$contains = $provider->contains('d');
		$this->assertFalse($contains);

		$contains = $provider->contains('foo');
		$this->assertFalse($contains);

		unset($provider);
		unset($contains);
	}

	public function testProviderEach() {
		$list = ['foo', 'bar', 'baz'];
		$provider = new Provider(...$list);
		$hit = 0;

		$provider->each(function($value, $index, $prv) use ($provider, $list, &$hit) {
			$this->assertEquals($list[$index], $value);
			$this->assertTrue(is_int($index));
			$this->assertEquals($hit, $index);
			$this->assertInstanceOf(Provider::class, $prv);
			$this->assertTrue($prv === $provider);

			++$hit;
		});

		unset($list);
		unset($provider);
		unset($hit);
	}

	public function testProviderForEach() {
		$list = ['one', 'two', 'three'];
		$provider = new Provider(...$list);
		$hit = 0;

		$provider->forEach(function($value, $index, $prv) use ($provider, $list, &$hit) {
			$this->assertEquals($list[$index], $value);
			$this->assertTrue(is_int($index));
			$this->assertEquals($hit, $index);
			$this->assertInstanceOf(Provider::class, $prv);
			$this->assertTrue($prv === $provider);

			++$hit;
		});

		unset($list);
		unset($provider);
		unset($hit);
	}

	public function testProviderMap() {
		$list = ['a', 'b', 'c'];
		$provider = new Provider(...$list);
		$hit = 0;

		$mapped = $provider->map(function($value, $index, $prv) use ($provider, $list, &$hit) {
			$this->assertEquals($list[$index], $value);
			$this->assertTrue(is_int($index));
			$this->assertEquals($hit, $index);
			$this->assertInstanceOf(Provider::class, $prv);
			$this->assertTrue($prv === $provider);

			++$hit;

			return sprintf('*%s*', $value);
		});

		$this->assertEquals(3, $hit);

		$this->assertInstanceOf(Provider::class, $mapped);
		$this->assertEquals(3, count($mapped));

		$this->assertEquals('*a*', $mapped[0]);
		$this->assertEquals('*b*', $mapped[1]);
		$this->assertEquals('*c*', $mapped[2]);

		unset($list);
		unset($provider);
		unset($hit);
		unset($mapped);
	}

	public function testProviderReduce() {
		$list = ['a', 'b', 'c', 'd', 'e', 'f'];
		$provider = new Provider(...$list);
		$hit = 0;

		$reduced = $provider->reduce(function($carry, $value, $index, $prv) use ($provider, $list, &$hit) {
			$this->assertEquals($list[$hit], $carry);
			$this->assertEquals($list[$index], $value);
			$this->assertTrue(is_int($index));
			$this->assertEquals($hit + 1, $index);
			$this->assertInstanceOf(Provider::class, $prv);
			$this->assertTrue($prv === $provider);

			++$hit;

			return $value;
		});

		$this->assertEquals(5, $hit);

		$this->assertEquals('f', $reduced);

		unset($list);
		unset($hit);
		unset($provider);
		unset($reduced);
	}

	public function testProviderReduceInit() {
		$list = ['a', 'b', 'c', 'd', 'e', 'f'];
		$provider = new Provider(...$list);
		$hit = 0;

		$reduced = $provider->reduce(function($carry, $value, $index, $prv) use ($provider, $list, &$hit) {
			if ($index === 0) {
				$this->assertEquals('foo', $carry);
			}
			else {
				$this->assertEquals($list[$index - 1], $carry);
			}

			$this->assertEquals($list[$index], $value);
			$this->assertTrue(is_int($index));
			$this->assertEquals($hit, $index);
			$this->assertInstanceOf(Provider::class, $prv);
			$this->assertTrue($prv === $provider);

			++$hit;

			return $value;
		}, 'foo');

		$this->assertEquals(6, $hit);

		$this->assertEquals('f', $reduced);

		unset($list);
		unset($hit);
		unset($provider);
		unset($reduced);
	}

	public function testProviderFilter() {
		$list = ['a', 'b', 'c', 'd', 'e', 'f'];
		$provider = new Provider(...$list);
		$hit = 0;

		$filtered = $provider->filter(function($value, $index, $prv) use ($provider, $list, &$hit) {
			$this->assertEquals($list[$index], $value);
			$this->assertTrue(is_int($index));
			$this->assertEquals($hit, $index);
			$this->assertInstanceOf(Provider::class, $prv);
			$this->assertTrue($prv === $provider);

			++$hit;

			return $value !== 'a' && $index % 2 === 0;
		});

		$this->assertEquals(6, $hit);

		$this->assertInstanceOf(Provider::class, $filtered);
		$this->assertEquals(2, count($filtered));

		$this->assertEquals('c', $filtered[0]);
		$this->assertEquals('e', $filtered[1]);

		unset($list);
		unset($hit);
		unset($provider);
		unset($filtered);
	}
}
