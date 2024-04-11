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
use function Omega\Helpers\app;
use function Omega\Helpers\env;
use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Runner\AfterLastTestHook;
use Symfony\Component\Process\Process;

/**
 * Server extension class.
 *
 * The `ServerExtension` class provides the ability to start and stop a local development server
 *  before and after running PHPUnit tests. It is useful for scenarios where tests
 *  require a live server environment.
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
final class ServerExtension implements BeforeFirstTestHook, AfterLastTestHook
{
    /**
     * The Symfony Process instance responsible for managing the server.
     *
     * @var Process $process Holds the Symfony Process instance responsible for managing the server.
     */
    private Process $process;

    /**
     * Flag indicating whether the server has been started during the test suite.
     *
     * @var bool $startedServer Flag indicating whether the server has been started during the test suite.
     */
    private bool $startedServer = false;

    /**
     * Start the local development server.
     *
     * Checks if the server is already running and starts it if not. The server is started
     * based on the configuration specified in the .env file.
     *
     * @return void
     */
    private function startServer() : void
    {
        if ( $this->serverIsRunning() ) {
            $this->startedServer = false;
            return;
        }

        $this->startedServer = true;

        $base = app( 'paths.base' );

        $this->process = new Process( [
            PHP_BINARY,
            $base . "/omega",
            "serve"
        ], $base );

        $this->process->start( function ( $type, $buffer ) {
            print $buffer;
        } );
    }

    /**
     * Check if the local development server is running.
     *
     * Attempts to establish a connection to the server. If successful, it indicates
     * that the server is running; otherwise, it is considered not running.
     *
     * @return bool Return true if the server is running, false otherwise.
     */
    private function serverIsRunning() : bool
    {
        $port       = filter_var( env( 'APP_PORT' ), FILTER_VALIDATE_INT );
        $connection = '';

        if ( $port !== false ) {
            $connection = @fsockopen( env( 'APP_HOST' ), $port );
        }

        if ( is_resource( $connection ) ) {
            fclose( $connection );
            return true;
        }

        return false;
    }

    /**
     * Stop the local development server if it was started during the test suite.
     *
     * Sends a termination signal to the server process, stopping it gracefully.
     *
     * @return void
     */
    private function stopServer() : void
    {
        if ( $this->startedServer ) {
            $this->process->signal( SIGTERM );
        }
    }

    /**
     * Execute actions before the first test is run.
     *
     * Starts the local development server.
     *
     * @return void
     */
    public function executeBeforeFirstTest() : void
    {
        $this->startServer();
    }

    /**
     * Execute actions after the last test is run.
     *
     * Stops the local development server if it was started.
     *
     * @return void
     */
    public function executeAfterLastTest() : void
    {
        $this->stopServer();
    }
}