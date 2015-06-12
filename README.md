Darsyn's Class Finder
=====================

A library for searching for classes in a specific root directory and namespace. Originally intended for searching for
classes in all registered Symfony bundles (similar to finding bundle commands), it can find classes that stick to a
standard without having to manually register each of those classes. Various filters can be specified, such as:

- In a sub-directory/namespace.
- Have a specific file extension.
- End with custom suffix.
- Implement or extend a parent class or interface.

Usually an array containing fully-qualified class names (as strings) is returned, but passing `true` as the fourth
parameter to the `findClasses()` method will return an array of `ReflectionClass` instances instead. Very useful in
certain situations.

License
-------

This project is licensed under [MIT](http://j.mp/mit-license).

Dependencies
------------

This project uses Symfony's [Finder](http://symfony.com/doc/current/components/finder.html) component to search for
classes, and PHP's [Reflection extension](http://php.net/manual/en/book.reflection.php) to process class definitions. It
also assumes you are using [Composer](https://getcomposer.org) or a similar autoloader.

If you plan to use this library with the Symfony framework, it also utilises the
[HTTP Kernel](http://symfony.com/doc/current/components/http_kernel/introduction.html) component; this will already be
a dependency of your project however.

Example
-------

```php
<?php

use Darsyn\ClassFinder\ClassFinder;

$finder = new ClassFinder;
$finder->setRootDirectory(__DIR__);

$subDir = 'Models';
$suffix = 'Entity';
$parent = 'Framework\\ActiveRecordEntity';

$classes = $finder->findClasses($subDir, $suffix, $parent);
foreach ($classes as $class) {
	echo $class . "\n";
}

/**
 * Example Output:
 *
 * Models\UserEntity
 * Models\GroupEntity
 * Models\CustomerEntity
 */
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
		// Find all container-aware commands across all bundles:
		$containerAwareCommands = $finder->findClasses(
			'Command',
			'Command',
			'Symfony\\Bundle\\FrameworkBundle\\Command\\ContainerAwareCommand'
		);
	}
}
```

You may also register this library as a service. To do so, add the following to `app/config/services.yml` (or in the
bundle that uses this library in your preferred configuration format):

```yaml
services:

    darsyn.class_finder:
        class: Darsyn\ClassFinder\BundleClassFinder
        arguments: [ @kernel ]
```

**Note:** Remember that the `kernel` service is synthetic, and cannot be used until Symfony injects the correct kernel
instance into the container.

Authors and Contributing
------------------------

Current authors include:

- [Zander Baldwin](https://zanderbaldwin.com) <[hello@zanderbaldwin.com](mailto:hello@zanderbaldwin.com)>
  (on [GitHub](https://github.com/zanderbaldwin "Zander Baldwin on GitHub")).

All contributions are welcome, don't forget to add your name here in the pull request!
