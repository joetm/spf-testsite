<?php

require_once __DIR__.'/vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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
        $this->response = (object) array(
            'head' => '<!-- Styles -->',
            'body' => new stdClass(),
            'foot' => '<!-- Scripts -->',
        );
    }
    public function setBody(array $arr) {
        $this->response->body = $arr;
    }
    public function getResponse() {
	return json_encode($this->response);
    }
    public function render($template, $app) {
        if ($app->isSpfRequest === true) {
            $this->response->setBody(['content' => $app['twig']->render($template.'.twig', array())]);
            return $this->getResponse();
        } else {
            return $app['twig']->render('base.twig', array(
                'header'  => $app['twig']->render('header.twig', array()),
                'content' => $app['twig']->render($template.'.twig', array()),
            ));
        }
    }
}

$checkSpfRoute = function (Request $request, MyApp $app) {
    if ($request->query->get('spf') === 'navigate') {
        // must return json-wrapped html fragments
        $app->isSpfRequest = true;
    } else {
        // must return the full html page
        $app->isSpfRequest = false;
    }
};



$app = new MyApp();

// set up twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

# INDEX
$app->get('/', function () use ($app) {
    return $app['twig']->render('base.twig', array(
    	'header'  => $app['twig']->render('header.twig', array()),
    	'content' => $app['twig']->render('index.twig', array()),
    ));
});

$app->get('/photos', function () use($app) {
    $spf = new SpfResponse();
    return $spf->render('photos', $app);
})->before($checkSpfRoute);

$app->get('/videos', function (Request $request) use($app) {
    $spf = new SpfResponse();
    return $spf->render('videos', $app);
})->before($checkSpfRoute);

$app->get('/login', function () use($app) {
    $spf = new SpfResponse();
    return $spf->render('login', $app);
})->before($checkSpfRoute);

$app->get('/join', function () use($app) {
    $spf = new SpfResponse();
    return $spf->render('join', $app);
})->before($checkSpfRoute);

$app['debug'] = true;

$app->run();
