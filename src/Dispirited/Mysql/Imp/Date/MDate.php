<?php


namespace Dispirited\Mysql\Imp\Date;


use Dispirited\Mysql\Basic\MField;

class MDate extends MField
{
    protected string $_type = "date";

    public function __toString(): string
    {
        $sql = implode(" ", [
            sprintf("`%s`", $this->_name),
            sprintf("%s", $this->_type),
            empty($this->_default) ? "" : sprintf("default %s", "'{$this->_default}'"),
            sprintf("%s", $this->_null),
            sprintf("comment '%s'", $this->_comment),
        ]);

        if (!is_null($this->_index) && !$this->_filter) {
            $sql = implode(",", [
                $sql,
                sprintf("%s (`%s`)", (string)$this->_index, $this->_name)
            ]);
        }
        return $sql;
    }
}