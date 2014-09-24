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
use stubbles\lang;
use stubbles\lang\ResourceLoader;
use stubbles\lang\Rootpath;
use stubbles\webapp\response\Headers;
/**
 * Test for stubbles\webapp\img\ImageFormatter.
 *
 * @group  response
 * @since  3.0.0
 */
class ImageFormatterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\webapp\img\ImageFormatter
     */
    private $imageFormatter;
    /**
     * actual root path
     *
     * @type  Rootpath
     */
    private $rootpath;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rootpath       = new Rootpath();
        $this->imageFormatter = new ImageFormatter(new ResourceLoader());
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = lang\reflectConstructor($this->imageFormatter);
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[1]->hasAnnotation('Property'));
        $this->assertEquals(
                'stubbles.img.error',
                $parameters[1]->getAnnotation('Property')->getValue()
        );
    }

    /**
     * @test
     */
    public function formatReturnsPassedImage()
    {
        $image = Image::load($this->rootpath->to('src/main/resources/pixel.png'));
        $this->assertSame(
                $image,
                $this->imageFormatter->format($image, new Headers())
        );
    }

    /**
     * @test
     */
    public function formatReturnsImageDefinedByGivenResource()
    {
        $this->assertEquals(
                Image::load($this->rootpath->to('src/main/resources/pixel.png'))->fileName(),
                $this->imageFormatter->format('pixel.png', new Headers())->fileName()
        );
    }

    /**
     * data provider for all image formatter methods
     *
     * @return  array
     */
    public function imageFormatterErrorMethods()
    {
        return [['formatForbiddenError', []],
                ['formatNotFoundError', []],
                ['formatMethodNotAllowedError', ['POST', ['HEAD, GET']]],
                ['formatInternalServerError', ['some error message']]
        ];
    }

    /**
     * @test
     * @dataProvider  imageFormatterErrorMethods
     */
    public function returnsImageForAllErrorMethods($method, $params)
    {
        $this->assertEquals(
                Image::load($this->rootpath->to('src/main/resources/pixel.png'))->fileName(),
                call_user_func_array([$this->imageFormatter, $method], $params)->fileName()
        );
    }

    /**
     * @test
     * @dataProvider  imageFormatterErrorMethods
     */
    public function returnsImageForAllErrorMethodsWithDifferentImage($method, $params)
    {
        $imageFormatter = new ImageFormatter(new ResourceLoader(), 'error.png');
        $this->assertEquals(
                Image::load($this->rootpath->to('src/main/resources/error.png'))->fileName(),
                call_user_func_array([$imageFormatter, $method], $params)->fileName()
        );
    }
}
