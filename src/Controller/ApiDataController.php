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

class ApiDataController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function dashboard(Request $request, SessionInterface $session )
    {
        $options = [
            'auto_refresh' => true,
        ];
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }
        $api = new SpotifyWebAPI\SpotifyWebAPI($options);;
        $api->setAccessToken($accessToken);

        $me = $api->me();
        $topartists = $api->getMyTop('artists',['limit'=>6,'time_range'=>'short_term']);
        $toptracks = $api->getMyTop('tracks',['limit'=>6,'time_range'=>'short_term']);
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
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function tracks(Request $request, SessionInterface $session )
    {
        $options = [
            'auto_refresh' => true,
        ];
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI($options);
        $api->setAccessToken($accessToken);

        $me = $api->me();
        $toptracks = $api->getMyTop('tracks',['limit'=>50,'time_range'=>'long_term']);
        $devices = $api->getMyDevices();

        return $this->render('view/tracks.html.twig', array(
            'me' => $me,
            'toptracks' => $toptracks,
            'devices' => $devices
        ));
    }

    /**
     * @Route("/artists", name="artists")
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */

    public function artists(Request $request, SessionInterface $session )
    {
        $options = [
            'auto_refresh' => true,
        ];
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI($options);;
        $api->setAccessToken($accessToken);

        $me = $api->me();
        $topartists = $api->getMyTop('artists',['limit'=>50,'time_range'=>'long_term']);
        $devices = $api->getMyDevices();

        return $this->render('view/artists.html.twig', array(
            'me' => $me,
            'topartists' => $topartists,
            'devices' => $devices
        ));
    }

    /**
     * @Route("/playlists", name="playlists")
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */

    public function playlists(Request $request, SessionInterface $session )
    {
        $options = [
            'auto_refresh' => true,
        ];
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI($options);;
        $api->setAccessToken($accessToken);
        $me = $api->me();
        $myplaylists = $api->getMyPlaylists();
        $devices = $api->getMyDevices();

        return $this->render('view/playlists.html.twig', array(
            'me' => $me,
            'myplaylists' => $myplaylists,
            'devices' => $devices
        ));
    }

    /**
     * @Route("/playlist/{id}", name="playlistEdit")
     * @param Request $request
     * @param SessionInterface $session
     * @param int $id
     * @return Response
     */

    public function playlistEdit(Request $request, SessionInterface $session,$id )
    {
        $options = [
            'auto_refresh' => true,
        ];
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI($options);;
        $api->setAccessToken($accessToken);
        $me = $api->me();
        $playlist = $api->getPlaylist($id);
        $devices = $api->getMyDevices();

        return $this->render('view/playlist.html.twig', array(
            'me' => $me,
            'playlist' => $playlist,
            'devices' => $devices
        ));
    }
}
