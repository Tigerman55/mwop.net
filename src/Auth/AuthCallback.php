<?php
namespace Mwop\Auth;

use Aura\Session\Session;
use Opauth;
use Phly\Http\Uri;

class AuthCallback
{
    private $config;
    private $session;

    public function __construct(array $authConfig, Session $session)
    {
        $this->config  = $authConfig;
        $this->session = $session;
    }

    public function __invoke($req, $res, $next)
    {
        $auth         = new Opauth($this->config, false);
        $authResponse = null;

        $this->session->start();
        switch($auth->env['callback_transport']) {
            case 'session':
                $authResponse = $_SESSION['opauth'];
                unset($_SESSION['opauth']);
                break;
            case 'post':
                $authResponse = unserialize(base64_decode($req->getQueryParams()['opauth']));
                break;
            case 'get':
                $authResponse = unserialize(base64_decode($req->getQueryParams()['opauth']));
                break;
            default:
                return $next('Invalid request', $res->withStatus(400));
                break;
        }

        if (array_key_exists('error', $authResponse)) {
            return $next('Error authenticating', $res->withStatus(403));
        }

        if (empty($authResponse['auth'])
            || empty($authResponse['timestamp'])
            || empty($authResponse['signature'])
            || empty($authResponse['auth']['provider'])
            || empty($authResponse['auth']['uid'])
        ) {
            return $next('Invalid authentication response', $res->withStatus(403));
        }
        
        if ($auth->env['callback_transport'] !== 'session'
            && ! $auth->validate(
                sha1(print_r($authResponse['auth'], true)),
                $authResponse['timestamp'],
                $response['signature'],
                $reason
            )
        ) {
            return $next('Invalid authentication response', $res->withStatus(403));
        }

        $auth = $this->session->getSegment('auth');
        $auth->set('user', $authResponse['auth']);

        $uri      = $req->getUri()->withPath('/');
        $redirect = $this->session->getSegment('redirect')->get('auth');
        if ($redirect) {
            $uri = new Uri($redirect);
            $this->session->getSegment('redirect')->set('auth', null);
        }

        return $res
            ->withStatus(302)
            ->withHeader('Location', (string) $uri);
    }
}