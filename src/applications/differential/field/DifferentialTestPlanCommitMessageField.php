<?php

final class DifferentialTestPlanCommitMessageField
  extends DifferentialCommitMessageField {

  const FIELDKEY = 'testPlan';

  public function getFieldName() {
    return pht('Test Plan');
  }

  public function getFieldOrder() {
    return 3000;
  }

  public function getFieldAliases() {
    return array(
      'Testplan',
      'Tested',
      'Tests',
    );
  }

  public static function getDefaultTitle() {
    return pht('<<Please provide a detailed test plan. Add explicit steps when possible>>');
  }

  public function isFieldEnabled() {
    return $this->isCustomFieldEnabled('differential:test-plan');
  }

  public function validateFieldValue($value) {
    $is_required = PhabricatorEnv::getEnvConfig(
      'differential.require-test-plan-field');

    if ($is_required && !strlen($value)) {
      $this->raiseValidationException(
        pht(
          'You must provide a test plan. Describe the actions you performed '.
          'to verify the behavior of this change.'));
    }

    if($is_required && stripos($value, self::getDefaultTitle()) !== false) {
      $this->raiseParseException(
        pht(
          'You must replace the default test plan line with a legitimate '.
          'test plan which describes how to test the changes you are making.'));
    }
  }

  public function readFieldValueFromObject(DifferentialRevision $revision) {
    $value = $revision->getTestPlan();
    
    if (!strlen($value)) {
      return "\n".self::getDefaultTitle();
    }

    return $value;
  }

  public function getFieldTransactions($value) {
    return array(
      array(
        'type' => DifferentialRevisionTestPlanTransaction::EDITKEY,
        'value' => $value,
      ),
    );
  }

}
