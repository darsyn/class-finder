Class Finder
============

Find classes that end-users have created that adhere to simple rules:

- Must be in `$directory`
- Must be a child of `$namespace`.
- Must have specific file `$extension`.
- Class name must end with `$suffix`.
- Must extend from `$parent` class or interface.

Example
-------

```php
<?php

use Darsyn\ClassFinder\ClassFinder;

$finder = new ClassFinder;
$finder->setRootDirectory(__DIR__);

$subDir = 'Controllers';
$suffix = 'Controller';
$parent = 'Darsyn\\Test\\ControllerInterface';

$classes = $finder->findClasses($subDir, $suffix, $parent);
foreach ($classes = $class) {
	echo $class . "\n";
}
```

Symfony Integration
-------------------

```php
<?php

use Darsyn\ClassFinder\BundleClassFinder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	public function indexAction()
	{
		$kernel = $this->container->get('kernel');
		$finder = new BundleClassFinder($kernel);
		$containerAwareCommand = 'Symfony\\Bundle\\FrameworkBundle\\Command\\ContainerAwareCommand';
		$classes = $finder->findClasses('Command', 'Command', $containerAwareCommand);
	}
}
```