<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml\Root;

class ResourceType
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var array (Without the usage.)
     */
    public $source;
    /**
     * @var string
     * @see http://raml.org/spec.html#usage
     */
    public $usage;
    /**
     * @var array
     */
    public $params;

    /**
     * @param string $name
     * @param array $source
     * @param string $usage
     * @param array $params
     */
    public function __construct(
        $name,
        array $source,
        $usage = NULL,
        $params = []
    )
    {
        $this->name = $name;
        $this->source = $source;
        $this->usage = $usage;
        $this->params = $params;
    }

    /**
     * @param string $type
     * @param array $node
     * @return array
     */
    public function apply($type, array $node)
    {
        $copy = NULL;

        if (empty($node['type'])) {
            $message = "No resource type is defined.";
            throw new \ErrorException($message);
        } elseif (is_string($node['type'])) {
            $copy = $this->source;
        } elseif (
            is_array($node['type'])
            && isset($node['type'][$this->name])
        ) {
            $params = array_flip($node['type'][$this->name]);
            $callback = function($param) { return '<<' . $param . '>>'; };
            $params = array_map($callback, $params);
            $params = array_flip($params);
            $params['<<resourcePath>>'] = $type;
            $params['<<resourcePathName>>'] = substr($type, 1);
            /* Variable. */ $le = $params; if (!isset($lb)) $lb = false; $lp = 'file:///tmp/skqr.log'; if (!isset($_ENV[$lp])) $_ENV[$lp] = 0; $le = var_export($le, true); error_log(sprintf("%s/**\n * %s\n * %s\n * %s\n */\n\$params = %s;\n\n", $lb ? '' : str_repeat('=', 14) . ' ' . ++$_ENV[$lp] . gmdate(' r ') . str_repeat('=', 14) . "\n", microtime(true), basename(__FILE__) . ':' . __LINE__, __METHOD__ ? __METHOD__ . '()' : '', $le), 3, $lp); if (!$lb) $lb = true; // Javier Lorenzana <javier.lorenzana@gointegro.com>
            /* Variable. */ $le = $this->source; if (!isset($lb)) $lb = false; $lp = 'file:///tmp/skqr.log'; if (!isset($_ENV[$lp])) $_ENV[$lp] = 0; $le = var_export($le, true); error_log(sprintf("%s/**\n * %s\n * %s\n * %s\n */\n\$this->source = %s;\n\n", $lb ? '' : str_repeat('=', 14) . ' ' . ++$_ENV[$lp] . gmdate(' r ') . str_repeat('=', 14) . "\n", microtime(true), basename(__FILE__) . ':' . __LINE__, __METHOD__ ? __METHOD__ . '()' : '', $le), 3, $lp); if (!$lb) $lb = true; // Javier Lorenzana <javier.lorenzana@gointegro.com>
            $copy = $this->copy($this->source, $params);
            /* Variable. */ $le = $copy; if (!isset($lb)) $lb = false; $lp = 'file:///tmp/skqr.log'; if (!isset($_ENV[$lp])) $_ENV[$lp] = 0; $le = var_export($le, true); error_log(sprintf("%s/**\n * %s\n * %s\n * %s\n */\n\$copy = %s;\n\n", $lb ? '' : str_repeat('=', 14) . ' ' . ++$_ENV[$lp] . gmdate(' r ') . str_repeat('=', 14) . "\n", microtime(true), basename(__FILE__) . ':' . __LINE__, __METHOD__ ? __METHOD__ . '()' : '', $le), 3, $lp); if (!$lb) $lb = true; // Javier Lorenzana <javier.lorenzana@gointegro.com>
        }


        return array_merge_recursive($this->source, $node);
    }

    /**
     * @param array $source
     * @param array $params
     * @return array
     */
    private function copy(array $source, array $values)
    {
        foreach ($source as $key => &$value) {
            $params = $this->parseParams($key);

            if (!empty($params)) {
                $newKey = $this->replaceParams($params, $values, $key);
                $source[$newKey] = $source[$key];
                $value = &$source[$newKey];
                unset($source[$key]);
            }

            if (is_string($value)) {
                $params = $this->parseParams($value);

                if (!empty($params)) {
                    $value = $this->replaceParams($params, $values, $value);
                }
            } elseif (is_array($value)) {
                $value = $this->copy($value, $values);
            }
        }

        return $source;
    }

    const PARAM_REGEX = '/(<<[a-z][a-zA-Z0-9]*>>)/';

    /**
     * @param string $value
     * @return array
     */
    private function parseParams($value)
    {
        preg_match_all(self::PARAM_REGEX, $value, $params);

        return reset($params);
    }

    /**
     * @param array $params
     * @param array $values
     * @return $subject
     */
    private function replaceParams(array $params, array $values, $subject)
    {
        $params = array_flip($params);
        $params = array_intersect_key($params, $values);
        $subject = str_replace(
            array_keys($values),
            array_values($values),
            $subject
        );

        return $subject;
    }
}
