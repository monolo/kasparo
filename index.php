<?php
require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader;

$app = new Silex\Application();
$app['debug'] = true;

// services
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views'
));
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallback' => 'es',
));

$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/locales/en.yml', 'en');
    $translator->addResource('yaml', __DIR__.'/locales/es.yml', 'es');
    $translator->addResource('yaml', __DIR__.'/locales/cat.yml', 'cat');

    return $translator;
}));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'user' => $dbuser,
        'password' => $dbpass,
        'dbname' => $dbname,
        'host' => $dbhost
    ),
));
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
	    'admin' => array(
	        'pattern' => '^/admin',
	        'http' => true,
	        'users' => array(
	            'admin' => array('ROLE_ADMIN', 'dpEqUE/KK1bik54L6Ve7CSe9Y3NB5PaS6HWKZd8XtYnFTqYJGjvXsYMeQSsyiac84GmX3lYsN3flYNVqlEuODA=='),
	        ),
	    ),
	),
));
$app->get("{_locale}/card", function() use($app){
	return $app["twig"]->render('card.html.twig');
})
->bind('card');

$app->get('{_locale}/about', function() use($app){
    return $app["twig"]->render('about.html.twig');
})
->bind('about');

$app->run();
