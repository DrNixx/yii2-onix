<?php
namespace onix\exceptions;

/**
 * Thrown when an application attempts to use null in a case where a non-null
 * value is required.
 */
class NullPointerException extends \RuntimeException
{
}
