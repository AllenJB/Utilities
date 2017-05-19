<?php

namespace AllenJB\Utilities;

use PHPUnit\Framework\TestCase;

class UTF8Test extends TestCase
{

    public function testTrim() : void
    {
        $this->assertEquals("", UTF8::trim(null));
        $this->assertEquals("", UTF8::trim(""));
        $this->assertEquals("", UTF8::trim(" "));
        $this->assertEquals("", UTF8::trim("\r"));
        $this->assertEquals("", UTF8::trim("\n"));
        $this->assertEquals("", UTF8::trim("\t"));
        $this->assertEquals("", UTF8::trim("\u{1680}"));
        $this->assertEquals("", UTF8::trim("\u{180E}"));
        $this->assertEquals("", UTF8::trim("\u{2000}"));
        $this->assertEquals("", UTF8::trim("\u{2001}"));
        $this->assertEquals("", UTF8::trim("\u{2002}"));
        $this->assertEquals("", UTF8::trim("\u{2003}"));
        $this->assertEquals("", UTF8::trim("\u{2004}"));
        $this->assertEquals("", UTF8::trim("\u{2005}"));
        $this->assertEquals("", UTF8::trim("\u{2006}"));
        $this->assertEquals("", UTF8::trim("\u{2007}"));
        $this->assertEquals("", UTF8::trim("\u{2008}"));
        $this->assertEquals("", UTF8::trim("\u{2009}"));
        $this->assertEquals("", UTF8::trim("\u{200A}"));
        $this->assertEquals("", UTF8::trim("\u{202F}"));
        $this->assertEquals("", UTF8::trim("\u{205F}"));
        $this->assertEquals("", UTF8::trim("\u{3000}"));

        $this->assertEquals("", UTF8::trim("\u{200B}"));
        $this->assertEquals("", UTF8::trim("\u{FEFF}"));

        $this->assertEquals("a", UTF8::trim(" a"));
        $this->assertEquals("a", UTF8::trim("a "));
        $this->assertEquals("a", UTF8::trim(" a "));
    }

}
