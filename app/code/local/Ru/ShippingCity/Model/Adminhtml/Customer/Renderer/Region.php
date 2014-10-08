<?php

class Ru_ShippingCity_Model_Adminhtml_Customer_Renderer_Region extends Mage_Adminhtml_Model_Customer_Renderer_Region {

    /*
     * Rewriting to add city selector
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr>'."\n";

        $countryId = false;
        if ($country = $element->getForm()->getElement('country_id')) {
            $countryId = $country->getValue();
        }
        $regionCollection = false;
        if ($countryId) {
            if (!isset(self::$_regionCollections[$countryId])) {
                self::$_regionCollections[$countryId] = Mage::getModel('directory/country')
                    ->setId($countryId)
                    ->getLoadedRegionCollection()
                    ->toOptionArray();
            }
            $regionCollection = self::$_regionCollections[$countryId];
        }

        $regionId = intval($element->getForm()->getElement('region_id')->getValue());
        $htmlAttributes = $element->getHtmlAttributes();
        foreach ($htmlAttributes as $key => $attribute) {
            if ('type' === $attribute) {
                unset($htmlAttributes[$key]);
                break;
            }
        }
        // Output two elements - for 'region' and for 'region_id'.
        // Two elements are needed later upon form post - to properly set data to address model,
        // otherwise old value can be left in region_id attribute and saved to DB.
        // Depending on country selected either 'region' (input text) or 'region_id' (selectbox) is visible to user
        $regionHtmlName = $element->getName();
        $regionIdHtmlName = str_replace('region', 'region_id', $regionHtmlName);
        $regionHtmlId = $element->getHtmlId();
        $regionIdHtmlId = str_replace('region', 'region_id', $regionHtmlId);

        if ($regionCollection && count($regionCollection) > 0) {
            $elementClass = $element->getClass();
            $html.= '<td class="label">'.$element->getLabelHtml().'</td>';
            $html.= '<td class="value">';

            $html .= '<select id="' . $regionIdHtmlId . '" name="' . $regionIdHtmlName . '" '
                . $element->serialize($htmlAttributes) .'>' . "\n";
            foreach ($regionCollection as $region) {
                $selected = ($regionId==$region['value']) ? ' selected="selected"' : '';
                $value =  is_numeric($region['value'])?(int)$region['value']:"";
                $html.= '<option value="'.$value.'"' . $selected . '>'
                    . Mage::helper('adminhtml')->escapeHtml(Mage::helper('directory')->__($region['label']))
                    . '</option>';
            }
            $html.= '</select>' . "\n";

            $html .= '<input type="hidden" name="' . $regionHtmlName . '" id="' . $regionHtmlId . '" value=""/>';

            /*
             * city selector Id
             */
            $pleaseSelectText = Mage::helper('customer')->__('-- Please select --');
            $cityListUrl = Mage::getUrl('adminhtml/city/list');
            $cityElementName = str_replace('region_id', 'city', $regionIdHtmlName);
            if($regionIdHtmlId != '_template_region_id') {
                $cityElementId = str_replace('region_id', 'city', $regionIdHtmlId);
                /*
                 * load city list and pre-selected
                 */
                $html .=
<<<TEXT
<script type="text/javascript">
            jQuery(document).ready(function() {
                alert('dddd');
                jQuery.ajax({
                    url: "{$cityListUrl}?ajax=true&isAjax=true",
                    type: "POST",
                    data: {regionId: jQuery("#{$regionIdHtmlId}").val(), form_key: FORM_KEY},
                    success: function (data) {
                        jQuery("#loading-mask").hide();
                        cityIdValue = jQuery("#{$cityElementId}").val();
                        var options = '<option value="">{$pleaseSelectText}</option>';
                        if (data.length > 0) {
                            for (i = 0; i < data.length; i++) {
                                var selected = '';
                                if (data[i].label == cityIdValue) {
                                    selected = ' selected="selected" ';
                                }
                                options += '<option value="' + data[i].value + '"'+selected+'>' + data[i].label + '</option>';
                            }
                        }
                        var citySelectorId = jQuery("#{$regionIdHtmlId}").attr('id').replace('region_id', 'city');
                        jQuery('#'+citySelectorId).replaceWith('<select id="'+citySelectorId+'" name="{$cityElementName}">'+options+'</select>');
                    },
                    error: function (data) {
                        console.log("Error receiving city data");
                        console.log(data);
                    }
                });
            });
</script>
TEXT;

            }

            /*
             * Ajax call to get list cities
             */
            $html .=
<<<TEXT
            <script type="text/javascript">
                jQuery("#{$regionIdHtmlId}").change(function () {
                    alert('change');
                    var regionId = jQuery(this).val();
                    var citySelectorId = jQuery(this).attr('id').replace('region_id', 'city');
                    jQuery("#loading-mask").show();
                    jQuery.ajax({
                        url: "{$cityListUrl}?ajax=true&isAjax=true",
                        type: "POST",
                        data: {regionId: regionId, form_key: FORM_KEY},
                        success: function (data) {
                            jQuery("#loading-mask").hide();
                            var options = '<option value="">{$pleaseSelectText}</option>';
                            if (data.length > 0) {
                                for (i = 0; i < data.length; i++) {
                                    options += '<option value="' + data[i].value + '">' + data[i].label + '</option>';
                                }
                            }
                            jQuery('#'+citySelectorId).replaceWith('<select id="'+citySelectorId+'" name="{$cityElementName}">'+options+'</select>');
                        },
                        error: function (data) {
                            console.log("Error receiving city data");
                            console.log(data);
                        }
                    });
                });

        </script>
TEXT;

            $html.= '</td>';
            $element->setClass($elementClass);
        } else {
            $element->setClass('input-text');
            $html.= '<td class="label"><label for="'.$element->getHtmlId().'">'
                . $element->getLabel()
                . ' <span class="required" style="display:none">*</span></label></td>';

            $element->setRequired(false);
            $html.= '<td class="value">';
            $html .= '<input id="' . $regionHtmlId . '" name="' . $regionHtmlName
                . '" value="' . $element->getEscapedValue() . '" '
                . $element->serialize($htmlAttributes) . "/>" . "\n";
            $html .= '<input type="hidden" name="' . $regionIdHtmlName . '" id="' . $regionIdHtmlId . '" value=""/>';
            $html .= '</td>'."\n";
        }
        $html.= '</tr>'."\n";
        return $html;
    }
}
