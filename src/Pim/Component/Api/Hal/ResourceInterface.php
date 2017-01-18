<?php

namespace Pim\Component\Api\Hal;

/**
 * Interface to manipulate a resource with the HAL format.
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ResourceInterface
{
    /**
     * Get the array of embedded list of resources.
     *
     * @return array
     */
    public function getEmbedded();

    /**
     * Get the links of the resource.
     *
     * @return LinkInterface[]
     */
    public function getLinks();

    /**
     * Get the data.
     *
     * @return array
     */
    public function getData();

    /**
     * Generate the resource into an array with the HAL format.
     *
     * [
     *     'data' => 'my_data',
     *     '_links'       => [
     *         'self'     => [
     *             'href' => 'http://akeneo.com/api/self/id',
     *         ],
     *     ],
     *     '_embedded' => [
     *         'items' => [
     *           [
     *               '_links' => [
     *                   'self' => [
     *                       'href' => 'http://akeneo.com/api/resource/id',
     *                   ],
     *               ],
     *               'data' => 'item_data',
     *           ],
     *         ],
     *     ],
     * ]
     *
     * @return array
     */
    public function toArray();
}
