<?php

namespace AcMarche\Volontariat\Service;

use Symfony\Component\HttpFoundation\File\File;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mime\MimeTypes;

class FileHelper
{
    public string $directorySeparator;
    public function __construct(private string $rootUploadPath, private string $rootDownloadPath)
    {
        $this->directorySeparator = DIRECTORY_SEPARATOR;
    }

    public function uploadFile(Uploadable $uploadable, UploadedFile $file, $fileName): File
    {
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
        $files = array();
        $directory = $this->getUploadPath($uploadable);
        $webDirectory = $this->getDownloadPath($uploadable);

        if (is_dir($directory)) {
            $finder->files()->in($directory);
            $i = 1;

            foreach ($finder as $file) {
                $f = array();

                $name = $file->getFilename();
                $url = $webDirectory.$name;
                $size = $file->getSize();
                $mime = MimeTypes::getDefault()->guessMimeType($file->getPathname());

                $f['size'] = $size;
                $f['name'] = $name;
                $f['url'] = $url;
                $f['mime'] = $mime;
                $f['i'] = $i; //pour id zoom
                $i++;

                $files[] = $f;
            }
        }

        return $files;
    }

    public function getImages(Uploadable $uploadable, $max = 60)
    {
        $files = $this->getFiles($uploadable);
        foreach ($files as $i => $file) {
            if (!preg_match('#image#', $file['mime'])) {
                unset($files[$i]);
            }
        }

        return $files;
    }

    public function getDocuments(Uploadable $uploadable, $max = 60)
    {
        $files = $this->getFiles($uploadable);

        foreach ($files as $i => $file) {
            if (preg_match('#image#', $file['mime'])) {
                unset($files[$i]);
            }
        }
        return $files;
    }

    public function traitementFiles($entity): void
    {
        if ($photoName = $this->traitementFile($entity->getImage(), $entity)) {
            $entity->setImageName($photoName);
        }
    }

    protected function traitementFile($file, Uploadable $uploadable): ?string
    {
        if ($file instanceof UploadedFile) {
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            try {
                $this->uploadFile($uploadable, $file, $fileName);

                return $fileName;
            } catch (FileException $error) {
                throw new FileException($error->getMessage());
            }
        }

        return null;
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
