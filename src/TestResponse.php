<?php
/**
 * Part of Omega CMS - Testing Package
 *
 * @link       https://omegacms.github.io
 * @author     Adriano Giovannini <omegacms@outlook.com>
 * @copyright  Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 */

/**
 * @declare
 */
declare( strict_types = 1 );

/**
 * @namespace
 */
namespace Omega\Testing;

/**
 * @use
 */
use Omega\Application\Application;
use Omega\Http\Response;

/**
 * Test response class.
 *
 * The `TestResponse` class provides utility methods to inspect and interact
 * with HTTP responses during testing.
 *
 * @final
 * @category    Omega
 * @package     Omega\Testing
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class TestResponse
{
    /**
     * The underlying Response instance.
     *
     * @var Response $response Holds an instance of the Response class.
     */
    private Response $response;

    /**
     * Create a new TestResponse instance.
     *
     * @param  Response $response The Response instance to be tested.
     * @return void
     */
    public function __construct( Response $response )
    {
        $this->response = $response;
    }

    /**
     * Check if the response is a redirection.
     *
     * @return bool Returns true if the response is a redirection, false otherwise.
     */
    public function isRedirecting() : bool
    {
        return $this->response->type() === Response::REDIRECT;
    }

    /**
     * Get the URL to which the response is redirecting.
     *
     * @return string|null Returns the URL if the response is a redirection, null otherwise.
     */
    public function redirectingTo() : ?string
    {
        return $this->response->redirect();
    }

    /**
     * Follow redirects until a non-redirect response is received.
     *
     * This method sends additional GET requests following redirects until a non-redirect
     * response is received.
     *
     * @return static Returns the current TestResponse instance after following redirects.
     */
    public function follow() : static
    {
        while ( $this->isRedirecting() ) {
            $_SERVER[ 'REQUEST_METHOD' ] = 'GET';
            $_SERVER[ 'REQUEST_URI' ] = $this->redirectingTo();
            $this->response = Application::getInstance()->bootstrap();
        }

        return $this;
    }

    /**
     * Dynamically call methods on the underlying Response instance.
     *
     * This method allows dynamic method calls on the Response instance, providing
     * flexibility to access other methods of the Response class not explicitly
     * defined in this TestResponse class.
     *
     * @param string $method The name of the method to call.
     * @param array $parameters The parameters to pass to the method.
     * @return mixed Returns the result of the method call on the Response instance.
     */
    public function __call( string $method, array $parameters = [] ) : mixed
    {
        return $this->response->$method( ...$parameters );
    }
}
