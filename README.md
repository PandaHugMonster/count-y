# count-y

Simple PHP Counter

Version: **2.0.0**

## To install


 1. Go to the folder of the project (git/code repo) `/home/user/my-code`
 2. Run commands in terminal in your code folder:


```shell
    composer require spaf/count-y;
```

## Basic usage (file-based)

```php

require __DIR__.'/vendor/autoload.php';

use spaf\county\Counter;
use spaf\county\storage\StorageFile;
use spaf\simputils\PHP;

PHP::init();

$c = new Counter(new StorageFile());
//  In case if the StorageFile is not specified, the value will be stored in memory (StorageMemory
//  class). So it will exist until end of the runtime/execution.
//$c = new Counter();

//  If you need events, first delegate/closure is an event function, the second - event condition. The first will work,
//  only if the second will return TRUE, or if condition delegate/closure is not specified.

//$c->add_event('My first test event', function ($value, $name) {
//	echo "EVENT of {$name} for value of {$value}\n";
//}, fn($v) => $v % 10 == 0 );

for ($i = 0; $i <= 105; $i++) {
	$c->visit();
	$c->visit();
	$c->visit();
}

echo "Test: {$c}";

```


That's it.
The lib right now supports really minimal functionality.
