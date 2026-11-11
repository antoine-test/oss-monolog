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

class EnvironmentProcessorTest extends \Monolog\Test\MonologTestCase
{
    public function testProcessor()
    {
        $processor = new EnvironmentProcessor('staging');
        $record = $processor($this->getRecord());

        $this->assertSame('staging', $record->extra['environment']);
    }

    public function testCustomKey()
    {
        $processor = new EnvironmentProcessor('staging', 'env');
        $record = $processor($this->getRecord());

        $this->assertSame('staging', $record->extra['env']);
        $this->assertArrayNotHasKey('environment', $record->extra);
    }

    public function testFromEnvironmentVariable()
    {
        putenv('MONOLOG_TEST_ENV=testing');

        try {
            $record = EnvironmentProcessor::fromEnvironmentVariable('MONOLOG_TEST_ENV')($this->getRecord());
        } finally {
            putenv('MONOLOG_TEST_ENV');
        }

        $this->assertSame('testing', $record->extra['environment']);
    }

    public function testFromEnvironmentVariableFallsBackToDefault()
    {
        putenv('MONOLOG_TEST_ENV');

        $record = EnvironmentProcessor::fromEnvironmentVariable('MONOLOG_TEST_ENV', 'dev')($this->getRecord());

        $this->assertSame('dev', $record->extra['environment']);
    }
}
