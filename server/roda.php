<?php
date_default_timezone_set('UTC');
function podeRodar($conn, $uid)
{
  $today = date("Y-m-d");
  $stmt = $conn->prepare("SELECT `valor` FROM `roda` WHERE `uid` = ? AND `data` = ?");
  $stmt->bind_param("is", $uid, $today);
  $stmt->execute();
  $pode = !($stmt->fetch());
  $stmt->close();
  return $pode;
}