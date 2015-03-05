<?php

/**
 * @file
 * Contains \Drupal\er_viewmode\Plugin\Field\FieldType\EntityReferenceViewModeItem.
 */

namespace Drupal\er_viewmode\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\entity_reference\ConfigurableEntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'Entity Reference View Mode' field type.
 *
 * @FieldType(
 *   id = "er_viewmode",
 *   label = @Translation("Entity reference with view mode"),
 *   description = @Translation("This field allows you to select an entity reference and specify a view mode."),
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 *   default_widget = "entity_reference_autocomplete",
 *   default_formatter = "er_viewmode_formatter",
 *   provider = "entity_reference"
 * )
 */
class EntityReferenceViewModeItem extends ConfigurableEntityReferenceItem {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field) {
    $schema = parent::schema($field);
    $schema['columns']['view_mode'] = array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => FALSE,
    );
    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);
    $properties['view_mode'] = DataDefinition::create('string')
      ->setLabel(t('View mode'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return array(
      'view_mode_settings' => array (
        'view_mode_selector_enabled' => FALSE,
        'allowed_view_modes' => array(),
      ),
    ) + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::fieldSettingsForm($form, $form_state);

    $settings = $this->getSettings();
    $selector_enabled = $settings['view_mode_settings']['view_mode_selector_enabled'];
    $element['view_mode_settings'] = array(
      '#type' => 'details',
      '#title' => t('View Mode Settings'),
      '#open' => true,
    );
    $element['view_mode_settings']['view_mode_selector_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable view mode selector'),
      '#description' => t('Allow selection of view mode per referenced entity'),
      '#default_value' => $selector_enabled,
    );

    // Get entity type
    $entity_type = $settings['target_type'];

    // Get all available entity view modes.
    $view_modes = \Drupal::entityManager()->getViewModes($entity_type);

    // Get currently-selected view modes.
    $current_view_modes = $settings['view_mode_settings']['allowed_view_modes'];

    // Build choices: view modes for the target entity type.
    $mode_choices = array();

    foreach ($view_modes as $key => $view_mode) {
      $id = str_replace('.', '__', $view_mode['id']);
      $mode_choices[$id] = $view_mode['label'];
    }

    $element['view_mode_settings']['allowed_view_modes'] = array(
      '#type' => 'checkboxes',
      '#options' => $mode_choices,
      '#title' => t('Select enabled view modes'),
      '#default_value' => $current_view_modes,
    );

    return $element;
  }
}