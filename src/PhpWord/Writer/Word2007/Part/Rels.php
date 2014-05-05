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
use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * Word2007 relationship writer
 *
 * @since 0.10.0
 */
class Rels extends AbstractPart
{
    /**
     * Base relationship URL
     */
    const RELS_BASE = 'http://schemas.openxmlformats.org/';

    /**
     * Write word/_rels/(header|footer|footnotes)*.xml.rels
     *
     * @param array $mediaRels
     */
    public function writeMediaRels($mediaRels)
    {
        $xmlWriter = $this->getXmlWriter();
        $this->writeRels($xmlWriter, null, $mediaRels);

        return $xmlWriter->getData();
    }

    /**
     * Write relationships
     *
     * @param \PhpOffice\PhpWord\Shared\XMLWriter $xmlWriter
     * @param null|array $xmlRels
     * @param null|array $mediaRels
     * @param integer $relId
     */
    protected function writeRels(XMLWriter $xmlWriter, $xmlRels = null, $mediaRels = null, $relId = 1)
    {
        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('Relationships');
        $xmlWriter->writeAttribute('xmlns', self::RELS_BASE . 'package/2006/relationships');

        // XML files relationships
        if (is_array($xmlRels)) {
            foreach ($xmlRels as $target => $type) {
                $this->writeRel($xmlWriter, $relId++, $type, $target);
            }
        }

        // Media relationships
        if (!is_null($mediaRels) && is_array($mediaRels)) {
            $mapping = array('image' => 'image', 'object' => 'oleObject', 'link' => 'hyperlink');
            $targetPaths = array('image' => 'media/', 'object' => 'embeddings/');
            foreach ($mediaRels as $mediaRel) {
                $mediaType = $mediaRel['type'];
                $type = array_key_exists($mediaType, $mapping) ? $mapping[$mediaType] : $mediaType;
                $target = array_key_exists($mediaType, $targetPaths) ? $targetPaths[$mediaType] : '';
                $target .= $mediaRel['target'];
                $targetMode = ($type == 'hyperlink') ? 'External' : '';
                $this->writeRel($xmlWriter, $relId++, "officeDocument/2006/relationships/{$type}", $target, $targetMode);
            }
        }

        $xmlWriter->endElement(); // Relationships
    }

    /**
     * Write individual rels entry
     *
     * Format:
     * <Relationship Id="rId..." Type="http://..." Target="....xml" TargetMode="..." />
     *
     * @param \PhpOffice\PhpWord\Shared\XMLWriter $xmlWriter
     * @param int $relId Relationship ID
     * @param string $type Relationship type
     * @param string $target Relationship target
     * @param string $targetMode Relationship target mode
     */
    private function writeRel(XMLWriter $xmlWriter, $relId, $type, $target, $targetMode = '')
    {
        if ($type != '' && $target != '') {
            if (strpos($relId, 'rId') === false) {
                $relId = 'rId' . $relId;
            }
            $xmlWriter->startElement('Relationship');
            $xmlWriter->writeAttribute('Id', $relId);
            $xmlWriter->writeAttribute('Type', self::RELS_BASE . $type);
            $xmlWriter->writeAttribute('Target', $target);
            if ($targetMode != '') {
                $xmlWriter->writeAttribute('TargetMode', $targetMode);
            }
            $xmlWriter->endElement();
        } else {
            throw new Exception("Invalid parameters passed.");
        }
    }
}
