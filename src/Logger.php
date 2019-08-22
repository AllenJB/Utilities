<?php
declare(strict_types=1);

namespace AllenJB\Utilities;

const CLI_NORMAL = "\033[0m";
const CLI_BLACK = "\033[0;30m";
const CLI_DGRAY = "\033[1;30m";
const CLI_BLUE = "\033[0;34m";
const CLI_LBLUE = "\033[1;34m";
const CLI_GREEN = "\033[0;32m";
const CLI_LGREEN = "\033[1;32m";
const CLI_CYAN = "\033[0;36m";
const CLI_LCYAN = "\033[1;36m";
const CLI_RED = "\033[0;31m";
const CLI_LRED = "\033[1;31m";
const CLI_PURPLE = "\033[0;35m";
const CLI_LPURPLE = "\033[1;35m";
const CLI_BROWN = "\033[0;33m";
const CLI_YELLOW = "\033[1;33m";
const CLI_LGRAY = "\033[0;37m";
const CLI_WHITE = "\033[1;37m";

const CLI_BG_BLACK = "\033[40m";
const CLI_BG_RED = "\033[41m";
const CLI_BG_GREEN = "\033[42m";
const CLI_BG_YELLOW = "\033[43m";
const CLI_BG_BLUE = "\033[44m";
const CLI_BG_MAGENTA = "\033[45m";
const CLI_BG_CYAN = "\033[46m";
const CLI_BG_LGRAY = "\033[47m";

class Logger
{

    /**
     * @var array All available log levels. The MUST be in order.
     * Note: Keys and values are flipped by the constructor
     */
    protected $levels = ['debug', 'info', 'warn', 'error', 'fatal'];

    protected $levelColors = [
        'debug' => CLI_LGRAY,
        'info' => CLI_LGRAY,
        'warn' => CLI_YELLOW,
        'error' => CLI_LRED,
        'fatal' => CLI_LRED,
    ];

    /**
     * @var int The current logging level
     */
    protected $level = 0;

    /**
     * @var bool Log to the console?
     */
    protected $logToConsole = true;

    /**
     * @var bool Log to disk?
     */
    protected $logToDisk = true;

    /**
     * @var bool Log to memory (store log for later dump)?
     */
    protected $logToMemory = false;

    /**
     * @var null|string Directory to store logs in
     */
    protected $directory = null;

    /**
     * @var string String to append to the filename
     */
    protected $filePart = '';

    /**
     * @var null|string Full filename, including path, of the file
     */
    protected $file = null;

    /**
     * @var string Prefix to append to every log line
     */
    protected $prefix = '';

    /**
     * @var string Date format used in the filename
     */
    protected $fileDateFormat = 'Y-m-d_His';

    /**
     * @var string Date format used in log lines
     */
    protected $lineDateFormat = 'Y-m-d H:i:s';

    protected $colorsEnabled = false;

    /**
     * @var string[] In-memory log
     */
    protected $memlog = [];

    protected $progressLogging = false;


    public function __construct()
    {
        // Flip levels, so that $this->levels[$level] gives us a number
        $this->levels = array_flip($this->levels);
    }


    public function setEnableColors(bool $enabled = true) : void
    {
        $this->colorsEnabled = $enabled;
    }


    public function setFilePart(?string $part) : void
    {
        $this->filePart = $part;
        if (is_string($this->directory) && ($this->directory !== "")) {
            $this->updateFilename();
        }
    }


    protected function updateFilename() : void
    {
        $file = '';

        if (is_string($this->fileDateFormat) && ($this->fileDateFormat !== "")) {
            $file .= date($this->fileDateFormat);
            if (is_string($this->filePart) && ($this->filePart !== "")) {
                $file .= '_';
            }
        }

        if (is_string($this->filePart) && ($this->filePart !== "")) {
            $file .= $this->filePart;
        } elseif (! (is_string($this->fileDateFormat) && ($this->fileDateFormat !== ""))) {
            $file .= 'current';
        }

        $this->file = $this->directory . $file . '.log';
    }


    public function setDirectory(string $dir) : void
    {
        $this->directory = rtrim($dir, '/') . '/';
        if (! (file_exists($this->directory) && is_dir($this->directory))) {
            $this->log("Creating log directory: {$this->directory}", 'info');
            if ((! mkdir($this->directory, 0775, true)) && (! is_dir($this->directory))) {
                $this->log("Failed creating directory: {$this->directory}", "error");
                throw new \RuntimeException("Failed creating log directory: {$this->directory}");
            }
        }
        $this->updateFilename();
        if (! defined('ERROR_HANDLER_LOG')) {
            define('ERROR_HANDLER_LOG', $this->file);
        }
    }


    public function setLevel(string $level) : void
    {
        if (! array_key_exists($level, $this->levels)) {
            throw new \InvalidArgumentException("Invalid log level specified: {$level}; Valid options are: "
                . implode(", ", array_keys($this->levels)));
        }
        $this->level = $this->levels[$level];

        $levels = array_flip($this->levels);
        $this->log("Log Level set to: " . $levels[$this->level], 'info');
    }


    public function setPrefix(string $string = '') : void
    {
        if (($string !== "") && (substr($string, -1) !== ' ')) {
            $string .= ' ';
        }
        $this->prefix = $string;
    }


    /**
     * @param string|null $format
     */
    public function setFileDateFormat(?string $format) : void
    {
        $this->fileDateFormat = $format;
        if (is_string($this->directory) && ($this->directory !== "")) {
            $this->updateFilename();
        }
    }


    public function setLineDateFormat(string $format) : void
    {
        $this->lineDateFormat = $format;
    }


    public function init() : void
    {
        $this->log(str_repeat('-', 80), 'info');
        $this->log("Logging to: {$this->file}", 'info');
    }


    public function log(string $msg, string $level = 'info', bool $diskOnly = false) : void
    {
        if ($this->progressLogging && (! $diskOnly)) {
            $this->logProgressEnd();
        }

        $logLevel = $this->levels[$level];
        if ($this->level > $logLevel) {
            return;
        }

        $levelTxt = str_pad(strtoupper($level), 5, ' ', STR_PAD_LEFT);
        $date = $this->date();
        if (is_string($date) && ($date !== "")) {
            $date .= ' ';
        }
        $line = "{$date}{$levelTxt} {$this->prefix}{$msg} \n";
        $consoleLine = $line;
        if ($this->colorsEnabled) {
            $consoleLine = $date . $this->levelColors[$level] . $levelTxt . CLI_NORMAL . " {$this->prefix}{$msg} \n";
        }

        if ($this->logToMemory) {
            $this->memlog[] = $line;
        }

        if ($this->logToDisk && is_string($this->file) && ($this->file !== "")) {
            $bytesWritten = file_put_contents($this->file, $line, FILE_APPEND);
            if ($bytesWritten === false) {
                $this->logToDisk = false;
                trigger_error("Failed to write to log file - Log to disk has been FORCE DISABLED", E_USER_NOTICE);
            }
        }

        if ($diskOnly) {
            return;
        }

        if ($this->logToConsole) {
            print $consoleLine;
        }
    }


    /**
     * Create a date/time stamp string in a specified format, using a method that makes resolutions lower than seconds
     * work.
     *
     * We also have to make sure that there is a decimal point with numbers after it, otherwise the 'create from
     * format' fails.
     *
     * @return bool|string
     */
    protected function date() : string
    {
        $ts = number_format(microtime(true), 6, '.', '');
        $dt = date_create_from_format("U.u", $ts);
        if (! is_object($dt)) {
            trigger_error(
                "Failed to create timestamp for {$ts}: " . print_r(\DateTime::getLastErrors(), true),
                E_USER_NOTICE
            );
            return date($this->lineDateFormat);
        }
        // DateTime objects created from timestamps are UTC by default - convert to the default tz
        $dt->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        return $dt->format($this->lineDateFormat);
    }


    /**
     * Log a progress message. This overwrites the previous contents of the current line on the console.
     *
     * @param String $msg Message to log
     * @param string $level Log level
     */
    public function logProgress(string $msg, string $level = 'info') : void
    {
        $logLevel = $this->levels[$level];
        if (! array_key_exists($level, $this->levels)) {
            trigger_error("Invalid log level specified: {$level}", E_USER_NOTICE);
            $logLevel = $this->levels[$level];
        }
        if ($this->level > $logLevel) {
            return;
        }
        $this->log($msg, $level, true);

        if ($this->logToConsole) {
            $this->progressLogging = true;
            $levelTxt = str_pad(strtoupper($level), 5, ' ', STR_PAD_LEFT);
            $date = $this->date();
            if (is_string($date) && ($date !== "")) {
                $date .= ' ';
            }

            $line = $date . $this->levelColors[$level] . "{$levelTxt} {$this->prefix}{$msg} \n";
            $consoleLine = $line;
            if ($this->colorsEnabled) {
                $consoleLine = $date . $this->levelColors[$level] . $levelTxt . CLI_NORMAL . " {$this->prefix}{$msg} \n";
            }

            $line = "\r\x1B[K" . trim($consoleLine) . CLI_NORMAL;
            print $line;
        }
    }


    /**
     * End a section of progress log messages (move to next console line)
     */
    public function logProgressEnd() : void
    {
        if ($this->logToConsole) {
            print "\n";
            $this->progressLogging = false;
        }
    }


    protected function bytes_to_human(int $bytes) : string
    {
        $human = null;
        if ($bytes < 1024) {
            $human = number_format($bytes, 0) . ' bytes';
        } else if ($bytes < 1024 * 1024) {
            $human = number_format(($bytes / 1024), 1) . ' KB';
        } else {
            $human = number_format(($bytes / (1024 * 1024)), 1) . ' MB';
        }
        return $human;
    }


    public function logMemoryUsage() : void
    {

        $memUsageString = "";
        if (function_exists('memory_get_usage')) {
            $mem = memory_get_usage();
            $mem_text = $this->bytes_to_human($mem);

            $rmem = memory_get_usage(true);
            $rmem_text = $this->bytes_to_human($rmem);
            $memUsageString .= "Memory Usage: " . $mem_text . " / Real: " . $rmem_text . " :: ";
            unset ($mem, $mem_text, $rmem, $rmem_text);
        }
        if (function_exists('memory_get_peak_usage')) {
            $mem = $this->bytes_to_human(memory_get_peak_usage());
            $rmem = $this->bytes_to_human(memory_get_peak_usage(true));
            $memUsageString .= "Peak Mem Usage: " . $mem . " / Real: " . $rmem;
        }
        if ($memUsageString !== "") {
            $this->log($memUsageString);
        }
    }


    /**
     * Is the specified level the same as or higher than 'error' (ie. does it include 'fatal' or anything else we come
     * up with)
     *
     * @param string $level Error level to check
     * @return bool
     */
    public function isErrorLevel(string $level) : bool
    {
        $logLevel = $this->levels[$level];
        if ($this->level > $logLevel) {
            trigger_error("Invalid log level: {$level}", E_USER_NOTICE);
            return false;
        }

        return ($logLevel >= $this->levels['error']);
    }


    public function compress() : ?string
    {
        if ($this->file === null) {
            return null;
        }

        exec("gzip -fq \"{$this->file}\"");
        return $this->file . '.gz';
    }


    public function getFile() : ?string
    {
        return $this->file;
    }


    public function setLogToConsole(bool $enabled = true) : void
    {
        $this->logToConsole = $enabled;
    }


    public function setLogToDisk(bool $enabled = true) : void
    {
        $this->logToDisk = $enabled;
    }


    public function setLogToMemory(bool $enabled = true) : void
    {
        $this->logToMemory = $enabled;
    }


    public function dumpLog() : array
    {
        return $this->memlog;
    }

}
