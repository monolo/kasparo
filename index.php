<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader;

error_reporting(E_ERROR | E_WARNING | E_PARSE);
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
/*$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
	    'admin' => array(
	        'pattern' => '^/admin',
	        'http' => true,
	        'users' => array(
	            'admin' => array('ROLE_ADMIN', 'dpEqUE/KK1bik54L6Ve7CSe9Y3NB5PaS6HWKZd8XtYnFTqYJGjvXsYMeQSsyiac84GmX3lYsN3flYNVqlEuODA=='),
	        ),
	    ),
	),
));*/

//controllers
$app->get('/admin', function() use($app) {
    return $app['twig']->render('adminIndex.html.twig');
})
->bind('admin');


$app->get('/admin/images/show/{id}', function($id) use($app) {
    $entity = $app['db']->fetchAssoc('SELECT * FROM images WHERE id = '.$id);
    return $app['twig']->render('adminImagesShow.html.twig', array("entity" => $entity));
})
->bind('admin_images_show');

$app->match('/admin/images/new', function(Request $request) use($app) {
    if($request->getMethod() == "POST"){
        if ($_FILES["file"]["error"] > 0)
        {
            echo "Ha ocurrido un error con la imagen subida o no ha subido una imagen, vuelve a intentarlo";
        }
        else
        {
            if($name = $request->get("name")){
                $extension = end(explode(".", $_FILES["file"]["name"]));
                $date = new \DateTime();
                $name2 = md5($date->format("Y-m-d H:i:s")).".".$extension;
                move_uploaded_file($_FILES["file"]["tmp_name"], "public/images/" . $name2);
                $sql = "Insert Into images (id, name, orden, path";
                $sql .=($request->get("slider1")) ? ", slider1" : "";
                $sql .=($request->get("slider2")) ? ", slider2" : "";
                $sql .=($request->get("slider3")) ? ", slider3" : "";
                $sql .=($request->get("description") != "") ? ", description" : "";
                $sql .=") Values (NULL, '".$name."','".$request->get('orden')."', '/public/images/".$name2."'";
                $sql .=($request->get("slider1")) ? ", '1'" : "";
                $sql .=($request->get("slider2")) ? ", '1'" : "";
                $sql .=($request->get("slider3")) ? ", '1'" : "";
                $sql .=($request->get("description") != "") ? ", '".$request->get("description")."'" : "";
                $sql .=")";
                $app["db"]->executeUpdate($sql);
                return $app->redirect("/admin/images");
            }
        }
    }
    return $app['twig']->render('adminImagesNew.html.twig',array('type'=>'new'));
})
->bind('admin_images_new');

$app->get('/admin/images/{type}', function($type) use($app) {
    $entities = $app['db']->fetchAll('SELECT * FROM images WHERE images.'.$type.'="1" ORDER BY orden ASC');
    return $app['twig']->render('adminImages.html.twig', array("entities" => $entities,'type'=>$type));
})
->bind('admin_images')->value('type', 'slider1');


$app->match('/admin/images/edit/{id}', function($id, Request $request) use($app) {

    $entity = $app['db']->fetchAssoc('SELECT * FROM images WHERE id = '.$id);
    if($request->getMethod() == "POST"){
        $description = ($request->get("description") != "") ? "'".$request->get("description")."'" : "NULL";
        if($_FILES["file"]["size"] > 0){
            if ($_FILES["file"]["error"] > 0)
            {
                echo "Ha ocurrido un error con la imagen subida, vuelve a intentarlo";
            }
            else
            {
                if($name = $request->get("name")){
                    $unlink = end(explode("/", $entity["path"]));
                    unlink("images/".$unlink);
                    $extension = end(explode(".", $_FILES["file"]["name"]));
                    $date = new \DateTime();
                    $name2 = md5($date->format("Y-m-d H:i:s")).".".$extension;
                    move_uploaded_file($_FILES["file"]["tmp_name"], "public/images/" . $name2);
                    $sql = "UPDATE images SET name='".$name."', path='/public/images/".$name2."', slider1=";
                    $sql .=($request->get("slider1")) ? "true" : "false";
                    $sql .=", orden='".$request->get('orden')."'";
                    $sql .=", slider2=";
                    $sql .=($request->get("slider2")) ? "true" : "false";
                    $sql .=", slider3=";
                    $sql .=($request->get("slider3")) ? "true" : "false";
                    $sql .=", description=";
                    $sql .=$description;
                    $sql .=" WHERE id = ".$id;
                    $app["db"]->executeUpdate($sql);
                    return $app->redirect("/admin/images");
                }
            }
        }
        else{
            if($name = $request->get("name")){
                $sql = "UPDATE images SET name='".$name."', slider1=";
                $sql .=($request->get("slider1")) ? "true" : "false";
                $sql .=", orden='".$request->get('orden')."'";
                $sql .=", slider2=";
                $sql .=($request->get("slider2")) ? "true" : "false";
                $sql .=", slider3=";
                $sql .=($request->get("slider3")) ? "true" : "false";
                $sql .=", description=";
                $sql .=$description;
                $sql .=" WHERE id = ".$id;
                $app["db"]->executeUpdate($sql);
                return $app->redirect("/admin/images");
            }
        }
    }
    return $app['twig']->render('adminImagesEdit.html.twig', array("entity" => $entity));
})
->bind('admin_images_edit');

$app->get('/admin/images/delete/{id}', function($id) use($app){
    $entity = $app['db']->fetchAssoc('SELECT * FROM images WHERE id = '.$id.' ORDER BY images.orden ASC');
    $unlink = end(explode("/", $entity["path"]));
    unlink("public/images/".$unlink);
    $sql = "DELETE FROM images WHERE id=".$id;
    $app["db"]->executeUpdate($sql);
    return $app->redirect("/admin/images");
})
->bind('admin_images_delete');
$app->get("/", function() use($app){
    return $app['twig']->render('landing.html.twig');
})
->bind("landing");

$app->get("/{_locale}/press", function() use($app){
    $entities = $app['db']->fetchAll('SELECT * FROM images WHERE slider3 = 1 ORDER BY orden ASC');
    return $app['twig']->render('press.html.twig',array('entities'=>$entities));
})
    ->bind("press");

$app->get("/{_locale}/gallery-outdoor", function() use($app){
    $entities = $app['db']->fetchAll('SELECT * FROM images WHERE slider2 = 1 ORDER BY orden ASC');
    return $app['twig']->render('gallery-outdoor.html.twig', array("entities" => $entities));
})
->bind("galleryoutdoor");

$app->get("/{_locale}/gallery", function() use($app){
    $entities = $app['db']->fetchAll('SELECT * FROM images WHERE slider1 = 1 ORDER BY orden ASC');
    return $app['twig']->render('gallery.html.twig', array("entities" => $entities));
})
->bind("gallery");

$app->get("{_locale}/card", function() use($app){
	return $app["twig"]->render('card.html.twig');
})
->bind('card');

$app->get('{_locale}/about', function() use($app){
    return $app["twig"]->render('about.html.twig');
})
->bind('about');

$app->run();
