<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Editor;
use App\Form\EditorType;

use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/editor')]
class EditorController extends AbstractController
{
    #[Route('', name: 'app_admin_editor_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/editor/index.html.twig', [
            'controller_name' => 'EditorController',
        ]);
    }

    #[Route('/EditorRequestForm', name: 'app_admin_editor_EditorRequestForm', methods:['POST', 'GET'])]
    public function EditorRequestForm(Request $request, EntityManagerInterface $manager): Response
    {
        $editor = new Editor;
        $form = $this->createForm(EditorType::class, $editor);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($editor);
            $manager->flush();
            
            return $this->redirectToRoute(route:'app_admin_editor_index'); 
        }

        return $this->render('admin/editor/EditorRequestForm.html.twig', [
            'editorform' => $form,
        ]);
    }

}
