<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class SiteformsController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->lang->load('rest_lang', 'english');

        if (!$this->verify_request()) {
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }

        $this->load->database();
        $this->load->helper('general');
        $this->load->model('Admin/SiteFormModel');
        $this->load->model('Admin/SiteFormSubmitModel');
        $this->load->model('Admin/SiteFormItemModel');
    }

    /**
     * GET /api/v1/siteforms[/{siteform_id}]
     */
    public function index_get($siteform_id = null)
    {
        $siteform = new SiteFormModel();
        if ($siteform_id) {
            $result = $siteform->find_with(array('siteform_id' => $siteform_id));
            $result = $result ? $siteform->as_data() : [];
            if ($result) {
                $this->response_ok($result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $this->respond_index_list($siteform, array('status_in' => array(0, 1)));
    }

    /**
     * POST /api/v1/siteforms
     */
    public function index_post()
    {
        $this->load->library('FormValidator');
        $form = new FormValidator();
        $config = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'required|min_length[1]'),
            array('field' => 'template', 'label' => 'template', 'rules' => 'required|min_length[1]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer'),
        );
        $form->set_rules($config);
        if (!$form->run()) {
            $this->response_error(lang('validations_error'), ['errors' => $form->_error_array], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $siteform = new SiteFormModel();
        $isUpdate = (bool) $this->input->post('siteform_id');
        if ($isUpdate) {
            $siteform->find($this->input->post('siteform_id'));
        } else {
            $siteform->date_create = date("Y-m-d H:i:s");
        }
        $siteform->name = $this->input->post('name');
        $siteform->template = $this->input->post('template');
        $siteform->properties = $this->input->post('properties');
        $siteform->user_id = userdata('user_id');
        $siteform->status = $this->input->post('status');

        if ($siteform->save()) {
            $siteform_items = $this->input->post('siteform_items');
            if (!is_array($siteform_items)) {
                $siteform_items = array();
            }

            $keepIds = array();
            foreach ($siteform_items as $item) {
                $item = (object) $item;
                $siteform_item = new SiteFormItemModel();
                if (!empty($item->siteform_item_id)) {
                    $siteform_item->find($item->siteform_item_id);
                }
                $siteform_item->siteform_id = $siteform->siteform_id;
                $siteform_item->order = isset($item->order) ? $item->order : '0';
                $siteform_item->item_type = $item->item_type;
                $siteform_item->item_name = $item->item_name;
                $siteform_item->item_label = $item->item_label;
                $siteform_item->item_class = $item->item_class;
                $siteform_item->item_title = $item->item_title;
                $siteform_item->item_placeholder = $item->item_placeholder;
                $siteform_item->properties = $item->properties;
                $siteform_item->data = $item->data;
                $siteform_item->status = $item->status;
                if (empty($siteform_item->date_create) || $siteform_item->date_create === '0000-00-00 00:00:00') {
                    $siteform_item->date_create = date("Y-m-d H:i:s");
                }
                $siteform_item->save();
                if (!empty($siteform_item->siteform_item_id)) {
                    $keepIds[] = (int) $siteform_item->siteform_item_id;
                }
            }

            $existing = (new SiteFormItemModel())->where(array('siteform_id' => $siteform->siteform_id));
            if ($existing) {
                foreach ($existing as $old) {
                    $oldId = (int) $old->siteform_item_id;
                    if (!in_array($oldId, $keepIds, true)) {
                        $orphan = new SiteFormItemModel();
                        if ($orphan->find($oldId)) {
                            $orphan->delete();
                        }
                    }
                }
            }

            system_logger('siteforms', $siteform->siteform_id, $isUpdate ? 'updated' : 'created', $isUpdate ? 'A siteform has been updated' : 'A siteform has been created');
            $this->response_ok($siteform);
            return;
        }
        $this->response_error(lang('unexpected_error'), [], REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
    }

    public function index_put($id)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    public function index_delete($siteform_id = null)
    {
        if ($siteform_id) {
            $siteform = new SiteFormModel();
            $result = $siteform->find($siteform_id);
            if ($result) {
                system_logger('siteforms', $siteform->siteform_id, 'deleted', 'A siteform has been deleted');
                $this->response_ok(["result" => $siteform->delete()]);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }
        $this->response_error(lang('not_found_error'));
    }

    public function templates_get()
    {
        $this->load->helper('directory');
        $directory = APPPATH . '/views/site/templates/forms';
        if (getThemePath()) {
            $directory = getThemePath() . '/views/site/templates/forms';
        }
        $map = directory_map($directory);
        $this->response_ok($map);
    }

    /**
     * GET /api/v1/siteforms/submit[/{id}]
     */
    public function submit_get($siteFormSubmit_id = null)
    {
        $SiteFormSubmit = new SiteFormSubmitModel();
        if ($siteFormSubmit_id) {
            $result = $SiteFormSubmit->find_with(array('siteform_submit_id' => $siteFormSubmit_id));
            $result = $result ? $SiteFormSubmit->as_data() : [];
            if ($result) {
                $this->response_ok($result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $where = array('status_in' => array(1, 2));
        $formId = $this->get('siteform_id');
        if ($formId === null || $formId === '') {
            $formId = $this->get('form');
        }
        if ($formId) {
            $where['siteform_id'] = (int) $formId;
        }

        $this->respond_index_list($SiteFormSubmit, $where, array('date_create', 'DESC'));
    }

    /**
     * DELETE /api/v1/siteforms/submit/{id}
     */
    public function submit_delete($id = null)
    {
        $SiteFormSubmit = new SiteFormSubmitModel();
        if ($id && $SiteFormSubmit->find($id)) {
            system_logger('siteforms', $SiteFormSubmit->siteform_submit_id, 'deleted', 'A siteform submission has been deleted');
            $this->response_ok(array('result' => $SiteFormSubmit->delete()));
            return;
        }
        $this->response_error(lang('not_found_error'));
    }

    /**
     * POST /api/v1/siteforms/submit_archive/{id}
     */
    public function submit_archive_post($id = null)
    {
        $SiteFormSubmit = new SiteFormSubmitModel();
        if ($id && $SiteFormSubmit->find($id)) {
            $SiteFormSubmit->status = 2;
            $SiteFormSubmit->save();
            system_logger('siteforms', $SiteFormSubmit->siteform_submit_id, 'archive', 'A siteform submission has been marked as seen');
            $this->response_ok($SiteFormSubmit);
            return;
        }
        $this->response_error(lang('not_found_error'));
    }
}
