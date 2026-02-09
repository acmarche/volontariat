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
    private string $directorySeparator = DIRECTORY_SEPARATOR;

    private string $rootUploadPath;

    private string $rootDownloadPath = '/files';

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        $this->rootUploadPath = $this->projectDir.'/public/files';
    }

    public function treatmentFile(Uploadable|Association|Page $uploadable, UploadedFile $uploadedFile): File
    {
        $orignalName = preg_replace(
            '#.'.$uploadedFile->guessClientExtension().'#',
            '',
            $uploadedFile->getClientOriginalName(),
        );
        $fileName = $orignalName.'-'.uniqid().'.'.$uploadedFile->guessClientExtension();
        str_replace('.'.$uploadedFile->getClientOriginalExtension(), '', $uploadedFile->getClientOriginalName());

        $directory = $this->getUploadPath($uploadable);

        return $uploadedFile->move($directory, $fileName);
    }

    public function deleteOneDoc(Uploadable $uploadable, string $filename): void
    {
        $directory = $this->getUploadPath($uploadable);
        $file = $directory.$this->directorySeparator.$filename;

        $filesystem = new Filesystem();
        $filesystem->remove($file);
    }

    public function deleteAllDocs(Uploadable $uploadable): void
    {
        $directory = $this->getUploadPath($uploadable);
        $filesystem = new Filesystem();
        $filesystem->remove($directory);
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

    /**
     * @return mixed[]
     */
    public function getImages(Uploadable $uploadable, $max = 60): array
    {
        $files = $this->getFiles($uploadable);
        foreach ($files as $i => $file) {
            if (!str_contains($file['mime'], 'image')) {
                unset($files[$i]);
            }
        }

        return $files;
    }

    /**
     * @return mixed[]
     */
    public function getDocuments(Uploadable $uploadable, $max = 60): array
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
