<?php
/**
 * Klein (klein.php) - A lightning fast router for PHP
 *
 * @author      Chris O'Hara <cohara87@gmail.com>
 * @author      Trevor Suarez (Rican7) (contributor and v2 refactorer)
 * @copyright   (c) Chris O'Hara
 * @link        https://github.com/chriso/klein.php
 * @license     MIT
 */

namespace Klein\DataCollection;

use Klein\Route;

/**
 * RouteCollection
 *
 * A DataCollection for Routes
 *
 * @uses        DataCollection
 * @package     Klein\DataCollection
 */
class RouteCollection extends DataCollection
{

    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @override (doesn't call our parent)
     * @param array $routes The routes of this collection
     * @access public
     */
    public function __construct(array $routes = array())
    {
        foreach ($routes as $value) {
            $this->add($value);
        }
    }

    /**
     * Set a route
     *
     * {@inheritdoc}
     *
     * A value may either be a callable or a Route instance
     * Callable values will be converted into a Route with
     * the "name" of the route being set from the "key"
     *
     * A developer may add a named route to the collection
     * by passing the name of the route as the "$key" and an
     * instance of a Route as the "$value"
     *
     * @see DataCollection::set()
     * @param string $key                   The name of the route to set
     * @param Route|callable $value         The value of the route to set
     * @access public
     * @return RouteCollection
     */
    public function set($key, $value)
    {
        if (!$value instanceof Route) {
            $value = new Route($value);
        }

        return parent::set($key, $value);
    }

    /**
     * Add a route instance to the collection
     *
     * This will auto-generate a name
     *
     * @param Route $route
     * @access public
     * @return RouteCollection
     */
    public function addRoute(Route $route)
    {
        /**
         * Auto-generate a name from the object's hash
         * This makes it so that we can autogenerate names
         * that ensure duplicate route instances are overridden
         */
        $name = spl_object_hash($route);

        return $this->set($name, $route);
    }

    /**
     * Add a route to the collection
     *
     * This allows a more generic form that
     * will take a Route instance, string callable
     * or any other Route class compatible callback
     *
     * @param mixed $route
     * @access public
     * @return RouteCollection
     */
    public function add($route)
    {
        if (!$route instanceof Route) {
            $route = new Route($route);
        }

        return $this->addRoute($route);
    }

    /**
     * Prepare the named routes in the collection
     *
     * This loops through every route to set the collection's
     * key name for that route to equal the routes name, if
     * its changed
     *
     * @access public
     * @return RouteCollection
     */
    public function prepareNamed()
    {
        foreach ($this as $key => $route) {
            $route_name = $route->getName();

            if (null !== $route_name) {
                // Remove the route from the collection
                $this->remove($key);

                // Add the route back to the set with the new name
                $this->set($route_name, $route);
            }
        }

        return $this;
    }
}
