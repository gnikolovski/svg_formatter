<?php

namespace Drupal\svg_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'svg_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "svg_formatter",
 *   label = @Translation("Svg formatter"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class SvgFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
      'apply_dimensions' => TRUE,
      'width' => 16,
      'height' => 16,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['apply_dimensions'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Apply dimensions.'),
      '#default_value' => $this->getSetting('apply_dimensions'),
    );
    $form['width'] = array(
      '#type' => 'number',
      '#title' => $this->t('Image width.'),
      '#default_value' => $this->getSetting('width'),
    );
    $form['height'] = array(
      '#type' => 'number',
      '#title' => $this->t('Image height.'),
      '#default_value' => $this->getSetting('height'),
      '#element_validate' => array('element_validate_integer_positive'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.
    if ($this->getSetting('apply_dimensions') && $this->getSetting('width')) {
      $summary[] = $this->t('Image width:') . ' ' . $this->getSetting('width');
    }
    if ($this->getSetting('apply_dimensions') && $this->getSetting('width')) {
      $summary[] = $this->t('Image height:') . ' ' . $this->getSetting('height');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $apply_dimensions = $this->getSetting('apply_dimensions');
    $width = $this->getSetting('width');
    $height = $this->getSetting('height');

    foreach ($items as $delta => $item) {
      if ($item->entity) {
        $uri = $item->entity->getFileUri();
        $elements[$delta] = array(
          '#theme' => 'svg_formatter',
          '#uri' => $uri,
          '#apply_dimensions' => $apply_dimensions,
          '#width' => $width,
          '#height' => $height,
        );
      }
    }

    return $elements;
  }

}
