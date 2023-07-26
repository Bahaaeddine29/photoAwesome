<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    public function __construct (
        private PaginatorInterface $paginator, 
        private MediaRepository $mediaRepository
    )
    {

    }
    #[Route('/media', name: 'app_media')]
    public function index(Request $request): Response
    {
        $qb = $this->mediaRepository->getQbAll(); 
        $pagination = $this->paginator->paginate( 
            $qb, 
            $request->query->getInt('page', 1),  
            15,
        ); 

        return $this->render('media/index.html.twig', [
            'medias' => $pagination
        ]);
    }
}
