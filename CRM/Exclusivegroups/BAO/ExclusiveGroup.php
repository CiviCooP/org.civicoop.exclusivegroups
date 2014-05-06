<?php
/**
 * Class ExclusiveGroup for exclusive Groups business rules
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 6 May 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to CiviCRM under AGPL-3.0.
 */
class CRM_Exclusivegroups_BAO_ExclusiveGroup {
  /**
   * Function to check if the group is exclusive
   * 
   * @params int $groupId
   * @return boolean
   * @access public
   * @static
   */
  public static function checkGroupExclusive($groupId) {
    if (empty($groupId)) {
      return FALSE;
    }
  }
  /**
   * Function to check if a contact is already member of a group somewhere in
   * exclusive tree
   * 
   * @params int $contactId
   * @params int $groupId
   * @return boolean
   * @access public
   * @static
   */
  public static function checkContactAlreadyMember($contactId, $groupId) {
    if (empty($contactId) || empty($groupId)) {
      return FALSE;
    }
  }
}

