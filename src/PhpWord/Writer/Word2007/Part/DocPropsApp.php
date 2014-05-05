<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @link        https://github.com/PHPOffice/PHPWord
 * @copyright   2010-2014 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Writer\Word2007\Part;

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\PhpWord;

/**
 * Word2007 extended document properties part writer
 *
 * @since 0.11.0
 */
class DocPropsApp extends AbstractPart
{
    /**
     * Write docProps/app.xml
     */
    public function write()
    {
        $phpWord = $this->parentWriter->getPhpWord();
        if (is_null($phpWord)) {
            throw new Exception('No PhpWord assigned.');
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('Properties');
        $xmlWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/officeDocument/2006/extended-properties');
        $xmlWriter->writeAttribute('xmlns:vt', 'http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes');

        $xmlWriter->writeElement('Application', 'PHPWord');
        $xmlWriter->writeElement('Company', $phpWord->getDocumentProperties()->getCompany());
        $xmlWriter->writeElement('Manager', $phpWord->getDocumentProperties()->getManager());

        $xmlWriter->endElement(); // Properties

        return $xmlWriter->getData();
    }
}
