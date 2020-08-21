<?php
namespace InterNations\Component\HttpMock\Tests\Request;

use InterNations\Component\HttpMock\Request\UnifiedRequest;
use InterNations\Component\Testing\AbstractTestCase;
use GuzzleHttp\Message\RequestInterface;
use PHPUnit\Framework\MockObject\MockObject;

class UnifiedRequestTest extends AbstractTestCase
{
    /** @var RequestInterface|MockObject */
    private $wrappedRequest;

    /** @var UnifiedRequest */
    private $unifiedRequest;

    /** @var UnifiedRequest */
    private $unifiedEnclosingEntityRequest;

    public function setUp()
    {
        $this->wrappedRequest = $this->createMock('GuzzleHttp\Message\RequestInterface');
        $this->unifiedRequest = new UnifiedRequest($this->wrappedRequest);
    }

    public static function provideMethods()
    {
        return [
            ['getHeaders'],
            ['getQuery'],
            ['getMethod'],
            ['getScheme'],
            ['getHost'],
            ['getProtocolVersion'],
            ['getPath'],
            ['getPort'],
            ['getUrl'],
            ['getHeader', ['header']],
            ['hasHeader', ['header']],
            ['getUrl'],
        ];
    }

    /** @dataProvider provideMethods */
    public function testMethodsFromRequestInterface($method, array $params = [])
    {
        $this->wrappedRequest
            ->expects($this->once())
            ->method($method)
            ->will($this->returnValue('REQ'))
            ->with(...$params);
        $this->assertSame('REQ', call_user_func_array([$this->unifiedRequest, $method], $params));
    }

    public function testUserAgent()
    {
        $this->assertNull($this->unifiedRequest->getUserAgent());

        $unifiedRequest = new UnifiedRequest($this->wrappedRequest, ['userAgent' => 'UA']);
        $this->assertSame('UA', $unifiedRequest->getUserAgent());
    }
}
