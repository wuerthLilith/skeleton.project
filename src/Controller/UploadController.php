<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Pimcore\Model\Asset;

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

            $asset = new Asset();
            $asset->setFilename($file->getClientOriginalName());
            $asset->setData(file_get_contents($file->getPathname()));

            $parentAsset = Asset::getByPath('/uploads');
            if (!$parentAsset) {
                $parentAsset = new Asset();
                $parentAsset->setFilename('uploads');
                $parentAsset->setParentId(1);  
                $parentAsset->save();
            }

            $asset->setParent($parentAsset);
            $asset->save();

            $message = 'Hochgeladen: ' . $file->getClientOriginalName();
        }

        return $this->render('upload.html.twig', [
            'success' => $message,
        ]);
    }
}
