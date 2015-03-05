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
}