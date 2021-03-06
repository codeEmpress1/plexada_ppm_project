<?php


namespace App\Utility\Excel\Handlers;

use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use App\Utility\Excel\ExcelDocument;
use App\Utility\Excel\ExcelDocumentException;
use App\Utility\Excel\UploadValidation;
use App\Utility\Excel\ValidateExcelDocument;

/**
 * ExamResults Controller
 *
 * @property \App\Model\Table\ProjectDetailsTable $ProjectDetails
 *
 */

class ProjectUploadHandler extends AbstractHandler
{
    private $ProjectDetails;

    private $model  = null;

    const MIN_SIZE = 0;

    const __SLUG = 'upload';

    const EXPECTED_COLUMNS = [
        'Type',
        'name' => 'Project Name',
        'Next Activity',
        'Assigned To',
        'Percentage Completion',
        'Description',
        'Priority',
        'Achievement',
        'Payment',
        'Status',
        'Trigger',
        'Expected Close Date',
        'Amount',
        'Issue',
        'Remediation',
        'Impact'
    ];

    /**
     * @param $filename
     * @param $file_size
     * @param $media_type
     * @param $file_location
     * @param null $error
     * @param null $stream
     */
    public function __construct($filename, $file_size, $media_type, $file_location, $error = null, $stream = null)
    {
        parent::__construct($filename, $file_size, $media_type, $file_location, $error, $stream);
        $this->ProjectDetails = TableRegistry::getTableLocator()->get('ProjectDetails');
    }

    /**
     * @throws ExcelDocumentException
     */
    protected function validate()
    {
        $uploadValidation = new UploadValidation($this->error, $this->fileSize, self::MIN_SIZE);

        if($uploadValidation->isMinSize())
        {
            throw new ExcelDocumentException('File is too small.');
        }

        if($uploadValidation->isMaxSize())
        {
            throw new ExcelDocumentException('File is too large.');
        }

        if(!ValidateExcelDocument::isMimeTypeIsSupported($this->mediaType))
        {
            throw new ExcelDocumentException('Media Type not supported.');
        }
    }

    protected function import()
    {
        $excelDocument = new ExcelDocument(
            $this->filename,
            $this->fileSrc,
            self::EXPECTED_COLUMNS
        );

        $dataInSheet = $excelDocument->listDataInSheet();
        $excelDocument->sheetColumns($dataInSheet[1]);
        $sheetRows = $excelDocument->sheetRows($dataInSheet);
        $fetchData = [];

        foreach($sheetRows as $key => $row) {
            $data = [];
            foreach ($row as $column => $value)
            {
                $value = filter_var(trim($value));
                $type = $row['type'];

                $data = $this->solve(@$data, $key, $type, $column, $value);
                if(empty($data))
                {
                    unset($data);
                }
            }
            if(isset($data)){
                $fetchData[] = $data;
            }
        }
//        debug($fetchData);die();
        return $fetchData;
    }

    private function solve($data, $key, $type, $column, $value)
    {
        if(strtolower($column) == 'row_id')
        {
            $data[$column] = $value;
            $data['system_user_id'] = $_SESSION['Auth']['Users']['id'];
            return $data;
        }
        if(strtolower($column) == 'status')
        {
            $lov = $this->ProjectDetails->Lov->find('all')
                ->where(['lov_value' => $value])
                ->first();
            if(isset($lov))
            {
                $column = 'status_id';
                $value = $lov->id;
            }
        }

        if(strtolower($column) == 'priority')
        {
            $lov = $this->ProjectDetails->Priorities->find('all')
                ->where(['lov_value' => $value])
                ->first();
            if(isset($lov))
            {
                $column = 'priority_id';
                $value = $lov->id;
            }
        }

        if(strtolower($column) == 'assigned_to')
        {
            $name = explode(" ", $value);
            if(isset($name[1])){
                $staff = $this->ProjectDetails->Staff->find('all')
                    ->where(['first_name' => $name[0], 'last_name' => $name[1]])
                    ->first();
            }else{
                $staff = $this->ProjectDetails->Staff->find('all')
                    ->where([('CONCAT(first_name,\' \', last_name) LIKE') => '%' . $name[0] . '%'])
                    ->first();
            }
            if(isset($staff)) {
                $column = 'assigned_to_id';
                $value = $staff->id;
            }
        }

        $name = null;
        if(strtolower($column) == 'name')
        {
            $name = $this->ProjectDetails->find('all')
                ->where([$column => $value])
                ->first();
            if(isset($name))
            {
                $column = 'id';
                $value = $name->id;
            }else{
                $data[$column] = $value;
                $lov = $this->ProjectDetails->Lov->find('all')
                    ->where(['lov_value' => 'Open'])
                    ->first();
                if(isset($lov))
                {
                    $column = 'status_id';
                    $value = $lov->id;
                }
            }

        }

        if ($this->ProjectDetails->hasField($column) && !isset($data['id'])){
            $data[$column] = $value;
        }

        if($column == 'id')
        {
            $column = 'project_id';
        }

        if($this->ProjectDetails->RiskIssues->hasField($column) && $type == 'risk'){
            if(!isset($data['risk_issues'][$key]['record_number']))
                $data['risk_issues'][$key] = $this->ProjectDetails->RiskIssues->identify([]);
            $data['risk_issues'][$key][$column] = $value;
        }elseif($this->ProjectDetails->Milestones->hasField($column) && $type == 'milestones achievements'){
            if(!isset($data['milestones'][$key]['record_number']))
                $data['milestones'][$key] = $this->ProjectDetails->Milestones->identify([]);
            $data['milestones'][$key][$column] = $value;
        }
        return $data;
    }


    private function check($data, $row, $type)
    {

        if(strtolower($type) == 'milestones achievements')
        {
            $this->model = $this->ProjectDetails->Milestones;
            $data[$type][] = $this->extract($row);
        }elseif (strtolower($type) == 'risk'){
            $this->model = $this->ProjectDetails->RiskIssues;
            $data = $this->extract($row, $type);
        }

        return $data;
    }

    private function extract($row = [], $type)
    {
        $data = [];
        foreach ($row as $column => $value)
        {
            $data = $this->bind($data, $column, $value, $type);
        }
        return $data;
    }

    /**
     * @param $data : array
     * @param $column : string
     * @param $value : string
     * @return void
     */
    private function bind($data, $column, $value, $type)
    {
        if( !is_null($this->model ))
        {
            if($this->model->hasField($column))
            {
                $value = filter_var(trim($value));
                $data[$type][$column] = $value;
            }else if($this->model->getSource()->hasField($column)){
                $data[$column] = $value;
            }
        }

//
//        if($this->Students->Users->hasField($column))
//        {
//            if($column == 'username' && $this->Students->Users->isExists($column, $value))
//            {
//                $user = $this->Students->Users->find()
//                    ->where([$column => $value])->first();
//                $data[$column] = $user->id;
//                unset($data['user']);
//                return $data;
//            }else{
//                if(!isset($data['username']))
//                    $data['user'][$column] = $value;
//            }
//        }else if($this->Students->hasField($column)){
//            if($column == 'enroll_number' && $this->Students->isExists($column, $value))
//            {
//                return [];
//            }
//            $data[$column] = $value;
//        }else if($column == 'row_id'){
//            $data[$column] = $value;
//        }
//        $data[$column] = $value;

        return $data;
    }

    protected function export()
    {
        // TODO: Implement export() method.
    }
}