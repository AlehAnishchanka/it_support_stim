<?php
namespace local\stim\it_support_stim\classes;
class DataElementBitrixList extends DataElementAbstract
{
    protected $listId;
    protected $arrFromIblockList;

    public function __construct( $listId )
    {
        $this->listId = $listId;
        $this->arrFromIblockList = $this->getArrValueOfProperties();
    }

    public function  getData()
    {
        return $this->arrFromIblockList;
    }

    public function getArrRecordsList ()
    {
        $arrIdRecList = [];
        $recList = \CIBlockElement::GetList([], ['IBLOCK_ID'=> $this->listId ]);
        while($rec = $recList->Fetch()) $arrIdRecList[$rec['ID']] = [ "ID"=>$rec['ID'], 'NAME'=>$rec['NAME']];
        return $arrIdRecList;
    }

    public function getArrPropretiesNameList()
    {
        $arrPropertyList = [];
        $res = \CIBlockProperty::GetList( Array(), Array( "IBLOCK_ID" => $this->listId ));
        while ( $res_arr = $res->Fetch() ) $arrPropertyList[ $res_arr['ID'] ] = [ 'ID'=>$res_arr['ID'], 'CODE'=>$res_arr['CODE'], 'NAME'=>$res_arr['NAME']];
        return $arrPropertyList;
    }

    public function getArrValueOfProperties()
    {
        $filter = ['IBLOCK_ID' => $this->listId ];
        $arrElements = $this->getArrRecordsList();

        $arrElementsForProperties = $arrElements;

        \CIBlockElement::GetPropertyValuesArray( $arrElementsForProperties, $this->listId, $filter);

        foreach ( $arrElements as $key=>$element )
            foreach ( $arrElementsForProperties[ $key ] as $codeProperty=>$elementProperty )
                $arrElements[ $key ][ $codeProperty] = [ 'ID'=>$elementProperty['ID'], 'NAME'=>$elementProperty['NAME'], 'VALUE'=>$elementProperty['VALUE'] ];

        return $arrElements;
    }

}