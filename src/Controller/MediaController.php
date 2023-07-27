<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\MediaSearchType;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/media')]
class MediaController extends AbstractController
{
    public function __construct (
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator, 
        private MediaRepository $mediaRepository, 

    )
    {

    }
    #[Route('/', name: 'app_media')]

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

    #[Route('/add', name: 'app_media_add')]

    public function add (Request $request, SluggerInterface $slugger ) : Response {

        /**
         * recupère l'utilisateur connecté 
         * soit une entitée User (si connecté)
         * soit null (si pas connecté)
         */

        $user = $this->getUser();

        $uploadDirectory = $this->getParameter( 'upload_file'); 
        // dd ($uploadDirectory); 

        $mediaEntity = new Media ();
        // je relis le media au user connceté 
        $mediaEntity->setUser($user);
        // Je donne la date de tout suite au media
        $mediaEntity->setCreateAt( new \DateTime()); 

        $form = $this->createForm(MediaType::class, $mediaEntity);
        $form->handleRequest($request);  

        if ($form->isSubmitted() &&$form->isValid()) {
            // j'utilise le sluggerInterface pour créer un slug à partir du titre 
            $slug = $slugger->slug($mediaEntity->getTitle()); 
            // je set le slug crée avant à mon media 
            $mediaEntity->setSlug ($slug); 

            $file = $form->get('file')->getData(); 

            if ($file){
                /** @var UploadedFile $file  */
                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName); 

                $newFileName = $safeFileName . '-' . uniqid() . '.' . $file->guessExtension();

                // je bouge le fichier dans le dossier d'upload avec son nouveau nom 
                try {
                    $file->move (
                        $this->getParameter('upload_file'), 
                        $newFileName
                    ); 

                // je donne le chemin du fichier à mon média 
                    $mediaEntity->setFilePath($newFileName); 
                } catch (FileException $e){

                }
            }
            // dd($file); 
        }

        return $this->render('media/add.html.twig', [
            'formMedia' => $form->createView(),
        ]); 

        $this->entityManager->persist($mediaEntity); 
        $this->entityManager->flush();

        return $this->redirectToRoute('app_media'); 

    }


}
