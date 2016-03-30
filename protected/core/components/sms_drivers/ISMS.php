<?php
/**
 * SMS interface file.
 *
 * @author lx <lx@tongda2000.com>
 */
interface ISMS {
    public function send($mobile=array(), $content='', $time=NULL);
}
