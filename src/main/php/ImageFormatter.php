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
use stubbles\lang\ResourceLoader;
use stubbles\webapp\response\Headers;
use stubbles\webapp\response\format\Formatter;
/**
 * Formatter which sets an image into response.
 *
 * @since  3.0.0
 */
class ImageFormatter implements Formatter
{
    /**
     * @type  ResourceLoader
     */
    private $resourceLoader;
    /**
     * image to be displayed in case of errors
     *
     * @type  string
     */
    private $errorImgResource;

    /**
     * constructor
     *
     * @param  \stubbles\lang\ResourceLoader  $resourceLoader
     * @param  string                         $errorImgResource  optional  image to be displayed in case of errors
     * @Inject
     * @Property{errorImgResource}('stubbles.img.error')
     */
    public function __construct(ResourceLoader $resourceLoader, $errorImgResource = 'pixel.png')
    {
        $this->resourceLoader   = $resourceLoader;
        $this->errorImgResource = $errorImgResource;
    }

    /**
     * formats resource for response
     *
     * @param   string|\stubbles\img\Image         $resource
     * @param   \stubbles\webapp\response\Headers  $headers
     * @return  string
     */
    public function format($resource, Headers $headers)
    {
        if ($resource instanceof Image) {
            return $resource;
        }

        return $this->resourceLoader->load(
                $resource,
                function($fileName) { return Image::load($fileName); }
        );
    }

    /**
     * write error message about 403 Forbidden error
     *
     * @return  string
     */
    public function formatForbiddenError()
    {
        return $this->loadErrorImage();
    }

    /**
     * write error message about 404 Not Found error
     *
     * @return  string
     */
    public function formatNotFoundError()
    {
        return $this->loadErrorImage();
    }

    /**
     * write error message about 405 Method Not Allowed error
     *
     * @param   string    $requestMethod   original request method
     * @param   string[]  $allowedMethods  list of allowed methods
     * @return  string
     */
    public function formatMethodNotAllowedError($requestMethod, array $allowedMethods)
    {
        return $this->loadErrorImage();
    }

    /**
     * write error message about 500 Internal Server error
     *
     * @param   string  $message
     * @return  string
     */
    public function formatInternalServerError($message)
    {
        return $this->loadErrorImage();
    }

    /**
     * loads error image
     *
     * @return  \stubbles\img\Image
     */
    private function loadErrorImage()
    {
        return $this->resourceLoader->load(
                $this->errorImgResource,
                function($fileName) { return Image::load($fileName); }
        );
    }
}
