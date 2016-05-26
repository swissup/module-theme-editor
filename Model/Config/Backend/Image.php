<?php
namespace Swissup\ThemeEditor\Model\Config\Backend;

class Image extends \Magento\Config\Model\Config\Backend\Image
{
    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir()
    {
        $path      = $this->getPath();
        $pathParts = explode('/', $path);
        $dir       = str_replace('_', '/', $pathParts[0]);
        $uploadDir = $dir . '/images';
        return $this->_mediaDirectory->getAbsolutePath($uploadDir);
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return false;
    }

    /**
     * Save uploaded file before saving config value
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $tmpName = $this->_requestData->getTmpName($this->getPath());
        $file = [];
        if ($tmpName) {
            $file['tmp_name'] = $tmpName;
            $file['name'] = $this->_requestData->getName($this->getPath());
        } elseif (!empty($value['tmp_name'])) {
            $file['tmp_name'] = $value['tmp_name'];
            $file['name'] = $value['value'];
        }
        if (!empty($file)) {
            return parent::beforeSave();
        }

        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('none');
        } else if (!empty($value['value'])) {
            $this->setValue($value['value']);
        } else {
            $this->unsValue();
        }

        return $this;
    }
}
