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

class ChannelFilterHandlerTest extends \Monolog\Test\MonologTestCase
{
    public function testForwardsRecordsOfAcceptedChannels()
    {
        $test = new TestHandler();
        $handler = new ChannelFilterHandler($test, ['app']);

        $handler->handle($this->getRecord(Level::Error, 'kept', channel: 'app'));
        $handler->handle($this->getRecord(Level::Error, 'dropped', channel: 'other'));

        $this->assertTrue($test->hasErrorThatContains('kept'));
        $this->assertFalse($test->hasErrorThatContains('dropped'));
    }

    public function testRespectsTheMinimumLevel()
    {
        $test = new TestHandler();
        $handler = new ChannelFilterHandler($test, ['app'], Level::Error);

        $handler->handle($this->getRecord(Level::Warning, 'too low', channel: 'app'));
        $handler->handle($this->getRecord(Level::Critical, 'high enough', channel: 'app'));

        $this->assertFalse($test->hasWarningRecords());
        $this->assertTrue($test->hasCriticalRecords());
    }

    public function testIsHandling()
    {
        $handler = new ChannelFilterHandler(new TestHandler(), ['app']);

        $this->assertTrue($handler->isHandling($this->getRecord(Level::Debug, channel: 'app')));
        $this->assertFalse($handler->isHandling($this->getRecord(Level::Debug, channel: 'other')));
    }
}
