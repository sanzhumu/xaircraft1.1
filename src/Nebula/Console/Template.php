<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/5
 * Time: 10:05
 */

namespace Xaircraft\Nebula\Console;


use Xaircraft\Core\Strings;

class Template
{
    public static function generateModel($table, $class, array $properties, $namespace = null)
    {
        $content = str_replace('{{property}}', self::generateProperties($properties), self::getModelTemplate());
        $namespace = isset($namespace) ? "namespace $namespace;" : "";
        $content = str_replace('{{namespace}}', $namespace, $content);
        $content = str_replace('{{model}}', $class, $content);
        $content = str_replace('{{create_at}}', date("Y-m-d H:i:s", time()), $content);
        $content = str_replace('{{table_name}}',
            ($table === Strings::camelToSnake($class) ? "" : '
private $table = ' . $table . ';
'),
            $content);
        return $content;
    }

    public static function generateProperties(array $properties)
    {
        $statements = array();
        if (!empty($properties)) {
            foreach ($properties as $property) {
                $statements[] = " * @property mixed $property";
            }
        }

        return implode('
', $statements);
    }

    private static function getModelTemplate()
    {
        return '<?php {{namespace}}
use Xaircraft\Nebula\Model;

/**
 * Date: {{create_at}}
{{property}}
 */
class {{model}} extends Model
{{{table_name}}
    public function beforeSave()
    {
        // TODO: Implement beforeSave() method.
    }

    public function afterSave()
    {
        // TODO: Implement afterSave() method.
    }

    public function beforeDelete()
    {
        // TODO: Implement beforeDelete() method.
    }

    public function afterDelete($fields)
    {
        // TODO: Implement afterDelete() method.
    }

    public function afterForceDelete($fields)
    {
        // TODO: Implement afterForceDelete() method.
    }
}';
    }
}