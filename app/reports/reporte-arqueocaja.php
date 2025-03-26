<?php
/**
 * Html2Pdf Library - example
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2025 Laurent MINGUET
 */
require_once '../../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {

    ob_start();
    
    require_once './content/data-reporte-caja.php';
    
    $content = ob_get_clean();

    $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 5));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content);
    $html2pdf->output('reporteCaja.pdf');
} catch (Html2PdfException $e) {
    $html2pdf->clean();

    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}
