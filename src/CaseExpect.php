<?php
/**
 * Created by PhpStorm.
 * User: a6y
 * Date: 16.09.15
 * Time: 11:23
 */
/**
 * Expect case wrapper
 */

class CaseExpect
{
    private $__mask = "";
    private $__type = EXP_EXACT;
    private $__payload = NULL; // Callable function

    public function __construct($mask, $type = EXP_EXACT, callable $func = NULL) {
        $this->__mask = $mask;
        $this->__type = $type;
        // Override default payload
        if (!empty($func)) {
            $this->__payload = $func;
        }
    }

    /**
     * Case associated action
     * @param $stream
     * @param $match
     * @return bool|mixed
     */
    public function proceed($stream, $match) {
        if (is_callable($this->__payload)) {
            return call_user_func($this->__payload, $stream, $match);
        }
        return is_array($match) ? $match[0] : false; // Default behaviour
    }

    /**
     * @return string
     */
    public function getMask() {
        return $this->__mask;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->__type;
    }

    /**
     * @param string $_mask
     */
    public function setMask($_mask) {
        $this->__mask = $_mask;
    }

    /**
     * @param mixed $_type
     */
    public function setType($_type) {
        $this->__type = $_type;
    }
}