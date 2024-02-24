<?php

namespace App\Controller;

use App\Entity\Activities;
use App\Form\ActivitiesType;
use App\Repository\ActivitiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Psr\Log\LoggerInterface;

#[Route('/activities')]
class ActivitiesController extends AbstractController
{
    


    #[Route('/', name: 'app_activities_index', methods: ['GET'])]
    public function index(ActivitiesRepository $activitiesRepository): Response
    {
        return $this->render('activities/index.html.twig', [
            'activities' => $activitiesRepository->findAll(),
        ]);
    }

    #[Route('/activities', name: 'app_activities_indextemp', methods: ['GET'])]

    public function showDest(EntityManagerInterface $entityManager)
    {
        // Récupérer tous les articles
        $act = $entityManager->getRepository(Activities::class)->findAll();

    
        // Créer le rendu Twig
        return $this->render('activities/activities.html.twig', [
            'activities' => $act,
        ]);
    }
    

    #[Route('/new', name: 'app_activities_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $activity = new Activities();
        $form = $this->createForm(ActivitiesType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('img')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('destination_img'),
                        $newFilename
                    );
                    
                } catch (FileException $e) {
                     // Handle exception if something happens during file upload
               
                }
                $activity->setImg($newFilename);
            }
            //if (!$brochureFile) {
                // Handle error if 'img' is not provided
               // $this->addFlash('error', 'Please upload an image.');
                //return $this->redirectToRoute('app_activities_new');
           // }
            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_activities_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activities/new.html.twig', [
            'activity' => $activity,
            'activityform' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activities_show', methods: ['GET'])]
    public function show(Activities $activity): Response
    {
        return $this->render('activities/show.html.twig', [
            'activity' => $activity,
        ]);
    }

    #[Route('/details/{id}', name: 'app_activities_showdetails', methods: ['GET'])]
    public function showdetails(Activities $activities): Response
    {
        return $this->render('activities/activitiesdetails.html.twig', [
            'activities' => $activities,
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_activities_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activities $activity, EntityManagerInterface $entityManager, SluggerInterface $slugger, $id): Response
{
    // Retrieve the activity
    $activity = $entityManager->getRepository(Activities::class)->find($id);
    
    // Create the form and bind it to the activity entity
    $form = $this->createForm(ActivitiesType::class, $activity);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Retrieve the uploaded file from the request
        $imageFile = $form->get('img')->getData();

        // Check if a file was uploaded
        if ($imageFile) {
            // Generate a unique name for the file
            $newFilename = md5(uniqid()).'.'.$imageFile->guessExtension();

            // Move the file to the desired location
            try {
                $imageFile->move(
                    $this->getParameter('destination_img'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Handle file upload error
            }

            // Set the file name in the activity entity
            $activity->setImg($newFilename);
        }

        // Persist changes to the database
        $entityManager->flush();

        // Redirect the user
        return $this->redirectToRoute('app_activities_index');
    }

    // Render the form
    return $this->render('activities/edit.html.twig', [
        'activity' => $activity,
        'activityform' => $form->createView(),
    ]);
}

    

    #[Route('/{id}', name: 'app_activities_delete', methods: ['POST'])]
    public function delete(Request $request, Activities $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->request->get('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activities_index', [], Response::HTTP_SEE_OTHER);
    }
}
