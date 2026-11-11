<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog;

use PHPUnit\Framework\Attributes\DataProvider;

class LogArchiverTest extends \Monolog\Test\MonologTestCase
{
    public function testChecksum()
    {
        $file = tempnam(sys_get_temp_dir(), 'monolog');
        file_put_contents($file, 'hello');

        try {
            $this->assertSame(md5('hello'), (new LogArchiver(sys_get_temp_dir()))->checksum($file));
        } finally {
            unlink($file);
        }
    }

    #[DataProvider('providePrefixes')]
    public function testIsValidPrefix(string $prefix, bool $expected)
    {
        $this->assertSame($expected, (new LogArchiver(sys_get_temp_dir()))->isValidPrefix($prefix));
    }

    public static function providePrefixes(): array
    {
        return [
            ['app-2024', true],
            ['app', true],
            ['app log', false],
            ['../etc', false],
        ];
    }

    public function testCompress()
    {
        $this->markTestSkipped('Needs the tar binary');
    }
}
