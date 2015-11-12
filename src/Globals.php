<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/11
 * Time: 18:53
 */

namespace Xaircraft;


class Globals
{
    const ENV_FRAMEWORK = 1001;
    const ENV_VERSION = 1002;
    const ENV_MODE = 1003;
    const ENV_HOST = 1004;
    const ENV_RUNTIME_MODE = 1005;
    const ENV_OS = 1006;
    const ENV_OS_INFO = 1007;

    const OS_WIN = 2001;
    const OS_LINUX = 2002;

    const MODE_DEV = 2003;
    const MODE_PUB = 2004;

    //// Exception code
    const EXCEPTION_ERROR_ENVIRONMENT = 4001;
    const EXCEPTION_ERROR_ENVIRONMENT_SET_LIMIT = 4002;

    const RUNTIME_MODE_APACHE2HANDLER = 'apache2handler';
    const RUNTIME_MODE_CLI = 'cli';
}