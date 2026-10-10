<?php
$heartbeat = '/tmp/portfolio-cron-heartbeat';
$interval = (int) (getenv('CRON_INTERVAL') ?: 60);
exit(is_file($heartbeat) && time() - (int) file_get_contents($heartbeat) < 2 * $interval + 30 ? 0 : 1);

