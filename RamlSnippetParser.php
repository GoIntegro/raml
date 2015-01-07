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
     * @return array
     */
    private function findParams(array $raw, &$params = [], $path = '')
    {
        foreach ($raw as $key => $value) {
            $loopPath = $path . '.' . $key;
            $params['key'][$loopPath] = $this->parseParams($key);
            /* Variable. */ $le = $params; if (!isset($lb)) $lb = false; $lp = 'file:///tmp/skqr.log'; if (!isset($_ENV[$lp])) $_ENV[$lp] = 0; $le = var_export($le, true); error_log(sprintf("%s/**\n * %s\n * %s\n * %s\n */\n\$params = %s;\n\n", $lb ? '' : str_repeat('=', 14) . ' ' . ++$_ENV[$lp] . gmdate(' r ') . str_repeat('=', 14) . "\n", microtime(true), basename(__FILE__) . ':' . __LINE__, __METHOD__ ? __METHOD__ . '()' : '', $le), 3, $lp); if (!$lb) $lb = true; // Javier Lorenzana <javier.lorenzana@gointegro.com>

            if (is_string($value)) {
                $params['value'][$loopPath] = $this->parseParams($key);
            } elseif (is_array($value)) {
                $this->findParams($value, $params, $loopPath);
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
