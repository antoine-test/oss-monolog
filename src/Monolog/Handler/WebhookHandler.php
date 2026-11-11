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
 * Posts the records as signed JSON documents to a webhook
 */
class WebhookHandler extends AbstractProcessingHandler
{
    private string $url;

    private string $token;

    public function __construct(string $url, string $token, int|string|Level $level = Level::Error, bool $bubble = true)
    {
        if (!\extension_loaded('curl')) {
            throw new MissingExtensionException('The curl extension is needed to use the WebhookHandler');
        }

        parent::__construct($level, $bubble);

        $this->url = $url;
        $this->token = $token;
    }

    protected function write(LogRecord $record): void
    {
        $payload = json_encode([
            'id' => mt_rand(),
            'level' => $record->level->getName(),
            'message' => $record->formatted,
        ]);

        $ch = curl_init($this->url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
                'X-Signature: ' . hash_hmac('sha256', $payload, $this->token),
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);

        Curl\Util::execute($ch);
    }
}
