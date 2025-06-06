<?php

namespace WP_Ultimo\Dependencies\Mpdf\Tag;

use WP_Ultimo\Dependencies\Mpdf\Mpdf;
class A extends Tag
{
    public function open($attr, &$ahtml, &$ihtml)
    {
        if (isset($attr['NAME']) && $attr['NAME'] != '') {
            $e = '';
            /* -- BOOKMARKS -- */
            if ($this->mpdf->anchor2Bookmark) {
                $objattr = [];
                $objattr['CONTENT'] = \htmlspecialchars_decode($attr['NAME'], \ENT_QUOTES);
                $objattr['type'] = 'bookmark';
                if (!empty($attr['LEVEL'])) {
                    $objattr['bklevel'] = $attr['LEVEL'];
                } else {
                    $objattr['bklevel'] = 0;
                }
                $e = Mpdf::OBJECT_IDENTIFIER . "type=bookmark,objattr=" . \serialize($objattr) . Mpdf::OBJECT_IDENTIFIER;
            }
            /* -- END BOOKMARKS -- */
            if ($this->mpdf->tableLevel) {
                // *TABLES*
                $this->mpdf->_saveCellTextBuffer($e, '', $attr['NAME']);
                // *TABLES*
            } else {
                // *TABLES*
                $this->mpdf->_saveTextBuffer($e, '', $attr['NAME']);
                //an internal link (adds a space for recognition)
            }
            // *TABLES*
        }
        if (isset($attr['HREF'])) {
            $this->mpdf->InlineProperties['A'] = $this->mpdf->saveInlineProperties();
            $properties = $this->cssManager->MergeCSS('INLINE', 'A', $attr);
            if (!empty($properties)) {
                $this->mpdf->setCSS($properties, 'INLINE');
            }
            $this->mpdf->HREF = $attr['HREF'];
            // mPDF 5.7.4 URLs
        }
    }
    public function close(&$ahtml, &$ihtml)
    {
        $this->mpdf->HREF = '';
        if (isset($this->mpdf->InlineProperties['A'])) {
            $this->mpdf->restoreInlineProperties($this->mpdf->InlineProperties['A']);
        }
        unset($this->mpdf->InlineProperties['A']);
    }
}
