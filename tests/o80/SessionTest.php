<?php
namespace o80;

class SessionTest extends \PHPUnit_Framework_TestCase {

    public function testFindDirectIp() {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $ip = Session::ip();
        $this->assertEquals('127.0.0.1', $ip);
    }

}
