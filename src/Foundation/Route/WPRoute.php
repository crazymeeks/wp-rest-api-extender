<?php

namespace Crazymeeks\WP\Foundation\Route;

use Closure;
use SplStack;

use Crazymeeks\WP\Foundation\Exceptions\ObjectProperyNotFoundException;

class WPRoute
{

	/**
	 * Route prefixes
	 * 
	 * @var string
	 */
	protected $prefix;

	/**
	 * Route namespace
	 * 
	 * @var string
	 */
	protected $namespace;

	/**
	 * Route resource
	 * 
	 * @var string
	 */
	protected $resource;

	/**
	 * Route options
	 * 
	 * @var array
	 */
	protected $options = [];

	/**
	 * The build routes
	 * 
	 * @var array
	 */
	protected $compiledRoutes = [];

	/**
	 * The route prefix stack
	 * 
	 * @var \SplStack
	 */
	protected $prefixStack;

	/**
	 * The route namespace stack
	 * 
	 * @var \SplStack
	 */
	protected $namespaceStack;

	public function __construct()
	{
		$this->prefixStack = new SplStack();

		$this->namespaceStack = new SplStack();
	}

	/**
	 * Route group
	 * 
	 * @param  array   $group
	 * @param  Closure $callback
	 * 
	 * @return void
	 */
	public function group(array $groups, Closure $callback)
	{

		$this->setStacks($groups);

		$callback($this);

		$this->popStacks($groups);

	}

	/**
	 * We will set the stack for specific route configurations
	 * like namespace and prefix
	 *
	 * @param  array $settings
	 */
	protected function setStacks(array $settings)
	{
		if (array_key_exists('prefix', $settings)) {
			 $this->setStackPrefix($settings['prefix']);
		}

		if (array_key_exists('namespace', $settings)) {

			$this->setStackNamespace($settings['namespace']);
		}
	}

	protected function popStacks($settings)
	{
		if (array_key_exists('prefix', $settings)) {
			if ( !is_null($this->getStackPrefix()) ) {
				$this->prefixStack->pop();
			}
		}

		if (array_key_exists('namespace', $settings)) {
			if ( !is_null($this->getStackNamespace()) ) {
				$this->namespaceStack->pop();
			}
		}
	}

	/**
	 * Get the prefix in stack(if any)
	 * 
	 * @return mixed
	 */
	protected function getStackPrefix()
	{
		if ($this->prefixStack->isEmpty()) {
			return null;
		}

		$topPrefix = $this->prefixStack->top();

		return rtrim($topPrefix, '/');
	}

	/**
	 * Set route group prefix in the stack
	 * 
	 * @param string $prefix
	 *
	 * @return  string
	 */
	protected function setStackPrefix($prefix)
	{
		if ( $this->prefixStack->isEmpty() ) {
			$this->prefixStack->push($prefix);

			return $prefix;
		}

		$topPrefix = $this->getStackPrefix();
		$prefix = $topPrefix . '/' . ltrim($prefix);

		$this->prefixStack->push($prefix);

		return $prefix;

	}


	/**
	 * Set route group prefix in the stack
	 * 
	 * @param string $prefix
	 *
	 * @return  string
	 */
	protected function setStackNamespace($namespace)
	{
		if ( $this->namespaceStack->isEmpty() ) {
			$this->namespaceStack->push($namespace);

			return $namespace;
		}

		$topNamespace = $this->getStackNamespace();
		$namespace = $topNamespace . '\\' . ltrim($namespace);

		$this->namespaceStack->push($namespace);

		return $namespace;

	}

	protected function getStackNamespace()
	{
		if ( $this->namespaceStack->isEmpty()) {
			return null;
		}

		$topNamespace = $this->namespaceStack->top();

		return rtrim($topNamespace, '\\');
	}

	/**
	 * Route get
	 * 
	 * @param  string $resource
	 * @param  string $classAndAction        The fully qualified namespace of the class and method
	 * 
	 * @return void
	 */
	public function get($resource, $classAndAction)
	{
		$this->addRoute('GET', $resource, $classAndAction);
	}

	/**
	 * Route post
	 * 
	 * @param  string $resource
	 * @param  string $classAndAction        The fully qualified namespace of the class and method
	 * 
	 * @return void
	 */
	public function post($resource, $classAndAction)
	{
		$this->addRoute('POST', $resource, $classAndAction);
	}

	/**
	 * Route PUT
	 * 
	 * @param  string $resource
	 * @param  string $classAndAction        The fully qualified namespace of the class and method
	 * 
	 * @return void
	 */
	public function put($resource, $classAndAction)
	{
		$this->addRoute('PUT', $resource, $classAndAction);
	}

	/**
	 * Route DELETE
	 * 
	 * @param  string $resource
	 * @param  string $classAndAction        The fully qualified namespace of the class and method
	 * 
	 * @return void
	 */
	public function delete($resource, $classAndAction)
	{
		$this->addRoute('DELETE', $resource, $classAndAction);
	}

	/**
	 * Route PATCH
	 * 
	 * @param  string $resource
	 * @param  string $classAndAction        The fully qualified namespace of the class and method
	 * 
	 * @return void
	 */
	public function patch($resource, $classAndAction)
	{
		$this->addRoute('PATCH', $resource, $classAndAction);
	}

	/**
	 * Add route
	 * 
	 * @param string $method         The HTTP Method
	 * @param string $resource       Api route resource
	 * @param string $classAndAction This is like Controller@action in Laravel
	 */
	protected function addRoute($method, $resource, $classAndAction)
	{
		$this->setPrefix($this->getStackPrefix())
			 ->setNamespace($this->getStackNamespace())
			 ->setResource($resource)
			 ->setOptions([$method, $classAndAction])
			 ->compile();
	}

	/**
	 * Set route prefix
	 * 
	 * @param string $namespace    The prefix popped from prefixStack
	 *
	 * @return  $this
	 */
	protected function setPrefix($prefix)
	{
		$this->prefix = $prefix;

		return $this;
	}

	/**
	 * Set route namespace
	 *
	 * @param  string $namespace    The namespace popped from namespaceStack
	 */
	protected function setNamespace($namespace)
	{
		$this->namespace = $namespace;

		return $this;
	}

	/**
	 * Set route resource
	 *
	 * @param  string $resource    The route resource. This comes from calling get() method
	 *
	 * @return  $this
	 */
	protected function setResource($resource)
	{
		$this->resource = $resource;

		return $this;
	}

	/**
	 * Set route options
	 * 
	 * @param array $options
	 *
	 * @return  $this
	 */
	protected function setOptions(array $options)
	{

		$callback = explode('@', $options[1]);
		$class = $this->namespace . '\\' . $callback[0];
		
		$this->options = ['methods' => strtoupper($options[0]), 'callback' => [(new $class), $callback[1]]];

		return $this;
	}

	/**
	 * Compile the routes
	 * 
	 * @return array
	 */
	public function compile()
	{
		$this->compiledRoutes[] = [
			'prefix' => $this->prefix,
			'resource' => $this->resource,
			'options'  => $this->options,
		];
	}

	public function __call($name, $args)
	{
		// every character after the first 3 characters.
        // lower case first
        $property = lcfirst(substr($name, 3));

        // if the first 3 characters is = get
        if (strncasecmp($name, "get", 3) == 0) {
            if (isset($this->$property)) {
                return $this->$property;
            }
        }

        throw new ObjectProperyNotFoundException("The property {$property} has not been set.");
	}
}