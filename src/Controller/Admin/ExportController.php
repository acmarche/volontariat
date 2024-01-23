<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Repository\VolontaireRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/export')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ExportController extends AbstractController
{
    public function __construct(private VolontaireRepository $volontaireRepository)
    {
    }

    #[Route(path: '/volontaire/xls', name: 'volontariat_admin_volontaire_xls', methods: ['GET'])]
    public function volontaireXls(Request $request): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $this->volontaireXSLObject($request, $spreadsheet);
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            Response::HTTP_OK,
            []
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=volontaires.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    private function volontaireXSLObject(Request $request, Spreadsheet $spreadsheet): Worksheet
    {
        $volontaires = $this->volontaireRepository->search([]);

        $sheet = $spreadsheet->getActiveSheet();

        /**
         * title.
         */
        $c = 1;
        $sheet->setCellValue('A'.$c, 'Nom')
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

            $sheet->setCellValue('A'.$l, $volontaire->name)
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

        return $sheet;
    }
}
