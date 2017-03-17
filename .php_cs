<?php
$header = <<<EOF
This file is part of Glue

(c) madewithlove <heroes@madewithlove.be>

For the full copyright and license information, please view the LICENSE
EOF;

return Madewithlove\PhpCsFixer\Config::fromFolders(['bin', 'src', 'tests'])->mergeRules([
    'header_comment' => ['header' => $header],
]);
