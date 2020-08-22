<?php
namespace InterNations\Component\HttpMock;

use GuzzleHttp\Client;
use GuzzleHttp\Event\ErrorEvent;
use hmmmath\Fibonacci\FibonacciFactory;
use Symfony\Component\Process\Process;
use RuntimeException;
use GuzzleHttp\Exception\TransferException;

class Server extends Process
{
    private $port;

    private $host;

    private $client;

    public function __construct($port, $host)
    {
        $this->port = $port;
        $this->host = $host;
        $packageRoot = __DIR__ . '/../';
        $command = [
            'php',
            '-dalways_populate_raw_post_data=-1',
            '-derror_log=',
            '-S=' . $this->getConnectionString(),
            '-t=public/',
            $packageRoot . 'public/index.php',
        ];

        parent::__construct($command, $packageRoot);
        $this->setTimeout(null);
    }

    public function start(callable $callback = null, array $env = [])
    {
        parent::start($callback, $env);

        $this->pollWait();
    }

    public function stop($timeout = 10, $signal = null)
    {
        return parent::stop($timeout, $signal);
    }

    public function getClient()
    {
        return $this->client ?: $this->client = $this->createClient();
    }

    private function createClient()
    {
        $client = new Client(['base_url' => $this->getBaseUrl()]);
        $client->getEmitter()->on(
            'error',
            static function (ErrorEvent $errorEvent) {
                $errorEvent->stopPropagation();
            }
        );

        return $client;
    }

    public function getBaseUrl()
    {
        return sprintf('http://%s', $this->getConnectionString());
    }

    public function getConnectionString()
    {
        return sprintf('%s:%d', $this->host, $this->port);
    }

    /**
     * @param Expectation[] $expectations
     * @throws RuntimeException
     */
    public function setUp(array $expectations)
    {
        /** @var Expectation $expectation */
        foreach ($expectations as $expectation) {
            $response = $this->getClient()->post(
                '/_expectation',
                [
                    'body' => [
                        'matcher'  => serialize($expectation->getMatcherClosures()),
                        'limiter'  => serialize($expectation->getLimiter()),
                        'response' => serialize($expectation->getResponse()),
                    ]
                ]
            );

            if ($response->getStatusCode() !== '201') {
                throw new RuntimeException('Could not set up expectations');
            }
        }
    }

    public function clean()
    {
        if (!$this->isRunning()) {
            $this->start();
        }

        $this->getClient()->delete('/_all');
    }

    private function pollWait()
    {
        foreach (FibonacciFactory::sequence(50000, 10000) as $sleepTime) {
            try {
                usleep($sleepTime);
                $this->getClient()->head('/_me');
                break;
            } catch (TransferException $e) {
                continue;
            }
        }
    }

    public function getIncrementalErrorOutput()
    {
        return self::cleanErrorOutput(parent::getIncrementalErrorOutput());
    }

    public function getErrorOutput()
    {
        return self::cleanErrorOutput(parent::getErrorOutput());
    }

    private static function cleanErrorOutput($output)
    {
        if (!trim($output)) {
            return '';
        }

        $errorLines = [];

        foreach (explode(PHP_EOL, $output) as $line) {
            if (!$line) {
                continue;
            }

            if (!self::stringEndsWithAny($line, ['Accepted', 'Closing', ' started'])) {
                $errorLines[] = $line;
            }
        }

        return $errorLines ? implode(PHP_EOL, $errorLines) : '';
    }

    private static function stringEndsWithAny($haystack, array $needles)
    {
        foreach ($needles as $needle) {
            if (substr($haystack, (-1 * strlen($needle))) === $needle) {
                return true;
            }
        }

        return false;
    }
}
