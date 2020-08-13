<?php
use CRM_EesterCleanup_ExtensionUtil as E;

/**
 * Contract.Cleanup API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_contract_cleanup_spec(&$spec) {
  $spec['verbose'] = [
    'type' => CRM_Utils_Type::T_BOOLEAN,
    'title' => 'Verbose',
    'description' => 'Sent output to console or return with the API'
  ];
}

/**
 * Contract.Cleanup API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_contract_cleanup($params) {
    if(isset($params['verbose']) && $params['verbose']){
      $verbose = true;
    } else {
      $verbose = false;
    }
    $cleanup = new CRM_EesterCleanup_Cleanup($verbose);
    $result = $cleanup->process();
    return civicrm_api3_create_success($result, $params, 'Contract', 'Cleanup');
}
