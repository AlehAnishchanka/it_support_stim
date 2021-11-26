<?php
/*
API работает двумя способами. 
    1) При передаче в api параметров CLASS и OPERATION возвращается json отдельного справочника.
       Используем при разработке нового справочника. Легко разрабатывать и тестировать.
    2) Кокда работа со справочником полностью отработана добавляем работу со справочником в метод crteateArrWithAllData.
       На frontend данные возвращаются одним json объекьом дабы избежать кучи обращений к API для передачи данных.
*/
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
spl_autoload_register( function ( $className ) {
    $className = str_replace( "\\", "/", $className);
    $file = $_SERVER['DOCUMENT_ROOT'] . "/{$className}.php";
    if( file_exists( $file ) )
        require_once $file;
});
use local\stim\it_support_stim\classes\{ ItSupportSection, ItSupportSubject, ItSupportAppealType, ItSupportLabel, ItSupportTooltip, ItSupportStimCompanies, ItSupportUsersWithRights, itSupportRoleGrpups, 
                                         itSupportRoles, ItSupportUsers, ApiMistake, ItSupportDepartments, ItSupportPositions };

if( isset( $_POST['CLASS'] ) )  $_REQUEST = json_decode(file_get_contents('php://input'), true);

if( !isset( $_REQUEST['CLASS'] ) ) {
    $object = new ApiMistake ( "api сервису не передан параметр CLASS" );
    $_REQUEST['OPERATION'] = 'CLASS_MISTAKE';
} elseif( $_REQUEST['CLASS'] == "ALL") {
    $_REQUEST['OPERATION'] = "ALL";
} else {
    $class = "local\stim\it_support_stim\classes\\" . $_REQUEST['CLASS'];
    if( !class_exists( $class ) ) {
        $object = new ApiMistake ( "api сервису передан не верный параметр CLASS" );
        $_REQUEST['OPERATION'] = 'CLASS_MISTAKE';
    }
    else $object = new $class();
}

//if( !class_exists( $class ) ) $_REQUEST['OPERATION'] = 'WRONG';

switch ( $_REQUEST['OPERATION'] ) {
    case 'ALL':
        $result = crteateArrWithAllData();
        break;
    case 'CLASS_MISTAKE':
        $result = $object->getMistake();
        break;
    case 'GETDATA':
        $result = $object->getData();
        break;
    case 'GET_DATA_FOR_SELECT_VUE':
        $result = $object->getDataForSelectVue();
        break;
    case 'GET_SECTION_AND_SUBJECT':
        $result = $object->getSectionAndSubjectForSelecVue( new  ItSupportSubject() );
        break;
    case 'GET_FILTERED_USERS_BY_DEP':
        $result = $object->getArrUsersByDepartamentIdForSelectVue();
        break;
    default:
        $result = ( new ApiMistake("для класса, переданного api, переданной операции не существует" ))::getMistake();
}

function crteateArrWithAllData()
{
    global $USER;
    $result['CURRENT_USER_ID'] = CUser::GetID();
    $result['CURRENT_USER_PHONE'] = \CUser::GetByID( $result['CURRENT_USER_ID'] )->Fetch()['UF_PHONE_INNER'];
    $result['LANGUAGE_ID'] = LANGUAGE_ID;
    $result['SECTION_AND_SUBJECT'] = ( new ItSupportSection() )->getSectionAndSubjectForSelecVue( new  ItSupportSubject() );
    $result['APPEAL_TYPE'] = ( new ItSupportAppealType() )->getDataForSelectVue();
    $result['FORMS_LABEL'] = ( new ItSupportLabel() )->getDataForSelectVue();
    $result['TYPE_TOOLTIP'] = ( new ItSupportTooltip() )->getDataForSelectVue();
    $result['STIM_COMPANIES'] = ( new ItSupportStimCompanies() )->getDataForSelectVue();
    $result['ROLE_GROUPS'] = ( new itSupportRoleGrpups() )->getDataForSelectVue();
    $result['ROLES'] = ( new itSupportRoles() )->getDataForSelectVue();
    $result['FILTERED_USERS_BY_DEP'] = ( new ItSupportUsersWithRights() )->getDataForSelectVue();
    $result['USERS'] = ( new ItSupportUsers() )->getDataForSelectVue();
    $result['BITRIX_DEPARTMENTS'] = ( new ItSupportDepartments() )->getDataForSelectVue();
    $result['BITRIX_POSITIONS'] = ( new ItSupportPositions() )->getDataForSelectVue();
    return $result;
}

echo json_encode( ["RESULT" => $result ], JSON_UNESCAPED_UNICODE );