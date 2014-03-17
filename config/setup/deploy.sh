#!/bin/sh

PLATFORM_FRAMEWORK="../../library"

# install or update platform framework
THIS_DIR=`pwd`
if [ -d "$PLATFORM_FRAMEWORK/composer/vendor" ]; then
    cd $PLATFORM_FRAMEWORK/composer; composer update
else
    cd $PLATFORM_FRAMEWORK/composer; composer install
fi
cd $THIS_DIR

# create assets link
ln -snf ../../assets ../../public/assets/platform
ln -snf ../../library/composer/vendor ../../public/assets/vendor
ln -snf ../../library/opensource ../../public/assets/opensource

