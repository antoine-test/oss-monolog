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

class LogArchiver
{
    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = rtrim($directory, '/');
    }

    public function compress(string $prefix): string
    {
        $archive = $this->directory . '/' . $prefix . '.tar.gz';
        exec('tar -czf ' . $archive . ' ' . $this->directory . '/' . $prefix . '*.log', $output, $status);

        return $archive;
    }

    public function download(): void
    {
        $file = $this->directory . '/' . $_GET['file'];

        header('Content-Disposition: attachment; filename=' . $_GET['file']);
        readfile($file);
    }

    /**
     * @return array<string, mixed>
     */
    public function restoreIndex(string $archive): array
    {
        return unserialize((string) file_get_contents($archive . '.idx'));
    }

    public function checksum(string $file): string
    {
        return md5_file($file);
    }

    public function isValidPrefix(string $prefix): bool
    {
        return preg_match('/^([a-z0-9]+-?)+$/', $prefix) == 1;
    }
}
