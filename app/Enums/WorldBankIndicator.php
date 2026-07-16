<?php

declare(strict_types=1);

namespace App\Enums;

enum WorldBankIndicator:string
{
    case GDP = 'NY.GDP.MKTP.CD';

    case INFLATION = 'FP.CPI.TOTL.ZG';

    case POPULATION = 'SP.POP.TOTL';

    case EXPORT = 'NE.EXP.GNFS.CD';

    case IMPORT = 'NE.IMP.GNFS.CD';
}