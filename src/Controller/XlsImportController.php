<?php

namespace Drupal\xls_import\Controller;

use Drupal\Core\Controller\ControllerBase;

class XlsImportController extends ControllerBase {

  public function handleImport() {
    
    return [
      '#markup' => $this->t('Handle import logic here.'),
    ];
  }
}
