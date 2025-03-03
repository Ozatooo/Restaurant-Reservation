<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ReservationRepository $reservationRepo, RestaurantRepository $restaurantRepo): Response {
        $restaurants = $restaurantRepo->findAll();
        $reservations = $reservationRepo->findAll();
    
        $restaurantCount = count($restaurants);
        $reservationCount = count($reservations);
    
        // Ilość rezerwacji na dzień
        $reservationsPerDay = [];
        foreach ($reservations as $res) {
            $date = $res->getDate()->format('Y-m-d');
            $reservationsPerDay[$date] = ($reservationsPerDay[$date] ?? 0) + 1;
        }
        ksort($reservationsPerDay); // Sortowanie dat rosnąco
    
        // Ilość rezerwacji w restauracjach na dzień
        $reservationsByRestaurantPerDay = [];
        foreach ($restaurants as $restaurant) {
            foreach ($reservations as $res) {
                if ($res->getTable()->getRestaurant()->getId() === $restaurant->getId()) {
                    $date = $res->getDate()->format('Y-m-d');
                    $reservationsByRestaurantPerDay[$restaurant->getName()][$date] =
                        ($reservationsByRestaurantPerDay[$restaurant->getName()][$date] ?? 0) + 1;
                }
            }
        }
    
        // Upewniamy się, że daty w tabeli są zgodne z wykresem
        $allDates = array_keys($reservationsPerDay);
        foreach ($reservationsByRestaurantPerDay as &$restaurantReservations) {
            foreach ($allDates as $date) {
                $restaurantReservations[$date] = $restaurantReservations[$date] ?? 0;
            }
            ksort($restaurantReservations);
        }
        
        return $this->render('home.html.twig', [
            'restaurantCount' => $restaurantCount,
            'reservationCount' => $reservationCount,
            'reservationsPerDay' => $reservationsPerDay,
            'reservationsByRestaurantPerDay' => $reservationsByRestaurantPerDay,
        ]);
    }
    
    
}
