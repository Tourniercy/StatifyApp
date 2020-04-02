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
            'client_id' => 'e4e6d0a8791448eebb643e4ec22d0e89',
            'client_secret' => '14b342672e7447a19b03f03c79a11bb8',
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
            'scope' => $this->spotifyParams['scope']
        ];

        $spotify_auth_url = $this->spotify->getAuthorizeUrl($options);
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
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
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(Request $request, SessionInterface $session )
    {
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $me = $api->me();
        $topartists = $api->getMyTop('artists',['limit'=>50]);
        $toptracks = $api->getMyTop('tracks',['limit'=>50]);
        $myplaylists = $api->getMyPlaylists();
        $devices = $api->getMyDevices();

        return $this->render('view/dashboard.html.twig', array(
            'me' => $me,
            'topartists' => $topartists,
            'toptracks' => $toptracks,
            'myplaylists' => $myplaylists,
            'devices' => $devices
        ));
    }

    /**
     * @Route("/tracks", name="tracks")
     */
    public function tracks(Request $request, SessionInterface $session )
    {
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $me = $api->me();
        $topartists = $api->getMyTop('artists',['limit'=>50]);
        $toptracks = $api->getMyTop('tracks',['limit'=>50]);
        $myplaylists = $api->getMyPlaylists();
        $devices = $api->getMyDevices();

        return $this->render('view/tracks.html.twig', array(
            'me' => $me,
            'topartists' => $topartists,
            'toptracks' => $toptracks,
            'myplaylists' => $myplaylists,
            'devices' => $devices
        ));
    }

    /**
     * @Route("/artists", name="artists")
     */

    public function artists(Request $request, SessionInterface $session )
    {
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $me = $api->me();
        $topartists = $api->getMyTop('artists',['limit'=>50]);
        $toptracks = $api->getMyTop('tracks',['limit'=>50]);
        $myplaylists = $api->getMyPlaylists();
        $devices = $api->getMyDevices();

        return $this->render('view/artists.html.twig', array(
            'me' => $me,
            'topartists' => $topartists,
            'toptracks' => $toptracks,
            'myplaylists' => $myplaylists,
            'devices' => $devices
        ));
    }
    /**
     * @Route("/playlists", name="playlists")
     */

    public function playlists(Request $request, SessionInterface $session )
    {
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $me = $api->me();
        $topartists = $api->getMyTop('artists',['limit'=>50]);
        $toptracks = $api->getMyTop('tracks',['limit'=>50]);
        $myplaylists = $api->getMyPlaylists();
        $devices = $api->getMyDevices();

        return $this->render('view/playlists.html.twig', array(
            'me' => $me,
            'topartists' => $topartists,
            'toptracks' => $toptracks,
            'myplaylists' => $myplaylists,
            'devices' => $devices
        ));
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