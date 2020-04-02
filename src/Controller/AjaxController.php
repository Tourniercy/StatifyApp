<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\AuthController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SpotifyWebAPI;

class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax", name="ajax")
     */
    public function index()
    {
        return $this->render('ajax/index.html.twig', [
            'controller_name' => 'AjaxController',
        ]);
    }
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
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }
        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        $api->play(false, [
            'uris' => [ $session->get('current_track')->item->uri],
        ]);
        $api->seek([
            'position_ms' => 60000 + 37000, // Move to the 1.37 minute mark
        ]);
        return($api->getMyCurrentPlaybackInfo());
    }
}
