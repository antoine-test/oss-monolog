<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Level;
use Monolog\LogRecord;

/**
 * Forwards only the records of the given channels to the wrapped handler
 */
class ChannelFilterHandler extends AbstractProcessingHandler
{
    /** @var list<string> */
    protected array $channels;

    protected HandlerInterface $handler;

    /**
     * @param list<string> $channels Names of the channels to forward
     */
    public function __construct(HandlerInterface $handler, array $channels, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->handler = $handler;
        $this->channels = $channels;
    }

    public function isHandling(LogRecord $record): bool
    {
        return \in_array($record->channel, $this->channels, true) && $record->level->value >= $this->level->value;
    }

    protected function write(LogRecord $record): void
    {
        $this->handler->handle($record);
    }
}
