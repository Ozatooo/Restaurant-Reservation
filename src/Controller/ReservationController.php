<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Restaurant;
use App\Entity\Table;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReservationController extends AbstractController
{
    #[Route('/reservations', name: 'reservation_list')]
    public function index(EntityManagerInterface $em): Response
    {
        $reservations = $em->getRepository(Reservation::class)->findAll();
        $tables = $em->getRepository(Table::class)->findAll(); 
        $restaurants = $em->getRepository(Restaurant::class)->findAll(); 
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
            'tables'       => $tables,
            'restaurants'  => $restaurants
        ]);
    }

    #[Route('/api/reservations/{restaurantId}', name: 'api_reservations')]
    public function getReservations(int $restaurantId, ReservationRepository $reservationRepository): JsonResponse
    {
        $reservations = $reservationRepository->findBy(['restaurant' => $restaurantId]);

        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'customerName' => $reservation->getCustomerName(),
                'tableNumber' => $reservation->getTable()->getNumber(),
                'date' => $reservation->getDate()->format('Y-m-d H:i'),
            ];
        }

        return $this->json($data);
    }

    #[Route('/reservation/add', name: 'reservation_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $tableId = $request->request->get('table_id');
        $customerName = $request->request->get('customer_name');
        $date = new \DateTime($request->request->get('date'));

        $table = $em->getRepository(Table::class)->find($tableId);
        if (!$table) {
            return $this->redirectToRoute('reservation_list');
        }

        $existing = $em->getRepository(Reservation::class)->findOneBy([
            'table' => $table,
            'date' => $date,
        ]);

        if ($existing) {
            return new Response('Ten stolik jest już zajęty na wybraną datę!', 400);
        }

        $reservation = new Reservation();
        $reservation->setTable($table);
        $reservation->setCustomerName($customerName);
        $reservation->setDate($date);

        $em->persist($reservation);
        $em->flush();

        return $this->redirectToRoute('reservation_list');
    }
}
