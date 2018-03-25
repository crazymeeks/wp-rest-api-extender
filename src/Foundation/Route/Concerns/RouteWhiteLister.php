<?php

namespace Crazymeeks\WP\Foundation\Route\Concerns;

trait RouteWhiteLister
{

	/**
	 * The additional routes to be whitelisted
	 * 
	 * @param  array  $routes
	 * 
	 * @return void
	 */
	public function whiteList(array $routes)
	{
		$this->whitelistedroutes =  array_merge($routes, $this->whitelistedroutes);
	}

	/**
	 * The list of routes to be whitelisted.
	 * 
	 * @param array $routes
	 *
	 * @return  void
	 */
	public function createRouteWhiteList()
	{

		$mainPrefix = '/' . ltrim(rtrim($this->prefix , '/'), '/');

		$this->whitelistedroutes =  array_unique(array_merge([$mainPrefix], $this->whitelistedroutes));

		$endpoint = $mainPrefix . '/' . ltrim($this->resource, '/');

		$routes =  array_unique(array_merge([$endpoint], $this->whitelistedroutes));
		// sort in ascending order
		asort($routes);
		$this->whitelistedroutes = array_values($routes);
	}

	/**
	 * Get the whitelisted routes
	 *
	 * The routes defined by develop in a group are automatically
	 * 
	 * @return array
	 */
	public function getWhiteListedRoutes()
	{
		return $this->whitelistedroutes;
	}

	/**
	 * After registering the routes, we will do the whitelisting
	 *
	 * 
	 * @return void
	 */
	public function doRouteWhiteListing()
	{
		$whitelistedroutes = $this->getWhiteListedRoutes();

		// FILTER REST EXPOSE ENDPOINTS
		add_filter( 'rest_endpoints', function ( $endpoints ) use ($whitelistedroutes){
			
		    // DISABLED ALL EXPOSE ROUTES EXCEPTS IN THE WHITELISTED
		    foreach ( $endpoints as $route => $headers ) {
		    	if ( !in_array($route, $whitelistedroutes) ) {
		    		unset($endpoints[$route]);
		    	}
		    }
		    
		    return $endpoints;
		} );
	}
}