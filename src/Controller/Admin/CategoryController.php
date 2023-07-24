<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/category')]
class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository, 
        private EntityManager $entityManager,
    )
    {
        
    }

    #[Route('/', name: 'app_category')]
    public function index(): Response
    {   
        $categoryEntities = $this->categoryRepository-> findAll();
        // dd($categoryentities); 
        return $this->render('category/index.html.twig', [
            'categories' => $categoryEntities,
        ]);
    }

    #[Route('/show/{id}', name: 'app_category_show')]
    public function detail($id): Response
    {   
        
        $categoryEntity = $this->categoryRepository-> find($id);
        // dd($categoryentities); 
        return $this->render('category/show.html.twig', [
           'category' => $categoryEntity
        ]);
    }

    #[Route('/new', name: 'app_category_new')]
    public function new (Request $request): Response
    {   
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) {

        }


        return $this->render('category/new.html.twig', [
            'form' => $form->createView()

        ]);
    }

}
