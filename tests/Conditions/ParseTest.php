<?php

namespace Tests\Connections;

use App\Conditions\Parse;
use Tests\TestCase;

class ParseTest extends TestCase
{
    private $_app;

    public function setUp()
    {
        $this->_app = $this->createApplication();
    }

    public function testExists()
    {
        $this->assertTrue(Parse::exists('Toi un jour je te crame ta famille, toi.', 'crame'));
        $this->assertFalse(Parse::exists('J\'voudrais pas faire ma raclette, mais la soirée s\'annonce pas super.', 'Raclette'));
    }

    public function testExistsInsensitive()
    {
        $this->assertTrue(Parse::exists_insensitive('Et toc ! Remonte ton slibard, Lothard !', 'remONte'));
        $this->assertTrue(Parse::exists_insensitive('Et toc ! Remonte ton slibard, Lothard !', 'Remonte'));
        $this->assertFalse(Parse::exists_insensitive('Non, vous, vous vous maravez. Quand on a pas de technique, il faut y aller à la zob.', 'Raclette'));
    }

    public function testNotExists()
    {
        $this->assertTrue(Parse::not_exists('Sloubi 1, sloubi 2, sloubi 3, sloubi 4, sloubi 5', 'cul de chouette'));
        $this->assertFalse(Parse::not_exists('sloubi 324, sloubi 325', 'sloubi'));
    }

    public function testNotExistsInsensitive()
    {
        $this->assertTrue(Parse::not_exists_insensitive('C\'est pas moi qu\'explique mal, c\'est les autres qui sont cons', 'taverne'));
        $this->assertTrue(Parse::not_exists_insensitive('Vous, vous avez une idée derrière la main, j\'en mettrais ma tête au feu', 'TAverne'));
        $this->assertFalse(Parse::not_exists_insensitive('Si la mémoire est à la tête ce que le passé, peut-on y accéder à six ? Oui, non, zbradaraldjan ?', 'zbraDARAldjan'));
    }
}
