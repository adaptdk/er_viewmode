<?php
/**
 * @file
 * Contains \Drupal\er_viewmode\Plugin\Field\FieldFormatter\EntityReferenceViewModeFormatter.
 */

namespace Drupal\er_viewmode\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;

/**
 * Plugin implementation of the Entity Reference View Mode Formatter.
 *
 * @FieldFormatter(
 *   id = "er_viewmode_formatter",
 *   label = @Translation("Rendered entity with view mode"),
 *   field_types = {
 *     "er_viewmode"
 *   }
 * )
 */
class EntityReferenceViewModeFormatter extends EntityReferenceFormatterBase {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();

    foreach ($this->getEntitiesToView($items) as $delta => $entity) {
      // Protect ourselves from recursive rendering.
      static $depth = 0;
      $depth++;
      if ($depth > 20) {
        $this->loggerFactory->get('entity')->error('Recursive rendering detected when rendering entity @entity_type @entity_id. Aborting rendering.', array('@entity_type' => $entity->getEntityTypeId(), '@entity_id' => $entity->id()));
        return $elements;
      }

      if ($entity->id()) {
        $view_mode = $items[$delta]->view_mode;
        $elements[$delta] = entity_view($entity, $view_mode, $entity->language()->getId());

        // Add a resource attribute to set the mapping property's value to the
        // entity's url. Since we don't know what the markup of the entity will
        // be, we shouldn't rely on it for structured data such as RDFa.
        if (!empty($items[$delta]->_attributes)) {
          $items[$delta]->_attributes += array('resource' => $entity->url());
        }
      }
      else {
        // This is an "auto_create" item.
        $elements[$delta] = array('#markup' => $entity->label());
      }
      $depth = 0;
    }

    return $elements;
  }

}
