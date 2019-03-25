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
 * @see https://tools.ietf.org/html/rfc1035#section-3.3.9
 */
class MX implements RdataInterface
{
    use RdataTrait;

    const TYPE = 'MX';

    /**
     * @var int
     */
    private $preference;

    /**
     * @var string
     */
    private $exchange;

    /**
     * @param string $exchange
     */
    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
    }

    /**
     * @return string
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @param int $preference
     */
    public function setPreference($preference)
    {
        $this->preference = $preference;
    }

    /**
     * @return int
     */
    public function getPreference()
    {
        return $this->preference;
    }

    /**
     * {@inheritdoc}
     */
    public function output()
    {
        return $this->preference.' '.$this->exchange;
    }
}
