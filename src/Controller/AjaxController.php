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
     * @Route("/pause", name="pause", methods={"POST"})
     */
    public function pause(Request $request, SessionInterface $session )
    {
        $apiAccess = $this->getApiAccess($session);
        $api = new SpotifyWebAPI\SpotifyWebAPI($apiAccess['options'],$apiAccess['SpotifyWebAPISession']);

        $session->set('current_track', $api->getMyCurrentTrack()); // symfony session
        $api->pause();
        return $this->json(['url' => $session->get('current_track')->item->uri,'progress_ms' => $session->get('current_track')->progress_ms]);
    }
    /**
     * @Route("/play", name="play", methods={"POST"})
     */
    public function play(Request $request, SessionInterface $session )
    {
        $trackid = $request->request->get('trackid');
        $apiAccess = $this->getApiAccess($session);
        $api = new SpotifyWebAPI\SpotifyWebAPI($apiAccess['options'],$apiAccess['SpotifyWebAPISession']);
        $response = $api->play(false, [
            'uris' => [$trackid],
        ]);
        return $this->json(['success' => $response]);
    }
    /**
     * @Route("/deleteTrackFromPlaylist", name="deleteTrackFromPlaylist", methods={"POST"})
     */
    public function deleteTrackFromPlaylist(Request $request, SessionInterface $session )
    {
        $apiAccess = $this->getApiAccess($session);
        $api = new SpotifyWebAPI\SpotifyWebAPI($apiAccess['options'],$apiAccess['SpotifyWebAPISession']);
        $trackid = $request->request->get('id');
        $tracks = ['tracks' => [['id' => $trackid]]];
        $playlistid =  $request->request->get('playlistId');
        $data = $api->deletePlaylistTracks($playlistid, $tracks);
        return $this->json(['data' => $data]);
    }
    /**
     * @Route("/addTrackToPlaylist", name="addTrackToPlaylist", methods={"POST"})
     */
    public function addTrackToPlaylist(Request $request, SessionInterface $session )
    {
        $apiAccess = $this->getApiAccess($session);
        $api = new SpotifyWebAPI\SpotifyWebAPI($apiAccess['options'],$apiAccess['SpotifyWebAPISession']);

        $trackid = $request->request->get('id');
        $playlistid =  $request->request->get('playlistId');
        $data = $api->addPlaylistTracks($playlistid, $trackid);
        return $this->json(['data' => $data]);
    }
    /**
     * @Route("/searchTrack", name="searchTrack", methods={"POST"})
     */
    public function searchTrack(Request $request, SessionInterface $session )
    {

        $apiAccess = $this->getApiAccess($session);
        $api = new SpotifyWebAPI\SpotifyWebAPI($apiAccess['options'],$apiAccess['SpotifyWebAPISession']);

        $track_startsWith = $request->request->get('track_startsWith');
        $data = $api->search($track_startsWith,'track',["limit"=>5]);
        return new JsonResponse($data->tracks);
    }

    public function getApiAccess(SessionInterface $session )
    {
        $redirect = false;
        $options = [
            'auto_refresh' => true,
        ];
        $accessToken = $session->get('accessToken');
        $SpotifyWebAPISession = $session->get('SpotifyWebAPI\Session');

        if( $accessToken===null ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $redirect = true;
        }
        if($SpotifyWebAPISession) {
            $newAccessToken = $SpotifyWebAPISession->getAccessToken();
            $newRefreshToken = $SpotifyWebAPISession->getRefreshToken();
        }

        return ['accessToken'=>$accessToken,'options'=>$options,'SpotifyWebAPISession'=>$SpotifyWebAPISession,'redirect'=>$redirect];
    }
    public function getActiveDevices($devices)
    {
        $active = false;
        foreach ($devices as $device){
            foreach ($device as $d){
                if($d->is_active) {
                    $active = true;
                }
            }
        }
        return $active;
    }
}
