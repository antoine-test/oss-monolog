<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\LogRecord;

/**
 * Adds the name of the application environment to the extra data of each record
 */
class EnvironmentProcessor implements ProcessorInterface
{
    public function __construct(private readonly string $environment, private readonly string $key = 'environment')
    {
    }

    /**
     * Reads the environment name from an environment variable
     */
    public static function fromEnvironmentVariable(string $variable = 'APP_ENV', string $default = 'production'): self
    {
        $value = getenv($variable);

        return new self($value === false || $value === '' ? $default : $value);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra[$this->key] = $this->environment;

        return $record;
    }
}
