<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAs;

/**
 * Class LocaleEnAs
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2018 Greg Roach
 * @license   GPLv3+
 */
class LocaleEnAs extends LocaleEn
{
    public function territory()
    {
        return new TerritoryAs();
    }
}
