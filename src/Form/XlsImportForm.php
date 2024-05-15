<?php

namespace Drupal\xls_import\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Drupal\node\Entity\Node;

class XlsImportForm extends FormBase {

  public function getFormId() {
    return 'xls_import_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['xls_file'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload XLS file'),
      '#description' => $this->t('Please upload the XLS file.'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $validators = ['file_validate_extensions' => ['xls xlsx']];
    if ($file = file_save_upload('xls_file', $validators, FALSE, 0, FILE_EXISTS_RENAME)) {
      $file_path = \Drupal::service('file_system')->realpath($file->getFileUri());
      $this->importFromFile($file_path);
      \Drupal::messenger()->addMessage($this->t('File imported successfully.'));
      $form_state->setRedirect('xls_import.import_form');
    } else {
      $form_state->setErrorByName('xls_file', $this->t('Failed to upload file.'));
    }
  }

  private function importFromFile($file_path) {
    require_once DRUPAL_ROOT . '/vendor/autoload.php';

    $spreadsheet = IOFactory::load($file_path);
    $sheet = $spreadsheet->getSheet(2);
    $data = $sheet->toArray();

    foreach ($data as $row) {
      $node = Node::create([
        'type' => 'product',
        'title' => $row[0],
        'field_price' => $row[1], 
        'field_description' => $row[2],
      ]);
      $node->save();
    }
  }
}
