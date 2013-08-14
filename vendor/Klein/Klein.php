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

namespace Klein;

use \Exception;
use \OutOfBoundsException;

use \Klein\DataCollection\RouteCollection;
use \Klein\Exceptions\LockedResponseException;
use \Klein\Exceptions\UnhandledException;
use \Klein\Exceptions\DispatchHaltedException;

/**
 * Klein
 *
 * Main Klein router class
 * 
 * @package     Klein
 */
class Klein
{

    /**
     * Class properties
     */

    /**
     * The regular expression used to compile and match URL's
     *
     * @const string
     */
    const ROUTE_COMPILE_REGEX = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';

    /**
     * Dispatch route output handling
     *
     * Don't capture anything. Behave as normal.
     *
     * @const int
     */
    const DISPATCH_NO_CAPTURE = 0;

    /**
     * Dispatch route output handling
     *
     * Capture all output and return it from dispatch
     *
     * @const int
     */
    const DISPATCH_CAPTURE_AND_RETURN = 1;

    /**
     * Dispatch route output handling
     *
     * Capture all output and replace the response body with it
     *
     * @const int
     */
    const DISPATCH_CAPTURE_AND_REPLACE = 2;

    /**
     * Dispatch route output handling
     *
     * Capture all output and prepend it to the response body
     *
     * @const int
     */
    const DISPATCH_CAPTURE_AND_PREPEND = 3;

    /**
     * Dispatch route output handling
     *
     * Capture all output and append it to the response body
     *
     * @const int
     */
    const DISPATCH_CAPTURE_AND_APPEND = 4;


    /**
     * Class properties
     */

    /**
     * Collection of the routes to match on dispatch
     *
     * @var RouteCollection
     * @access protected
     */
    protected $routes;

    /**
     * The namespace of which to collect the routes in
     * when matching, so you can define routes under a
     * common endpoint
     *
     * @var string
     * @access protected
     */
    protected $namespace;

    /**
     * An array of error callback callables
     *
     * @var array[callable]
     * @access protected
     */
    protected $errorCallbacks = array();


    /**
     * Route objects
     */

    /**
     * The Request object passed to each matched route
     *
     * @var Request
     * @access protected
     */
    protected $request;

    /**
     * The Response object passed to each matched route
     *
     * @var Response
     * @access protected
     */
    protected $response;

    /**
     * The service provider object passed to each matched route
     *
     * @var ServiceProvider
     * @access protected
     */
    protected $service;

    /**
     * A generic variable passed to each matched route
     *
     * @var mixed
     * @access protected
     */
    protected $app;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * Create a new Klein instance with optionally injected dependencies
     * This DI allows for easy testing, object mocking, or class extension
     *
     * @param ServiceProvider $service  Service provider object responsible for utilitarian behaviors
     * @param mixed $app                An object passed to each route callback, defaults to a new App instance
     * @param RouteCollection $routes   Collection object responsible for containing all of the route instances
     * @access public
     */
    public function __construct(ServiceProvider $service = null, $app = null, RouteCollection $routes = null)
    {
        // Instanciate and fall back to defaults
        $this->service = $service ?: new ServiceProvider();
        $this->app     = $app     ?: new App();
        $this->routes  = $routes  ?: new RouteCollection();
    }

    /**
     * Returns the routes object
     *
     * @access public
     * @return RouteCollection
     */
    public function routes()
    {
        return $this->routes;
    }

    /**
     * Returns the request object
     *
     * @access public
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Returns the response object
     *
     * @access public
     * @return Response
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * Returns the service object
     *
     * @access public
     * @return ServiceProvider
     */
    public function service()
    {
        return $this->service;
    }

    /**
     * Returns the app object
     *
     * @access public
     * @return mixed
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * Add a new route to be matched on dispatch
     *
     * This method takes its arguments in a very loose format
     * The only "required" parameter is the callback (which is very strange considering the argument definition order)
     *
     * <code>
     * $router = new Klein();
     *
     * $router->respond( function() {
     *     echo 'this works';
     * });
     * $router->respond( '/endpoint', function() {
     *     echo 'this also works';
     * });
     * $router->respond( 'POST', '/endpoint', function() {
     *     echo 'this also works!!!!';
     * });
     * </code>
     *
     * @param string | array $method    HTTP Method to match
     * @param string $path              Route URI path to match
     * @param callable $callback        Callable callback method to execute on route match
     * @access public
     * @return callable $callback
     */
    public function respond($method, $path = '*', $callback = null)
    {
        // Get the arguments in a very loose format
        $args = func_get_args();
        $callback = array_pop($args);
        $path = array_pop($args);
        $method = array_pop($args);

        // If no path was passed, make our path our "match-all" symbol
        if (null === $path) {
            $path = '*';
        }

        // only consider a request to be matched when not using matchall
        $count_match = ($path !== '*');

        // If a custom regular expression (or negated custom regex)
        if ($this->namespace && $path[0] === '@' || ($path[0] === '!' && $path[1] === '@')) {
            // Is it negated?
            if ($path[0] === '!') {
                $negate = true;
                $path = substr($path, 2);
            } else {
                $negate = false;
                $path = substr($path, 1);
            }

            // Regex anchored to front of string
            if ($path[0] === '^') {
                $path = substr($path, 1);
            } else {
                $path = '.*' . $path;
            }

            if ($negate) {
                $path = '@^' . $this->namespace . '(?!' . $path . ')';
            } else {
                $path = '@^' . $this->namespace . $path;
            }

        } elseif ($this->namespace && ('*' === $path)) {
            // Empty route with namespace is a match-all
            $path = '@^' . $this->namespace . '(/|$)';
        } else {
            // Just prepend our namespace
            $path = $this->namespace . $path;
        }

        $route = new Route($callback, $path, $method, $count_match);

        $this->routes->add($route);

        return $route;
    }

    /**
     * Collect a set of routes under a common namespace
     *
     * The routes may be passed in as either a callable (which holds the route definitions),
     * or as a string of a filename, of which to "include" under the Klein router scope
     *
     * <code>
     * $router = new Klein();
     *
     * $router->with('/users', function($router) {
     *     $router->respond( '/', function() {
     *         // do something interesting
     *     });
     *     $router->respond( '/[i:id]', function() {
     *         // do something different
     *     });
     * });
     *
     * $router->with('/cars', __DIR__ . '/routes/cars.php');
     * </code>
     *
     * @param string $namespace                     The namespace under which to collect the routes
     * @param callable | string[filename] $routes   The defined routes to collect under the namespace
     * @access public
     * @return void
     */
    public function with($namespace, $routes)
    {
        $previous = $this->namespace;
        $this->namespace .= $namespace;

        if (is_callable($routes)) {
            $routes($this);
        } else {
            require $routes;
        }

        $this->namespace = $previous;
    }

    /**
     * Dispatch the request to the approriate route(s)
     *
     * Dispatch with optionally injected dependencies
     * This DI allows for easy testing, object mocking, or class extension
     *
     * @param Request $request          The request object to give to each callback
     * @param Response $response        The response object to give to each callback
     * @param boolean $send_response    Whether or not to "send" the response after the last route has been matched
     * @param int $capture              Specify a DISPATCH_* constant to change the output capturing behavior
     * @access public
     * @return void|string
     */
    public function dispatch(
        Request $request = null,
        Response $response = null,
        $send_response = true,
        $capture = self::DISPATCH_NO_CAPTURE
    ) {
        // Set/Initialize our objects to be sent in each callback
        $this->request = $request ?: Request::createFromGlobals();
        $this->response = $response ?: new Response();

        // Bind our objects to our service
        $this->service->bind($this->request, $this->response);

        // Prepare any named routes
        $this->routes->prepareNamed();


        // Grab some data from the request
        $uri = $this->request->pathname();
        $req_method = $this->request->method();

        // Set up some variables for matching
        $skip_num = 0;
        $matched = $this->routes->cloneEmpty(); // Get a clone of the routes collection, as it may have been injected
        $methods_matched = array();
        $params = array();
        $apc = function_exists('apc_fetch');

        ob_start();

        foreach ($this->routes as $handler) {
            // Are we skipping any matches?
            if ($skip_num > 0) {
                $skip_num--;
                continue;
            }

            // Grab the properties of the route handler
            $method = $handler->getMethod();
            $_route = $handler->getPath();
            $callback = $handler->getCallback();
            $count_match = $handler->getCountMatch();

            // Keep track of whether this specific request method was matched
            $method_match = null;

            // Was a method specified? If so, check it against the current request method
            if (is_array($method)) {
                foreach ($method as $test) {
                    if (strcasecmp($req_method, $test) === 0) {
                        $method_match = true;
                    } elseif (strcasecmp($req_method, 'HEAD') === 0
                          && (strcasecmp($test, 'HEAD') === 0 || strcasecmp($test, 'GET') === 0)) {

                        // Test for HEAD request (like GET)
                        $method_match = true;
                    }
                }

                if (null === $method_match) {
                    $method_match = false;
                }
            } elseif (null !== $method && strcasecmp($req_method, $method) !== 0) {
                $method_match = false;

                // Test for HEAD request (like GET)
                if (strcasecmp($req_method, 'HEAD') === 0
                    && (strcasecmp($method, 'HEAD') === 0 || strcasecmp($method, 'GET') === 0 )) {

                    $method_match = true;
                }
            } elseif (null !== $method && strcasecmp($req_method, $method) === 0) {
                $method_match = true;
            }

            // If the method was matched or if it wasn't even passed (in the route callback)
            $possible_match = (null === $method_match) || $method_match;

            // ! is used to negate a match
            if (isset($_route[0]) && $_route[0] === '!') {
                $negate = true;
                $i = 1;
            } else {
                $negate = false;
                $i = 0;
            }

            // Check for a wildcard (match all)
            if ($_route === '*') {
                $match = true;

            } elseif (($_route === '404' && $matched->isEmpty() && count($methods_matched) <= 0)
                   || ($_route === '405' && $matched->isEmpty() && count($methods_matched) > 0)) {

                // Easily handle 40x's

                $this->handleResponseCallback($callback, $matched, $methods_matched);

                continue;

            } elseif (isset($_route[$i]) && $_route[$i] === '@') {
                // @ is used to specify custom regex

                $match = preg_match('`' . substr($_route, $i + 1) . '`', $uri, $params);

            } else {
                // Compiling and matching regular expressions is relatively
                // expensive, so try and match by a substring first

                $route = null;
                $regex = false;
                $j = 0;
                $n = isset($_route[$i]) ? $_route[$i] : null;

                // Find the longest non-regex substring and match it against the URI
                while (true) {
                    if (!isset($_route[$i])) {
                        break;
                    } elseif (false === $regex) {
                        $c = $n;
                        $regex = $c === '[' || $c === '(' || $c === '.';
                        if (false === $regex && false !== isset($_route[$i+1])) {
                            $n = $_route[$i + 1];
                            $regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
                        }
                        if (false === $regex && $c !== '/' && (!isset($uri[$j]) || $c !== $uri[$j])) {
                            continue 2;
                        }
                        $j++;
                    }
                    $route .= $_route[$i++];
                }

                // Check if there's a cached regex string
                if (false !== $apc) {
                    $regex = apc_fetch("route:$route");
                    if (false === $regex) {
                        $regex = $this->compileRoute($route);
                        apc_store("route:$route", $regex);
                    }
                } else {
                    $regex = $this->compileRoute($route);
                }

                $match = preg_match($regex, $uri, $params);
            }

            if (isset($match) && $match ^ $negate) {
                if ($possible_match) {
                    if (!empty($params)) {
                        /**
                         * URL Decode the params according to RFC 3986
                         * @link http://www.faqs.org/rfcs/rfc3986
                         *
                         * Decode here AFTER matching as per @chriso's suggestion
                         * @link https://github.com/chriso/klein.php/issues/117#issuecomment-21093915
                         */
                        $params = array_map('rawurldecode', $params);

                        $this->request->paramsNamed()->merge($params);
                    }

                    // Handle our response callback
                    try {
                        $this->handleResponseCallback($callback, $matched, $methods_matched);

                    } catch (DispatchHaltedException $e) {
                        switch ($e->getCode()) {
                            case DispatchHaltedException::SKIP_THIS:
                                continue 2;
                                break;
                            case DispatchHaltedException::SKIP_NEXT:
                                $skip_num = $e->getNumberOfSkips();
                                break;
                            case DispatchHaltedException::SKIP_REMAINING:
                                break 2;
                            default:
                                throw $e;
                        }
                    }

                    if ($_route !== '*') {
                        $count_match && $matched->add($handler);
                    }
                }

                // Keep track of possibly matched methods
                $methods_matched = array_merge($methods_matched, (array) $method);
                $methods_matched = array_filter($methods_matched);
                $methods_matched = array_unique($methods_matched);
            }
        }

        try {
            if ($matched->isEmpty() && count($methods_matched) > 0) {
                if (strcasecmp($req_method, 'OPTIONS') !== 0) {
                    $this->response->code(405);
                }

                $this->response->header('Allow', implode(', ', $methods_matched));
            } elseif ($matched->isEmpty()) {
                $this->response->code(404);
            }

            if ($this->response->chunked) {
                $this->response->chunk();

            } else {
                // Output capturing behavior
                switch($capture) {
                    case self::DISPATCH_CAPTURE_AND_RETURN:
                        return ob_get_clean();
                        break;
                    case self::DISPATCH_CAPTURE_AND_REPLACE:
                        $this->response->body(ob_get_clean());
                        break;
                    case self::DISPATCH_CAPTURE_AND_PREPEND:
                        $this->response->prepend(ob_get_clean());
                        break;
                    case self::DISPATCH_CAPTURE_AND_APPEND:
                        $this->response->append(ob_get_clean());
                        break;
                    case self::DISPATCH_NO_CAPTURE:
                    default:
                        ob_end_flush();
                        break;
                }
            }

            // Test for HEAD request (like GET)
            if (strcasecmp($req_method, 'HEAD') === 0) {
                // HEAD requests shouldn't return a body
                $this->response->body('');

                if (ob_get_level()) {
                    ob_clean();
                }
            }
        } catch (LockedResponseException $e) {
            // Do nothing, since this is an automated behavior
        }

        if ($send_response && !$this->response->isSent()) {
            $this->response->send();
        }
    }

    /**
     * Compiles a route string to a regular expression
     *
     * @param string $route     The route string to compile
     * @access protected
     * @return void
     */
    protected function compileRoute($route)
    {
        if (preg_match_all(static::ROUTE_COMPILE_REGEX, $route, $matches, PREG_SET_ORDER)) {
            $match_types = array(
                'i'  => '[0-9]++',
                'a'  => '[0-9A-Za-z]++',
                'h'  => '[0-9A-Fa-f]++',
                's'  => '[0-9A-Za-z-_]++',
                '*'  => '.+?',
                '**' => '.++',
                ''   => '[^/]+?'
            );

            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($match_types[$type])) {
                    $type = $match_types[$type];
                }
                if ($pre === '.') {
                    $pre = '\.';
                }
                // Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                         . ($pre !== '' ? $pre : null)
                         . '('
                         . ($param !== '' ? "?P<$param>" : null)
                         . $type
                         . '))'
                         . ($optional !== '' ? '?' : null);

                $route = str_replace($block, $pattern, $route);
            }
        }

        return "`^$route$`";
    }

    /**
     * Get the path for a given route
     *
     * This looks up the route by its passed name and returns
     * the path/url for that route, with its URL params as
     * placeholders unless you pass a valid key-value pair array
     * of the placeholder params and their values
     *
     * If a pathname is a complex/custom regular expression, this
     * method will simply return the regular expression used to
     * match the request pathname, unless an optional boolean is
     * passed "flatten_regex" which will flatten the regular
     * expression into a simple path string
     *
     * This method, and its style of reverse-compilation, was originally
     * inspired by a similar effort by Gilles Bouthenot (@gbouthenot)
     *
     * @link https://github.com/gbouthenot
     * @param string $route_name        The name of the route
     * @param array $params             The array of placeholder fillers
     * @param boolean $flatten_regex    Optionally flatten custom regular expressions to "/"
     * @throws OutOfBoundsException     If the route requested doesn't exist
     * @access public
     * @return string
     */
    public function getPathFor($route_name, array $params = null, $flatten_regex = true)
    {
        // First, grab the route
        $route = $this->routes->get($route_name);

        // Make sure we are getting a valid route
        if (null === $route) {
            throw new OutOfBoundsException('No such route with name: '. $route_name);
        }

        $path = $route->getPath();

        if (preg_match_all(static::ROUTE_COMPILE_REGEX, $path, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($params[$param])) {
                    $path = str_replace($block, $pre. $params[$param], $path);
                } elseif ($optional) {
                    $path = str_replace($block, '', $path);
                }
            }

        } elseif ($flatten_regex && strpos($path, '@') === 0) {
            // If the path is a custom regular expression and we're "flattening", just return a slash
            $path = '/';
        }

        return $path;
    }

    /**
     * Handle a response callback
     *
     * This handles common exceptions and their output
     * to keep the "dispatch()" method DRY
     *
     * @param callable $callback
     * @param RouteCollection $matched
     * @param int $methods_matched
     * @access protected
     * @return void
     */
    protected function handleResponseCallback($callback, $matched, $methods_matched)
    {
        // Handle the callback
        try {
            $returned = call_user_func(
                $callback,
                $this->request,
                $this->response,
                $this->service,
                $this->app,
                $this, // Pass the Klein instance
                $matched,
                $methods_matched
            );

            if ($returned instanceof Response) {
                $this->response = $returned;
            } else {
                $this->response->append($returned);
            }
        } catch (LockedResponseException $e) {
            // Do nothing, since this is an automated behavior
        } catch (DispatchHaltedException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->error($e);
        }
    }

    /**
     * Adds an error callback to the stack of error handlers
     *
     * @param callable $callback            The callable function to execute in the error handling chain
     * @access public
     * @return boolean|void
     */
    public function onError($callback)
    {
        $this->errorCallbacks[] = $callback;
    }

    /**
     * Routes an exception through the error callbacks
     *
     * @param Exception $err        The exception that occurred
     * @throws UnhandledException   If the error/exception isn't handled by an error callback
     * @access protected
     * @return void
     */
    protected function error(Exception $err)
    {
        $type = get_class($err);
        $msg = $err->getMessage();

        if (count($this->errorCallbacks) > 0) {
            foreach (array_reverse($this->errorCallbacks) as $callback) {
                if (is_callable($callback)) {
                    if (is_string($callback)) {
                        if ($callback($this, $msg, $type, $err)) {
                            return;
                        }
                    } else {
                        if (call_user_func($callback, $this, $msg, $type, $err)) {
                            return;
                        }
                    }
                } else {
                    if (null !== $this->service && null !== $this->response) {
                        $this->service->flash($err);
                        $this->response->redirect($callback);
                    }
                }
            }
        } else {
            $this->response->code(500);
            throw new UnhandledException($err);
        }
    }


    /**
     * Method aliases
     */

    /**
     * Quick alias to skip the current callback/route method from executing
     *
     * @throws DispatchHaltedException To halt/skip the current dispatch loop
     * @access public
     * @return void
     */
    public function skipThis()
    {
        throw new DispatchHaltedException(null, DispatchHaltedException::SKIP_THIS);
    }

    /**
     * Quick alias to skip the next callback/route method from executing
     *
     * @param int $num The number of next matches to skip
     * @throws DispatchHaltedException To halt/skip the current dispatch loop
     * @access public
     * @return void
     */
    public function skipNext($num = 1)
    {
        $skip = new DispatchHaltedException(null, DispatchHaltedException::SKIP_NEXT);
        $skip->setNumberOfSkips($num);

        throw $skip;
    }

    /**
     * Quick alias to stop the remaining callbacks/route methods from executing
     *
     * @throws DispatchHaltedException To halt/skip the current dispatch loop
     * @access public
     * @return void
     */
    public function skipRemaining()
    {
        throw new DispatchHaltedException(null, DispatchHaltedException::SKIP_REMAINING);
    }

    /**
     * Alias to set a response code, lock the response, and halt the route matching/dispatching
     *
     * @param int $code     Optional HTTP status code to send
     * @throws DispatchHaltedException To halt/skip the current dispatch loop
     * @access public
     * @return void
     */
    public function abort($code = null)
    {
        if (null !== $code) {
            $this->response->code($code);
        }

        // Disallow further response modification
        $this->response->lock();

        throw new DispatchHaltedException();
    }

    /**
     * GET alias for "respond()"
     *
     * @param string $route
     * @param callable $callback
     * @access public
     * @return callable
     */
    public function get($route = '*', $callback = null)
    {
        $args = func_get_args();
        $callback = array_pop($args);
        $route = array_pop($args);

        return $this->respond('GET', $route, $callback);
    }

    /**
     * POST alias for "respond()"
     *
     * @param string $route
     * @param callable $callback
     * @access public
     * @return callable
     */
    public function post($route = '*', $callback = null)
    {
        $args = func_get_args();
        $callback = array_pop($args);
        $route = array_pop($args);

        return $this->respond('POST', $route, $callback);
    }

    /**
     * PUT alias for "respond()"
     *
     * @param string $route
     * @param callable $callback
     * @access public
     * @return callable
     */
    public function put($route = '*', $callback = null)
    {
        $args = func_get_args();
        $callback = array_pop($args);
        $route = array_pop($args);

        return $this->respond('PUT', $route, $callback);
    }

    /**
     * DELETE alias for "respond()"
     *
     * @param string $route
     * @param callable $callback
     * @access public
     * @return callable
     */
    public function delete($route = '*', $callback = null)
    {
        $args = func_get_args();
        $callback = array_pop($args);
        $route = array_pop($args);

        return $this->respond('DELETE', $route, $callback);
    }
}
