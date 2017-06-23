#!/bin/sh

echo '#--------------------------------------------------------------#'
echo '| Ruunning tests                                               |'
echo '#--------------------------------------------------------------#'

vendor/bin/phpunit --configuration App/Tests/phpunit.xml App/Tests/Unit

