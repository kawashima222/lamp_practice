<?php

function str_escape($str) {
  return htmlspecialchars($str,'ENT_QUOTES','UTF-8');
}