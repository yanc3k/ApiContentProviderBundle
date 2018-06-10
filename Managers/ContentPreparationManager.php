<?php

namespace ApiContentProviderBundle\Managers;

use ApiContentProviderBundle\Models\ApiContent;

class ContentPreparationManager
{
    // These constant need to become a configurable parameter later on.
    //The name of the input field in the CMS xml template.
    const CONTENT_TYPE_TEXT_LINE = 'value-text-line';
    const CONTENT_TYPE_TEXT_EDITOR = 'value-text-editor';
    const CONTENT_TYPE_IMAGE = 'value-image';

    // Type of the blocks that are defined in the template xml.
    const BLOCK_TYPE_KEY_VALUE = 'key-value-pair';

    public function mapCmsContentToApiContent($cmsContent)
    {
        $apiContent = new ApiContent();

        // These are required by default in all Sulu templates, but we check anyway.
        if (isset($cmsContent['content']['title'])) {
            $apiContent->setTitle($cmsContent['content']['title']);
        }
        if (isset($cmsContent['content']['url'])) {
            $apiContent->setUrl($cmsContent['content']['url']);
        }

        // Handle the blocks of the content page if they are present.
        if (isset($cmsContent['content']['blocks'])) {
            $this->addKeyValuesFromCmsContentBlocks($cmsContent['content']['blocks'], $apiContent);
        }

        return $apiContent;
    }

    /**
     * Create a simple key value array from the content blocks from the cms content.
     *
     * @param array $blocks
     * @param ApiContent $apiContent
     */
    public function addKeyValuesFromCmsContentBlocks($blocks, $apiContent)
    {
        // Handle each of the content blocks that was created in the CMS.
        foreach ($blocks as $block) {
            // If the type or the key are not given, this block is useless for us.
            if (!isset($block['type'] || !isset($block['key']))) {
                continue;
            }
            // Check if the block is a key-value-pair type.
            if ($block['type'] === self::BLOCK_TYPE_KEY_VALUE) {
                $resultKey = $block['key'];
                $resultValue = null;
                $wasMatched = false;

                // Need to handle all the different content types (text_line, text_area, media selection, ...)
                // that a block can handle.
                foreach ($block as $key => $value) {
                    // Check for empty inputs.
                    if (!$value || $wasMatched || $key === 'key' || $key === 'type') {
                        continue;
                    }

                    // Handle value-text-lines that may be present.
                    switch ($key) {
                        case self::CONTENT_TYPE_TEXT_LINE:
                            $this->handleTextLineContent($resultKey, $value, $apiContent);
                            $wasMatched = true;
                            break;

                        case self::CONTENT_TYPE_TEXT_EDITOR:
                            $this->handleTextEditorContent($resultKey, $value, $apiContent);
                            $wasMatched = true;
                            break;

                        case self::CONTENT_TYPE_IMAGE:
                            $this->handleImageContent($resultKey, $value, $apiContent);
                            $wasMatched = true;
                        default:
                            break;
                    }
                }
            }
        }
    }

    /**
     * Function to handle the text line content type coming from sulu.
     *
     * @return ApiContent
     */
    private function handleTextLineContent($key, $value, $apiContent)
    {
        $apiContent->addToKeyValueStore($key, $value);

        return $apiContent;
    }

    /**
     * Function to handle the text editor content type coming from sulu.
     *
     * @return ApiContent
     */
    private function handleTextEditorContent($key, $value, $apiContent)
    {
        $apiContent->addToKeyValueStore($key, $value);

        return $apiContent;
    }

    /**
     * Function to handle the image content type coming from sulu.
     *
     * @return ApiContent
     */
    private function handleImageContent($key, $value, $apiContent)
    {
        if (!is_array($value)) {
            return;
        }

        $imagePath = $value[0]->getUrl();
        $apiContent->addToKeyValueStore($key, $imagePath);

        return $apiContent;
    }
}
