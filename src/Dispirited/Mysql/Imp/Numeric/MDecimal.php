<?php


namespace Dispirited\Mysql\Imp\Numeric;


use Dispirited\Mysql\Basic\MField;

/**
 * @link  https://dev.mysql.com/doc/refman/8.0/en/fixed-point-types.html
 * Class MDecimal
 * @package Dispirited\Mysql\Imp
 */
class MDecimal extends MField
{
    protected string $_type = "decimal";

    public function __toString(): string
    {
        $sql = implode(" ", [
            sprintf("`%s`", $this->_name),
            sprintf("%s(%d,%d)", $this->_type, $this->_length, $this->_scale),
            sprintf("default %f", $this->_default),
            sprintf("%s", $this->_null),
            sprintf("comment '%s'", $this->_comment),
            is_null($this->_charset) ? "" : sprintf("CHARACTER set %s %s",
                (string)$this->_charset,
                !is_null($this->_collate) ? sprintf("COLLATE %s", $this->_collate) : ""
            ),
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