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
      'width' => 25,
      'height' => 25,
      'enable_alt' => TRUE,
      'enable_title' => TRUE,
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
    $form['enable_alt'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable alt attribute.'),
      '#default_value' => $this->getSetting('enable_alt'),
    ];
    $form['enable_title'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable title attribute.'),
      '#default_value' => $this->getSetting('enable_title'),
    ];
    $form['notice'] = [
      '#type' => 'markup',
      '#markup' => '<div><small>' . $this->t('Alt and title attributes will be created from an image filename by removing file extension and replacing eventual underscores and dashes with spaces.') . '</small></div>',
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
    if ($this->getSetting('enable_alt')) {
      $summary[] = $this->t('Alt enabled');
    }
    if ($this->getSetting('enable_title')) {
      $summary[] = $this->t('Title enabled');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $attributes = [];
    if ($this->getSetting('apply_dimensions')) {
      $attributes['width'] = $this->getSetting('width');
      $attributes['height'] = $this->getSetting('height');
    }

    foreach ($items as $delta => $item) {
      if ($item->entity) {
        $filename = $item->entity->getFilename();
        $alt = $this->generateAltAttribute($filename);
        if ($this->getSetting('enable_alt')) {
          $attributes['alt'] = $alt;
        }
        if ($this->getSetting('enable_title')) {
          $attributes['title'] = $alt;
        }
        $uri = $item->entity->getFileUri();
        $elements[$delta] = [
          '#theme' => 'svg_formatter',
          '#attributes' => $attributes,
          '#uri' => $uri,
        ];
      }
    }

    return $elements;
  }

  /**
   * Generate alt attribute from an image filename.
   */
  private function generateAltAttribute($filename) {
    $alt = str_replace(['.svg', '-', '_'], ['', ' ', ' '], $filename);
    $alt = ucfirst($alt);
    return $alt;
  }

}
