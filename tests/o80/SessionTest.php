<?php
namespace o80;

class SessionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider ipsProvider
     */
    public function testShouldFindIpOfClient($remoteAddr, $forwardedFor, $expected) {
        $_SERVER['REMOTE_ADDR'] = $remoteAddr;
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $forwardedFor;
        $ip = Session::ip();
        $this->assertEquals($expected, $ip);
    }

    public function ipsProvider() {
        return array(
            array('RA', null, 'RA'),
            array('RA', 'FF', 'FF'),
            array(null, 'FF', 'FF'),
            array('RA', 'FF1,FF2', 'FF1'),
            array(null, 'FF1,FF2', 'FF1')
        );
    }

}
