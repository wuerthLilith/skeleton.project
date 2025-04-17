<?php
namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Product;

class UploadController extends FrontendController
{
    /**
     * @Route("/upload", name="upload")
     */
    public function uploadAction(Request $request): Response
    {
        $message = null;

        if ($request->isMethod('POST') && $request->files->get('pdf')) {
            /** @var UploadedFile $file */
            $file = $request->files->get('pdf');
            $title = $request->request->get('title') ?? 'upload';

            // Asset erstellen und speichern
            $asset = new Asset();
            $asset->setFilename($file->getClientOriginalName());
            $asset->setData(file_get_contents($file->getPathname()));

            // Prüfen, ob der Upload-Ordner existiert, andernfalls erstellen
            $parentAsset = Asset::getByPath('/uploads');
            if (!$parentAsset) {
                $parentAsset = new Asset();
                $parentAsset->setFilename('uploads');
                $parentAsset->setParentId(1);  
                $parentAsset->save();
            }

            $asset->setParent($parentAsset);
            $asset->save();

            // PDF dem Produkt zuweisen (falls eine Produkt-ID übergeben wird)
            $productId = $request->request->get('product_id');
            if ($productId) {
                $product = Product::getById($productId);
                if ($product) {
                    // Das PDF dem Produkt zuweisen
                    $pdfs = $product->getPDF();
                    $pdfs[] = $asset;  // PDF zu der bestehenden Liste hinzufügen
                    $product->setPDF($pdfs); // Setzt das PDF-Feld
                    $product->save();
                    $message = 'PDF erfolgreich hochgeladen und mit Produkt verbunden!';
                } else {
                    $message = 'Produkt nicht gefunden!';
                }
            } else {
                $message = 'Hochgeladen: ' . $file->getClientOriginalName();
            }
        }

        return $this->render('upload.html.twig', [
            'success' => $message,
        ]);
    }
}

