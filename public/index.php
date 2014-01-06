<?php

require '../library/vendor/autoload.php';
define("PLATFORM_CONFIG_JSON", "../config/platform.json");
define("PLATFORM_MODE", "development");
define("PLATFORM_DATABASE", "piray");
define("PLATFORM_ROOT", "../");

// load platform config file
$platformConfig = new library\Config(PLATFORM_CONFIG_JSON);

// slim core url routing init
$app = new Slim\Slim($platformConfig->getSlimSetting(PLATFORM_MODE));

// twig template init
$twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/../templates');
$ui = new Twig_Environment($twigLoader, $platformConfig->getTwigSetting(PLATFORM_MODE));
$ui->addGlobal('base_url', $app->request->getRootUri());
$ui->addExtension(new Twig_Extension_Debug());

// orm init
ORM::configure($platformConfig->getDbSetting(PLATFORM_DATABASE));
ORM::configure($platformConfig->getIdiormSetting(PLATFORM_MODE));

// piray platform
$piray = new routes\Platform($app, $ui);

// auto load modules
$platformModuleLoader = new library\ModuleLoader(PLATFORM_ROOT);
$platformModuleLoader->autoLoaderModules($piray);

$app->run();

