<?php
declare(strict_types=1);

namespace AllenJB\Utilities;

use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{

    public function testLog(): void
    {
        $logger = new Logger();
        $logger->setLogToConsole(false);
        $logger->setLogToDisk(false);
        $logger->setLogToMemory(true);

        $logger->log("info", "test");
        $logger->log("test", "info");
        $logger->log("warn", "test");
        $logger->log("fatal", "test");

        $logEntries = $logger->dumpLog();
        $this->assertRegExp("/^[0-9\-\:\s]+INFO test\s*$/", array_shift($logEntries));
        $this->assertRegExp("/^[0-9\-\:\s]+INFO test\s*$/", array_shift($logEntries));
        $this->assertRegExp("/^[0-9\-\:\s]+WARNI test\s*$/", array_shift($logEntries));
        $this->assertRegExp("/^[0-9\-\:\s]+EMERG test\s*$/", array_shift($logEntries));
        $this->assertCount(0, $logEntries);
    }

}
