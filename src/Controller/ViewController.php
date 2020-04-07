<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use SpotifyWebAPI;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Entity\TopTracks;
use Doctrine\ORM\EntityManagerInterface;

class ViewController extends AbstractController
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
//        $sessiontracks =  $session->get('tracks');
//        $tracks = [];
//        $i = 0;
//        foreach ($toptracks->items as $item) {
//            $tracks[$i] = $item->id;
//            $i++;
//        }
//        $entityManager = $this->getDoctrine()->getManager();
//        $tracksindb = new TopTracks();
//        if(!isset($sessiontracks)){
//            $lastTrack = $this->getDoctrine()
//                ->getRepository(TopTracks::class)
//                ->findOneBy(['userid' => $me->id]);
//            if ($lastTrack) {
//                foreach ($lastTrack->getTracks() as $key => $value) {
//                    $secondkey = array_search($value, $tracks);
//                    if ($secondkey != $key) {
////                        dd($key-$secondkey);
//                    }
//                };
//                $entityManager->remove($lastTrack);
//                $session->set('tracks', $lastTrack); // symfony session
//            }
//        } else {
//            foreach ($sessiontracks as $key => $value) {
//                $secondkey = array_search($value, $tracks);
//                if ($secondkey != $key) {
////                    dd($key-$secondkey);
//                }
//            };
//        }
//        $tracksindb->setUserid($me->id)->setTracks($tracks);
//        $entityManager->persist($tracksindb);
//        $entityManager->flush();

        $active = false;
        foreach ($devices as $device){
            foreach ($device as $d){
                if($d->is_active) {
                    $active = true;
                }

            }

        }
        return $this->render('view/dashboard.html.twig', array(
            'me' => $me,
            'topartists' => $topartists,
            'toptracks' => $toptracks,
            'myplaylists' => $myplaylists,
            'devices' => $devices,
            'active' => $active
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
        $active = false;
        foreach ($devices as $device){
            foreach ($device as $d){
                if($d->is_active) {
                    $active = true;
                }

            }

        }
        return $this->render('view/tracks.html.twig', array(
            'me' => $me,
            'toptracks' => $toptracks,
            'devices' => $devices,
            'active' => $active
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
        $active = false;
        foreach ($devices as $device){
            foreach ($device as $d){
                if($d->is_active) {
                    $active = true;
                }

            }

        }
        return $this->render('view/playlists.html.twig', array(
            'me' => $me,
            'myplaylists' => $myplaylists,
            'devices' => $devices,
            'active' => $active
        ));
    }

    /**
     * @Route("/playlist/new", name="playlistNew")
     * @param Request $request
     * @param SessionInterface $session
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return Response
     */

    public function playlistNew(Request $request, SessionInterface $session)
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
        $devices = $api->getMyDevices();

        $form = $this->createFormBuilder()
            ->add('title', TextType::class, ['constraints' =>new NotBlank(),'required'=>false])
            ->add('description', TextareaType::class, ['required'=>false])
            ->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $data["description"];
            $api->createPlaylist([
                'name' => $data["title"],
                'description' => $data["description"]
            ]);
        }

        return $this->render('view/newplaylist.html.twig', array(
            'me' => $me,
            'devices' => $devices,
            'form' => $form->createView()
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => new Length(['min' => 3]),
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
        ;
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
