<?php
exec('git --version 2>&1', $output);
echo implode("\n", $output);
?>