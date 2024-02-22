<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationController extends AbstractController
{  private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
/*
    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }*/
            // RESERVATION PRICE

/*
            public function calculateTotalPrice(): void
            {
                $destinationPrice = 100; 
                $this->totalPrice = $this->daysNumbers * $destinationPrice;
            }*/
        
            #[Route('/reservation/create/{destinationId}', name: 'app_reservation_create')]
            public function create(Request $request, $destinationId): Response
            {
                // Fetch the destination from the database using EntityManagerInterface
                $destination = $this->entityManager->getRepository(Destination::class)->find($destinationId);
        
                // Create a new reservation and associate it with the destination
                $reservation = new Reservation();
                $reservation->setDestination($destination);
        
                // Create the form
                $form = $this->createForm(ReservationType::class, $reservation);
                $form->handleRequest($request);
        
                if ($form->isSubmitted() && $form->isValid()) {
                    // Calculate the number of days
                    $daysNumbers = $this->calculateDays($reservation->getStartDate(), $reservation->getEndDate());
        
                    // Calculate total price based on daysNumbers and destination price
                    $totalPrice = $this->calculateTotalPrice($daysNumbers, $reservation->getDestination());
        
                    // Set the calculated values to the reservation object
                    $reservation->setNumber($daysNumbers);
                    $reservation->setTotalPrice($totalPrice);
                    $reservation->setStatus("Done");

                    // Handle form submission
                    $this->entityManager->persist($reservation);
                    $this->entityManager->flush();
        
                    return $this->redirectToRoute('app_reservation_show', ['id' => $reservation->getId()]);
                }
        
                return $this->render('reservation/new.html.twig', [
                    'form' => $form->createView()
                ]);
            }
/** 
    * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     * @return int
     */
    private function calculateDays(\DateTimeInterface $startDate, \DateTimeInterface $endDate): int
    {
        $interval = $endDate->diff($startDate);
        return $interval->days;
    }

    /**
     * Calculates the total price based on the number of days and destination.
     *
     * @param int $daysNumbers
     * @param Destination $destination
     * @return float
     */
    private function calculateTotalPrice(int $daysNumbers, Destination $destination): float
    {
        // Fetch the destination price from the Destination entity
        $destinationPrice = $destination->getPrix(); // Assuming you have a method to get the price from the Destination entity

        // Calculate the total price based on the destination price and the number of days
        return $daysNumbers * $destinationPrice;
    }

    // Your other methods...

  /*  public function calculateDays(\DateTimeInterface $startDate, \DateTimeInterface $endDate): int
    {
        $interval = $endDate->diff($startDate);
        return $interval->days;
    }
    public function calculateTotalPrice(int $daysNumbers, string $destination): float
    {
        // Implement your pricing logic here based on daysNumbers, destination, or any other factors
        // For example, let's say the price per day varies based on the destination
        $pricePerDay = $this->getPrix(); // Assume a method to retrieve destination price
        return $daysNumbers * $pricePerDay;
    }*/

   


    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
