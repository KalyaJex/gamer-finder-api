<?php

namespace App\Core;

define('EMERGENCY', 'emergency');
define('ALERT', 'alert');
define('CRITICAL', 'critical');
define('ERROR', 'error');
define('WARNING', 'warning');
define('NOTICE', 'notice');
define('INFO', 'info');
define('DEBUG', 'debug');

class Logger {


  public function __construct(private $db, private $account_id = null, private $authorized_by = null) {}

  function log(string $level, string $message)
    {
        $this->db->log($message, $level, $this->account_id, $this->authorized_by);
    }

    function error(string $message)
    {
        $this->log(ERROR, $message);
    }

}