<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
spl_autoload_register(function ($className) {
    $className = str_replace("\\", "/", $className);
    $file = $_SERVER['DOCUMENT_ROOT'] . "/{$className}.php";
    if (file_exists($file)) {
        require_once $file;
    }
});
use local\stim\it_support_stim\classes\{ ItSupportSection, ItSupportSubject, ItSupportAppealType, ItSupportLabel, ItSupportTooltip, ItSupportStimCompanies,
                                         itSupportRoleGrpups, itSupportRoles, ItSupportUsersWithRights, ItSupportUsers, ItSupportTasks, ItSupportDepartments, ItSupportPositions };
//DepartmentTree, Department, DataElementBitrixList, DataElementAbstract, AbstractTree, ContainerTree, DataProvider, ContainerContent};
use local\stim\libraries\library_departments_tree\{ DepartmentTree, AbstractTree, ContainerTree, DataProvider, Department, ContainerContent };
use local\stim\libraries\bp\{ BpStim };
//use local\stim\it_support_stim\classes\ItSupportTasks;

//$sn = new ItSupportSubject();
//print_r( $sn->getApprovingUserIdId( 184743 ) );

//$sn = new ItSupportUsers();
//print_r( $sn->getArrUsersByDepartamentId( 515 ));
//print_r( $sn->getSectionAndSubjectForSelecVue( new ItSupportSubject() ) );

//$sn = new itSupportRoles();
//print_r( $sn->getSubjectByRecordId( 184898 ));
/*
$problem = new SupportProblems();
$problemId = 184103;
print_r( $problem->getResponsibleByProblemId( $problemId ));
echo "<br>";
print_r( $problem->getArrAccomplicesByProblemId( $problemId ));
*/
//$depTree = new DepartmentTree("select ID, NAME, IBLOCK_SECTION_ID from b_iblock_section where IBLOCK_ID=5");
//print_r($depTree->getArrUsersFromDepartmentPoint(515));

//$users = new ItSupportUsersWithRights();
//print_r($users->getDataForSelectVue());

//$itSupportTasks = new ItSupportTasks( ['CURRENT_USER_ID'=>198, 'CURRENT_USER_PHONE'=>'2050','SUBJECT'=>[ "title"=>"1С", "selected"=>184733 ], 'TYPE_APPEAL'=>[ "title"=>"Запрос на информацию", "selected"=>184806],
//    'YOUR_QUESTION'=>[ "title"=>"Ваш  Вопрос?", "question"=>"Тестовый вопрос, который не требует ответа. Задача будет удалена."],
//    "OBJ_LINK"=>[ "title"=>"Ссылка на интересующий объект", "link"=>"https://24.stim.by/local/stim/it_support_stim/test.php"],
//] );
/* $arrForCreateTask = [
    "SUBJECT_TITLE"=>"Предмет обращения",//
    "SUBJECT_SELECTED"=>"184733",//
    "TYPE_APPEAL_TITLE"=>"Тип обращения",
    "TYPE_APPEAL_SELECTED"=>"184806",
    "YOUR_QUESTION_TITLE"=>"Ваш вопрос?",
    "YOUR_QUESTION_QUESTION"=>"Тест. Не обращать внимания. Будет удален.",
    "OBJ_LINK_TEXT"=>"Ссылка на интересующий объект",
    "OBJ_LINK_LINK"=>"https://docs.google.com/document/d/1zgwhPStPfCcOI2O7pk0L-flyG5ZeFJrGUEriEcIXisM/edit",
];
$itSupportTasks = new ItSupportTasks( $arrForCreateTask, [] );
print_r( $itSupportTasks->getDepartmentNameByID( 525 ) ); */

/* $drpTree = new ItSupportDepartments();
print_r( $drpTree->getDataForSelectVue() );
 */
//$bp = new BpStim( 156, 940 );
//var_dump( $bp );
$_POST = include_once("paramArray.php");
//print_r($_POST);
$itSupportTasks = new ItSupportTasks( $_POST, [] );
$subjectRequest = $itSupportTasks->getSubject();
$selectedSubjectId = $itSupportTasks->getArrForCreateTask()['SUBJECT_SELECTED'];
$selectedAppealId = $itSupportTasks->getArrForCreateTask()['TYPE_APPEAL_SELECTED'];
//print_r($selectedAppealId );
//print_r(ItSupportTasks::APPEAL_ACCESS);
if( $selectedAppealId == ItSupportTasks::APPEAL_ACCESS )
{
    $bpObg = new BpStim( $subjectRequest->getBpId( $selectedSubjectId ), $subjectRequest->getBpTemplateId( $selectedSubjectId ) );
    $bpId = $bpObg->createBP( "Техподдержка МДР", "SUPPORT_MDR");
    $paramArr = [   'userCompany'=> $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_COMPANY_VALUE'],
        'userDepartment'=>$itSupportTasks->getDepartmentNameByID( $itSupportTasks->getArrForCreateTask()['RULES_REQUEST_DEPARTMENT_VALUE'] ),
        'user'=>$itSupportTasks->getArrForCreateTask()['RULES_REQUEST_USER_NAME_VALUE'],
        'userSameRoles'=>$itSupportTasks->getArrForCreateTask()['RULES_REQUEST_USER_NAME_SAME_ROLE_VALUE'],
        'declarer'=> $itSupportTasks->getArrForCreateTask()['CURRENT_USER_ID'],
        'phone'=>$itSupportTasks->getArrForCreateTask()['CONTACT_PHONE_VALUE'],
        'rolesCompanies'=>$itSupportTasks->getArrForCreateTask()['RULES_REQUEST_STIM_COMPANY_VALUE'],
        'roles'=>$itSupportTasks->getArrForCreateTask()['RULES_REQUEST_USER_ROLE_VALUE'],
        'approvingUser'=>$subjectRequest->getApprovingUserIdId( $selectedSubjectId ),
        'createdBy'=>$itSupportTasks->getCreatedBy(),
        'auditors'=>$itSupportTasks->getAuditors(),
        'tags'=>$itSupportTasks->getArrTags(),
        'groupId'=>$itSupportTasks->getGroupId(),
        'responsible'=>$itSupportTasks->getResponsible(),
        'title'=>$itSupportTasks->getTitle(),
        'description'=>$itSupportTasks->getDescription(),
        'priority'=>$itSupportTasks->getPriority(),
    ];
    $bpError = $bpObg->startBP( $bpId, $paramArr );
    echo json_encode( $paramArr );
}