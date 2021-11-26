<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
spl_autoload_register(function ($className) {
    $className = str_replace("\\", "/", $className);
    $file = $_SERVER['DOCUMENT_ROOT'] . "/{$className}.php";
    if (file_exists($file)) {
        require_once $file;
    }
});
use local\stim\it_support_stim\classes\{ ItSupportSection, ItSupportSubject, ItSupportAppealType, ItSupportLabel, ItSupportTooltip, ItSupportStimCompanies, itSupportRoleGrpups, itSupportRoles, ItSupportUsersWithRights, ItSupportUsers, ItSupportTasks };
use local\stim\libraries\bp\{ BpStim };
//echo json_encode( $_POST['RULES_REQUEST_STIM_COMPANY_VALUE'] );
//$_POST = json_decode(file_get_contents('php://input'), true);
$itSupportTasks = new ItSupportTasks( $_POST, $_FILES );
$subjectRequest = $itSupportTasks->getSubject();
$selectedSubjectId = $itSupportTasks->getArrForCreateTask()['SUBJECT_SELECTED'];
$selectedAppealId = $itSupportTasks->getArrForCreateTask()['TYPE_APPEAL_SELECTED'];
if( $selectedAppealId == ItSupportTasks::APPEAL_ACCESS  )
{
    $bpObg = new BpStim( $subjectRequest->getBpId( $selectedSubjectId ), $subjectRequest->getBpTemplateId( $selectedSubjectId ) );
    $bpId = $bpObg->createBP( "Техподдержка IT STiM", "SUPPORT_IT_STiM");
    $paramArr = [   'userCompany'=> $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_COMPANY_VALUE'],
        'userDepartment'=>$itSupportTasks->getDepartmentNameByID( $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_DEPARTMENT_VALUE'] ),
        'user'=>$itSupportTasks->getArrForCreateTask()['RULES_REQUEST_USER_NAME_VALUE'],
        'userSameRoles'=>$itSupportTasks->getArrForCreateTask()['RULES_REQUEST_USER_NAME_SAME_ROLE_VALUE'],
        'declarer'=> $itSupportTasks->getArrForCreateTask()['CURRENT_USER_ID'],
        'phone'=>$itSupportTasks->getArrForCreateTask()['CONTACT_PHONE_VALUE'],
        // Танец с бубном из за непонятного поведения axios. Для передачи файлов создаю виртуальную форму ( иначе файлы не передаются ), в форму добавляю массив строк, но в $_POST на сервер прилетает строка
        'rolesCompanies'=>( gettype( $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_STIM_COMPANY_VALUE'] ) == 'string' ) ? 
                               explode( ",", $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_STIM_COMPANY_VALUE']) :
                               $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_STIM_COMPANY_VALUE'],
        'roles'=>( gettype( $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_USER_ROLE_VALUE'] ) == 'string' ) ?
                        explode( ",", $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_USER_ROLE_VALUE'] ) :
                        $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_USER_ROLE_VALUE'],
        // ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------                
        'approvingUser'=>$subjectRequest->getApprovingUserIdId( $selectedSubjectId ),
        'createdBy'=>$itSupportTasks->getCreatedBy(),
        'auditors'=>$itSupportTasks->getAuditors(),
        'tags'=>$itSupportTasks->getArrTags(),
        'groupId'=>$itSupportTasks->getGroupId(),
        'responsible'=>$itSupportTasks->getResponsible(),
        'title'=>$itSupportTasks->getTitle(),
        'description'=>$itSupportTasks->getNoteText(),
        'priority'=>$itSupportTasks->getPriority(),
    ];
    $bpError = $bpObg->startBP( $bpId, $paramArr );
    echo json_encode( $paramArr );
}
else echo json_encode( $itSupportTasks->add() );
