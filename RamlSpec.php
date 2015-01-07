<?php
/**
 * @copyright 2014 Integ S.A.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author Javier Lorenzana <javier.lorenzana@gointegro.com>
 */

namespace GoIntegro\Raml;

/**
 * @see http://raml.org/spec.html
 */
class RamlSpec
{
    const HTTP_OPTIONS = 'options',
        HTTP_HEAD = 'head',
        HTTP_GET = 'get',
        HTTP_POST = 'post',
        HTTP_PUT = 'put',
        HTTP_DELETE = 'delete',
        HTTP_PATCH = 'patch';

    const MEDIA_TYPE_JSON = 'application/json',
        MEDIA_TYPE_XML = 'text/xml';

    const REQUEST_BODY = 'body',
        BODY_SCHEMA = 'schema';

    const PARAMETER_RESOURCE_PATH = 'resourcePath',
        PARAMETER_RESOURCE_PATH_NAME = 'resourcePathName',
        PARAMETER_METHOD_NAME = 'methodName';

    const PROPERTY_DECLARE_RESOURCE_TYPES = 'resourceTypes',
        PROPERTY_DECLARE_TRAITS = 'traits',
        PROPERTY_DECLARE_SCHEMAS = 'schemas',
        PROPERTY_APPLY_RESOURCE_TYPE = 'type',
        PROPERTY_APPLY_TRAITS = 'is',
        PROPERTY_USAGE = 'usage';

    /**
     * @var array
     */
    public static $methods = [
        self::HTTP_OPTIONS,
        self::HTTP_HEAD,
        self::HTTP_GET,
        self::HTTP_POST,
        self::HTTP_PUT,
        self::HTTP_DELETE,
        self::HTTP_PATCH
    ];

    /**
     * @var array
     */
    public static $mediaTypes = [
        self::MEDIA_TYPE_JSON,
        self::MEDIA_TYPE_XML
    ];

    /**
     * @var array
     */
    public static $resourceTypeParameters = [
        self::PARAMETER_RESOURCE_PATH,
        self::PARAMETER_RESOURCE_PATH_NAME
    ];

    /**
     * @var array
     */
    public static $traitParameters = [
        self::PARAMETER_METHOD_NAME
    ];

    /**
     * @var array
     */
    public static $rootLevelDeclarations = [
        self::PROPERTY_DECLARE_RESOURCE_TYPES,
        self::PROPERTY_DECLARE_TRAITS,
        self::PROPERTY_DECLARE_SCHEMAS
    ];
}
