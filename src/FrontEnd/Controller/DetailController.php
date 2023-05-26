<?php

namespace App\FrontEnd\Controller;

use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/detail',name:'app_detail_')]
class DetailController extends AbstractController
{
    private $placeRepository;
    public function __construct(PlaceRepository $placeRepository)
    {
        $this->placeRepository = $placeRepository;
    }

    #[Route('/{user}/place-list', name: 'list_place')]
    public function placeList($user): Response
    {
        $places = $this->placeRepository->findBy(['user' => $user]);
        return $this->render('frontEnd/pages/listPlace.html.twig', [ 
            'places' => $places,
        ]);
    }

    #[Route('/place/{id}', name: 'place')]
    public function placeDetail($id): Response
    {
        $place = $this->placeRepository->findOneBy(['id' => $id]);
        return $this->render('frontEnd/pages/placeDetail.html.twig', [ 
            'place' => $place,
        ]);
    }
}
