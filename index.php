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

class SpfResponse { # extends stdClass
    private $response;
    public function __construct () {
        # parent::__construct();
        $r = array(
            'head' => '<!-- Styles -->',
            'body' => new stdClass(),
            'foot' => '<!-- Scripts -->',
        );
        $this->response = (object) $r;
    }
    public function setBody(array $arr) {
        $this->response->body = $arr;
    }
    public function getResponse() {
	return $this->response;
    }
}




$app = new MyApp();

// set up twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

# INDEX
$app->get('/', function () use ($app) {
    return $app['twig']->render('base.twig', array(
    	'header' => $app['twig']->render('header.twig', array()),
    	'content' => $app['twig']->render('index.twig', array()),
    ));
});

# HEADER (loaded with ajax)
// $app->get('/header', function () use($app) {
//     $spf = new SpfResponse();
//     $spf->setBody(['header' => $app['twig']->render('header.twig', array())]);
//     return json_encode($spf->getResponse());
// });
// $app->get('/footer', function () use($app) {
//     $spf = new SpfResponse();
//     $spf->setBody(['footer' => $app['twig']->render('footer.twig', array())]);
//     return json_encode($spf->getResponse());
// });

# NAVBAR (loaded with ajax)
$app->get('/navbar', function () use($app) {
    $spf = new SpfResponse();
    $spf->setBody(['navbar' => $app['twig']->render('navbar.twig', array())]);
    return json_encode($spf->getResponse());
});

// $app->get('/index', function () use($app) {
//     $spf = new SpfResponse();
//     $spf->setBody(['content' => $app['twig']->render('index.twig', array())]);
//     return json_encode($spf->getResponse());
// });

$app->get('/photos', function () use($app) {
    $spf = new SpfResponse();
    $spf->setBody(['content' => $app['twig']->render('photos.twig', array())]);
    return json_encode($spf->getResponse());
});

$app->get('/videos', function () use($app) {
    $spf = new SpfResponse();
    $spf->setBody(['content' => $app['twig']->render('videos.twig', array())]);
    return json_encode($spf->getResponse());
});

$app['debug'] = true;

$app->run();
