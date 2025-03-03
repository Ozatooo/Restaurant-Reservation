<?php

namespace App\Command;

use App\Entity\Reservation;
use App\Entity\Table;
use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-data',
    description: 'Generuje testowe dane: restaurację, stoliki i rezerwacje',
)]
class GenerateDataCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $faker = Factory::create();

        $restaurant = new Restaurant();
        $restaurant->setName('Restauracja Testowowa');
        $restaurant->setLocation('ul. Kozacka 415');

        $this->entityManager->persist($restaurant);
        $this->entityManager->flush();

        $output->writeln('<info>Dodano restaurację: Restauracja Testowowa</info>');

        for ($i = 22; $i <= 33; $i++) {
            $table = new Table();
            $table->setNumber($i);
            $table->setSeats($faker->numberBetween(2, 6));
            $table->setRestaurant($restaurant);

            $this->entityManager->persist($table);
        }
        $this->entityManager->flush();
        $output->writeln('<info>Dodano 10 stolików!</info>');

        $tables = $this->entityManager->getRepository(Table::class)->findAll();

        for ($i = 1; $i <= 10; $i++) {
            $reservation = new Reservation();
            $reservation->setCustomerName($faker->name);
            $reservation->setDate($faker->dateTimeBetween('now', '+1 month'));
            $reservation->setTable($faker->randomElement($tables));

            $this->entityManager->persist($reservation);
        }
        $this->entityManager->flush();
        $output->writeln('<info>Dodano 15 rezerwacji!</info>');

        return Command::SUCCESS;
    }
}
