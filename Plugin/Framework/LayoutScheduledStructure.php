<?php
namespace Swissup\ThemeEditor\Plugin\Framework;

use Magento\Framework\View\Layout\ScheduledStructure;

class LayoutScheduledStructure
{
    /**
     * @var \Swissup\ThemeEditor\Helper\Header
     */
    private $helper;

    /**
     * @var array
     */
    protected $headerConfig = null;

    /**
     * @param \Swissup\ThemeEditor\Helper\Header $helper
     */
    public function __construct(
        \Swissup\ThemeEditor\Helper\Header $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Add/replace move instructions for header elements
     *
     * @param  ScheduledStructure $subject
     * @return null
     */
    public function beforeGetListToMove(ScheduledStructure $subject)
    {
        if (!$this->helper->isHeaderEnabled()) {
            return null;
        }

        $children = [];
        foreach ($this->getHeaderConfig() as $container) {
            $prevChild = null;
            foreach ($container['children'] as $child) {
                $subject->setElementToMove(
                    $child['name'],
                    [$container['name'], $prevChild ?: '-', (bool)$prevChild, '']
                );
                $prevChild = $child['name'];
                $children[] = $child['name'];
            }
        }

        // fix for top links
        if (in_array('header_account', $children) && !in_array('header.links', $children)) {
            $subject->setElementToMove(
                'top.links',
                ['header_account', '-', true, '']
            );
        }

        return null;
    }

    /**
     * Cancel remove instructions for header elements
     *
     * @param  ScheduledStructure $subject
     * @return null
     */
    public function beforeGetListToRemove(ScheduledStructure $subject)
    {
        if (!$this->helper->isHeaderEnabled()) {
            return null;
        }

        foreach ($this->getHeaderConfig() as $container) {
            foreach ($container['children'] as $child) {
                $subject->unsetElementFromListToRemove($child['name']);
            }
        }

        return null;
    }

    /**
     * Add classes to header columns
     *
     * @param  ScheduledStructure $subject
     * @param  string $elementName
     * @param  array $data
     * @return array|null
     */
    public function beforeSetElement(
        ScheduledStructure $subject, $elementName, array $data
    ) {
        if (!$this->helper->isHeaderEnabled()) {
            return null;
        }

        foreach ($this->getHeaderConfig() as $container) {
            if ($elementName == $container['name']) {
                $data[1]['attributes']['htmlClass'] = $this->getClasses($container['config']);

                return [$elementName, $data];
            }
        }

        return null;
    }

    /**
     * Get css classes configured for header column
     *
     * @param  array $config
     * @return string
     */
    protected function getClasses($config)
    {
        $classes = 'flex-col-' . $config['align'];

        if ($config['grow']) {
            $classes .= ' flex-grow-' . $config['grow'];
        }

        if ($config['classes']) {
            $classes .= ' ' . $config['classes'];
        }

        return $classes;
    }

    /**
     * Get header layout configuration
     * @return array
     */
    protected function getHeaderConfig()
    {
        if ($this->headerConfig === null) {
            $this->headerConfig = $this->helper->getHeaderLayout();
        }

        return $this->headerConfig;
    }
}
