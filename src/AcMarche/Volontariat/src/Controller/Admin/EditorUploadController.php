<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Security\RolesEnum;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Upload endpoint for CKEditor 5 SimpleUploadAdapter.
 * Responds with {"url": "..."} or {"error": {"message": "..."}}.
 */
#[IsGranted(RolesEnum::admin->value)]
class EditorUploadController extends AbstractController
{
    public const string CSRF_TOKEN_ID = 'ckeditor_upload';

    public function __construct(
        private readonly FileHelper $fileHelper,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route(
        path: '/admin/editor/upload/{id?}',
        name: 'volontariat_admin_editor_upload',
        requirements: ['id' => '\d+'],
        methods: ['POST'],
    )]
    public function upload(Request $request, ?Page $page = null): JsonResponse
    {
        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->error('Jeton CSRF invalide, rechargez la page.', Response::HTTP_FORBIDDEN);
        }

        $file = $request->files->get('upload');
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            return $this->error('Aucun fichier reçu ou fichier trop volumineux.');
        }

        $violations = $this->validator->validate($file, new Image(
            maxSize: '10M',
            mimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            mimeTypesMessage: 'Seules les images JPEG, PNG, GIF ou WebP sont acceptées.',
        ));
        if (\count($violations) > 0) {
            return $this->error((string) $violations->get(0)->getMessage());
        }

        try {
            $url = $this->fileHelper->uploadEditorImage($file, $page);
        } catch (\Exception $exception) {
            return $this->error('Erreur upload image: '.$exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['url' => $request->getBasePath().$url]);
    }

    private function error(string $message, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->json(['error' => ['message' => $message]], $status);
    }
}
