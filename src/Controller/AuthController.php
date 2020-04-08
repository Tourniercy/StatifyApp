<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use SpotifyWebAPI;

class AuthController extends AbstractController
{

    private $spotifyParams;
    private $spotify;

    public function __construct()
    {
        $this->spotifyParams = [
            'client_id' => '435c7b3a2d4f4425917888fc7f637e67',
            'client_secret' => '228cabcd82fa40d1b7db1b314ec6e559',
            'scope' => ['user-read-email','user-read-private','playlist-read-private',
                'playlist-read-collaborative','playlist-modify-public',
                'playlist-modify-private','user-follow-read','user-follow-modify','user-library-read','user-top-read','user-read-playback-state','streaming' ]
        ];

        $this->spotify = new SpotifyWebAPI\Session(
            $this->spotifyParams['client_id'],
            $this->spotifyParams['client_secret'],
            'http://127.0.0.1:8000/login/oauth'
        );


    }

    /**
     * @Route("/", name="login")
     */
    public function login( SessionInterface $session )
    {

        $options = [
            'scope' => $this->spotifyParams['scope'],
        ];

        $spotify_auth_url = $this->spotify->getAuthorizeUrl($options);
        $accessToken = $session->get('accessToken');
        if(!$accessToken ) {
            return $this->render('auth/login.html.twig', array(
                'spotify_auth_url' => $spotify_auth_url

            ));
        }
        else {
            return $this->redirectToRoute('dashboard');
        }

    }

    /**
     * @Route("/login/oauth", name="oauth")
     */
    public function oauth(Request $request, SessionInterface $session)
    {

        $accessCode = $request->get('code');
        $session->set('accessCode', $accessCode); // symfony session

        $this->spotify->requestAccessToken($accessCode);
        $accessToken = $this->spotify->getAccessToken();
        $session->set('accessToken', $accessToken); // symfony session

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout( SessionInterface $session )
    {
        $session->clear();
        $session->getFlashBag()->add('success', 'You have successfully logged out');

        return $this->redirectToRoute('login');
    }

}

?>