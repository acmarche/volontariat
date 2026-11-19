<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Search\Searcher;
use AcMarche\Volontariat\Security\RolesEnum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::admin->value)]
class ExportController extends AbstractController
{
    public function __construct(
        private VolontaireRepository $volontaireRepository,
        private readonly AssociationRepository $associationRepository
    ) {
    }

    #[Route(path: '/admin/export/volontaire/xls', name: 'volontariat_admin_volontaire_xls', methods: ['GET'])]
    public function volontaireXls(Request $request): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $this->volontaireXSLObject($request, $spreadsheet);
        $xlsx = new Xlsx($spreadsheet);
        $streamedResponse = new StreamedResponse(
            function () use ($xlsx): void {
                $xlsx->save('php://output');
            },
            Response::HTTP_OK,
            []
        );
        $streamedResponse->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $streamedResponse->headers->set('Content-Disposition', 'attachment;filename=volontaires.xls');
        $streamedResponse->headers->set('Pragma', 'public');
        $streamedResponse->headers->set('Cache-Control', 'maxage=1');

        return $streamedResponse;
    }

    private function volontaireXSLObject(Request $request, Spreadsheet $spreadsheet): Worksheet
    {
        $query = $request->getSession()->get(Searcher::searchVolontaires, []);
        $volontaires = $this->volontaireRepository->search($query);

        $worksheet = $spreadsheet->getActiveSheet();

        /**
         * title.
         */
        $c = 1;
        $worksheet->setCellValue('A'.$c, 'Nom')
            ->setCellValue('B'.$c, 'Prenom')
            ->setCellValue('C'.$c, 'Rue')
            ->setCellValue('D'.$c, 'Code postal')
            ->setCellValue('E'.$c, 'Localité')
            ->setCellValue('F'.$c, 'Né le')
            ->setCellValue('G'.$c, 'Téléphone')
            ->setCellValue('H'.$c, 'Gsm')
            ->setCellValue('I'.$c, 'Email')
            ->setCellValue('J'.$c, 'Emploi')
            ->setCellValue('K'.$c, 'Disponibilité')
            ->setCellValue('L'.$c, 'Description');

        $l = 2;

        foreach ($volontaires as $volontaire) {
            $neLe = null != $volontaire->birthyear;

            $worksheet->setCellValue('A'.$l, $volontaire->name)
                ->setCellValue('B'.$l, $volontaire->surname)
                ->setCellValue('C'.$l, $volontaire->address)
                ->setCellValue('D'.$l, $volontaire->postalCode)
                ->setCellValue('E'.$l, $volontaire->city)
                ->setCellValue('F'.$l, $neLe)
                ->setCellValue('G'.$l, $volontaire->phone)
                ->setCellValue('H'.$l, $volontaire->mobile)
                ->setCellValue('I'.$l, $volontaire->email)
                ->setCellValue('J'.$l, $volontaire->job)
                ->setCellValue('K'.$l, $volontaire->availability)
                ->setCellValue('L'.$l, $volontaire->description);
            ++$l;
        }

        return $worksheet;
    }

    #[Route(path: '/admin/export/association/xls', name: 'volontariat_admin_association_xls', methods: ['GET'])]
    public function associationXls(Request $request): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $this->associationXlsObject($request, $spreadsheet);
        $xlsx = new Xlsx($spreadsheet);
        $streamedResponse = new StreamedResponse(
            function () use ($xlsx): void {
                $xlsx->save('php://output');
            },
            Response::HTTP_OK,
            []
        );
        $streamedResponse->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $streamedResponse->headers->set('Content-Disposition', 'attachment;filename=associations.xls');
        $streamedResponse->headers->set('Pragma', 'public');
        $streamedResponse->headers->set('Cache-Control', 'maxage=1');

        return $streamedResponse;
    }

    private function associationXlsObject(Request $request, Spreadsheet $spreadsheet): Worksheet
    {
        $query = $request->getSession()->get(Searcher::searchAssocations, []);
        $associations = $this->associationRepository->search($query);

        $worksheet = $spreadsheet->getActiveSheet();

        /**
         * title.
         */
        $c = 1;
        $worksheet->setCellValue('A'.$c, 'Nom')
            ->setCellValue('B'.$c, 'Rue')
            ->setCellValue('C'.$c, 'Code postal')
            ->setCellValue('D'.$c, 'Localité')
            ->setCellValue('E'.$c, 'Téléphone')
            ->setCellValue('F'.$c, 'Gsm')
            ->setCellValue('G'.$c, 'Email')
            ->setCellValue('H'.$c, 'Site')
            ->setCellValue('I'.$c, 'Valide')
            ->setCellValue('J'.$c, 'Inscrit le');

        $l = 2;

        foreach ($associations as $association) {
            $neLe = '';
            if ($association->getCreatedAt() instanceof \DateTimeInterface) {
                $neLe = $association->getCreatedAt()->format('Y-m-d');
            }

            $worksheet->setCellValue('A'.$l, $association->name)
                ->setCellValue('B'.$l, $association->address)
                ->setCellValue('C'.$l, $association->postalCode)
                ->setCellValue('D'.$l, $association->city)
                ->setCellValue('E'.$l, $association->phone)
                ->setCellValue('F'.$l, $association->mobile)
                ->setCellValue('G'.$l, $association->email)
                ->setCellValue('H'.$l, $association->web_site)
                ->setCellValue('I'.$l, $association->valider)
                ->setCellValue('J'.$l, $neLe);
            ++$l;
        }

        return $worksheet;
    }
}
