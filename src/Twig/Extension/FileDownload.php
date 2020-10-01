<?php

namespace AcMarche\Volontariat\Twig\Extension;

use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Service\FileHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FileDownload extends AbstractExtension
{
    private $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * @Override
     * @return array
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('acmarche_volontariat_download_image', array($this, 'download')),
        );
    }

    public function download($fileName, Uploadable $uploadable)
    {
        return $this->fileHelper->getDownloadPath($uploadable) . $fileName;
    }
}
