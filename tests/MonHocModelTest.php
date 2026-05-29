<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../mvc/core/DB.php';
require_once __DIR__ . '/../mvc/models/ChuDeModel.php';

class MonHocModelTest extends TestCase
{
    private $monHocModel;

    protected function setUp(): void
    {
        $this->monHocModel = new ChuDeModel();
    }

    public function testCreate()
    {
        $mamon = '123456';
        $tenmon = 'Toán';
        $sotinchi = 3;
        $sotietlythuyet = 2;
        $sotietthuchanh = 1;

        $result = $this->monHocModel->create($mamon, $tenmon, $sotinchi, $sotietlythuyet, $sotietthuchanh);
        $this->assertTrue($result);
    }

    public function testDelete()
    {
        $mamon = '123456';

        $result = $this->monHocModel->delete($mamon);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->monHocModel = NULL;
    }
}
