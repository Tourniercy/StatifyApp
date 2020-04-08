<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\AuthController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SpotifyWebAPI;

class AjaxController extends AbstractController
{
    /**
     * @Route("/pause", name="pause")
     */
    public function pause(Request $request, SessionInterface $session )
    {
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $session->set('current_track', $api->getMyCurrentTrack()); // symfony session
        $api->pause();
        return $this->json(['url' => $session->get('current_track')->item->uri,'progress_ms' => $session->get('current_track')->progress_ms]);
    }
    /**
     * @Route("/play", name="play")
     */
    public function play(Request $request, SessionInterface $session )
    {
        $trackid = $request->request->get('trackid');
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        $response = $api->play(false, [
            'uris' => [$trackid],
        ]);
        return $this->json(['success' => $response]);
    }
    /**
     * @Route("/deleteTrackFromPlaylist", name="deleteTrackFromPlaylist")
     */
    public function deleteTrackFromPlaylist(Request $request, SessionInterface $session )
    {
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        $trackid = $request->request->get('id');
        $tracks = ['tracks' => [['id' => $trackid]]];
        $playlistid =  $request->request->get('playlistId');
        $data = $api->deletePlaylistTracks($playlistid, $tracks);
        return $this->json(['data' => $data]);
    }
    /**
     * @Route("/addTrackToPlaylist", name="addTrackToPlaylist")
     */
    public function addTrackToPlaylist(Request $request, SessionInterface $session )
    {
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        $trackid = $request->request->get('id');
        $playlistid =  $request->request->get('playlistId');
        $data = $api->addPlaylistTracks($playlistid, $trackid);
        return $this->json(['data' => $data]);
    }
    /**
     * @Route("/searchTrack", name="searchTrack")
     */
    public function searchTrack(Request $request, SessionInterface $session )
    {
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        $track_startsWith = $request->request->get('track_startsWith');
        $data = $api->search($track_startsWith,'track',["limit"=>5]);
        return new JsonResponse($data->tracks);
    }
}
