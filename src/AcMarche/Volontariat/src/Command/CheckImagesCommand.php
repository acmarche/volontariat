<?php

namespace AcMarche\Volontariat\Command;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AsCommand(
    name: 'volontariat:check-images',
    description: 'List associations and volontaires whose image file is missing on disk',
)]
class CheckImagesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageInterface $storage,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('fix', null, InputOption::VALUE_NONE, 'Clear imageName for entities whose file is missing');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fix = (bool) $input->getOption('fix');
        $rows = [];
        $copied = 0;
        $cleared = 0;

        foreach ([Association::class, Volontaire::class] as $class) {
            $entities = $this->entityManager->getRepository($class)->createQueryBuilder('e')
                ->andWhere('e.imageName IS NOT NULL')
                ->andWhere("e.imageName != ''")
                ->getQuery()
                ->getResult();

            foreach ($entities as $entity) {
                $path = $this->storage->resolvePath($entity, 'image');
                if (null !== $path && is_file($path)) {
                    continue;
                }

                // Legacy uploads were stored in a {id}/ subdirectory
                $legacyPath = null !== $path
                    ? \dirname($path).'/'.$entity->getId().'/'.$entity->getImageName()
                    : null;
                $isLegacy = null !== $legacyPath && is_file($legacyPath);

                $rows[] = [
                    (new \ReflectionClass($entity))->getShortName(),
                    $entity->getId(),
                    (string) $entity,
                    $entity->getImageName(),
                    $isLegacy ? 'in '.$entity->getId().'/ subfolder' : 'missing',
                ];

                if (!$fix) {
                    continue;
                }

                if ($isLegacy) {
                    copy($legacyPath, $path) ? ++$copied : $io->error('Copy failed: '.$legacyPath);
                } else {
                    $entity->setImageName(null);
                    ++$cleared;
                }
            }
        }

        if ([] === $rows) {
            $io->success('All image files are present.');

            return Command::SUCCESS;
        }

        $io->table(['Type', 'Id', 'Name', 'imageName', 'Status'], $rows);

        if ($fix) {
            $this->entityManager->flush();
            $io->success(sprintf('%d file(s) copied from legacy subfolder, %d imageName value(s) cleared.', $copied, $cleared));

            return Command::SUCCESS;
        }

        $io->warning(sprintf(
            '%d problem(s). Re-run with --fix to copy legacy files to the expected path and clear imageName for missing ones.',
            count($rows)
        ));

        return Command::FAILURE;
    }
}
