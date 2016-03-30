<?php
/**
 *
 * @author F.L
 */
interface IUserSync {
    public function add($data);
    public function deletePermanent($data);
    public function markDelete($data);
    public function resumeMarked($data);
    public function update($data);
    public function updatePassword($data);
    public function updateAllUser($data);
}
