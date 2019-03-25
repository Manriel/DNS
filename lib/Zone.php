<?php

/*
 * This file is part of Badcow DNS Library.
 *
 * (c) Samuel Williams <sam@badcow.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badcow\DNS;

class Zone implements \Countable, \IteratorAggregate
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $defaultTtl;

    /**
     * @var ResourceRecord[]
     */
    private $resourceRecords = [];

    /**
     * Zone constructor.
     *
     * @param string $name
     * @param int    $defaultTtl
     * @param array  $resourceRecords
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($name = null, $defaultTtl = null, array $resourceRecords = [])
    {
        $this->name = $name;
        $this->defaultTtl = $defaultTtl;
        $this->fromArray($resourceRecords);
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        if (!Validator::fullyQualifiedDomainName($name)) {
            throw new \InvalidArgumentException(sprintf('Zone "%s" is not a fully qualified domain name.', $name));
        }

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDefaultTtl()
    {
        return $this->defaultTtl;
    }

    /**
     * @param int $defaultTtl
     */
    public function setDefaultTtl($defaultTtl)
    {
        $this->defaultTtl = $defaultTtl;
    }

    /**
     * @return ResourceRecord[]
     */
    public function getResourceRecords()
    {
        return $this->resourceRecords;
    }

    /**
     * @param ResourceRecord[] $resourceRecords
     */
    public function fromArray(array $resourceRecords)
    {
        foreach ($resourceRecords as $resourceRecord) {
            $this->addResourceRecord($resourceRecord);
        }
    }

    /**
     * @param ResourceRecord ...$resourceRecords
     */
    public function fromList(ResourceRecord ...$resourceRecords)
    {
        $this->fromArray($resourceRecords);
    }

    /**
     * @param ResourceRecord $resourceRecord
     */
    public function addResourceRecord(ResourceRecord $resourceRecord)
    {
        $this->resourceRecords[] = $resourceRecord;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->resourceRecords);
    }

    /**
     * @return int
     */
    public function count()
    {
        return \count($this->resourceRecords);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->resourceRecords);
    }

    /**
     * @param ResourceRecord $resourceRecord
     *
     * @return bool
     */
    public function contains(ResourceRecord $resourceRecord)
    {
        foreach ($this->resourceRecords as $_item) {
            if ($_item === $resourceRecord) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ResourceRecord $resourceRecord
     *
     * @return bool
     */
    public function remove(ResourceRecord $resourceRecord)
    {
        foreach ($this->resourceRecords as $key => $_item) {
            if ($_item === $resourceRecord) {
                unset($this->resourceRecords[$key]);

                return true;
            }
        }

        return false;
    }

    /**
     * Return the class of the zone, defaults to 'IN'.
     *
     * @return string
     */
    public function getClass()
    {
        foreach ($this->resourceRecords as $resourceRecord) {
            if (null !== $resourceRecord->getClass()) {
                return $resourceRecord->getClass();
            }
        }

        return Classes::INTERNET;
    }
}
