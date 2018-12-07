<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Media;

/**
 * Class ReportPdf
 */
class ReportPdf extends ReportBase {
    /**
     * PDF compression - Zlib extension is required
     *
     * @var bool const
     */
    const COMPRESSION = true;
    /**
     * If true reduce the RAM memory usage by caching temporary data on filesystem (slower).
     *
     * @var bool const
     */
    const DISK_CACHE = false;
    /**
     * true means that the input text is unicode (PDF)
     *
     * @var bool const
     */
    const UNICODE = true;
    /**
     * false means that the full font is embedded, true means only the used chars
     * in TCPDF v5.9 font subsetting is a very slow process, this leads to larger files
     *
     * @var bool const
     */
    const SUBSETTING = false;
    /**
     * A new object of the PDF class
     *
     * @var ReportTcpdf
     */
    public $pdf;

    /**
     * PDF Setup - ReportPdf
     */
    public function setup() {
        parent::setup();

        // Setup the PDF class with custom size pages because WT supports more page sizes. If WT sends an unknown size name then the default would be A4
        $this->pdf = new ReportTcpdf($this->orientation, parent::UNITS, array(
            $this->pagew,
            $this->pageh,
        ), self::UNICODE, "UTF-8", self::DISK_CACHE);

        // Setup the PDF margins
        $this->pdf->SetMargins($this->leftmargin, $this->topmargin, $this->rightmargin);
        $this->pdf->setHeaderMargin($this->headermargin);
        $this->pdf->setFooterMargin($this->footermargin);
        //Set auto page breaks
        $this->pdf->SetAutoPageBreak(true, $this->bottommargin);
        // Set font subsetting
        $this->pdf->setFontSubsetting(self::SUBSETTING);
        // Setup PDF compression
        $this->pdf->SetCompression(self::COMPRESSION);
        // Setup RTL support
        $this->pdf->setRTL($this->rtl);
        // Set the document information
        // Only admin should see the version number
        $appversion = WT_WEBTREES;
        if (Auth::isAdmin()) {
            $appversion .= " " . WT_VERSION;
        }
        $this->pdf->SetCreator($appversion . " (" . parent::WT_URL . ")");
        // Not implemented yet - ReportBase::setup()
        $this->pdf->SetAuthor($this->rauthor);
        $this->pdf->SetTitle($this->title);
        $this->pdf->SetSubject($this->rsubject);
        $this->pdf->SetKeywords($this->rkeywords);

        $this->pdf->setReport($this);

        if ($this->showGenText) {
            // The default style name for Generated by.... is 'genby'
            $element = new ReportPdfCell(0, 10, 0, "C", "", "genby", 1, ".", ".", 0, 0, "", "", true);
            $element->addText($this->generatedby);
            $element->setUrl(parent::WT_URL);
            $this->pdf->addFooter($element);
        }
    }

    /**
     * Add an element - ReportPdf
     *
     * @param object|string $element Object or string
     *
     * @return int
     */
    public function addElement($element) {
        if ($this->processing == "B") {
            return $this->pdf->addBody($element);
        } elseif ($this->processing == "H") {
            return $this->pdf->addHeader($element);
        } elseif ($this->processing == "F") {
            return $this->pdf->addFooter($element);
        }

        return 0;
    }

    /**
     * Run the report.
     */
    public function run() {
        $this->pdf->body();
        header('Expires:');
        header('Pragma:');
        header('Cache-control:');
        $this->pdf->Output('webtrees-' . uniqid() . '.pdf', 'I');
    }

    /**
     * Clear the Header - ReportPdf
     */
    public function clearHeader() {
        $this->pdf->clearHeader();
    }

    /**
     * Clear the Page Header - ReportPdf
     */
    public function clearPageHeader() {
        $this->pdf->clearPageHeader();
    }

    /**
     * Create a new Cell object - ReportPdf
     *
     * @param int    $width   cell width (expressed in points)
     * @param int    $height  cell height (expressed in points)
     * @param mixed  $border  Border style
     * @param string $align   Text alignement
     * @param string $bgcolor Background color code
     * @param string $style   The name of the text style
     * @param int    $ln      Indicates where the current position should go after the call
     * @param mixed  $top     Y-position
     * @param mixed  $left    X-position
     * @param int    $fill    Indicates if the cell background must be painted (1) or transparent (0). Default value: 1
     * @param int    $stretch Stretch carachter mode
     * @param string $bocolor Border color
     * @param string $tcolor  Text color
     * @param bool   $reseth
     *
     * @return ReportPdfCell
     */
    public function createCell(
        $width, $height, $border, $align, $bgcolor, $style, $ln, $top, $left, $fill, $stretch, $bocolor, $tcolor, $reseth
    ) {
        return new ReportPdfCell($width, $height, $border, $align, $bgcolor, $style, $ln, $top, $left, $fill, $stretch, $bocolor, $tcolor, $reseth);
    }

    /**
     * Create a new TextBox object - ReportPdf
     *
     * @param float  $width   Text box width
     * @param float  $height  Text box height
     * @param bool   $border
     * @param string $bgcolor Background color code in HTML
     * @param bool   $newline
     * @param mixed  $left
     * @param mixed  $top
     * @param bool   $pagecheck
     * @param string $style
     * @param bool   $fill
     * @param bool   $padding
     * @param bool   $reseth
     *
     * @return ReportPdfTextbox
     */
    public function createTextBox(
        $width, $height, $border, $bgcolor, $newline, $left, $top, $pagecheck, $style, $fill, $padding, $reseth
    ) {
        return new ReportPdfTextbox($width, $height, $border, $bgcolor, $newline, $left, $top, $pagecheck, $style, $fill, $padding, $reseth);
    }

    /**
     * Create a new Text object- ReportPdf
     *
     * @param string $style The name of the text style
     * @param string $color HTML color code
     *
     * @return ReportPdfText
     */
    public function createText($style, $color) {
        return new ReportPdfText($style, $color);
    }

    /**
     * Create a new Footnote object - ReportPdf
     *
     * @param string $style Style name
     *
     * @return ReportPdfFootnote
     */
    public function createFootnote($style) {
        return new ReportPdfFootnote($style);
    }

    /**
     * Create a new Page Header object - ReportPdf
     *
     * @return ReportPdfPageheader
     */
    public function createPageHeader() {
        return new ReportPdfPageheader;
    }

    /**
     * Create a new image object - ReportPdf
     *
     * @param string  $file  Filename
     * @param mixed   $x
     * @param mixed   $y
     * @param int $w     Image width
     * @param int $h     Image height
     * @param string  $align L:left, C:center, R:right or empty to use x/y
     * @param string  $ln    T:same line, N:next line
     *
     * @return ReportPdfImage
     */
    public function createImage($file, $x, $y, $w, $h, $align, $ln) {
        return new ReportPdfImage($file, $x, $y, $w, $h, $align, $ln);
    }

    /**
     * Create a new image object from Media Object - ReportPdf
     *
     * @param Media  $mediaobject
     * @param mixed  $x
     * @param mixed  $y
     * @param int    $w           Image width
     * @param int    $h           Image height
     * @param string $align       L:left, C:center, R:right or empty to use x/y
     * @param string $ln          T:same line, N:next line
     *
     * @return ReportPdfImage
     */
    public function createImageFromObject($mediaobject, $x, $y, $w, $h, $align, $ln) {
        return new ReportPdfImage($mediaobject->getServerFilename('thumb'), $x, $y, $w, $h, $align, $ln);
    }

    /**
     * Create a new line object - ReportPdf
     *
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     *
     * @return ReportPdfLine
     */
    public function createLine($x1, $y1, $x2, $y2) {
        return new ReportPdfLine($x1, $y1, $x2, $y2);
    }

    /**
     * Create an HTML element.
     *
     * @param $tag
     * @param $attrs
     *
     * @return ReportPdfHtml
     */
    public function createHTML($tag, $attrs) {
        return new ReportPdfHtml($tag, $attrs);
    }
}
