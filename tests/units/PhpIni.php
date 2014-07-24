<?php

namespace Pickle\tests\units;

use atoum;
use Pickle\tests;

class PhpIni extends atoum
{
    protected function getPhpDetectionMock($path)
    {
        $php =  new \mock\Pickle\PhpDetection;

        $this->calling($php)->getPhpIniDir = function() use ($path) {
            return $path;
        };

        return $php;
    }

    public function test__construct()
    {
        $php = $this->getPhpDetectionMock("");
        $this->assert
                ->exception(function() use($php) {
                        new \Pickle\PhpIni($php);
                    });

        $php = $this->getPhpDetectionMock(FIXTURES_DIR . DIRECTORY_SEPARATOR . "ini" . DIRECTORY_SEPARATOR . "php.ini.empty");
        $this
            ->object(new \Pickle\PhpIni($php))
                ->isInstanceOf("\Pickle\PhpIni");
    }

    public function testupdatePickleSection_empty()
    {

        /* empty file */
        $f = FIXTURES_DIR . DIRECTORY_SEPARATOR . "ini" . DIRECTORY_SEPARATOR . "php.ini.empty";
        $this
            ->string(file_get_contents($f))
                ->isEmpty();
        $this->do_testupdatePickleSection($f);
    }

    public function testupdatePickleSection_nofooter()
    {
        /* missing pickle section footer*/
        $f = FIXTURES_DIR . DIRECTORY_SEPARATOR . "ini" . DIRECTORY_SEPARATOR . "php.ini.only.sect.begin";
        $this->do_testupdatePickleSection($f);
    }

    public function testupdatePickleSection_simple()
    {
        /* simple file with correct pickle section */
        $f =FIXTURES_DIR . DIRECTORY_SEPARATOR . "ini" . DIRECTORY_SEPARATOR . "php.ini.simple";
        $this->do_testupdatePickleSection($f);
    }

    protected function do_testupdatePickleSection($orig)
    {
        $fl = "$orig.test";
        $fl_exp = "$orig.exp";
        copy($orig, $fl);

        $php = $this->getPhpDetectionMock($fl);

        $ini = new \Pickle\PhpIni($php);
        $ini->updatePickleSection(array("php_pumpkin.dll", "php_hello.dll"));

        $this
            ->string(file_get_contents($fl))
                ->isEqualToContentsOfFile($fl_exp);

        unlink($fl);
    }
    
}
