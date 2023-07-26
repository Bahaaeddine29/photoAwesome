<?php

namespace App\Controller;

use App\Form\MediaSearchType;
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


        $form = $this->createForm(MediaSearchType::class);
        $form->handleRequest($request);   // écoute les globales

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if($data['mediaTitle'] !== null) {
                $qb->andwhere('m.slug LIKE :toto')
                ->setParameter('toto', '%'. $data['mediaTitle'] .'%');
            }
            if($data['userEmail'] !== null) {
                $qb ->innerJoin('m.user', 'u')
                    ->andWhere('u.email = :email')
                     ->setParameter('email', $data['userEmail']);
            }
            // if($data['searchDate'] !== null) {
            //     $qb->andWhere('m.createAt >= :createAt')
            //         ->setParameter('createAt', $data['searchDate']);
            // }
        }


        $pagination = $this->paginator->paginate( 
            $qb, 
            $request->query->getInt('page', 1),  
            15,
        ); 

        return $this->render('media/index.html.twig', [
            'medias' => $pagination, 
            'form' => $form->createView()
        ]);
    }
}
