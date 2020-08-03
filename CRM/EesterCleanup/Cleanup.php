<?php


class CRM_EesterCleanup_Cleanup {

  /* print feedback to output / otherwise buffer and store in string */
  var $verbose = false;
  var $output  = [];

  /**
   * CRM_EesterCleanup_Cleanup constructor.
   *
   * @param bool $verbose
   */
  public function __construct($verbose = false) {
    $this->verbose = $verbose;
  }

  private function output($line){
    if($this->verbose){
      echo $line." \n";
    } else {
      $this->output[]=$line;
    }
  }

  public function process(){
    $this->output('---- Custom Groups ----');
    $this->output('Contract Cancellation');
    $this->output('Contract Updates');
    $this->output('Membership Cancellation');
    $this->output('Membership Payment');
    $this->output('---Other Stuff----');
    $this->output('Activities for Contracts');
    $this->output('Change Membership Status');
    $this->output('---- Option Groups---');
    $this->output('Activity Types (Option Group)');
    $this->output('Activity Status');
    $this->output('Contract Cancel Reason');
    $this->output('Remove Payment Frequency');
    $this->output('Remove Shirt Size');
    $this->output('Remove Shirt Type');
    $this->output('---- Data - Model ---');
    $this->output('Drop the output table');
    return $this->output;
  }



}
