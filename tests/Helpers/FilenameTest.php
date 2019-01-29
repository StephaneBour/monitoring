<?php

namespace Tests\Helpers;

use App\Helpers\FilenameHelper;
use Tests\TestCase;

class FilenameTest extends TestCase
{
    public function testConvertPast()
    {
        $result = 'repoussoir_2018-10-10';
        $date = new \DateTime('2018-10-10');
        $this->assertEquals($result, FilenameHelper::dynamic('repoussoir_\Y-\m-\d', $date));
    }

    public function testConvertToday()
    {
        $result = 'repoussoir_' . date('Y-m-d');
        $this->assertEquals($result, FilenameHelper::dynamic('repoussoir_\Y-\m-\d'));
    }
}
