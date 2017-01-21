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
 *   label = @Translation("SVG formatter"),
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

    $form['apply_dimensions'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Apply dimensions.'),
      '#default_value' => $this->getSetting('apply_dimensions'),
    ];
    $form['width'] = [
      '#type' => 'number',
      '#title' => $this->t('Image width.'),
      '#default_value' => $this->getSetting('width'),
    ];
    $form['height'] = [
      '#type' => 'number',
      '#title' => $this->t('Image height.'),
      '#default_value' => $this->getSetting('height'),
    ];

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
        $filename = $item->entity->getFilename();
        $alt = $this->generateAltAttribute($filename);
        $elements[$delta] = [
          '#theme' => 'svg_formatter',
          '#uri' => $uri,
          '#alt' => $alt,
          '#apply_dimensions' => $apply_dimensions,
          '#width' => $width,
          '#height' => $height,
        ];
      }
    }

    return $elements;
  }

  /**
   * Generate alt attribute from image filename.
   */
  private function generateAltAttribute($filename) {
    $alt = str_replace(['.svg', '-', '_'], ['', ' ', ' '], $filename);
    $alt = ucfirst($alt);
    return $alt;
  }

}
