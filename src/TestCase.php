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
declare(strict_types=1);

/**
 * @namespace
 */
namespace Omega\Testing;

/**
 * @use
 */
use Closure;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Throwable;

/**
 * Test case class.
 *
 * The `TestCase` class extends the The `PHPUnit\Framework\TestCase` and provides additional
 * assertion methods to simplify testing scenrarios where specific exceptions are expexted
 * to be throw.
 *
 * @category    Omega
 * @package     Omega\Testing
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class TestCase extends BaseTestCase
{
    /**
     * Assert that a specific exception type is thrown during the execution of a risky operation.
     *
     * This method expects a closure that performs a risky operation, and it asserts that the
     * specified exception type is thrown during the execution of that operation.
     *
     * @param  Closure $risky         Holds the closure representing the risky operation.
     * @param  string  $exceptionType Holds the fully qualified class name of the expected exception.
     * @return array Return an array containing the thrown exception and the result of the risky operation.
     */
    protected function assertExceptionThrown( Closure $risky, string $exceptionType ) : array
    {
        $result = null;
        $exception = null;

        try {
            $result = $risky();
            $this->fail( 'Exception was not thrown' );
        } catch ( Throwable $e ) {
            $actualType = $e::class;

            if ( $actualType !== $exceptionType ) {
                $this->fail( "exception was {$actualType}, but expected {$exceptionType}" );
            }

            $exception = $e;
        }

        return [ $exception, $result ];
    }
}