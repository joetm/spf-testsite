<?php

require_once __DIR__.'/vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

$app['debug'] = true;

// set up twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

### ------
### ROUTES
### ------

# INDEX
$app->get('/', function () use ($app) {
    return $app['twig']->render('base.twig', array(
    	'header'  => $app['twig']->render('header.twig', array()),
    	'content' => $app['twig']->render('index.twig', array()),
    ));
});
# PHOTOS
$app->get('/photos', function () use($app) {
    $spf = new SpfResponse();
    return $spf->render('photos', $app);
})->before($checkSpfRoute);
	# SINGLE PHOTO
	$app->get('/photos/{id}', function () use($app) {
	    $spf = new SpfResponse();
	    return $spf->render('photo', $app);
	})->assert('id', '\d+')
	->before($checkSpfRoute);
# VIDEOS
$app->get('/videos', function (Request $request) use($app) {
    $spf = new SpfResponse();
    return $spf->render('videos', $app);
})->before($checkSpfRoute);
	# SINGLE VIDEO
	$app->get('/video/{id}', function () use($app) {
	    $spf = new SpfResponse();
	    return $spf->render('video', $app);
	})->assert('id', '\d+')
	->before($checkSpfRoute);
# LOGIN
$app->get('/login', function () use($app) {
    $spf = new SpfResponse();
    return $spf->render('login', $app);
})->before($checkSpfRoute);
# JOIN
$app->get('/join', function () use($app) {
    $spf = new SpfResponse();
    return $spf->render('join', $app);
})->before($checkSpfRoute);

# Error Handler
$app->error(function (\Exception $e, $code) use ($app) {
    # use default debug handler
    if ($app['debug']) {
        return;
    }
    switch ($code) {
        case 401:
            $message = '401 - Unauthorized';
            break;
        case 402:
            $message = '402 - Payment Required';
            break;
        case 403:
            $message = '403 - Forbidden';
            break;
        case 404:
            $message = '404 - Not Found';
            break;
        case 405:
            $message = '405 - Method Not Allowed';
            break;
        case 500:
            $message = '500 - Internal Server Error';
            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.';
    }
    return new Response($message);
});

$app->run();
