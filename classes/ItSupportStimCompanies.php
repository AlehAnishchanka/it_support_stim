<?php
namespace local\stim\it_support_stim\classes;
class ItSupportStimCompanies extends DataElementBitrixList
{
    const BITRIX_LIST_SUPPORT_IT_STIM_COMPANIES = 151;
    public function __construct()
    {
        parent::__construct( self::BITRIX_LIST_SUPPORT_IT_STIM_COMPANIES );
    }

    public function getDataForSelectVue()
    {
        $dataFromNameLists = $this->getArrRecordsList ();
        $dataForSelectVue = [];
        foreach ( $dataFromNameLists as $recordId=>$recordValue ) 
        {
            $dataForSelectVue[] = [ 'value'=>$recordValue['ID'], "text"=>$recordValue['NAME'] ];
        }
        return $dataForSelectVue;
    }

    public function getSubjectByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            return $name['value'] == $recordId;
        });
        $firstKey = array_key_first( $arrFilteredNames );
        return [ 'TEKST_RU'=>$arrFilteredNames[$firstKey]['text'] ];
    }

}