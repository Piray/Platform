#!/bin/sh

PLATFORM_FRAMEWORK="../../library"

# install or update platform framework
THIS_DIR=`pwd`
if [ -d "$PLATFORM_FRAMEWORK/vendor" ]; then
    cd $PLATFORM_FRAMEWORK; composer update
else
    cd $PLATFORM_FRAMEWORK; composer install
fi
cd $THIS_DIR

# create assets link
ln -snf ../../assets ../../public/assets/platform
ln -snf ../../library/vendor ../../public/assets/vendor

