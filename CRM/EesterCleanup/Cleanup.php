<?php


class CRM_EesterCleanup_Cleanup {

  /* print feedback to output / otherwise buffer and store in string */
  var $verbose = false;
  var $output  = [];
  var $activityTypes = [
    "Contract_Signed",
    "Contract_Paused",
    "Contract_Resumed",
    "Contract_Updated",
    "Contract_Cancelled",
    "Contract_Revived"
  ];

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

  private function deleteCustomGroup($name) {

    try {
      $groupId = civicrm_api3('CustomGroup', 'getvalue', [
        'return' => "id",
        'name' => $name,
      ]);
      civicrm_api3('CustomGroup', 'delete', ['id' => $groupId]);
      $this->output("Found $name, and now its gone");
    } catch (Exception $ex) {
      $this->output("Not deleting ${name} - not found");
    }
  }

  private function deleteOptionValue($groupName,$name){
    try{$optionId = civicrm_api3('OptionValue', 'getvalue',[
      'return' => 'id',
      'name' => $name,
      'option_group_id' => $groupName
    ]);
    $this->output("Deleting $name in $groupName");
      civicrm_api3('OptionValue', 'delete', ['id' => $optionId]);
    } catch (Exception $ex) {
      $this->output("Not deleting OptionValue ${name} - not found");
    }
  }

  private function deleteOptionGroup($groupName) {
    try {
      $optionId = civicrm_api3('OptionGroup', 'getvalue', [
        'return' => 'id',
        'name' => $groupName,
      ]);
      $this->output("Deleting OptionGroup $groupName");
      civicrm_api3('OptionGroup', 'delete', ['id' => $optionId]);
    } catch (Exception $ex) {
      $this->output("Not deleting OptionGroup $groupName- not found");
    }
  }

  public function process(){
    $this->output('---- Custom Groups ----');
    $this->output('Contract Cancellation');
    $this->deleteCustomGroup('contract_cancellation');
    $this->output('Contract Updates');
    $this->deleteCustomGroup('contract_updates');
    $this->output('Membership Cancellation');
    $this->deleteCustomGroup('membership_cancellation');
    $this->output('Membership Payment');
    $this->deleteCustomGroup('membership_payment');
    $this->output('Membership General');
    $this->deleteCustomGroup('membership_general');
    $this->output('---Other Stuff----');
    $this->output('Activities for Contracts');

    try{$activities = civicrm_api3('Activity', 'get', [
      'activity_type_id' => ['IN' => $this->activityTypes],
    ])['values'];

    foreach ($activities as $activity) {
      $this->output("Deleting activity ${activity['id']}");
      civicrm_api3('Activity', 'delete', ['id' => $activity['id']]);
    }} catch (Exception $ex){
      $this->output('Skipping activity delete - not possible the activity status are gone already');
    }

    $this->output('Change Membership Status');
    CRM_Core_DAO::executeQuery('delete from civicrm_membership_status where name = %1',[
      1 => ['Pause','String']
    ]);
    $this->output('---- Option Groups---');
    $this->output('Activity Types (Option Group)');
    foreach($this->activityTypes as $activityType) {
       $this->deleteOptionValue('activity_type',$activityType);
    }
    $this->output('Activity Status');
    foreach(['Failed','Needs Review'] as $activityStatus){
      $this->deleteOptionValue('activity_status',$activityStatus);
    }
    $this->output('Contract Cancel Reason');
    $this->deleteOptionGroup('contract_cancel_reason');
    $this->output('Remove Payment Frequency'); //
    $this->deleteOptionGroup('payment_frequency');
    $this->output('Remove Shirt Size');
    $this->deleteOptionGroup('shirt_size');
    $this->output('Remove Shirt Type');
    $this->deleteOptionGroup('shirt_type');
    $this->output('---- Data - Model ---');
    $this->output('Drop the contract payment table');
    CRM_Core_DAO::executeQuery('drop table if exists civicrm_contract_payment');
    return $this->output;
  }



}
