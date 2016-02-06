<?php

error_reporting(-1);

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

class SpfResponse {
    public static function wrapJSON(array $body, $head='', $foot='') {
        return (object) array(
            'head' => $head ? $head : '<!-- Styles -->',
            'body' => $body,
            'foot' => $foot ? $foot : '<!-- Scripts -->',
        );
    }
    public static function render($template, $twig, $isSpfRequest) {
        if ($isSpfRequest === true) {
	    // wrap the content in JSON
            $tpl = $twig->render($template.'.twig', array());
            return json_encode(self::wrapJSON(['content' => $tpl]));
        } else {
            // render the html directly
            return $twig->render('base.twig', array(
                'header'  => $twig->render('header.twig', array()),
                'content' => $twig->render($template.'.twig', array()),
                'footer'  => $twig->render('footer.twig', array()),
            ));
        }
    }
}

$checkSPF = function (Request $request, MyApp $app) {
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
    // index is always full html
    return $app['twig']->render('base.twig', array(
    	'header'  => $app['twig']->render('header.twig', array()),
    	'content' => $app['twig']->render('index.twig', array()),
    	'footer'  => $app['twig']->render('footer.twig', array()),
    ));
});
# PHOTOS
$app->get('/photos', function () use ($app) {
    return SpfResponse::render('photos', $app['twig'], $app->isSpfRequest);
})->before($checkSPF);
	# SINGLE PHOTO
	# $app->get('/photos/{id}', function () use($app) {
	#     // return $spf->render('photo', $app);
	# })->assert('id', '\d+');
	
# VIDEOS
$app->get('/videos', function () use ($app) {
    return SpfResponse::render('videos', $app['twig'], $app->isSpfRequest);
})->before($checkSPF);
	# SINGLE VIDEO
	# $app->get('/video/{id}', function () use($app) {
	#     return $spf->render('video', $app);
	# })->assert('id', '\d+');
	
# LOGIN
$app->get('/login', function () use($app) {
    return SpfResponse::render('login', $app['twig'], $app->isSpfRequest);
})->before($checkSPF);
# JOIN
$app->get('/join', function () use($app) {
    return SpfResponse::render('join', $app['twig'], $app->isSpfRequest);
})->before($checkSPF);

# -------------
# Error Handler
# -------------
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
