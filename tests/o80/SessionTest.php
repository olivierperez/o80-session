<?php
namespace o80;

class SessionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Session
     */
    private $session;

    public function setUp() {
        $this->session = new Session();
    }

    /**
     * @dataProvider ipsProvider
     */
    public function testShouldFindIpOfClient($remoteAddr, $forwardedFor, $expected) {
        $_SERVER['REMOTE_ADDR'] = $remoteAddr;
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $forwardedFor;
        $ip = $this->session->ip();
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

    /**
     * @dataProvider isSessionStolenProvider
     */
    public function testShouldCheckIfSessionIsStolen($sAgent, $sLanguage, $sCharset, $sEncoding, $sIP, $cAgent, $cLanguage, $cCharset, $cEncoding, $cIP, $expected) {
        // given
        $_SESSION[Session::SESSION_MASTER_KEY]['AGENT'] = $sAgent;
        $_SESSION[Session::SESSION_MASTER_KEY]['LANGUAGE'] = $sLanguage;
        $_SESSION[Session::SESSION_MASTER_KEY]['CHARSET'] = $sCharset;
        $_SESSION[Session::SESSION_MASTER_KEY]['ENCODING'] = $sEncoding;
        $_SESSION[Session::SESSION_MASTER_KEY]['IP'] = $sIP;

        $_SERVER['HTTP_USER_AGENT'] = $cAgent;
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $cLanguage;
        $_SERVER['HTTP_ACCEPT_CHARSET'] = $cCharset;
        $_SERVER['HTTP_ACCEPT_ENCODING'] = $cEncoding;

        // Stub "ip()" method
        $stub = $this->getMockBuilder('o80\\Session')
            ->setMethods(array('ip'))
            ->getMock();
        $stub->method('ip')->willReturn($cIP);

        // when
        $stolen = $stub->isSessionStolen();

        // then
        $this->assertEquals($expected, $stolen);
    }

    public function isSessionStolenProvider() {
        return array(
            array('A', 'L', 'C', 'E', 'I', 'A', 'L', 'C', 'E', 'I', false),
            array('A', 'L', 'C', 'E', 'I', 'A', 'L', 'C', 'E', '-', true),
            array('A', 'L', 'C', 'E', 'I', 'A', 'L', 'C', '-', 'I', true),
            array('A', 'L', 'C', 'E', 'I', 'A', 'L', '-', 'E', 'I', true),
            array('A', 'L', 'C', 'E', 'I', '-', '-', 'C', 'E', 'I', true),
            array('A', 'L', 'C', 'E', 'I', '-', '-', '-', '-', '-', true),
        );
    }

}
