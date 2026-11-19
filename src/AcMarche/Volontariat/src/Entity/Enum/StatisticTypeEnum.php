<?php

namespace AcMarche\Volontariat\Entity\Enum;

enum StatisticTypeEnum: string
{
    case CONTACT_GENERAL = 'contact_general';
    case CONTACT_VOLONTAIRE = 'contact_volontaire';
    case CONTACT_ASSOCIATION = 'contact_association';
    case CONTACT_VOLONTAIRE_BY_SECTEUR = 'contact_volontaire_by_secteur';

    public function label(): string
    {
        return match ($this) {
            self::CONTACT_GENERAL => 'Contact général',
            self::CONTACT_VOLONTAIRE => 'Contact volontaire',
            self::CONTACT_ASSOCIATION => 'Contact association',
            self::CONTACT_VOLONTAIRE_BY_SECTEUR => 'Contact volontaires par secteur',
        };
    }
}
