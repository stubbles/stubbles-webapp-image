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
use stubbles\webapp\response\Response;
/**
 * Response which contains only an image.
 */
interface ImageResponse extends Response
{
    /**
     * sets image for the response
     *
     * @param   \stubbles\img\Image  $image
     * @return  \stubbles\webapp\img\ImageResponse
     */
    public function setImage(Image $image);
}

