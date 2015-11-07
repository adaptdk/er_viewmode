<?php

class ERViewModeBehavior extends EntityReference_BehaviorHandler_Abstract {

  public function schema_alter(&$schema, $field) {
    $schema['columns']['view_mode'] = array(
      'description' => 'Target view mode machine name.',
      'type' => 'varchar',
      'length' => 32,
      'default' => 'full',
      'not null' => FALSE,
    );
    $schema['columns']['view_options'] = array(
      'type' => 'blob',
      'size' => 'big',
      'description' => 'Serialized data containing the target entity view options.',
    );
  }

  public function load($entity_type, $entities, $field, $instances, $langcode, &$items) {
    foreach ($items as &$by_entity) {
      foreach ($by_entity as &$item) {
        $item['view_options'] = unserialize($item['view_options']);
      }
    }
  }

  public function insert($entity_type, $entity, $field, $instance, $langcode, &$items) {
    foreach ($items as &$item) {
      $item['view_options'] = serialize($item['view_options']);
    }
  }

  public function update($entity_type, $entity, $field, $instance, $langcode, &$items) {
    foreach ($items as &$item) {
      $item['view_options'] = serialize($item['view_options']);
    }
  }

  public function property_info_alter(&$info, $entity_type, $field, $instance, $field_type) {

  }

  /**
   * Generate a settings form for this handler.
   */
  public function settingsForm($field, $instance) {

    $viewmodes = er_viewmode_get_view_modes($field, $instance, TRUE);

    $form['enabled_viewmodes'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Select enabled view modes'),
      '#options' => $viewmodes,
    );

    return $form;
  }
}
