<?php
namespace local\stim\it_support_stim\classes;
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
spl_autoload_register(function ($className) {
    $className = str_replace("\\", "/", $className);
    $file = $_SERVER['DOCUMENT_ROOT'] . "/{$className}.php";
    if (file_exists($file)) {
        require_once $file;
    }
});
use local\stim\it_support_stim\classes\{ ItSupportSubject, ItSupportSection, ItSupportAppealType, LoadFile, ItSupportStimCompanies, ItSupportDepartments, ItSupportPositions };
use local\stim\libraries\library_departments_tree\{ DepartmentTree, AbstractTree, ContainerTree, DataProvider, Department, ContainerContent };
class ItSupportTasks extends \CTasks
{
    const APPEAL_INFO = 184806; // Запрос на информвцию
    const APPEAL_SERVICE = 184807; // Запрос на обслуживание
    const APPEAL_CHANGE = 184808; // Заппрос на изменения
    const APPEAL_ACCESS = 184809; // Запрос на предоставление доступа
    const MISTAKE = 184810; // Ошибка
    const FAILURE = 184811; // Сбой
    const DIFFICULT_CHOICE = 184737; // Затрудняюсь ответить
    const NEW_USER = 185071; // Новый сотрудник
    const REQUEST_FOR_DEPARTMENT_TREE = "select ID, NAME, IBLOCK_SECTION_ID from b_iblock_section where IBLOCK_ID=5";

    private $arrForTaskCreate;
    private $subjectId;
    private $appealId;
    private $itSupportSubject;
    private $ItSupportSection;
    private $itSupportAppealType;
    private $LoadFile;
    private $objForCreateTaskFields;
    private $filesFrom_FILES_var;

    public function __construct( $arrForTaskCreate, $filesFrom_FILES_var )
    {
        $this->arrForTaskCreate = $arrForTaskCreate;
        $this->filesFrom_FILES_var = $filesFrom_FILES_var;
        $this->subjectId = $arrForTaskCreate['SUBJECT_SELECTED'];
        $this->appealId = $arrForTaskCreate['TYPE_APPEAL_SELECTED'];
        $this->itSupportSubject = new ItSupportSubject();
        $this->ItSupportSection = new ItSupportSection();
        $this->itSupportAppealType = new ItSupportAppealType();
        $this->LoadFile = new LoadFile();
        $this->objForCreateTaskFields = $this->itSupportSubject->ifRecordExists( $this->subjectId ) ? $this->itSupportSubject :
            ( $this->ItSupportSection->ifRecordExists( $this->subjectId ) ? $this->ItSupportSection : null );
    }

    public function add()
    {
        $arrForTask  = [
            'TITLE'=>$this->getTitle(),
            'DESCRIPTION'=>$this->getDescription(),
            'PRIORITY'=>$this->getPriority(),
            'AUDITORS'=>$this->getAuditors(),
            'TAGS'=>$this->getArrTags(),
            'GROUP_ID'=>$this->getGroupId(),
            'RESPONSIBLE_ID'=>$this->getResponsible(),
            'CREATED_BY'=>$this->getCreatedBy(),
        ];
        $taskId = parent::add( $arrForTask );
        $filesQuantity = count( $this->filesFrom_FILES_var['files']['name'] );
        $arrIdRemovedFiles = [];
        for( $fileCount=0; $fileCount<$filesQuantity; $fileCount++ )
        {
            $arrFile = [
                "name" => $_FILES['files']['name'][$fileCount],
                "size" => $_FILES['files']['size'][$fileCount],
                "tmp_name" => $_FILES['files']['tmp_name'][$fileCount],
                "type" => $_FILES['files']['type'][$fileCount],
            ];
            //$arrIdRemovedFiles[] = $this->LoadFile->removeFileFromPostRequestToUserFolderStorage( $arrFile );
            $FILE_ID = $this->LoadFile->removeFileFromPostRequestToUserFolderStorage( $arrFile );
            $arrIdRemovedFiles[] = "n" . $FILE_ID;
            //$this->LoadFile->attachFileToTask( $taskId, $FILE_ID );
        }
            $this->LoadFile->attachFileToTask( $taskId, $arrIdRemovedFiles );
            //$FILE_ID = $this->LoadFile->removeFileFromPostRequestToUserFolderStorage( $arrFileForRemove );
            //$this->Update(array("UF_TASK_WEBDAV_FILES" => Array("n$FILE_ID")));
            //$this->LoadFile->attachFileToTask( $taskId, $FILE_ID );

        return $taskId ;
    }
    public function getResponsible()
    {
        return $this->objForCreateTaskFields->getData()[ $this->subjectId ]['DISPATCHER']['VALUE'];
    }
    public function getTitle()
    {
        return $this->getTextValueSubject() . ( $this->getTextValueAppeal() ? ":" : "") . $this->getTextValueAppeal() . ":" . $this->get50SymFromQuery();
    }
    private function get50SymFromQuery()
    {
        $str50Sym = "";
        if( $this->arrForTaskCreate['SUBJECT_SELECTED'] == self::DIFFICULT_CHOICE ) {
            $str50Sym = count($this->arrForTaskCreate["YOUR_QUESTION_QUESTION"])<50 ? $this->arrForTaskCreate["YOUR_QUESTION_QUESTION"] : substr( $this->arrForTaskCreate["YOUR_QUESTION_QUESTION"], 0, 50);
        } elseif( $this->arrForTaskCreate['SUBJECT_SELECTED'] == self::NEW_USER ) {
            $str50Sym = count($this->arrForTaskCreate["NOTE_VALUE"])<50 ? $this->arrForTaskCreate["NOTE_VALUE"] : substr( $this->arrForTaskCreate["NOTE_VALUE"], 0, 50);
        } else {
            switch ( $this->arrForTaskCreate['TYPE_APPEAL_SELECTED'])
            {
                case self::APPEAL_INFO:
                    $str50Sym = count($this->arrForTaskCreate["YOUR_QUESTION_QUESTION"])<50 ? $this->arrForTaskCreate["YOUR_QUESTION_QUESTION"] : substr( $this->arrForTaskCreate["YOUR_QUESTION_QUESTION"], 0, 50);
                    break;
                case self::APPEAL_SERVICE:
                    $str50Sym = count($this->arrForTaskCreate["NEED_EXECUTE_VALUE"])<50 ? $this->arrForTaskCreate["NEED_EXECUTE_VALUE"] : substr( $this->arrForTaskCreate["NEED_EXECUTE_VALUE"], 0, 50);
                    break;
                case self::APPEAL_CHANGE:
                    $str50Sym = count($this->arrForTaskCreate["DIRECTION_VALUE"])<50 ? $this->arrForTaskCreate["DIRECTION_VALUE"] : substr( $this->arrForTaskCreate["DIRECTION_VALUE"], 0, 50);
                    break;
                case self::APPEAL_ACCESS:
                    $str50Sym = count($this->arrForTaskCreate["NOTE_VALUE"])<50 ? $this->arrForTaskCreate["NOTE_VALUE"] : substr( $this->arrForTaskCreate["NOTE_VALUE"], 0, 50);
                    break;
                case self::MISTAKE:
                    $str50Sym = count($this->arrForTaskCreate["MISTAKE_DESCRIPTION_VALUE"])<50 ? $this->arrForTaskCreate["MISTAKE_DESCRIPTION_VALUE"] : substr( $this->arrForTaskCreate["MISTAKE_DESCRIPTION_VALUE"], 0, 50);
                    break;
                case self::FAILURE:
                    $str50Sym = count($this->arrForTaskCreate["FAILURE_PROBLEM_DESCRIPTION_VALUE"])<50 ? $this->arrForTaskCreate["FAILURE_PROBLEM_DESCRIPTION_VALUE"] : substr( $this->arrForTaskCreate["FAILURE_PROBLEM_DESCRIPTION_VALUE"], 0, 50);
                    break;
               /*  case self::DIFFICULT_CHOICE:
                    $str50Sym = count($this->arrForTaskCreate["YOUR_QUESTION_QUESTION"])<50 ? $this->arrForTaskCreate["YOUR_QUESTION_QUESTION"] : substr( $this->arrForTaskCreate["YOUR_QUESTION_QUESTION"], 0, 50);
                    break;
                case self::NEW_USER:
                    $str50Sym = "";
                    break; */
                default:
                    $str50Sym = "";
            }
        }
        return $str50Sym;

    }
    public function getDescription()
    {
        $descriptionStr = "";
        $descriptionStr .= $this->arrForTaskCreate["YOUR_QUESTION_QUESTION"] ? $this->arrForTaskCreate["YOUR_QUESTION_TITLE"] . ":\r\n</br>" . $this->arrForTaskCreate["YOUR_QUESTION_QUESTION"] . "\r\n</br>" : "";
        $descriptionStr .= $this->arrForTaskCreate["NEED_EXECUTE_VALUE"] ? $this->arrForTaskCreate["NEED_EXECUTE_TEXT"] . ":\r\n</br>" . $this->arrForTaskCreate["NEED_EXECUTE_VALUE"] . "\r\n</br>" : "";
        $descriptionStr .= $this->arrForTaskCreate["DIRECTION_VALUE"] ? $this->arrForTaskCreate["DIRECTION_TEXT"] . ":\r\n</br>" . $this->arrForTaskCreate["DIRECTION_VALUE"] . "\r\n</br>" : "";
        $descriptionStr .= $this->arrForTaskCreate["YOUR_LOCATION_VALUE"] ? $this->arrForTaskCreate["YOUR_LOCATION_TEXT"] . ":\r\n</br>" . $this->arrForTaskCreate["YOUR_LOCATION_VALUE"] . ":\r\n</br>" : "";
        
        $descriptionStr .= $this->arrForTaskCreate["RULES_REQUEST_COMPANY_VALUE"] ? $this->arrForTaskCreate["RULES_REQUEST_COMPANY_TEXT"] . ":\r\n" . 
                           $this->getCompanyNameByID( $this->arrForTaskCreate["RULES_REQUEST_COMPANY_VALUE"] ) . ":\r\n" : "";
        $descriptionStr .= $this->arrForTaskCreate["RULES_REQUEST_DEPARTMENT_VALUE"] ? $this->arrForTaskCreate["RULES_REQUEST_DEPARTMENT_TEXT"] . ":\r\n</br>" . 
                           $this->getDepartmentNameByID( $this->arrForTaskCreate["RULES_REQUEST_DEPARTMENT_VALUE"] ) . ":\r\n</br>" : "";
        //$descriptionStr .= $this->arrForTaskCreate["NOTE_VALUE"] ? $this->arrForTaskCreate["NOTE_TEXT"] . ":\r\n" . $this->arrForTaskCreate["NOTE_VALUE"] . ":\r\n" : "";
        //$descriptionStr .= $this->arrForTaskCreate["RULES_REQUEST_USER_NAME_VALUE"] ? $this->arrForTaskCreate["RULES_REQUEST_USER_NAME_TEXT"] . ":\r\n" . $this->arrForTaskCreate["RULES_REQUEST_USER_NAME_VALUE"] . ":\r\n" : "";
        $descriptionStr .= $this->arrForTaskCreate["RULES_REQUEST_USER_NAME_VALUE"] ? $this->arrForTaskCreate["RULES_REQUEST_USER_NAME_TEXT"] . ":\r\n" . $this->arrForTaskCreate["RULES_REQUEST_USER_NAME_VALUE"] . ":\r\n" : "";
        $descriptionStr .= $this->arrForTaskCreate["RULES_REQUEST_USER_NAME_SAME_ROLE_VALUE"] ? $this->arrForTaskCreate["RULES_REQUEST_USER_SAME_ROLE_TEXT"] . ":\r\n" . $this->arrForTaskCreate["RULES_REQUEST_USER_NAME_SAME_ROLE_VALUE"] . ":\r\n" : "";

        $descriptionStr .= $this->arrForTaskCreate["RULES_REQUEST_STIM_COMPANY_VALUE"] ? $this->arrForTaskCreate["RULES_REQUEST_STIM_COMPANY_TEXT"] . ":\r\n" . 
                           $this->getCompanyNameByID( $this->arrForTaskCreate["RULES_REQUEST_STIM_COMPANY_VALUE"] ) . ":\r\n" : "";
        $descriptionStr .= $this->arrForTaskCreate["RULES_REQUEST_USER_ROLE_VALUE"] ? $this->arrForTaskCreate["RULES_REQUEST_USER_ROLE_TEXT"] . ":\r\n" . $this->arrForTaskCreate["RULES_REQUEST_USER_ROLE_VALUE"] . ":\r\n" : "";
        $descriptionStr .= $this->arrForTaskCreate["CONTACT_PHONE_VALUE"]  ? $this->arrForTaskCreate["CONTACT_PHONE_TEXT"] . ":\r\n" . $this->arrForTaskCreate["CONTACT_PHONE_VALUE"] . ":\r\n" : "";

        $descriptionStr .= $this->arrForTaskCreate["FAILURE_PROBLEM_DESCRIPTION_VALUE"] ? $this->arrForTaskCreate["FAILURE_PROBLEM_DESCRIPTION_TEXT"] . ":\r\n</br>" . $this->arrForTaskCreate["FAILURE_PROBLEM_DESCRIPTION_VALUE"] . ":\r\n</br>" : "";
        $descriptionStr .= $this->arrForTaskCreate["FAILURE_CORRECT_LAST_TIME_VALUE"] ? $this->arrForTaskCreate["FAILURE_CORRECT_LAST_TIME_TEXT"] . ":\r\n</br>" . $this->arrForTaskCreate["FAILURE_CORRECT_LAST_TIME_VALUE"] . ":\r\n</br>" : "";
        
        $descriptionStr .= $this->arrForTaskCreate["MISTAKE_DESCRIPTION_VALUE"] !="" ? $this->arrForTaskCreate["MISTAKE_DESCRIPTION_TEXT"] . ":\r\n</br>" . $this->arrForTaskCreate["MISTAKE_DESCRIPTION_VALUE"] . "\r\n</br>" : "";
        $descriptionStr .= $this->arrForTaskCreate["MISTAKE_ERLIER_SOLVING_PROBLEM_VALUE"] !="" ? $this->arrForTaskCreate["MISTAKE_ERLIER_SOLVING_PROBLEM_TEXT"] . ":\r\n</br>" . $this->arrForTaskCreate["MISTAKE_ERLIER_SOLVING_PROBLEM_VALUE"] . "\r\n</br>" : "";
        $descriptionStr .= $this->arrForTaskCreate["MISTAKE_INSTRUCTION_LINK_VALUE"] !="" ? $this->arrForTaskCreate["MISTAKE_INSTRUCTION_LINK_TEXT"] . ":\r\n</br>" . $this->arrForTaskCreate["MISTAKE_INSTRUCTION_LINK_VALUE"] . "\r\n</br>" : "";
        
        $descriptionStr .= $this->arrForTaskCreate["OBJ_LINK_LINK"] !="" ? $this->arrForTaskCreate["OBJ_LINK_TEXT"] . ":\r\n</br>" . $this->arrForTaskCreate["OBJ_LINK_LINK"] . "\r\n</br>" : "";

        $descriptionStr .= $this->arrForTaskCreate["COMPANY_VALUE"] ? $this->arrForTaskCreate["COMPANY_TEXT"] . ":\r\n" . 
                           $this->getCompanyNameByID( $this->arrForTaskCreate["COMPANY_VALUE"] ) . ":\r\n" : "";
        //$descriptionStr .= $this->arrForTaskCreate["POSITION_VALUE"] ? $this->arrForTaskCreate["DEPARTMENT_TEXT"] . ":\r\n" . $this->arrForTaskCreate["POSITION_VALUE"] . ":\r\n" : "";
        $descriptionStr .= $this->arrForTaskCreate["POSITION_VALUE"] ? $this->arrForTaskCreate["POSITION_TEXT"] . ":\r\n" . 
                           $this->getPositionNameByID( $this->arrForTaskCreate["POSITION_VALUE"] ) . ":\r\n" : "";
        $descriptionStr .= $this->arrForTaskCreate["USER_VALUE"] ? $this->arrForTaskCreate["USER_TEXT"] . ":\r\n" . $this->arrForTaskCreate["USER_VALUE"] . ":\r\n" : "";
        $descriptionStr .= $this->arrForTaskCreate["NEED_MAIL_VALUE"]  ? $this->arrForTaskCreate["NEED_MAIL_TEXT"] . ":\r\n" . $this->arrForTaskCreate["NEED_MAIL_VALUE"] . ":\r\n" : "";
        $descriptionStr .= $this->arrForTaskCreate["NOTE_VALUE"]  ? $this->arrForTaskCreate["NOTE_TEXT"] . ":\r\n" . $this->arrForTaskCreate["NOTE_VALUE"] . ":\r\n" : "";
        return $descriptionStr;
    }
    public function getCreatedBy()
    {
        return $this->arrForTaskCreate['CURRENT_USER_ID'];
    }
    public function getAuditors()
    {
        return $this->objForCreateTaskFields->getData()[ $this->subjectId ]['AUDITORS']['VALUE'];
    }
    public function getHighPriorityAuditors()
    {
        return array_merge( $this->getAuditors(), $this->objForCreateTaskFields->getData()[ $this->subjectId ]['AUDITORS_HIGH_PRIORITET']['VALUE'] );
    }
//    public function getArrAccomplices()
//    {
//        return $this->objForCreateTaskFields->getData()[ $this->subjectId ]['AUDITORS']['VALUE'];
//    }
    public function getArrTags()
    {
        $arrTags =  $this->objForCreateTaskFields->getData()[ $this->subjectId ]['TAGS']['VALUE'];
        if( $this->subjectId ) $arrTags[] = $this->getTextValueSubject() ;
        if( $this->arrForTaskCreate['TYPE_APPEAL_SELECTED'] ) $arrTags[] = $this->getTextValueAppeal();
        return $arrTags;
    }
    public function getGroupId()
    {
        return $this->objForCreateTaskFields->getData()[ $this->subjectId ]['PROJECT']['VALUE'];
    }
    public function getPriority()
    {
        return 0;
    }
    private function getTextValueSubject()
    {
        if( $this->objForCreateTaskFields ) return $this->objForCreateTaskFields->getSubjectByRecordId( $this->subjectId )['TEKST_RU'];
        else return "";
    }
    private function getTextValueAppeal()
    {
        if( $this->arrForTaskCreate['TYPE_APPEAL_SELECTED']  ) return $this->itSupportAppealType->getSubjectByRecordId( $this->appealId )['TEKST_RU'];
        else return "";
    }
    public function getCompanyNameByID( $companyId ) {
        return ( new ItSupportStimCompanies() )->getSubjectByRecordId( $companyId )['TEKST_RU'];
    }

    public function getDepartmentNameByID( $departmentId ) {
        return ( new DepartmentTree( self::REQUEST_FOR_DEPARTMENT_TREE ) )->getContainerById( $departmentId )->getContainerContent();
    }
    public function getPositionNameByID( $positionId ) {
        return ( new ItSupportPositions() )->getPositionByRecordId( $positionId )['POSITION_RU'];
    }
    public function getSubject()
    {
        return $this->objForCreateTaskFields;
    }
    public function getAppealId()
    {
        return $this->appealId;
    }
    public function getArrForCreateTask()
    {
        return $this->arrForTaskCreate;
    }
    public function getNoteText() { // use in bp and task, created from bp
        return $this->arrForTaskCreate["NOTE_VALUE"];
    }
}