<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\webapp\img
 */
namespace stubbles\webapp\img;
use stubbles\img\Image;
use stubbles\img\driver\DummyDriver;
use stubbles\lang\Rootpath;
use stubbles\peer\http\Http;
use stubbles\peer\http\HttpVersion;
/**
 * Test for sstubbles\webapp\img\DefaultImageResponse.
 */
class DefaultImageResponseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\webapp\img\DefaultImageResponse
     */
    private $defaultImageResponse;
    /**
     * image
     *
     * @type  resource
     */
    private $handle;
    /**
     * image to be used in test
     *
     * @type  \stubbles\img\Image
     */
    private $image;
    /**
     * dummy driver for the image
     *
     * @type  \stubbles\img\driver\DummyDriver
     */
    private $dummyDriver;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultImageResponse = $this->createResponse();
        $rootpath                   = new Rootpath();
        $this->handle               = imagecreatefrompng($rootpath->to('/src/test/resources/empty.png'));
        $this->dummyDriver          = new DummyDriver();
        $this->image                = new Image('test', $this->dummyDriver, $this->handle);
    }

    /**
     * creates response where output facing methods are mocked
     *
     * @param   string  $requestMethod  optional  http request method to use, defaults to GET
     * @return  DefaultImageResponse
     */
    private function createResponse($requestMethod = Http::GET)
    {
        $mockRequest = $this->getMock('stubbles\input\web\WebRequest');
        $mockRequest->expects($this->once())
                    ->method('protocolVersion')
                    ->will($this->returnValue(HttpVersion::fromString(HttpVersion::HTTP_1_1)));
        $mockRequest->expects($this->any())
                    ->method('method')
                    ->will($this->returnValue($requestMethod));
        return $this->getMock(
                'stubbles\webapp\img\DefaultImageResponse',
                ['header', 'sendBody'],
                [$mockRequest]
        );
    }


    /**
     * @test
     */
    public function sendWithoutImageShouldStillWork()
    {
        $this->assertSame(
                $this->defaultImageResponse,
                $this->defaultImageResponse->send()
        );
    }

    /**
     * @test
     */
    public function addingImageSetsContentTypeHeaderToImageContentType()
    {
        $this->defaultImageResponse->expects($this->at(1))
                                   ->method('header')
                                   ->with($this->equalTo('Content-type: ' . $this->dummyDriver->mimeType()));
        $this->defaultImageResponse->write($this->image)
                                   ->send();
    }

    /**
     * @test
     */
    public function sendResponseWithImage()
    {
        $this->defaultImageResponse->write($this->image)->send();
        $this->assertSame(
                $this->handle,
                $this->dummyDriver->lastDisplayedHandle()
        );
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function imageIsNotSendWhenRequestMethodIsHead()
    {
        $this->defaultImageResponse = $this->createResponse(Http::HEAD);
        $this->defaultImageResponse->write($this->image)->send();
        $this->assertNull($this->dummyDriver->lastDisplayedHandle());
    }

    /**
     * @test
     * @since  2.0.2
     */
    public function clearRemovesImageFromResponse()
    {
        $this->defaultImageResponse->write($this->image)->clear()->send();
        $this->assertNull($this->dummyDriver->lastDisplayedHandle());
    }

    /**
     * @test
     * @since  2.0.3
     */
    public function stringBodyIsNeverSendWhenImagePresent()
    {
        $this->defaultImageResponse->expects($this->never())
                                   ->method('sendBody');
        $this->defaultImageResponse->write('something')->write($this->image)->send();
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function writeOverwritesExistingImage()
    {
        $this->defaultImageResponse->expects($this->once())
                                   ->method('sendBody')
                                   ->with($this->equalTo('something'));
        $this->defaultImageResponse->write($this->image)->write('something')->send();
        $this->assertNull($this->dummyDriver->lastDisplayedHandle());
    }
}
