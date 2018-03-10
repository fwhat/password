<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

use Dowte\Password\pass\components\FileUtil;

defined('DB_DIR') or define('DB_DIR', FileUtil::realPath(__DIR__ . '/../../data/'));
defined('DB_PASS') or define('DB_PASS', '');
defined('PASS_ENV') or define('PASS_ENV', 'prod');
defined('CONF_FILE') or define('CONF_FILE', FileUtil::realPath(__DIR__ . '/../../.pass-conf.yaml'));
defined('SQLITE_FILE') or define('SQLITE_FILE', __DIR__ . '/../../data/pass.db');
defined('CONF_FILE_TEMP') or define('CONF_FILE_TEMP', realpath(__DIR__ . '/../../pass-conf-template.yaml'));
defined('ALFRED_CONF_FILE') or define('ALFRED_CONF_FILE', FileUtil::realPath(__DIR__ . '/../../data/.pass'));
defined('PASS_USER_CONF_DIR') or define('PASS_USER_CONF_DIR', __DIR__ . '/../../data/');
