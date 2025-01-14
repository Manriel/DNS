<?php

/*
 * This file is part of Badcow DNS Library.
 *
 * (c) Samuel Williams <sam@badcow.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badcow\DNS\Tests;

use Badcow\DNS\Classes;
use Badcow\DNS\Zone;
use Badcow\DNS\ResourceRecord;
use Badcow\DNS\Rdata\Factory;
use Badcow\DNS\AlignedBuilder;
use Badcow\DNS\ZoneBuilder;

class ZoneTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Zone "example.com" is not a fully qualified domain name.
     */
    public function testSetName()
    {
        $zone = new Zone();
        $zone->setName('example.com.');
        $this->assertEquals('example.com.', $zone->getName());

        //Should throw exception
        $zone->setName('example.com');
    }

    public function testFillOut()
    {
        $zone = new Zone('example.com.');
        $zone->setDefaultTtl(3600);

        $soa = new ResourceRecord();
        $soa->setName('@');
        $soa->setRdata(Factory::Soa(
            '@',
            'post',
            '2014110501',
            3600,
            14400,
            604800,
            3600
        ));

        $soa->setClass(\Badcow\DNS\Classes::INTERNET);

        $ns1 = new ResourceRecord();
        $ns1->setName('@');
        $ns1->setRdata(Factory::Ns('ns1.nameserver.com.'));

        $ns2 = new ResourceRecord();
        $ns2->setName('@');
        $ns2->setRdata(Factory::Ns('ns2.nameserver.com.'));

        $a = new ResourceRecord();
        $a->setName('sub.domain');
        $a->setRdata(Factory::A('192.168.1.42'));
        $a->setComment('This is a local ip.');

        $a6 = new ResourceRecord();
        $a6->setName('ipv6.domain');
        $a6->setRdata(Factory::Aaaa('::1'));
        $a6->setComment('This is an IPv6 domain.');

        $mx1 = new ResourceRecord();
        $mx1->setName('@');
        $mx1->setRdata(Factory::Mx(10, 'mail-gw1.example.net.'));

        $mx2 = new ResourceRecord();
        $mx2->setName('@');
        $mx2->setRdata(Factory::Mx(20, 'mail-gw2.example.net.'));

        $mx3 = new ResourceRecord();
        $mx3->setName('@');
        $mx3->setRdata(Factory::Mx(30, 'mail-gw3.example.net.'));

        $loc = new ResourceRecord();
        $loc->setName('canberra');
        $loc->setRdata(Factory::Loc(
            -35.3075,   //Lat
            149.1244,   //Lon
            500,        //Alt
            20.12,      //Size
            200.3,      //HP
            300.1       //VP
        ));
        $loc->setComment('This is Canberra');

        $zone->fromList($loc, $mx2);
        $zone->addResourceRecord($soa);
        $zone->addResourceRecord($ns1);
        $zone->addResourceRecord($mx3);
        $zone->addResourceRecord($a);
        $zone->addResourceRecord($a6);
        $zone->addResourceRecord($ns2);
        $zone->addResourceRecord($mx1);

        $apl = new \Badcow\DNS\Rdata\APL();
        $apl->addAddressRange(['version' => 4, 'first_ip' => '192.168.0.0', 'prefix' => 23]);
        $apl->addAddressRange(['version' => 4, 'first_ip' => '192.168.1.64', 'prefix' => 28], false);
        $apl->addAddressRange(['version' => 6, 'first_ip' => '2001:acad:1::', 'prefix' => 112], true);
        $apl->addAddressRange(['version' => 6, 'first_ip' => '2001:acad:1::8', 'prefix' => 128], false);

        $multicast = new ResourceRecord('multicast', $apl);

        $zone->addResourceRecord($multicast);

        ZoneBuilder::fillOutZone($zone);
        $expectation = file_get_contents(__DIR__.'/Resources/example.com_filled-out.txt');

        $this->assertEquals($expectation, AlignedBuilder::build($zone));
    }

    public function testOtherFunctions()
    {
        $zone = $this->buildTestZone();
        $this->assertCount(13, $zone);
        $this->assertFalse($zone->isEmpty());

        $rr = $zone->getResourceRecords()[0];
        $this->assertTrue($zone->contains($rr));
        $this->assertTrue($zone->remove($rr));
        $this->assertFalse($zone->remove($rr));
        $this->assertFalse($zone->contains($rr));
    }

    public function testGetClassReturnsDefaultClass()
    {
        $h1 = new ResourceRecord('host1');
        $h2 = new ResourceRecord('host2');
        $h3 = new ResourceRecord('host3');
        $zone = new Zone('example.com.');
        $zone->fromList($h1, $h2, $h3);

        $this->assertNull($h1->getClass());
        $this->assertNull($h2->getClass());
        $this->assertNull($h3->getClass());

        $this->assertEquals(Classes::INTERNET, $zone->getClass());
    }
}
