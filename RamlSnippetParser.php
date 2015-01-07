<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

class RamlSnippetParser
{
    /**
     * @param mixed $raw
     * @return Root\RamlSnippet
     * @throws \ErrorException
     */
    public function parse(array $raw)
    {
        $usage = isset($raw['usage']) ? $raw['usage'] : '';
        unset($raw['usage']);
        $params = [];
        $this->findParams($raw, $params);

        return new Root\RamlSnippet($raw, $usage, $params);
    }

    const PARAM_REGEX = '/<<([a-z][a-zA-Z0-9]*)>>/';

    /**
     * @param array $raw
     * @param array &$allParams
     * @param string $path
     * @return array
     */
    private function findParams(array $raw, &$allParams = [], $path = [])
    {
        foreach ($raw as $key => $value) {
            $loopPath = array_merge($path, [$key]);

            foreach ($this->parseParams($key) as $param) {
                $allParams['key'][$param][] = $path;
            }

            if (is_string($value)) {
                foreach ($this->parseParams($value) as $param) {
                    $allParams['value'][$param][] = $loopPath;
                }
            } elseif (is_array($value)) {
                $this->findParams($value, $allParams, $loopPath);
            }
        }
    }

    /**
     * @param string $value
     * @return array
     */
    private function parseParams($value)
    {
        preg_match_all(self::PARAM_REGEX, $value, $params);

        return reset($params);
    }
}
