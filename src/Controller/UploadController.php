<?php

namespace App\Controller;

use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Asset;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends AbstractController
{
    #[Route('/upload/pdfs', name: 'upload_pdfs', methods: ['POST'])]
    public function uploadPDFs(Request $request): Response
    {
        $data = $request->request->all();
        $files = $request->files->all();

        foreach ($files as $key => $pdfList) {
            if (preg_match('/^pdfs_(\d+)$/', $key, $matches)) {
                $productId = (int) $matches[1];
                /** @var Product $product */
                $product = Product::getById($productId);

                if (!$product) {
                    continue;
                }

                $existingPdfs = $product->getPDF() ?? [];

                /** @var UploadedFile $pdf */
                foreach ($pdfList as $pdf) {
                    if ($pdf instanceof UploadedFile && $pdf->isValid()) {
                        $asset = new Asset\Document();
                        $asset->setFilename(uniqid() . '-' . $pdf->getClientOriginalName());
                        $asset->setData(file_get_contents($pdf->getRealPath()));
                        $asset->setMimeType($pdf->getClientMimeType());
                        $asset->setParentId(1); 
                        $asset->save();

                        $existingPdfs[] = $asset;
                    }
                }

                $product->setPDF($existingPdfs);
                $product->save();
            }
        }

        if (isset($data['delete_ids'])) {
            foreach ($data['delete_ids'] as $id) {
                $product = Product::getById((int) $id);
                if ($product) {
                    $product->delete();
                }
            }
        }

        $this->addFlash('success', 'Daten wurden gespeichert.');
        return $this->redirectToRoute('catalog'); 
    }


 /**
     * @Route("/csv", name="csv")
     */
    public function csvAction(Request $request): Response
    {
        $message = null;

        if ($request->isMethod('POST') && $request->files->get('csv')) {
            /** @var UploadedFile $file */
            $file = $request->files->get('csv');

            if ($file && $file->getClientOriginalExtension() === 'csv') {
                $csvData = array_map('str_getcsv', file($file->getPathname()));
                

                foreach ($csvData as $row) {

                    $product = new Product();
                    $product->setArtikelnummer($row[0]);
                    $product->setArtikelbezeichnung($row[1]);
                    $product->setMengeneinheit($row[2]);
                    $product->setPreis($row[3]);
                    $product->setHersteller($row[4]);
                    $product->save();
                }

                $message = 'CSV-Datei erfolgreich verarbeitet und Produkte gespeichert.';
            } else {
                $message = 'Bitte lade eine gültige CSV Datei hoch.';
            }
        }

        return $this->render('csvUpload.html.twig', [
            'success' => $message,
        ]);
    }
}


