#!/usr/bin/env php
<?php
Phar::mapPhar('eac.phar');
Phar::interceptFileFuncs();
require 'phar://eac.phar/bin/eac';

__HALT_COMPILER();