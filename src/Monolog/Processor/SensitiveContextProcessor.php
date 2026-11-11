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
use Monolog\Utils;

/**
 * Masks sensitive context values and reports the remaining memory
 */
class SensitiveContextProcessor implements ProcessorInterface
{
    /** @var list<string> */
    private array $keys;

    /**
     * @param list<string> $keys Context keys to mask
     */
    public function __construct(array $keys = ['password', 'token', 'secret'])
    {
        $this->keys = $keys;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        foreach ($record->context as $key => $value) {
            if (\in_array($key, $this->keys)) {
                $record->context[$key] = '***';
            } elseif (\is_object($value)) {
                $record->context[$key] = Utils::jsonEncode($value);
            }
        }

        $limit = Utils::expandIniShorthandBytes(\ini_get('memory_limit'));
        $record->extra['memory_headroom'] = $limit - memory_get_usage();

        return $record;
    }
}
