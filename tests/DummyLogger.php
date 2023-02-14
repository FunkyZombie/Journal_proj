<?php
namespace Journal\Blog\UnitTests;

use Psr\Log\LoggerInterface;
use Stringable;

final class DummyLogger implements LoggerInterface
{
	/**
	 * System is unusable.
	 *
	 * @param Stringable|string $message
	 * @param array $context
	 * @return void
	 */
	public function emergency(Stringable|string $message, array $context = array()): void {
	}
	
	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param Stringable|string $message
	 * @param array $context
	 * @return void
	 */
	public function alert(Stringable|string $message, array $context = array()): void {
	}
	
	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param Stringable|string $message
	 * @param array $context
	 * @return void
	 */
	public function critical(Stringable|string $message, array $context = array()): void {
	}
	
	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param Stringable|string $message
	 * @param array $context
	 * @return void
	 */
	public function error(Stringable|string $message, array $context = array()): void {
	}
	
	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param Stringable|string $message
	 * @param array $context
	 * @return void
	 */
	public function warning(Stringable|string $message, array $context = array()): void {
	}
	
	/**
	 * Normal but significant events.
	 *
	 * @param Stringable|string $message
	 * @param array $context
	 * @return void
	 */
	public function notice(Stringable|string $message, array $context = array()): void {
	}
	
	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param Stringable|string $message
	 * @param array $context
	 * @return void
	 */
	public function info(Stringable|string $message, array $context = array()): void {
	}
	
	/**
	 * Detailed debug information.
	 *
	 * @param Stringable|string $message
	 * @param array $context
	 * @return void
	 */
	public function debug(Stringable|string $message, array $context = array()): void {
	}
	
	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed $level
	 * @param Stringable|string $message
	 * @param array $context
	 * @return void
	 */
	public function log($level, Stringable|string $message, array $context = array()): void {
	}
}