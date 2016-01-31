<?php

require_once __DIR__.'/vendor/autoload.php';

use Silex\Application;

class MyApp extends Application {
    use Application\TwigTrait;
    # use Application\SecurityTrait;
    # use Application\FormTrait;
    # use Application\UrlGeneratorTrait;
    # use Application\SwiftmailerTrait;
    # use Application\MonologTrait;
    # use Application\TranslationTrait;
}

$app = new MyApp();

// set up twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

# INDEX
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig', array());
});

# HEADER (loaded with ajax)
$app->get('/header', function () use($app) {
    $response = (object) array(
        'head' => '<!-- Styles -->',
        'body' => new stdClass(),
        'foot' => '<!-- Scripts -->',
    );
    $response->body->header = $app['twig']->render('header.twig', array());
    return json_encode($response);
});
# NAVBAR (loaded with ajax)
$app->get('/navbar', function () use($app) {
    $response = (object) array(
        'head' => '<!-- Styles -->',
        'body' => new stdClass(),
        'foot' => '<!-- Scripts -->',
    );
    $response->body->navbar = $app['twig']->render('navbar.twig', array());
    return json_encode($response);
});
$app->get('/footer', function () use($app) {
    $response = (object) array(
        'head' => '<!-- Styles -->',
        'body' => new stdClass(),
        'foot' => '<!-- Scripts -->',
    );
    $response->body->footer = $app['twig']->render('footer.twig', array());
    return json_encode($response);
});

$app->get('/photos', function () use($app) {
    $response = (object) array(
        'head' => '<!-- Styles -->',
        'body' => new stdClass(),
        'foot' => '<!-- Scripts -->',
    );
    $response->body->content = $app['twig']->render('photos.twig', array());
    return json_encode($response);
});

$app->get('/videos', function () use($app) {
    $response = (object) array(
        'head' => '<!-- Styles -->',
        'body' => new stdClass(),
        'foot' => '<!-- Scripts -->',
    );
    $response->body->content = $app['twig']->render('videos.twig', array());
    return json_encode($response);
});

$app['debug'] = true;

$app->run();
