<?php

require_once 'exclusivegroups.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function exclusivegroups_civicrm_config(&$config) {
  _exclusivegroups_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function exclusivegroups_civicrm_xmlMenu(&$files) {
  _exclusivegroups_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function exclusivegroups_civicrm_install() {
  return _exclusivegroups_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function exclusivegroups_civicrm_uninstall() {
  return _exclusivegroups_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function exclusivegroups_civicrm_enable() {
  /*
   * create custom group (done here to enable support for versions before 4.4
   * when custom-xml can be generated with civix)
   */
  $exclusiveGroupsConfig = CRM_Exclusivegroups_Config::singleton();
  if (_exclusivegroups_check_custom_group($exclusiveGroupsConfig->customGroupName) == FALSE) {
    _exclusivegroups_create_custom_group($exclusiveGroupsConfig->customGroupName);
  }
  return _exclusivegroups_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function exclusivegroups_civicrm_disable() {
  return _exclusivegroups_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function exclusivegroups_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _exclusivegroups_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function exclusivegroups_civicrm_managed(&$entities) {
  return _exclusivegroups_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function exclusivegroups_civicrm_caseTypes(&$caseTypes) {
  _exclusivegroups_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function exclusivegroups_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _exclusivegroups_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
/**
 * Function to check if there is already a custom group
 */
function _exclusivegroups_check_custom_group($customGroupName) {
  $groupCount = civicrm_api3('CustomGroup', 'Getcount', array('name' => $customGroupName));
  switch ($groupCount) {
    case 0:
      $returnValue = FALSE;
      break;
    case 1:
      $session = CRM_Core_Session::singleton();
      $session->setStatus('CustomGroup with name excl_groups found. The enable process '
      . 'assumes this is the correct one, please check and take action if required'
      , 'Custom group already present', 'alert');
      $returnValue = TRUE;
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $customGroupName));
      $exclusiveGroupsConfig = CRM_Exclusivegroups_Config::singleton();
      $exclusiveGroupsConfig->customGroupId = $customGroup['id'];
      $exclusiveGroupsConfig->customGroupTable = $customGroup['table_name'];
      break;
    default:
      CRM_Core_Error::fatal(ts('More than 1 CustomGroups with name excl_groups found. '
        . 'Please make sure there is only one before you enable this extension'));
      break;
  }
  return $returnValue;
}
/**
 * Function to create custom group
 */
function _exclusivegroups_create_custom_group($customGroupName) {
  try {
    $customGroupParms = _exclusivegroups_set_custom_group_params($customGroupName);
    $createdCustomGroup = civicrm_api3('CustomGroup', 'Create', $customGroupParms);
    $exclusiveGroupsConfig = CRM_Exclusivegroups_Config::singleton();
    $exclusiveGroupsConfig->customGroupId = $createdCustomGroup['id'];
    $exclusiveGroupsConfig->customGroupTable = $createdCustomGroup['table_name'];
    _exclusivegroups_create_custom_field($exclusiveGroupsConfig->customGroupId);
  } catch (CiviCRM_API3_Exception $ex) {
    CRM_Core_Error::fatal(ts('Could not create required custom group with name '
      .$customGroupName.', Error from API CustomGroup Create : '.$ex->getMessage()));
  }
}
/**
 * Function to set custom group params
 */
function _exclusivegroups_set_custom_group_params($customGroupName) {
  $result = array(
    'name'        =>  $customGroupName,
    'title'       =>  'Exclusive Groups',
    'extends'     =>  'Group',
    'is_active'   =>  1,
    'table_name'  =>  'civicrm_value_exclusivegroups',
    'created_date'=>  date('Ymd')
  );
  return $result;
}
/**
 * Function to create required custom field
 */
function _exclusivegroups_create_custom_field($customGroupId) {
    $exclusiveConfig = CRM_Exclusivegroups_Config::singleton();
  try {
    $customFieldParams = _exclusivegroups_set_custom_field_params();
    $createdCustomField = civicrm_api3('CustomField', 'Create', $customFieldParams);
    $exclusiveConfig->customFieldExclId = $createdCustomField['id'];
    $exclusiveConfig->customFieldExclColumn = $createdCustomField['column_name'];
  } catch (CiviCRM_API3_Exception $ex) {
    CRM_Core_Error::fatal(ts('Could not create required custom field with name'
      .$exclusiveConfig->customFieldExclName.', error from API CustomGroup Create : '.$ex->getMessage()));
  }
}
/**
 * Function to set custom field params
 */
function _exclusivegroups_set_custom_field_params() {
  $exclusiveConfig = CRM_Exclusivegroups_Config::singleton();
  $helpText = ts('If you set this field to Yes a contact can only be member of ONE '
    . 'of the child groups of this group. For example, if you create a group Donors and '
    . 'then add Children Groups Regular Donors, Special Donors, Major Donors and '
    . 'Incidental Donors a contact in CiviCRM can only be a member of one of these groups.');
  $result = array(
    'name'            =>  $exclusiveConfig->customFieldExclName,
    'label'           =>  ts('Exclusive Children?'),
    'custom_group_id' =>  $exclusiveConfig->customGroupId,
    'data_type'       =>  'Boolean',
    'html_type'       =>  'Radio',
    'is_required'     =>  '1',
    'is_searchable'   =>  '1',
    'default_value'   =>  '0',
    'is_active'       =>  '1',
    'column_name'     =>  'exclusive_children',
    'pre_help'        =>  $helpText);
  return $result;
}