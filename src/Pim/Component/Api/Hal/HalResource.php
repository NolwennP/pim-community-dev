<?php

namespace Pim\Component\Api\Hal;

/**
 * Basic implementation of a HAL resource.
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class HalResource implements ResourceInterface
{
    /** @var array */
    protected $links = [];

    /** @var array */
    protected $embedded = [];

    /** @var array */
    protected $data = [];

    /**
     * @param string $url      url of the self link
     * @param array  $links    links of the resource
     * @param array  $embedded array of list of embedded resources
     * @param array  $data     additional data
     */
    public function __construct($url, array $links, array $embedded, array $data)
    {
        $selfLink = $this->createSelfLink($url);
        $this->addLink($selfLink);

        foreach ($links as $link) {
            $this->addLink($link);
        }

        foreach ($embedded as $key => $resources) {
            $this->setEmbedded($key, $resources);
        }

        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmbedded()
    {
        return $this->embedded;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $data['_links'] = $this->normalizeLinks($this->links);

        foreach ($this->data as $key => $value) {
            $data[$key] = $value;
        }

        foreach ($this->embedded as $rel => $embedded) {
            $data['_embedded'][$rel] = $this->normalizeEmbedded($embedded);
        }

        return $data;
    }

    /**
     * Normalize a list of embedded resources into an array.
     *
     * @param array $embedded list of embedded resource
     *
     * @return array
     */
    protected function normalizeEmbedded(array $embedded)
    {
        $data = [];
        foreach ($embedded as $embed) {
            $data[] = $embed->toArray();
        }

        return $data;
    }

    /**
     * Normalize the links into an array.
     *
     * @param array $links list of links
     *
     * @return array
     */
    protected function normalizeLinks(array $links)
    {
        $data = [];
        foreach ($links as $link) {
            $data = array_merge($data, $link->toArray());
        }

        return $data;
    }

    /**
     * Add a link in the resource.
     *
     * @param LinkInterface $link
     *
     * @return ResourceInterface
     */
    protected function addLink(LinkInterface $link)
    {
        $this->links[] = $link;

        return $this;
    }

    /**
     * Create a self link.
     *
     * @param string $url
     *
     * @return Link
     */
    protected function createSelfLink($url)
    {
        return new Link('self', $url);
    }

    /**
     * Add a resource in the list of embedded resources for a given key.
     *
     * @param string            $key      key of the list
     * @param ResourceInterface $resource resource to add
     *
     * @return ResourceInterface
     */
    protected function addEmbedded($key, ResourceInterface $resource)
    {
        $this->embedded[$key][] = $resource;

        return $this;
    }

    /**
     * Set the list of embedded resources for a given key.
     *
     * @param string $key       key of the list
     * @param array  $resources array of resources
     *
     * @return ResourceInterface
     */
    protected function setEmbedded($key, array $resources)
    {
        $this->embedded[$key] = [];

        foreach ($resources as $resource) {
            $this->addEmbedded($key, $resource);
        }

        return $this;
    }
}
