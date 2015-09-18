<?php
/**
 * Created by PhpStorm.
 * User: a6y
 * Date: 17.09.15
 * Time: 16:34
 */
/**
 * Test shell wrapper
 */

class Shell
{
    private $__stream = NULL;
    private $__cmds = array();
    private $__results = array();
    /**
     * Local shell wrapper
     * @throws \Exception
     */
    public function __construct() {
        $cur_shell = trim(shell_exec('echo $0'));
        if (!in_array(
            $cur_shell,
            array(
                'sh',
                'bash'
            )
        )) {
            throw new \Exception ("Unknown shell");
        }
        $this->__stream = expect_popen ($cur_shell);
    }

    public function exec(Command $cmd) {
        $match = NULL;
        // Get clear shell for work
        while (true) {
            switch (expect_expectl ($this->__stream, array (
                array ("(.*)[$|#][ ]$", SHELL, EXP_REGEXP), // Ready for work
            ), $match)) {
                case SHELL:
                    $cmd->run($this);
                    return $this; // Chain of commands
                    break 2;

                case EXP_EOF:
                case EXP_FULLBUFFER:
                    break 2;

                case EXP_TIMEOUT:
                    throw new \Exception ("Connection time out");
                    break 2;

                default:
                    throw new \Exception ("Error has occurred!");
                    break 2;
            }
        }
        return false;
    }

    /**
     * Get connection stream
     * @return stream null
     */
    public function getStream() {
        return $this->__stream;
    }

    /**
     * Add comand to log
     * @param $cmd
     */
    public function addCmd($cmd) {
        $this->__cmds[] = $cmd;
    }

    /**
     * Add result to log
     * @param $result
     */
    public function addResult($result) {
        $this->__results[] = $result;
    }

    /**
     * Get last result
     * @return mixed
     */
    public function getResult() {
        return array(
            array_shift($this->__cmds) => array_shift($this->__results)
        );
    }
}