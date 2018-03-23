<?php

namespace Crazymeeks\WP\Foundation\Route;

use Closure;
use Crazymeeks\WP\Foundation\Route\Route;
use Crazymeeks\WP\Foundation\Route\RouteCollection;

class WPRoute
{

	/**
	 * Route prefixes
	 * 
	 * @var array
	 */
	protected $prefix = [];

	/**
	 * Route namespace
	 * 
	 * @var array
	 */
	protected $namespace = [];

	/**
	 * Route resource
	 * 
	 * @var array
	 */
	protected $resource = [];

	/**
	 * The build routes
	 * 
	 * @var array
	 */
	protected $compiledRoutes = [];

	/**
	 * The registered routes
	 * 
	 * @var array
	 */
	protected $routes = [];

	/**
	 * The grouped routes
	 * 
	 * @var array
	 */
	protected $groups = [];


	protected $routeCollection;

	public function __construct()
	{
		$this->routeCollection = new RouteCollection();
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
		$this->groups = $groups;

		return $callback($this);

	}

	/**
	 * Route get
	 * 
	 * @param  string $resource
	 * @param  string $classAndAction        The fully qualified namespace of the class and method
	 * 
	 * @return $this
	 */
	public function get($resource, $classAndAction)
	{

		$this->addRoute('GET', $resource, $classAndAction);

		return $this;
	}

	protected function addRoute($method, $resource, $classAndAction)
	{
		return $this->routeCollection->add($this->createRoute($method, $resource, $classAndAction));
	}

	/**
	 * Create route
	 * 
	 * @param  string $method         The HTTP methods(GET, PUT, POST, DELETE, PATCH)
	 * @param  string $resource
	 * @param  string $classAndAction The class and action method(Class@method)
	 * 
	 * @return Crazymeeks\WP\Foundation\Route\Route
	 */
	protected function createRoute($method, $resource, $classAndAction)
	{
		$this->mergeWithGroups($method, $resource, $classAndAction);

		return new Route($this->routes);
	}

	/**
	 * Merge routes to group route
	 * 
	 * @param  string $method         The HTTP method
	 * @param  string $resource       The route resource
	 * @param  string $classAndAction The class and action method(Class@method)
	 * 
	 * @return void
	 */
	protected function mergeWithGroups($method, $resource, $classAndAction)
	{

		foreach($this->groups as $key => $group){
			if ($key === 'namespace') {
					$group = $group . '\\' . $classAndAction;
				}
				
				$this->routes[$this->groups['namespace']][$resource][$method][] = [$key => $group];

				$group = null;
			
		}
	}

	/**
	 * Compile and format the registered rest route
	 * 
	 * @return Crazymeeks\WP\Foundation\Route\RouteCollection
	 */
	public function routeCollection()
	{
		return $this->routeCollection;
	}

	/**
	 * Compile the routes
	 * 
	 * @return array
	 */
	public function compile()
	{

		static $formattedRoutes = [];

		$routeCollections = $this->routeCollection()->all();

		foreach($routeCollections as $routes){
			foreach($routes as $resource => $route){
				foreach($route as $method => $items){
					$callback = explode('@', $items[1]['namespace']);

					$formattedRoutes[] = array(
						'namespace' => $items[0]['prefix'],
						'resource'  => $resource,
						'options'   => array(
							'methods' => strtoupper($method),
							'callback' => array((new $callback[0]), $callback[1]),
						),
					);
				}
			}
		}

		$this->compiledRoutes = $formattedRoutes;
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


/*


// $route = array(
		// 	array(
		// 		'namespace' => 'myplugin/v1',
		// 		'resource'  => '/author',
		// 		'options'   => ,
		// 	),
		// );

foreach($routes as $route){
	register_rest_route($route['namespace'], $route['resource'], $route['options']);
}

register_rest_route( 'myplugin/v1', '/author/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'my_awesome_func',
		'args' => array(
			'id' => array(
				'validate_callback' => function($param, $request, $key) {
					return is_numeric( $param );
				}
			),
		),
	) );
 */