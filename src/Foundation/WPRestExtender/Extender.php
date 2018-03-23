<?php

namespace Crazymeeks\WP\Foundation\WPRestExtender;

use Crazymeeks\WP\Foundation\Route\WPRoute;

class Extender extends \WP_REST_Controller
{


	protected $route;

	public function __construct(WPRoute $route)
	{
		$this->route = $route;
		$this->register_routes();
	}

	/**
	 * Register the routes to WP Rest Route
	 * 
	 * @return bool
	 */
	public function register_routes()
	{
		$this->route->compile();

		$compiledRoutes = $this->route->getCompiledRoutes();

	  	foreach($compiledRoutes as $route){
	      register_rest_route( $route['prefix'], '/' . $route['resource'], $route['options']);
	  	}

	  	return true;
	}
}