<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

class Color extends \Magento\Config\Block\System\Config\Form\Field {

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/colorpicker/js/colorpicker"], function ($) {
                $(document).ready(function () {
                    var $el = $("#' . $element->getHtmlId() . '");
                    $el.css("backgroundColor", "'. $value .'");
                    $el.css("color", getContrastColor("'. $value .'"));

                    // Attach the color picker
                    $el.ColorPicker({
                        color: "'. $value .'",
                        onChange: function (hsb, hex, rgb) {
                            $el.css("backgroundColor", "#" + hex).val("#" + hex);
                            $el.css("color", getContrastColor(hex));
                        }
                    });

                    function getContrastColor(hexcolor) {
                        var rgb = hexToRgb(hexcolor);
                        if (rgb) {
                            var o = Math.round(((parseInt(rgb.r) * 299) + (parseInt(rgb.g) * 587) + (parseInt(rgb.b) * 114)) / 1000);
                            return (o >= 125) ? "black" : "white";
                        } else {
                            return "black";
                        }
                    }

                    function hexToRgb(hex) {
                        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                        return result ? {
                            r: parseInt(result[1], 16),
                            g: parseInt(result[2], 16),
                            b: parseInt(result[3], 16)
                        } : null;
                    }
                });
            });
            </script>';
        return $html;
    }
}
