<?php
/**
 *
 * @author F.L
 */
interface IOrgSync {
    public function add($data);
    public function addUserToOrg($orgData, $userData);
    public function deleteOrgPermanent($data);
    public function markDeleteOrg($data);
    public function removeUserFromOrg($orgData, $userData);
    public function updateOrg($data);
    public function updateAllOrg($data);
}
