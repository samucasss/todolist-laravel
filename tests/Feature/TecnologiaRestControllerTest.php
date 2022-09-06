<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tecnologia;

class TecnologiaRestControllerTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        Tecnologia::query()->delete();
    }

    public function test_find_all_ok() {
        $this->saveTecnologias();

        $response = $this->get('api/tecnologias');

        $elementCount = count($response->json());
        printf('elementCount: '.$elementCount);
        
        $response->assertOk();
        $this->assertEquals(2, $elementCount);
    }

    private function saveTecnologias(): void {
        $tecnologia1 = new Tecnologia;
        $tecnologia1->nome = 'Node JS';
        $tecnologia1->tipo = 'Backend';

        $tecnologia1->save();

        $tecnologia2 = new Tecnologia;
        $tecnologia2->nome = 'Angular';
        $tecnologia2->tipo = 'Frontend';

        $tecnologia2->save();
    }
}
