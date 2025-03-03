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
    
        $reservationsPerDay = [];
        foreach ($reservations as $res) {
            $date = $res->getDate()->format('Y-m-d');
            $reservationsPerDay[$date] = ($reservationsPerDay[$date] ?? 0) + 1;
        }

        ksort($reservationsPerDay);

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
    
        $allDates = array_keys($reservationsPerDay);
        foreach ($reservationsByRestaurantPerDay as &$restaurantReservations) {
            foreach ($allDates as $date) {
                $restaurantReservations[$date] = $restaurantReservations[$date] ?? 0;
            }
            ksort($restaurantReservations);
        }
        
        $reservationsPerRestaurant = [];

        foreach ($restaurants as $restaurant) {
            foreach ($reservations as $res) {
                if ($res->getTable()->getRestaurant()->getId() === $restaurant->getId()) {
                    $reservationsPerRestaurant[$restaurant->getName()] = 
                        ($reservationsPerRestaurant[$restaurant->getName()] ?? 0) + 1;
                }
            }
        }
        
        $tableSizeReservations = [];

        foreach ($reservations as $res) {
            $tableSize = $res->getTable()->getSeats();

            if (!isset($tableSizeReservations[$tableSize])) {
                $tableSizeReservations[$tableSize] = 0;
            }

            $tableSizeReservations[$tableSize]++;
        }


        return $this->render('home.html.twig', [
            'restaurantCount' => $restaurantCount,
            'reservationCount' => $reservationCount,
            'reservationsPerDay' => $reservationsPerDay,
            'reservationsByRestaurantPerDay' => $reservationsByRestaurantPerDay,
            'reservationsPerRestaurant' => $reservationsPerRestaurant,
            'tableSizeReservations' => $tableSizeReservations,
        ]);
    }
    
    
}
