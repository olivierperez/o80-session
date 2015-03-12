<?php
namespace o80;

class Session {

    const SESSION_MASTER_KEY = 'o80-session';

    /**
     * This method secures the call to session_start() fonction.
     */
    public static function start() {
        session_start();

        if (self::get('IP') === null) {
            // First creation
            self::newSession();
        } else if ( // Check if stored values are still the same
            self::get('AGENT') !== self::fromServer('HTTP_USER_AGENT')
            || self::get('LANGUAGE') !== self::fromServer('HTTP_ACCEPT_LANGUAGE')
            || self::get('CHARSET') !== self::fromServer('HTTP_ACCEPT_CHARSET')
            || self::get('ENCODING') !== self::fromServer('HTTP_ACCEPT_ENCODING')
            || self::get('IP') !== self::ip()
        ) {
            // If one of the stored valed changed, we create a new session
            self::newSession();
        }
    }

    /**
     * This method get the user IP address.
     *
     * @return string the IP address
     */
    public static function ip() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (strchr($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $realip = $ips[0];
            } else {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }

        return $realip;
    }

    /**
     * Generate a new session. This method is call when the session is created or when an attack is detected.
     */
    private static function newSession() {
        // New session
        session_regenerate_id();
        $_SESSION = array();

        // Store new values
        self::put('AGENT', self::fromServer('HTTP_USER_AGENT'));
        self::put('LANGUAGE', self::fromServer('HTTP_ACCEPT_LANGUAGE'));
        self::put('CHARSET', self::fromServer('HTTP_ACCEPT_CHARSET'));
        self::put('ENCODING', self::fromServer('HTTP_ACCEPT_ENCODING'));
        self::put('IP', self::ip());
    }

    /**
     * Store a value in session.
     *
     * @param $key string the key of the value
     * @param $value mixed the value to store
     */
    private static function put($key, $value) {
        $_SESSION[self::SESSION_MASTER_KEY][$key] = $value;
    }

    /**
     * Read a value from session.
     *
     * @param $key string the key of the value
     * @return mixed|null null is the value is not found
     */
    private static function get($key) {
        return isset($_SESSION[self::SESSION_MASTER_KEY][$key]) ? $_SESSION[self::SESSION_MASTER_KEY][$key] : null;
    }

    /**
     * Get a value from $_SERVER.
     *
     * @param $key string the key of the value
     * @return string|null null is the value is not found
     */
    private static function fromServer($key) {
        return !empty($_SERVER[$key]) ? $_SERVER[$key] : '';
    }

}
