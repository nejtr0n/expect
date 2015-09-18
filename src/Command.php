<?php
/**
 * Created by PhpStorm.
 * User: a6y
 * Date: 17.09.15
 * Time: 16:47
 */

/**
 * Shell command
 */


class Command
{
    private $__cmd = "";
    private $__next = NULL;
    private $__cases_list = array();

    public function __construct($cmd, CaseExpect $def_case = NULL) {
        $this->__cmd = $cmd;
        // Default shell case
        if (empty($def_case)) {
            $this->__cases_list[] = new CaseExpect("(.*)[$|#][ ]$", EXP_REGEXP);
        } else {
            $this->__cases_list[] = $def_case;
        }
    }

    public function setNext(Command $command) {
        $this->__next = $command;
    }

    /**
     * Prepare case array for expect_expectl function
     * @return array
     */
    private function formatCases() {
        $return = array();
        foreach ($this->__cases_list as $key => $case) {
            $return[] = array ($case->getMask(), $key, $case->getType()); // output
        }
        return $return;
    }

    /**
     * Remove line breaks and command from result
     * @param $result
     * @return string
     */
    private function formatResult($result) {
        $data = explode(PHP_EOL,trim(str_replace(trim($this->__cmd), '', $result)));
        array_pop($data);
        return implode(PHP_EOL, $data);;
    }
    /**
     * Executo command on shell
     * @param Shell $shell
     * @return bool
     * @throws \Exception
     */
    public function run(Shell $shell) {
        $match = NULL;
        while (true) {
            fwrite($shell->getStream(), $this->__cmd);
            switch ($key = expect_expectl ($shell->getStream(), $cases = $this->formatCases(), $match)) {
                case in_array($key, $cases):
                    // Run next
                    if ($this->__next instanceof TCommand) {
                        $this->__next->run($shell);
                    }
                    if ('break' == $result = $this->__cases_list[$key]->proceed($shell->getStream(), $match)) {
                        break;
                    }
                    $shell->addCmd($this->__cmd);
                    $shell->addResult($this->formatResult($result));
                    return true;
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
}