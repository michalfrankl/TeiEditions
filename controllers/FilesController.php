<?php
/**
 * TeiEditions
 *
 * @copyright Copyright 2017 King's College London Department of Digital Humanities
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The TeiEditions Edition record class.
 *
 * @package TeiEditions
 */
class TeiEditions_FilesController extends Omeka_Controller_AbstractActionController
{

    public function init()
    {
        // Set the model class so this controller can perform some functions,
        // such as $this->findById()
        $this->_helper->db->setDefaultModelName('Item');
    }

    public function indexAction()
    {
    }

    /**
     * Display the "Field Configuration" form.
     */
    public function importAction()
    {
        // Set the created by user ID.
        $form = $this->_getForm();
        $this->view->form = $form;
        $this->_processFieldForm($form, 'import');
    }

    private function _getForm()
    {
        $formOptions = array('type' => 'tei_editions_upload');
        $form = new Omeka_Form($formOptions);

        $form->addElement('file', 'file', array(
            'id' => 'tei-editions-upload-file',
            'label' => __('Select TEI'),
            'description' => __('A TEI file to upload as a new item'),
            'required' => true
        ));
        $form->addElement('submit', 'submit', array(
            'label' => __('Import TEI')
        ));

        $form->addDisplayGroup(array('file'), 'teiimport_info');
        $form->addDisplayGroup(array('submit'), 'teiimport_submit');

        return $form;
    }

    /**
     * Process the page edit and edit forms.
     */
    private function _processFieldForm($form, $action)
    {
        if ($this->getRequest()->isPost()) {
            if (!$form->isValid($_POST)) {
                $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
                return;
            }
            $item = new Item;
            $xpaths = TeiEditionsFieldMapping::fieldMappings();
            $path = $_FILES["file"]["tmp_name"];
            $data = @tei_editions_extract_metadata($path, $xpaths);
            error_log("Extracted from " . $path . " -> " .
                json_encode($data, JSON_PRETTY_PRINT));

            $item->addElementTextsByArray($data);
            $item->save();
            @insert_files_for_item($item, "Upload", "file");

            $this->_helper->flashMessenger(
                __('The TEI was successfully imported!'), 'success');

            $this->_helper->redirector($item->id, 'show', "items");
        }
    }
}
