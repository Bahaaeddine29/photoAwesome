<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/category')]
class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository
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

    #[Route('/show/{id}', name: 'app_category')]
    public function detail($id): Response
    {   
        
        $categoryEntity = $this->categoryRepository-> find($id);
        // dd($categoryentities); 
        return $this->render('category/show.html.twig', [
           'category' => $categoryEntity
        ]);
    }
}
