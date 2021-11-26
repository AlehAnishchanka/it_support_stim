<?php
namespace local\stim\it_support_stim\classes;
class ItSupportLabel extends DataElementBitrixList
{
    const BITRIX_LIST_SUPPORT_IT_FORMS_LABEL = 149;
    public function __construct()
    {
        parent::__construct( self::BITRIX_LIST_SUPPORT_IT_FORMS_LABEL );
    }

    public function getDataForSelectVue()
    {
        $dataFromNameLists = $this->getArrRecordsList ();
        $dataForSelectVue = [];
        foreach ( $this->arrFromIblockList as $recordId=>$recordValue ) 
        {
            $dataForSelectVue[] = [ 'value'=>$recordValue['ID'], "text"=>$recordValue['TEKST_RU']['VALUE'], 'text_pl'=>$recordValue['TEKST_PL']['VALUE'], 'label_name'=>$dataFromNameLists[$recordId]['NAME'] ];
        }
        return $dataForSelectVue;
    }

    public function getSubjectByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            //print_r($name['value']==$recordId );
            return $name['value'] == $recordId;
        });
        $firstKey = array_key_first( $arrFilteredNames );
        return [ 'TEKST_RU'=>$arrFilteredNames[$firstKey]['text'], 'TEKST_PL'=>$arrFilteredNames[$firstKey]['text_pl'] ];
    }

}