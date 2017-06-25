<?php

namespace Konfirm\Collection;


class Provider implements \Iterator, \ArrayAccess, \Countable {
	/**
	 * @var array
	 */
	protected $source;

	/**
	 * Provider constructor.
	 * @param array ...$source
	 */
	public function __construct(...$source) {
		$this->source = $source;
	}

	/**
	 * @param $seek
	 * @return Provider
	 */
	public function find($seek): Provider {
		return $this->filter(function($item) use ($seek) {
			return $this->compare($seek, $item);
		});
	}

	/**
	 * @param $seek
	 * @return bool
	 */
	public function contains($seek): bool {
		return (bool) count($this->find($seek));
	}

	/**
	 * @param \Closure $each
	 */
	public function each(\Closure $each) {
		foreach ($this as $key=>$value) {
			$each($value, $key, $this);
		}
	}

	/**
	 * @param \Closure $filter
	 * @return Provider
	 */
	public function filter(\Closure $filter): Provider {
		$result = [];

		foreach ($this as $key=>$value) {
			if ($filter($value, $key, $this)) {
				$result[] = $value;
			}
		}

		return new static(...$result);
	}

	/**
	 * @param \Closure $map
	 * @return Provider
	 */
	public function map(\Closure $map): Provider {
		$result = [];

		foreach ($this as $key=>$value) {
			$result[] = $map($value, $key, $this);
		}

		return new static(...$result);
	}

	/**
	 * @param \Closure $each
	 */
	public function forEach(\Closure $each) {
		$this->each($each);
	}

	/**
	 * @param \Closure $reduce
	 * @param null $initial
	 * @return mixed|null
	 */
	public function reduce(\Closure $reduce, $initial=null) {
		$initialize = func_num_args() < 2;
		$result = $initial;

		foreach ($this as $key=>$value) {
			if ($initialize) {
				$initialize = false;
				$result = $value;
				continue;
			}

			$result = $reduce($result, $value, $key, $this);
		}

		return $result;
	}

	/**
	 * @param Provider $provider
	 * @return Provider
	 */
	public function intersect(Provider $provider): Provider {
		$intersect = [];

		foreach ($this->source as $source) {
			$intersect = array_merge($intersect, $provider->find($source)->toArray());
		}

		return new static(...$intersect);
	}

	/**
	 * @return Provider
	 */
	public function unique() {
		$unique = [];

		foreach ($this->source as $source) {
			$exists = false;

			foreach ($unique as $seen) {
				if ($this->compare($source, $seen)) {
					$exists = true;
				}
			}

			if (!$exists) {
				$unique[] = $source;
			}
		}

		return new static(...$unique);
	}


	/**
	 * @return array
	 */
	public function toArray(): array {
		return $this->source;
	}

	/**
	 * @param $a
	 * @param $b
	 * @return bool
	 */
	protected function compare($a, $b) {
		return $a === $b || ($a instanceof Comparable && $b instanceof Comparable && $a->getComparison() === $b->getComparison());
	}

	//  Iterator implementation

	/**
	 * @return mixed
	 */
	public function current() {
		return current($this->source);
	}

	/**
	 * @return mixed
	 */
	public function next() {
		return next($this->source);
	}

	/**
	 * @return mixed
	 */
	public function key() {
		return key($this->source);
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return !is_null(key($this->source));
	}

	/**
	 * @return mixed
	 */
	public function rewind() {
		return reset($this->source);
	}


	//   ArrayAccess implementation

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->source[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return mixed|null
	 */
	public function offsetGet($offset) {
		return $this->source[$offset] ?? null;
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		$this->source[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
		unset($this->source[$offset]);
	}


	//  Countable implementation

	/**
	 * @return int
	 */
	public function count() {
		return count($this->source);
	}
}
