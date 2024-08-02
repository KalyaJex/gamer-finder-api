<?php

function timestampUnixToSql(int $unixTimestamp) {
  return date('Y-m-d H:i:s', $unixTimestamp);
}

