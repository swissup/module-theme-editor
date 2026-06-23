<?php

namespace Swissup\ThemeEditor\Model\Layout;

/**
 * Builds the layout update XML that wires the configurable header.
 *
 * Historically this work was done at runtime by a plugin on
 * Magento\Framework\View\Layout\ScheduledStructure
 * (beforeGetListToMove / beforeGetListToRemove / beforeSetElement).
 *
 * Magento 2.4.9 introduced an "isolated reader context" in
 * Magento\Framework\View\Layout::generateElements() that runs the
 * generator pool against a freshly `new`'d ScheduledStructure - an
 * instance with no interceptor - so that plugin no longer fires during
 * layout generation. Expressing the same instructions as a layout update
 * string lets them be parsed by readerPool->interpret() *before* the
 * isolated copy is taken, so they survive into generation.
 *
 * Isolation was added in commit:
 * https://github.com/magento/magento2/commit/61945814f4ffba41ee518cfe11b432a7c33e3612
 *
 * @see \Swissup\ThemeEditor\Plugin\Framework\LayoutMerge
 */
class ConfigurableHeaderUpdate
{
    /**
     * Container that the stock header lives in. Removed only when the
     * configurable replacement is being built.
     */
    const STOCK_HEADER_CONTAINER = 'header.container';

    /**
     * @var \Swissup\ThemeEditor\Helper\Header
     */
    private $helper;

    /**
     * @param \Swissup\ThemeEditor\Helper\Header $helper
     */
    public function __construct(
        \Swissup\ThemeEditor\Helper\Header $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Build the layout update XML for the configurable header.
     *
     * Returns an empty string when the feature is disabled, so callers can
     * safely concatenate the result unconditionally.
     *
     * @return string
     */
    public function getLayoutUpdateXml()
    {
        if (!$this->helper->isHeaderEnabled()) {
            return '';
        }

        $config = $this->helper->getHeaderLayout();

        $body = $this->buildMoves($config)
            . $this->buildColumnClasses($config)
            // Remove the stock header only here, alongside the moves that
            // populate the configurable replacement - so removal and
            // creation always travel through the same reader pass.
            . '<referenceContainer name="' . self::STOCK_HEADER_CONTAINER . '" remove="true"/>';

        return $body;
    }

    /**
     * Move configured children into the configurable header columns.
     *
     * Mirrors LayoutScheduledStructure::beforeGetListToMove().
     *
     * @param  array $config
     * @return string
     */
    private function buildMoves(array $config)
    {
        $xml = '';
        $children = [];

        foreach ($config as $container) {
            $prevChild = null;
            foreach ($container['children'] as $child) {
                $attributes = ' element="' . $this->escape($child['name']) . '"'
                    . ' destination="' . $this->escape($container['name']) . '"';
                if ($prevChild) {
                    $attributes .= ' after="' . $this->escape($prevChild) . '"';
                } else {
                    $attributes .= ' before="-"';
                }

                $xml .= '<move' . $attributes . '/>';

                $prevChild = $child['name'];
                $children[] = $child['name'];
            }
        }

        // fix for top links
        if (in_array('header_account', $children) && !in_array('header.links', $children)) {
            $xml .= '<move element="top.links" destination="header_account" before="-"/>';
        }

        return $xml;
    }

    /**
     * Apply configured css classes to the header columns.
     *
     * Mirrors LayoutScheduledStructure::beforeSetElement().
     *
     * @param  array $config
     * @return string
     */
    private function buildColumnClasses(array $config)
    {
        $xml = '';

        foreach ($config as $container) {
            $classes = $this->getClasses($container['config']);
            $xml .= '<referenceContainer name="' . $this->escape($container['name']) . '"'
                . ' htmlClass="' . $this->escape($classes) . '"/>';
        }

        return $xml;
    }

    /**
     * Get css classes configured for header column.
     *
     * Mirrors LayoutScheduledStructure::getClasses().
     *
     * @param  array $config
     * @return string
     */
    private function getClasses($config)
    {
        $classes = 'flex-col-' . $config['align'];

        if (!empty($config['grow'])) {
            $classes .= ' flex-grow-' . $config['grow'];
        }

        if (!empty($config['classes'])) {
            $classes .= ' ' . $config['classes'];
        }

        return $classes;
    }

    /**
     * Escape a value for use inside an XML attribute.
     *
     * @param  string $value
     * @return string
     */
    private function escape($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
