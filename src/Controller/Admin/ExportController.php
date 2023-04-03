<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\NonUniqueResultException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Service\VolontariatConstante;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Route(path: '/admin/export')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ExportController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }
    #[Route(path: '/volontaire/xls', name: 'volontariat_admin_volontaire_xls', methods: ['GET'])]
    public function volontaireXls(Request $request) : StreamedResponse
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
        $session = $request->getSession();
        $em = $this->managerRegistry->getManager();

        $critere = $session->get(VolontariatConstante::VOLONTAIRE_ADMIN_SEARCH, false);

        $data_s = $critere ? unserialize($critere) : array();

        $volontaires = $em->getRepository(Volontaire::class)->search($data_s);

        $sheet = $spreadsheet->getActiveSheet();

        /**
         * title
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
            ->setCellValue('I'.$c, 'Fax')
            ->setCellValue('J'.$c, 'Email')
            ->setCellValue('K'.$c, 'Emploi')
            ->setCellValue('L'.$c, 'Disponibilité')
            ->setCellValue('M'.$c, 'Véhicule(s)')
            ->setCellValue('N'.$c, 'Description');

        $l = 2;
        $format = 'd-m-Y';

        foreach ($volontaires as $volontaire) {
            $neLe = $volontaire->getBirthday() != null ? $volontaire->getBirthday()->format($format) : '';
            $vehiculesJ = $volontaire->getVehicules();
            $vehicules = [];
            foreach ($vehiculesJ as $vehicule) {
                $vehicules[] = $vehicule->getNom();
            }

            $sheet->setCellValue('A'.$l, $volontaire->getName())
                ->setCellValue('B'.$l, $volontaire->getSurname())
                ->setCellValue('C'.$l, $volontaire->getAddress())
                ->setCellValue('D'.$l, $volontaire->getPostalCode())
                ->setCellValue('E'.$l, $volontaire->getCity())
                ->setCellValue('F'.$l, $neLe)
                ->setCellValue('G'.$l, $volontaire->getPhone())
                ->setCellValue('H'.$l, $volontaire->getMobile())
                ->setCellValue('I'.$l, $volontaire->getFax())
                ->setCellValue('J'.$l, $volontaire->getEmail())
                ->setCellValue('K'.$l, $volontaire->getJob())
                ->setCellValue('L'.$l, $volontaire->getAvailability())
                ->setCellValue('M'.$l, implode(",", $vehicules))
                ->setCellValue('N'.$l, $volontaire->getDescription());
            $l++;
        }

        return $sheet;
    }
}
