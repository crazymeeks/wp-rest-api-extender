<?php

namespace Crazymeeks\WP\Foundation\WPRestExtender;

use Crazymeeks\WP\Foundation\Route\WPRoute;


class Extender extends \WP_REST_Controller
{

	protected $route;

	public function __construct(WPRoute $route)
	{
		$this->route = $route;

		$this->register();
	}

	/**
	 * Register the routes to WP Rest Route
	 * 
	 * @return $this
	 */
	public function register_routes()
	{
		$this->route->compile();

		$compiledRoutes = $this->route->getCompiledRoutes();

	  	foreach($compiledRoutes as $route){
	      register_rest_route( $route['prefix'], '/' . $route['resource'], $route['options']);
	  	}

	  	$this->route->doRouteWhiteListing();

	  	return true;
	}

	/**
	 * Alias for register_routes
	 * 
	 * @return void
	 */
	public function register()
	{
		$this->register_routes();
	}
}