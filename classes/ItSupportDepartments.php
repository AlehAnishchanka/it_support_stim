<?php
namespace local\stim\it_support_stim\classes;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
spl_autoload_register( function ( $className ) {
    $className = str_replace( "\\", "/", $className);
    $file = $_SERVER['DOCUMENT_ROOT'] . "/{$className}.php";
    if( file_exists( $file ) )
        require_once $file;
});

use local\stim\libraries\library_departments_tree\{ DepartmentTree, AbstractTree, ContainerTree, DataProvider, Department, ContainerContent };

class ItSupportDepartments
{
    const DEP_TREE_SQL_RTEQUEST = "select ID, NAME, IBLOCK_SECTION_ID from b_iblock_section where IBLOCK_ID=5";
    private $depTree;

    public function __construct()
    {
        $this->depTree = new DepartmentTree( self::DEP_TREE_SQL_RTEQUEST );
    }

    public function getDataForSelectVue()
    {
        return $this->depTree->getSelectDepartmentsForVueJs();
    }
//$depTree = new DepartmentTree( self::DEP_TREE_SQL_RTEQUEST );
}