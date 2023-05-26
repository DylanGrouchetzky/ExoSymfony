<?php

namespace App\FrontEnd\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\PlaceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $placeRepository;
    private $userRepository;
    private $hash;
    public function __construct(PlaceRepository $placeRepository, UserRepository $userRepository,UserPasswordHasherInterface $hash)
    {
        $this->placeRepository = $placeRepository;
        $this->userRepository = $userRepository;
        $this->hash = $hash;
    }

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        $places = $this->placeRepository->findBy([],['id'=>'DESC']);
        return $this->render('frontEnd/pages/home.html.twig', [ 
            'places' => $places,
        ]);
    }
    
    #[Route('/sign-in', name:'app_sign_in')]
    public function signIn(Request $request): Response{
        $newUser = new User();
        $userForm = $this->createForm(UserType::class, $newUser);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid()){
            $password = $this->hash->hashPassword($newUser,$newUser->getPassword());
            $newUser->setPassword($password)->setRoles(['ROLE_USER']);
            $this->userRepository->save($newUser,true);
            $this->addFlash('success','Ton compte a bien été créé');
            return $this->redirectToRoute('app_home');

        }
        return $this->render('frontEnd/pages/user/sign_in.html.twig', [ 
            'userForm' => $userForm,
        ]);

    }
}
