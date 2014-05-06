<?php
/**
 * Class following Singleton pattern for specific extension configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 6 May 2014
 */
class CRM_Exclusivegroups_Config {
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
  public $customGroupId = NULL;
  public $customGroupTable = NULL;
  public $customGroupName = NULL;
  public $customFieldExclId = NULL;
  public $customFieldExclColumn = NULL;
  public $customFieldExclName = NULL;
  /**
   * Constructor function
   */
  function __construct() {
    $this->setCustomGroupName('excl_groups');
    $this->setCustomFieldExclName('excl_children');
    $this->getCustomGroup();
    $this->getCustomField();
  }
  /**
   * Function to get custom group
   */
  private function getCustomGroup() {
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->customGroupName));
      if (isset($customGroup['id'])) {
        $this->setCustomGroupId($customGroup['id']);
      } else {
        $this->setCustomGroupId(0);
      }
      if (isset($customGroup['table_name'])) {
        $this->setCustomGroupTable($customGroup['table_name']);
      } else {
        $this->setCustomGroupTable('');
      }
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setCustomGroupId(0);
      $this->setCustomGroupTable('');
    }
  }
  /**
   * Function to get custom field
   */
  private function getCustomField() {
    try {
      $customField = civicrm_api3('CustomField', 'Getsingle', 
        array('custom_group_id' => $this->customGroupId, 'name' => $this->customFieldExclName));
      if (isset($customField['id'])) {
        $this->setCustomFieldExclId($customField['id']);
      } else {
        $this->setCustomFieldExclId(0);
      }
      if (isset($customField['column_name'])) {
        $this->setCustomFieldExclColumn($customField['column_name']);
      } else {
        $this->setCustomFieldExclColumn('');
      }
    } catch (CiviCRM_API3_Exception $ex) {
      $this->setCustomFieldExclId(0);
      $this->setCustomFieldExclColumn('');
    }
  }
  /**
   * Function to return singleton object
   * 
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Exclusivegroups_Config();
    }
    return self::$_singleton;
  }
  /** 
   * Function to set customGroupName
   */
  private function setCustomGroupName($customGroupName) {
    $this->customGroupName = $customGroupName;
  }
  /** 
   * Function to set customGroupId
   */
  private function setCustomGroupId($customGroupId) {
    $this->customGroupId = $customGroupId;
  }
  /**
   * Function to set customGroupTable
   */
  private function setCustomGroupTable($customGroupTable) {
    $this->customGroupTable = $customGroupTable;
  }
  /** 
   * Function to set customFieldName
   */
  private function setCustomFieldExclName($customFieldExclName) {
    $this->customFieldExclNameName = $customFieldExclName;
  }
  /** 
   * Function to set customFieldId
   */
  private function setCustomFieldExclId($customFieldExclId) {
    $this->customFieldExclId = $customFieldExclId;
  }
  /**
   * Function to set cstuomFieldColumn
   */
  private function setCustomFieldExclColumn($customFieldExclColumn) {
    $this->customFieldExclColumn = $customFieldExclColumn;
  }
}
