<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;

use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/author')]
class AuthorController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('', name: 'app_admin_author_index', methods: ['GET'])]
    public function index(Request $request, AuthorRepository $repository): Response
    {
        $dates = [];
        if ($request->query->has('start')) {
            $dates['start'] = $request->query->get('start');
        }
        
        if ($request->query->has('end')) {
            $dates['end'] = $request->query->get('end');
        }
        
        $authors = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->findByDateOfBirth($dates)),
            $request->query->get('page', 1),
            maxPerPage:10
        );
            
        //sans Pagerfanta
        //$authors = $repository->findByDateOfBirth($dates);
        //$authors = $repository->findAll();
        
        return $this->render('admin/author/index.html.twig', [
            'controller_name' => 'AuthorController',
            'authors' => $authors,
        ]);
    }

    //2 routes pour le controleur
    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Author $author, Request $request, EntityManagerInterface $manager): Response
    {
        // Controle d'accès sur l'ajout d'un nouvel autheur :
        // seul les utilisateurs ayant le role admin définit dans Entity User peuvent accéder à cette page
        if($author == null) {
                $this->denyAccessUnlessGranted(attribute:'ROLE_ADMIN');
        }
        // un opérateur de fusion null ??= signifie que si $author vaut null, 
        // on lui assigne comme valeur une nouvelle instance
        // sinon on garde celle qui est passée en argument.
        $author ??= new Author();
        
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // préparer a écrire en bdd
            $manager->persist($author);
            // ecrire en bdd
            $manager->flush();
            //retour à la page d'accueil d'author
            return $this->redirectToRoute(route:'app_admin_author_index');  
        }
        return $this->render('admin/author/new.html.twig', [
            'form' => $form,
        ]);
    }
    // dans la route ci-dessous il y a :
    //       /{id} qui permet de dire qu'il y a un argument a passer dans l'URL
    //      et requirements qui est un tableau contenant une expression régulière pour chaque argument qui transite
    #[Route('/{id}', name: 'app_admin_author_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Author $author): Response
    {

        return $this->render('admin/author/show.html.twig', ['author' => $author]);
    }
}
