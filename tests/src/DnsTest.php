<?php

namespace AllenJB\Utilities;

use PHPUnit\Framework\TestCase;

class DnsTest extends TestCase
{

    public function testIsValidIP4() : void
    {
        $this->assertTrue(Dns::isValidIP4('8.8.8.8'));
        $this->assertTrue(Dns::isValidIP4('169.254.1.1'));
        $this->assertTrue(Dns::isValidIP4('192.168.1.1'));
        $this->assertTrue(Dns::isValidIP4('127.0.0.1'));
        $this->assertTrue(Dns::isValidIP4('10.0.0.1'));
        $this->assertFalse(Dns::isValidIP4('192.168.1.1/24'));
        $this->assertFalse(Dns::isValidIP4('1.1.1.256'));
        $this->assertFalse(Dns::isValidIP4('0.0.0.0'));
        $this->assertFalse(Dns::isValidIP4('192.168.1.'));
        $this->assertFalse(Dns::isValidIP4('192.168.1'));
        $this->assertFalse(Dns::isValidIP4('192.168.1.1.'));
        $this->assertFalse(Dns::isValidIP4('192.168.1.1.1'));
        $this->assertFalse(Dns::isValidIP4('a.b.c.d'));
        $this->assertFalse(Dns::isValidIP4('a'));
        $this->assertFalse(Dns::isValidIP4(''));
        $this->assertFalse(Dns::isValidIP4('fe80::216:3eff:febd:948f/64'));
    }


    public function testIsValidIP6() : void
    {
        $this->assertTrue(Dns::isValidIP6('::1'));
        $this->assertFalse(Dns::isValidIP6('::'));
        $this->assertFalse(Dns::isValidIP6('::/0'));
        $this->assertTrue(Dns::isValidIP6('2001:0db8:85a3:0000:0000:8a2e:0370:733'));
        $this->assertFalse(Dns::isValidIP6('fe80::216:3eff:febd:948f/64'));
        $this->assertFalse(Dns::isValidIP6('192.168.1.1'));
        $this->assertFalse(Dns::isValidIP6('a'));
        $this->assertFalse(Dns::isValidIP6(''));
    }


    public function testIsValidIp() : void
    {
        $this->assertTrue(Dns::isValidIP('8.8.8.8'));
        $this->assertTrue(Dns::isValidIP('192.168.1.1'));
        $this->assertTrue(Dns::isValidIP('127.0.0.1'));
        $this->assertTrue(Dns::isValidIP('10.0.0.1'));
        $this->assertTrue(Dns::isValidIP('::1'));
        $this->assertTrue(Dns::isValidIP('2001:0db8:85a3:0000:0000:8a2e:0370:733'));
        $this->assertFalse(Dns::isValidIP('fe80::216:3eff:febd:948f/64'));
        $this->assertFalse(Dns::isValidIP('1.1.1.256'));
        $this->assertFalse(Dns::isValidIP('0.0.0.0'));
        $this->assertFalse(Dns::isValidIP('192.168.1.'));
        $this->assertFalse(Dns::isValidIP('192.168.1'));
        $this->assertFalse(Dns::isValidIP('192.168.1.1.'));
        $this->assertFalse(Dns::isValidIP('192.168.1.1.1'));
        $this->assertFalse(Dns::isValidIP('a.b.c.d'));
        $this->assertFalse(Dns::isValidIP('a'));
        $this->assertFalse(Dns::isValidIP(''));
    }


    public function testIsReservedIp() : void
    {
        $this->assertTrue(Dns::isReservedIp('169.254.1.1'));
        $this->assertTrue(Dns::isReservedIp('192.168.1.1'));
        $this->assertTrue(Dns::isReservedIp('127.0.0.1'));
        $this->assertTrue(Dns::isReservedIp('0.0.0.0'));
        $this->assertTrue(Dns::isReservedIp('10.0.0.1'));
        $this->assertTrue(Dns::isReservedIp('192.0.0.1'));
        $this->assertTrue(Dns::isReservedIp('192.0.2.1'));
        $this->assertTrue(Dns::isReservedIp('198.51.100.1'));
        $this->assertTrue(Dns::isReservedIp('203.0.113.1'));
        $this->assertTrue(Dns::isReservedIp('::1'));
        $this->assertTrue(Dns::isReservedIp('::'));
        $this->assertTrue(Dns::isReservedIp('2001:0db8:85a3:0000:0000:8a2e:0370:733'));

        $this->assertFalse(Dns::isReservedIp('8.8.8.8'));
        $this->assertFalse(Dns::isReservedIp('2001:0:9d38:6abd:3c34:325f:da14:8501'));
    }

    public function testGetIpFromServerArray() : void
    {
        $server = [];
        $this->assertEquals(null, Dns::getIpFromServerArray($server));

        $server = [
            'REMOTE_ADDR' => '192.168.1.1',
        ];

        $this->assertEquals('192.168.1.1', Dns::getIpFromServerArray($server));

        $server = [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_CLUSTER_CLIENT_IP' => '192.168.1.2',
        ];
        $this->assertEquals('192.168.1.2', Dns::getIpFromServerArray($server));

        $server = [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.2',
        ];
        $this->assertEquals('192.168.1.2', Dns::getIpFromServerArray($server));

        $server = [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_CLIENT_IP' => '192.168.1.2',
        ];
        $this->assertEquals('192.168.1.2', Dns::getIpFromServerArray($server));

        $server = [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_CLIENT_IP' => '192.168.1.2',
        ];
        $this->assertEquals('192.168.1.2', Dns::getIpFromServerArray($server));

        $server = [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_CLUSTER_CLIENT_IP' => '192.168.1.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3',
        ];
        $this->assertEquals('192.168.1.2', Dns::getIpFromServerArray($server));

        $server = [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_CLUSTER_CLIENT_IP' => '192.168.1.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3',
        ];
        $proxyIps = ['192.168.1.2'];
        $this->assertEquals('192.168.1.3', Dns::getIpFromServerArray($server, $proxyIps));
    }

}
