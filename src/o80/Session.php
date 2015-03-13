<?php
namespace o80;

class Session {

    const SESSION_MASTER_KEY = 'o80-session';

    /**
     * This method secures the call to session_start() fonction.
     */
    public function start() {
        session_start();

        if ($this->get('IP') === null) {
            // First creation
            $this->newSession();
        } else if ($this->isSessionStolen()) {
            // If one of the stored valed changed, we create a new session
            $this->newSession();
        }
    }

    /**
     * This method get the user IP address.
     *
     * @return string the IP address
     */
    public function ip() {
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
    private function newSession() {
        // New session
        session_regenerate_id();
        $_SESSION = array();

        // Store new values
        $this->put('AGENT', $this->fromServer('HTTP_USER_AGENT'));
        $this->put('LANGUAGE', $this->fromServer('HTTP_ACCEPT_LANGUAGE'));
        $this->put('CHARSET', $this->fromServer('HTTP_ACCEPT_CHARSET'));
        $this->put('ENCODING', $this->fromServer('HTTP_ACCEPT_ENCODING'));
        $this->put('IP', $this->ip());
    }

    /**
     * Store a value in session.
     *
     * @param $key string the key of the value
     * @param $value mixed the value to store
     */
    public function put($key, $value) {
        $_SESSION[self::SESSION_MASTER_KEY][$key] = $value;
    }

    /**
     * Read a value from session.
     *
     * @param $key string the key of the value
     * @return mixed|null null is the value is not found
     */
    public function get($key) {
        return isset($_SESSION[self::SESSION_MASTER_KEY][$key]) ? $_SESSION[self::SESSION_MASTER_KEY][$key] : null;
    }

    /**
     * Get a value from $_SERVER.
     *
     * @param $key string the key of the value
     * @return string|null null is the value is not found
     */
    public function fromServer($key) {
        return !empty($_SERVER[$key]) ? $_SERVER[$key] : '';
    }

    /**
     * Check if stored values are still the same or not.
     *
     * @return bool true if the session seamed to be stolen
     */
    public function isSessionStolen() {
        return $this->get('AGENT') !== $this->fromServer('HTTP_USER_AGENT')
        || $this->get('LANGUAGE') !== $this->fromServer('HTTP_ACCEPT_LANGUAGE')
        || $this->get('CHARSET') !== $this->fromServer('HTTP_ACCEPT_CHARSET')
        || $this->get('ENCODING') !== $this->fromServer('HTTP_ACCEPT_ENCODING')
        || $this->get('IP') !== $this->ip()
            ;
    }

}
