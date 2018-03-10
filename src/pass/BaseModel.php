<?php
/**
 * Password - A command-line tool to help you manage your password
 *
 * @author  admin@dowte.com
 * @link    https://github.com/dowte/password
 * @license https://opensource.org/licenses/MIT
 */

namespace Dowte\Password\pass;

use Dowte\Password\pass\db\ActiveRecord;

class BaseModel extends ActiveRecord
{
    public function loadData($data)
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }
}