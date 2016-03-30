<?php

/**
 * ITokenizer class file.
 *
 * @author lx <lx@tongda2000.com>
 */

interface ITokenizer {
    public function setText($text);
    public function getTokens($attr);
    public function getTops($limit, $filter);
    public function getTopWords($limit, $filter);
}
?>
