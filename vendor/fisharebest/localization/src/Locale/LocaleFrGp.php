<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGp;

/**
 * Class LocaleFrGp
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2018 Greg Roach
 * @license   GPLv3+
 */
class LocaleFrGp extends LocaleFr
{
    public function territory()
    {
        return new TerritoryGp();
    }
}
