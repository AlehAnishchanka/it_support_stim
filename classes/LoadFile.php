<?php
namespace local\stim\it_support_stim\classes;

class LoadFile
{
    private $userId;
    private $file = [];

    public function setUserId( $userId )
    {
        $this->userId = $userId;
    }

    public function getUserStorage()
    {
        global $USER;
        $this->userId = $this->userId ?? $USER->GetID();
        $storage = \Bitrix\Disk\Driver::getInstance()->getStorageByUserId( $this->userId );
        return $storage;
    }

    public function getUserFolder()
    {
        $folder = $this->getUserStorage()->getFolderForUploadedFiles();
        return $folder;
    }

    public function removeFileFromPostRequestToUserFolderStorage( $arr_file )
    {
        global $USER;
        $currentUser = $this->userId ?: $USER->GetID();
        $file = $this->getUserFolder()->uploadFile( $arr_file, array(
            'NAME' => $arr_file["name"],
            'CREATED_BY' => $currentUser
        ), array(), true );
        $this->file[] = $file;
        $FILE_ID = $file->getId();
        return $FILE_ID;
        //if( gettype( $FILE_ID == "string")) return (integer) preg_replace('[\D]', '', $FILE_ID ); // почему-то библиотечный $file->getId() позвращает строку "int(1597443)\nnull"
        //else return $FILE_ID;
    }

    public function getArrFilesId()
    {
        $arrFilesId = [];
        foreach( $this->file as $file ) $arrFilesId[] = $file->getId();
        return $arrFilesId;
    }

    public function delAllRecivedFiles() 
    {
        foreach( $this->getArrFilesId as $fileId ) \CFile::Delete( $fileId );
    }

    public function getArrReceivedFiles()
    {
        return $this->file;
    }

    public function attachFileToTask( $taskId, $arrFilesId )
  {
        global $USER;
        $currentUser = $this->userId ?: $USER->GetID();
        $oTaskItem = new \CTaskItem( $taskId, $currentUser );
        try
        {
            $rs = $oTaskItem->Update(array("UF_TASK_WEBDAV_FILES" => $arrFilesId ));
        }
        catch( \Exception $e)
        {
            print("TASK_FILES_LINKED_ERROR");
        }
    }

    public function getArrFilesLinks()
    {
        $urlManager = \Bitrix\Disk\Driver::getInstance()->getUrlManager();
        $arrFilesLink = [];
        foreach( $this->file as $file) {
            $extLink = $file->addExternalLink( 
                array( 
                    'CREATED_BY' => 1, 
                    'TYPE' => \Bitrix\Disk\Internals\ExternalLinkTable::TYPE_MANUAL, 
                ) 
            ); 
            $extLinkUrl = $urlManager->getShortUrlExternalLink( 
                array( 
                    'hash' => $extLink->getHash(), 
                    'action' => 'default', 
                ), 
                true 
            );
            $arrFilesLink[] = $extLinkUrl;
        }

        //foreach( $this->file as $file) $arrFilesLink[] = $urlManager->getPathFileDetail($file);
        return $arrFilesLink;
    }

    public function grtStrFilesLink()
    {
        $strLinks = "";
        foreach( $this->getArrFilesLinks() as $link ) $strLinks .= $link . "\n";
        return $strLinks;
    }
}