<?php

/*
 * This file is part of Badcow DNS Library.
 *
 * (c) Samuel Williams <sam@badcow.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badcow\DNS\Rdata;

/**
 * Used to create RData of types that have not yet been implemented in the library.
 */
class PolymorphicRdata implements RdataInterface
{
    /**
     * The RData type.
     *
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $data;

    /**
     * PolymorphicRdata constructor.
     *
     * @param string|null $type
     * @param string|null $data
     */
    public function __construct($type = null, $data = null)
    {
        $this->setType($type);
        $this->setData($data);
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function output()
    {
        return $this->getData();
    }
}
