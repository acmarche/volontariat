<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\InterfaceDef\Uploadable;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mime\MimeTypes;

class FileHelper
{
    private $rootUploadPath;
    private $rootDownloadPath;

    public function __construct(string $uploadPath, string $downloadPath)
    {
        $this->rootUploadPath = $uploadPath;
        $this->rootDownloadPath = $downloadPath;
        $this->directorySeparator = DIRECTORY_SEPARATOR;
    }

    public function uploadFile(Uploadable $uploadable, UploadedFile $file, $fileName)
    {
        $directory = $this->getUploadPath($uploadable);
        $result = $file->move($directory, $fileName);

        return $result;
    }

    public function deleteOneDoc(Uploadable $uploadable, $filename)
    {
        $directory = $this->getUploadPath($uploadable);
        $file = $directory.$this->directorySeparator.$filename;

        $fs = new Filesystem();
        $fs->remove($file);
    }

    public function deleteAllDocs(Uploadable $uploadable)
    {
        $directory = $this->getUploadPath($uploadable);
        $fs = new Filesystem();
        $fs->remove($directory);
    }

    public function getImages(Uploadable $uploadable, $max = 60)
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
                if (!preg_match('#image#', $mime)) {
                    continue;
                }

                $f['size'] = $size;
                $f['name'] = $name;
                $f['url'] = $url;
                $f['mime'] = $mime;
                $f['i'] = $i; //pour id zoom
                $i++;

                $files[] = $f;
                if ($i > $max) {
                    break;
                }
            }
        }

        return $files;
    }

    public function traitementFiles($entity)
    {
        if ($photoName = $this->traitementFile($entity->getImage(), $entity)) {
            $entity->setImageName($photoName);
        }
    }

    protected function traitementFile($file, Uploadable $uploadable)
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

    protected function makePath(Uploadable $uploadable)
    {
        return DIRECTORY_SEPARATOR.$uploadable->getPath().$this->directorySeparator.$uploadable->getId(
            ).$this->directorySeparator;
    }

    public function getUploadPath(Uploadable $uploadable)
    {
        return $this->rootUploadPath.$this->makePath($uploadable);
    }

    public function getDownloadPath(Uploadable $uploadable)
    {
        return $this->rootDownloadPath.$this->makePath($uploadable);
    }
}
