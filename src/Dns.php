<?php
declare(strict_types = 1);

namespace AllenJB\Utilities;

class Dns
{

    public function __construct()
    {
    }


    public static function isValidIP4(string $value) : bool
    {
        if (substr_count($value, '.') !== 4) {
            return false;
        }

        return (long2ip(ip2long($value)) === $value);
    }


    public static function isValidIP6(string $value) :  bool
    {
        if (strpos($value, ':') === false) {
            return false;
        }

        return (inet_ntop(inet_pton($value)) === $value);
    }


    public static function isValidIP(string $value) :  bool
    {
        if (strpos($value, ':') !== false) {
            return self::isValidIP6($value);
        }
        return self::isValidIP4($value);
    }


    /**
     * Is the specified IP address in a reserved range, and therefore not available for use on the public Internet?
     *
     * Reference: https://en.wikipedia.org/wiki/Reserved_IP_addresses
     * You can confirm any of these entries using 'whois <ip>'
     *
     * @param string $target IP address
     * @return bool IP is reserved?
     */
    public static function isReservedIp(string $target) : bool
    {
        // Localhost or broadcast addresses quick check
        if (($target === '127.0.0.1') || ($target === '::1') || ($target === '255.255.255.255')) {
            return true;
        }

        // IPv6
        // TODO Handle IPv6 reserved addresses correctly
        if (strpos($target, ':') !== false) {
            return false;
        }

        $targetParts = explode('.', $target);
        // Not a valid IPv4 address
        if (count($targetParts) !== 4) {
            return false;
        }

        // Broadcast range
        if ($targetParts[0] === '0') {
            return true;
        }
        // Loopback range
        if ($targetParts[0] === '127') {
            return true;
        }

        // LAN ranges
        if ($targetParts[0] === '10') {
            return true;
        }
        if (($targetParts[0] === '192') && ($targetParts[1] === '168')) {
            return true;
        }
        if (($targetParts[0] === '169') && ($targetParts[1] === '254')) {
            return true;
        }

        // Test Networks (examples / documentation only)
        if (($targetParts[0] === '192') && ($targetParts[1] === '0') && ($targetParts[2] === '2')) {
            return true;
        }
        if (($targetParts[1] === '198') && ($targetParts[1] === '51') && ($targetParts[2] === '100')) {
            return true;
        }
        if (($targetParts[0] === '203') && ($targetParts[1] === '0') && ($targetParts[3] === '113')) {
            return true;
        }

        return false;
    }


    /**
     * Return the clients IP according to the headers.
     *
     * We initially ignore reserved / local ranges, but fall back to these if no other valid IP is found.
     * We always ignore IPs in the proxy list.
     * If all else fails, we try $_SERVER['REMOTE_ADDR']
     *
     * @param array|null $server The server array to use - uses current contents of $_SERVER if not specified
     * @param array|null $proxy_ips List of IP addresses to exclude from the search. If not specified, uses codeigniter
     *     config if available.
     * @return mixed|null
     */
    public static function getIpFromServerArray(array $server = null, array $proxy_ips = null) : ?string
    {
        if ($server === null) {
            $server = $_SERVER;
        }

        // Retrieve list of local proxy IPs from CodeIgniter config
        if ($proxy_ips === null) {
            $proxy_ips = [];
            if (function_exists('config_item')) {
                $proxy_ips = config_item('proxy_ips');
                $proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
            }
        }

        $headers = [
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'HTTP_X_CLIENT_IP',
        ];

        $stack = [];
        foreach ($headers as $header) {
            if (! array_key_exists($header, $server)) {
                continue;
            }

            $ipList = explode(',', $server[$header]);
            if (count($ipList) < 1) {
                continue;
            }

            foreach ($ipList as $ip) {
                if (in_array($ip, $proxy_ips, true)) {
                    continue;
                }

                $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
                if (false === filter_var($ip, FILTER_VALIDATE_IP, $flags)) {
                    if (false !== filter_var($ip, FILTER_VALIDATE_IP)) {
                        $stack[] = $ip;
                    }
                    continue;
                }

                return $ip;
            }
        }

        if (count($stack) > 0) {
            return array_shift($stack);
        }

        return ($server['REMOTE_ADDR'] ?? null);
    }

}
