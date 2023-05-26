<?php

namespace App\UserWebsite\Controller;

use App\Entity\Place;
use App\Entity\User;
use App\Form\ParmUserType;
use App\Form\PlaceType;
use App\Form\UserType;
use App\Repository\PlaceRepository;
use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/espace-user', name:'app_espace_user_')]
class HomeUserController extends AbstractController
{
    private $placeRepository;
    private $userRepository;
    private $slugify;
    private $hash;
    public function __construct(PlaceRepository $placeRepository, UserRepository $userRepository,UserPasswordHasherInterface $hash)
    {
        $this->placeRepository = $placeRepository;
        $this->userRepository = $userRepository;
        $this->slugify = new Slugify();
        $this->hash = $hash;
    }

    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        $places = $this->placeRepository->findBy(['user'=>$this->getUser()],['id'=>'DESC']);
        return $this->render('frontEnd/pages/user/home.html.twig', [ 
            'places' => $places,
        ]);
    }

    #[Route('/parametre', name:'param_user')]
    public function paramUser(Request $request): Response
    {
        $newUser = $this->userRepository->findOneBy(['id' => $this->getUser()]);
        $userForm = $this->createForm(ParmUserType::class, $newUser);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid()){
            $password = $this->hash->hashPassword($newUser,$newUser->getPassword());
            $newUser->setPassword($password);
            $this->userRepository->save($newUser,true);
            $this->addFlash('success','Ton compte a bien été mise à jour');
            return $this->redirectToRoute('app_espace_user_param_user');

        }
        return $this->render('frontEnd/pages/user/paramUser.html.twig', [ 
            'userForm' => $userForm,
        ]);

    }


    #[Route('/add', name: 'add_place')]
    public function addPlace(Request $request): Response
    {
        $newPlace = new Place();
        $placeForm = $this->createForm(PlaceType::class, $newPlace);
        $placeForm->handleRequest($request);
        if($placeForm->isSubmitted() && $placeForm->isValid()){
            $newPlace->setSlug($this->slugify->slugify($newPlace->getName()))->setDatePublish(new DateTime())->setNumberLike(0)->setUser($this->getUser());
            $this->placeRepository->save($newPlace,true);
            $this->addFlash('success','Le lieux a bien été ajouté');
            return $this->redirectToRoute('app_espace_user_home');

        }
        return $this->render('frontEnd/pages/user/add_place.html.twig', [ 
            'placeForm' => $placeForm,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_place')]
    public function editPlace(Request $request,$id): Response
    {
        $newPlace =$this->placeRepository->findOneBy(['id' => $id]);
        $placeForm = $this->createForm(PlaceType::class, $newPlace);
        $placeForm->handleRequest($request);
        if($placeForm->isSubmitted() && $placeForm->isValid()){
            $newPlace->setSlug($this->slugify->slugify($newPlace->getName()))->setDateModified(new DateTime());
            $this->placeRepository->save($newPlace,true);
            $this->addFlash('success','Le lieux a bien été modifié');
            return $this->redirectToRoute('app_espace_user_home');

        }
        return $this->render('frontEnd/pages/user/edit_place.html.twig', [ 
            'placeForm' => $placeForm,
        ]);
    }

    #[Route('/remove/{id}', name: 'remove_place')]
    public function removePlace($id): Response
    {
        $place = $this->placeRepository->findOneBy(['id' => $id]);
        $this->placeRepository->remove($place, true);
        $this->addFlash('success','Le lieux a bien été supprimé');
        return $this->redirectToRoute('app_espace_user_home');
    }
}
