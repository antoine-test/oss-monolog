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

class SensitiveContextProcessorTest extends \Monolog\Test\MonologTestCase
{
    public function testMasksSensitiveKeys()
    {
        $processor = new SensitiveContextProcessor();
        $record = $processor($this->getRecord(context: ['user' => 'bob', 'password' => 'hunter2']));

        $this->assertSame(['user' => 'bob', 'password' => '***'], $record->context);
    }

    public function testCustomKeys()
    {
        $processor = new SensitiveContextProcessor(['iban']);
        $record = $processor($this->getRecord(context: ['iban' => 'FR76', 'password' => 'visible']));

        $this->assertSame(['iban' => '***', 'password' => 'visible'], $record->context);
    }

    public function testObjectsAreEncodedAsJson()
    {
        $processor = new SensitiveContextProcessor();
        $record = $processor($this->getRecord(context: ['payload' => (object) ['a' => 1]]));

        $this->assertSame('{"a":1}', $record->context['payload']);
    }

    public function testReportsMemoryHeadroom()
    {
        $processor = new SensitiveContextProcessor();
        $record = $processor($this->getRecord());

        $this->assertArrayHasKey('memory_headroom', $record->extra);
    }
}
