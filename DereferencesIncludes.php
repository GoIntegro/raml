<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

// YAML.
use Symfony\Component\Yaml\Yaml;

/**
 * A class that has this trait dereferences includes.
 *
 * I'm tryint to start a new naming convention in which traits are names like
 * traits. What are this classes traits? Well, it's handsome, polite, and it
 * dereferences includes.
 */
trait DereferencesIncludes
{
    /**
     * @param string $value
     * @param string $fileDir
     * @return mixed
     */
    protected function dereferenceInclude($value, $fileDir = __DIR__)
    {
        $filePath = $fileDir . preg_replace('/^!include +/', '/', $value);

        if (!is_readable($filePath)) {
            throw new \ErrorException(
                DocNavigator::ERROR_INCLUDED_FILE_PERMISSION
            );
        } elseif ($this->isJsonFile($filePath)) {
            return $this->jsonCoder->decode($filePath, TRUE);
        } elseif ($this->isYamlFile($filePath)) {
            return Yaml::parse($filePath);
        } else {
            throw new \ErrorException(
                DocNavigator::ERROR_INCLUDED_FILE_TYPE
            );
        }
    }

    /**
     * @param string $path
     * @return boolean
     */
    protected function isJsonFile($path)
    {
        return 1 === preg_match('/\.json$/', $path);
    }

    /**
     * @param string $path
     * @return boolean
     */
    protected function isYamlFile($path)
    {
        return 1 === preg_match('/\.(y|ra|ya)ml$/', $path);
    }
}
