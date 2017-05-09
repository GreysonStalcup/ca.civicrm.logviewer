<?php

class CRM_Logviewer_Page_LogViewer extends CRM_Core_Page {

  public function run() {
    $this->assign('currentTime', date('Y-m-d H:i:s'));
    $config = CRM_Core_Config::singleton();
    $file_log = CRM_Core_Error::createDebugLogger();
    // print_r($file_log); die();
    $logFileName = $file_log->_filename;
    $logFileFormat = $file_log->_lineFormat;
    $file_log->close();
    $this->assign('fileName', $logFileName);
    $handle = fopen($logFileName,'r') or die ('File opening failed');
    $entries = array();
    $line = 0;
    while (!feof($handle)) {
      $line++;
      $dd = fgets($handle);
      if (strlen($dd) >= 15 && (' ' != $dd[0])) {
        $date = substr($dd,0,15);
        if (preg_match("/^[A-Z][a-z]{2} [0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/",$date)) {
          $entry_url = CRM_Utils_System::url('civicrm/admin/logviewer/logentry', $query = 'lineNumber='.$line);
          $entries[$line] = array('lineNumber' => '<a href="'.$entry_url.'">'.$line.'</a>', 'dateTime' => $date, 'message' => substr($dd,16));
        }
        //else die($date);
      }      
    }
    fclose($handle);
    krsort($entries);
    $this->assign('logEntries', $entries);
    parent::run();
  }

}
