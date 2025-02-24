<?php

/*
 * This file is part of Badcow DNS Library.
 *
 * (c) Samuel Williams <sam@badcow.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badcow\DNS\Ip;

use Badcow\DNS\Validator;

class Toolbox
{
    /**
     * Expands an IPv6 address to its full, non-shorthand representation.
     *
     * E.g. 2001:db8:9a::42 -> 2001:0db8:009a:0000:0000:0000:0000:0042
     *
     * @param string $ip IPv6 address
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function expandIpv6($ip)
    {
        if (!Validator::ipv6($ip)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid IPv6 address.', $ip));
        }

        $hex = unpack('H*hex', inet_pton($ip));

        return implode(':', str_split($hex['hex'], 4));
    }

    /**
     * This function will expand in incomplete IPv6 address.
     * An incomplete IPv6 address is of the form `2001:db8:ff:abcd`
     * i.e. one where there is less than eight hextets.
     *
     * @param string $ip IPv6 address
     *
     * @return string Expanded incomplete IPv6 address
     */
    public static function expandIncompleteIpv6($ip)
    {
        $hextets = array_map(function ($hextet) {
            return str_pad($hextet, 4, '0', STR_PAD_LEFT);
        }, explode(':', $ip));

        return implode(':', $hextets);
    }

    /**
     * Takes a valid IPv6 address and contracts it
     * to its shorter version.
     *
     * E.g.: 2001:0000:0000:acad:0000:0000:0000:0001 -> 2001:0:0:acad::1
     *
     * Note: If there is more than one set of consecutive hextets, the function
     * will favour the larger of the sets. If both sets of zeroes are the same
     * the first will be favoured in the omission of zeroes.
     *
     * E.g.: 2001:0000:0000:ab80:2390:0000:0000:000a -> 2001:0:0:ab80:2390::a
     *
     * @param string $ip IPv6 address
     *
     * @throws \InvalidArgumentException
     *
     * @return string Contracted IPv6 address
     */
    public static function contractIpv6($ip)
    {
        if (!Validator::ipv6($ip)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid IPv6 address.', $ip));
        }

        return inet_ntop(inet_pton($ip));
    }

    /**
     * Creates a reverse IPv4 address.
     *
     * E.g. 192.168.1.213 -> 213.1.168.192.in-addr.arpa.
     *
     * @param string $ip Valid IPv4 address
     *
     * @return string Reversed IP address appended with ".in-addr.arpa."
     */
    public static function reverseIpv4($ip)
    {
        $octets = array_reverse(explode('.', $ip));

        return implode('.', $octets).'.in-addr.arpa.';
    }

    /**
     * Creates a reverse IPv6 address.
     *
     * E.g. 2001:db8::567:89ab -> b.a.9.8.7.6.5.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.8.b.d.0.1.0.0.2.ip6.arpa.
     *
     * @param string $ip           A full or partial IPv6 address
     * @param bool   $appendSuffix Whether or not to append ".ip6.arpa.' suffix.
     *
     * @return string The reversed address appended with ".ip6.arpa."
     */
    public static function reverseIpv6($ip, $appendSuffix = true)
    {
        try {
            $ip = self::expandIpv6($ip);
        } catch (\InvalidArgumentException $e) {
            $ip = self::expandIncompleteIpv6($ip);
        }

        $ip = str_replace(':', '', $ip);
        $ip = strrev($ip);
        $ip = implode('.', str_split($ip));
        $ip .= $appendSuffix ? '.ip6.arpa.' : '';

        return $ip;
    }
}
