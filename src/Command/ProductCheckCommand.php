<?php

namespace App\Command;

use Pimcore\Model\DataObject\Product;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductCheckCommand extends Command
{
    protected static $defaultName = 'app:check-product';

    protected function configure()
    {
        $this->setDescription('Check a product by ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $productId = 1; 
        $product = Product::getById($productId);

        if ($product) {
            $output->writeln("Produkt gefunden:");
            $output->writeln("Artikelnummer: " . $product->getArtikelnummer());
            $output->writeln("Bezeichnung: " . $product->getArtikelbezeichnung());
            $output->writeln("Preis: " . $product->getPreis() . " €");
            $output->writeln("Hersteller: " . $product->getHersteller());
        } else {
            $output->writeln("Produkt mit der ID $productId nicht gefunden.");
        }

        return Command::SUCCESS;
    }
}
