<?php

namespace App\Enum;

enum ConseillerType: string {
    case ANCIEN = 'Ancien élève';
    case ORIENTATION = 'Conseiller d\'orientation';
    case PRO = 'Professionnel';
}
