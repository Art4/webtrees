<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySe;

/**
 * Class LocaleEnDe - English
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2018 Greg Roach
 * @license   GPLv3+
 */
class LocaleEnSe extends LocaleEn
{
    public function numberSymbols()
    {
        return array(
            self::GROUP => self::NBSP,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }

    public function territory()
    {
        return new TerritorySe();
    }
}
