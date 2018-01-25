<?php

// GENERAL SETTING SMTP EMAIL FOR ORDERS
function generalSetting($controller)
{
  require(CONFIG_PATH);
  $stmt = $mysqli->prepare('SELECT `value` FROM '. DB_TABLE_SETTING .' WHERE `controller` = ?');
  $stmt->bind_param("s", $controller);
  $stmt->execute();
  $stmt->bind_result($value);
  $stmt->fetch();
  $stmt->close();
  $mysqli->close();
  return htmlspecialchars($value);
}
