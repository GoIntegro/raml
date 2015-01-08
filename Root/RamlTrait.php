<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml\Root;

class RamlTrait
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
     * @param string $name
     * @param array $source
     */
    public function __construct($name, array $source)
    {
        $this->name = $name;
        $this->usage = !empty($source['usage']) ? $source['usage'] : NULL;
        unset($source['usage']);
        $this->source = $source;
    }

    /**
     * @param string $type
     * @param array $node
     * @return array
     */
    public function apply($type, array $node)
    {
        $copy = NULL;

        if (empty($node['is'])) {
            $message = "No traits are defined.";
            throw new \ErrorException($message);
        } elseif (is_array($node['is'])) {
            foreach ($node['is'] as $trait) {
                if (is_string($trait) && $trait === $this->name) {
                    $copy = $this->source;
                    break;
                } elseif (is_array($trait)) {
                    if (isset($trait[$this->name])) {
                        $params = $trait[$this->name];
                        $params = $this->prepareParams($params);
                        $params = $this->addNodeParams($params, $type);
                        $copy = $this->copy($this->source, $params);
                        break;
                    }
                } else {
                    $message = "A trait declaration is neither a string nor a map.";
                    throw new \ErrorException($message);
                }
            }
        }

        if (is_null($copy)) {
            $message = "This trait is not applied to the given node.";
            throw new \ErrorException($message);
        }

        return array_merge_recursive($copy, $node);
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

    /**
     * @param array $params
     * @param string $type
     * @return array
     */
    private function prepareParams(array $params)
    {
        $params = array_flip($params);
        $callback = function($param) { return '<<' . $param . '>>'; };
        $params = array_map($callback, $params);
        $params = array_flip($params);

        return $params;
    }

    /**
     * @param array $params
     * @param string $type
     * @return array
     */
    private function addNodeParams(array $params, $type)
    {
        $params['<<resourcePath>>'] = $type;
        $params['<<resourcePathName>>'] = substr($type, 1);

        return $params;
    }
}
