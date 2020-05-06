<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Category;

use Swissup\ThemeEditor\Model\Config\Backend\PageLayout;

class MoveDescription extends PageLayout
{
    /**
     * @var array
     */
    protected $mapping = [];

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->mapping = [
            // move category description after products
            'content.after' => '<move element="category.description" destination="content" after="category.products" />',
            // move category description after all columns
            'main.content.after' => '<move element="category.description" destination="main.content" after="content" />',
            // move category description before products
            'content.before' => '<move element="category.description" destination="content" before="category.products" />',
            // move category description before all columns
            'main.content.before' => '<move element="category.description" destination="category.view.container" />',
        ];

        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function generateXml()
    {
        $value = $this->getData('value');

        return $this->mapping[$value] ?? '';
    }
}
