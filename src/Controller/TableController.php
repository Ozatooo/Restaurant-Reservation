<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TableRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

final class TableController extends AbstractController
{
    // #[Route('/table', name: 'app_table')]
    // public function index(): Response
    // {
    //     return $this->render('table/index.html.twig', [
    //         'controller_name' => 'TableController',
    //     ]);
    // }

    #[Route('/api/tables/{restaurantId}', name: 'api_tables')]
    public function getTables(int $restaurantId, TableRepository $tableRepository): JsonResponse
    {
        $tables = $tableRepository->findBy(['restaurant' => $restaurantId]);

        $data = [];
        foreach ($tables as $table) {
            $data['tables'][] = [
                'id' => $table->getId(),
                'number' => $table->getNumber(),
                'seats' => $table->getSeats(),
            ];
        }

        return $this->json($data);
    }
}
