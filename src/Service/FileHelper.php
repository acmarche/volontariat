<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

class FileHelper
{
    private string $directorySeparator;
    private string $rootUploadPath;
    private string $rootDownloadPath;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        $this->rootDownloadPath = '/uploads/volontariat';
        $this->rootUploadPath = $this->projectDir.'/public/uploads/volontariat';
        $this->directorySeparator = DIRECTORY_SEPARATOR;
    }

    /**
     * @param Uploadable|Association|Page $uploadable
     * @param UploadedFile $file
     * @return File
     */
    public function treatmentFile(Uploadable|Association|Page $uploadable, UploadedFile $file): File
    {
        $orignalName = preg_replace(
            '#.'.$file->guessClientExtension().'#',
            '',
            $file->getClientOriginalName(),
        );
        $fileName = $orignalName.'-'.uniqid().'.'.$file->guessClientExtension();
        $nom = str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName());

        $directory = $this->getUploadPath($uploadable);

        return $file->move($directory, $fileName);
    }

    public function deleteOneDoc(Uploadable $uploadable, $filename): void
    {
        $directory = $this->getUploadPath($uploadable);
        $file = $directory.$this->directorySeparator.$filename;

        $fs = new Filesystem();
        $fs->remove($file);
    }

    public function deleteAllDocs(Uploadable $uploadable): void
    {
        $directory = $this->getUploadPath($uploadable);
        $fs = new Filesystem();
        $fs->remove($directory);
    }

    public function getFiles(Uploadable $uploadable): array
    {
        $finder = new Finder();
        $files = [];
        $directory = $this->getUploadPath($uploadable);
        $downloadPath = $this->getDownloadPath($uploadable);

        if (is_dir($directory)) {
            $finder->files()->in($directory);
            $i = 1;

            foreach ($finder as $file) {
                $f = [];

                $name = $file->getFilename();
                $url = $downloadPath.$name;
                $size = $file->getSize();
                $mime = MimeTypes::getDefault()->guessMimeType($file->getPathname());

                $f['size'] = $size;
                $f['name'] = $name;
                $f['url'] = $url;
                $f['mime'] = $mime;
                $f['i'] = $i; // pour id zoom
                ++$i;

                $files[] = $f;
            }
        }

        return $files;
    }

    public function getImages(Uploadable $uploadable, $max = 60)
    {
        $files = $this->getFiles($uploadable);
        foreach ($files as $i => $file) {
            if (!str_contains($file['mime'], 'image')) {
                unset($files[$i]);
            }
        }

        return $files;
    }

    public function getDocuments(Uploadable $uploadable, $max = 60)
    {
        $files = $this->getFiles($uploadable);

        foreach ($files as $i => $file) {
            if (str_contains($file['mime'], 'image')) {
                unset($files[$i]);
            }
        }

        return $files;
    }

    protected function makePath(Uploadable $uploadable): string
    {
        return DIRECTORY_SEPARATOR.$uploadable->getPath().$this->directorySeparator.$uploadable->getId(
            ).$this->directorySeparator;
    }

    public function getUploadPath(Uploadable $uploadable): string
    {
        return $this->rootUploadPath.$this->makePath($uploadable);
    }

    public function getDownloadPath(Uploadable $uploadable): string
    {
        return $this->rootDownloadPath.$this->makePath($uploadable);
    }
}
