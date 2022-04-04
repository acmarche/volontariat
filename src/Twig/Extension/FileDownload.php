<?php

namespace AcMarche\Volontariat\Twig\Extension;

use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Service\FileHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FileDownload extends AbstractExtension
{
    public function __construct(private FileHelper $fileHelper)
    {
    }

    /**
     * @Override
     */
    public function getFilters(): array
    {
        return array(
            new TwigFilter('acmarche_volontariat_download_image', fn($fileName, Uploadable $uploadable) => $this->download($fileName, $uploadable)),
        );
    }

    public function download($fileName, Uploadable $uploadable): string
    {
        return $this->fileHelper->getDownloadPath($uploadable) . $fileName;
    }
}
