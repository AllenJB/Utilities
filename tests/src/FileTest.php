<?php

namespace AllenJB\Utilities;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{

    public function testHuman2Bytes() : void
    {
        $this->assertEquals('0', File::human2bytes('0'));
        $this->assertEquals('1', File::human2bytes('1'));
        $this->assertEquals('1024', File::human2bytes('1k'));
        $this->assertEquals('1024', File::human2bytes('1K'));
        $this->assertEquals('1048576', File::human2bytes('1m'));
        $this->assertEquals('1048576', File::human2bytes('1M'));
        $this->assertEquals('1073741824', File::human2bytes('1g'));
        $this->assertEquals('1073741824', File::human2bytes('1G'));

        $this->assertEquals('1099511627776', File::human2bytes('1024g'));
    }


    public function testBytes2Human() : void
    {
        $this->assertEquals('0 bytes', File::bytes2human(0));
        $this->assertEquals('1 bytes', File::bytes2human(1));
        $this->assertEquals('1 KB', File::bytes2human(1024));
        $this->assertEquals('1 KB', File::bytes2human(1535));
        $this->assertEquals('2 KB', File::bytes2human(1536));
        $this->assertEquals('1 MB', File::bytes2human(1048576));
        $this->assertEquals('1 GB', File::bytes2human(1073741824));
        $this->assertEquals('1,024 GB', File::bytes2human(1099511627776));
    }

}
