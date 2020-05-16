<?php


namespace Dispirited\Mysql\Basic;


use Dispirited\Basic\Engine;
use Dispirited\Basic\Field;
use Dispirited\Basic\Table;

final class MTable implements Table
{
    protected Engine $_engine;
    protected string $_name;
    private array $_fields;
    private string $_comment;

    public function __construct(string $name, Engine $engine)
    {
        $this->_name = $name;
        $this->_engine = $engine;
    }


    public function add(Field $field, Field ...$args): Table
    {
        array_unshift($args, $field);
        foreach ($args as $f) {
            $this->_fields[$f->getName()] = $f;
        }
        return $this;
    }

    public function comment(string $comment): Table
    {
        $this->_comment = $comment;
        return $this;
    }

    public function filter(string ...$args): string
    {
        $keys = array_keys($this->_fields);
        $result = array_reduce($keys, function ($result, $item) use ($args, $keys) {
            if (!in_array($item, $args)) {
                /**
                 * @var $f Field
                 */
                $f = $this->_fields[$item];
                if (array_search($item, $keys) == 0) {
                    $result[] = sprintf($f->alter(), $this->_name, "first", "");
                } else if (array_search($item, $keys) == count($keys) - 1) {
                    $result[] = sprintf($f->alter(), $this->_name, "", "");
                } else {
                    $result[] = sprintf($f->alter(), $this->_name, "after", $keys[array_search($item, $keys) - 1]);
                }
            }
            return $result;
        }, []);
        if (!empty($result)) {
            return sprintf("ALTER TABLE `%s` %s", $this->_name, implode(",\r\n", $result));
        }
        return false;

    }

    public function __toString()
    {
        $sql = array_reduce($this->_fields, function ($result, $item) {
            if ($result) {
                $result .= ",\r\n" . $item;
            } else {
                $result = $item;
            }
            return $result;
        },);

        return implode("\r\n", [
            sprintf("create table `%s` (", $this->_name),
            sprintf("%s", $sql),
            sprintf(") engine %s", (string)$this->_engine),
            $this->_comment ? sprintf("comment '%s'", $this->_comment) : ""
        ]);
    }
}