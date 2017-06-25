# Collection
Simple implementation of common array operations with a unified argument list for filter, map and reduce similar to the ECMA/Javascript implementations 

## Example use

### Unique
Remove all duplicate entries
```php
<?php

use Konfirm\Collection\Provider;

$provider = new Provider('a', 'b', 'c', 'b', 'a');
$unique = $provider->unique();
//  'a', 'b', 'c'
```

### Intersect
Create a new Provider containing the elements which are present in two Providers
```php
<?php

use Konfirm\Collection\Provider;

$first = new Provider('a', 'b', 'c', 'b', 'a');
$second = new Provider('foo', 'bar', 'c', 'b');

$common = $first->intersect($second);
//  'b', 'c', 'b';
```

### Filter
```php
<?php

use Konfirm\Collection\Provider;

$provider = new Provider('a', 'b', 'c', 'b', 'a');
$filtered = $provider->filter(function($value, $index /*, $provider*/) {
	return $value !== 'a' && $index % 2 === 0;
});
// 'c'
```

### Map
```php
<?php

use Konfirm\Collection\Provider;

$provider = new Provider('a', 'b', 'c', 'b', 'a');
$mapped = $provider->map(function($value /*, $index, $provider*/) {
	return sprintf('*%s*');
});
// '*a*', '*b*', '*c*', '*b*', '*a*'   
```

### Reduce
Support for both the uninitialized (using the first item from the Provider) and the initialized (initial value is provided) reduce

#### uninitialized
```php
<?php

use Konfirm\Collection\Provider;

$provider = new Provider('a', 'b', 'c');
$reduce = $provider->map(function($carry, $value, $index) {
	return $carry + $value;
});
// 'abc'   
```

#### initialized
```php
<?php

use Konfirm\Collection\Provider;

$provider = new Provider('a', 'b', 'c');
$reduce = $provider->map(function($carry, $value, $index) {
	return $carry + $value;
}, 'my result: ');
// 'my result: abc'   
```


## Comparing objects
If there is need for comparing objects based on (a subset of) properties, one can implement the `Konfirm\Collection\Comparable`-interface, which specifies the `getComparison` method which is used (should both objects implement the interface) if (and only if) both objects are not identical (`===`) 


## Features

* PSR-4 autoloading compliant structure
* Full code coverage with PHPUnit
