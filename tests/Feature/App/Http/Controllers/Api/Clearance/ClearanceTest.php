<?php

namespace Tests\Feature\App\Http\Controllers\Api\Clearance;

use App\Model\User\User;
use App\Repositories\ClearanceRepo;
use DB;
use App\Model\FileSystem\File;
use Faker\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ClearanceTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateSection()
    {
        $faker = Factory::create();
        $data = [
            'name' => $faker->slug,
            'instruction' => $faker->words(10, true),
        ];
        $response = ClearanceRepo::createSection($data);
        $this->assertArrayHasKey('status', $response);
        $this->assertTrue($response['status']);
    }

    public function testUpdateSection()
    {
        $faker = Factory::create();
        $data = [
            'name' => $faker->slug,
            'instruction' => $faker->words(10, true),
        ];
        $section = DB::table('clearances_sections')->latest()->first();
        $response = ClearanceRepo::updateSection($data, $section->id);
        $this->assertArrayHasKey('status', $response);
        $this->assertTrue($response['status']);
    }

    public function testCreateSession()
    {
        $faker = Factory::create();
        $data = [
            'name' => $faker->slug
        ];
        
        $response = ClearanceRepo::createSession($data);
        $this->assertArrayHasKey('status', $response);
        $this->assertTrue($response['status']);
    }
    
       public function testUpdateSession()
    {
        $faker = Factory::create();
        $data = [
            'name' => $faker->slug,
      
        ];
        $session = DB::table('sessions')->latest()->first();
        $response = ClearanceRepo::updateSession($data, $session->id);
        $this->assertArrayHasKey('status', $response);
        $this->assertTrue($response['status']);
    }

    public function testUploadFile()
    {
        Storage::fake('file');

        $this->json('post', '/upload', [
            'file' => $file = UploadedFile::fake()->image('random.jpg')
        ]);
        $session = DB::table('sessions')->latest()->first();
        $section = DB::table('clearances_sections')->latest()->first();

        $data = [
            'session' => $session->id,
            'section' => $section->id,
            'file' => [$file]
        ];
        $user = User::latest()->first();
        $this->actingAs(User::find($user->id));
        ClearanceRepo::uploadFile($data);
        $this->assertDatabaseHas('clearances', [
            'session' => $session->name,
            'section_id' => $data['section'],
            'user_id' => $user->id
        ]);
    }

    public function testReviewClearance()
    {
        $faker = Factory::create();
        $data = [
            'status' => 0,
            'message' => $faker->words(10, true),
        ];
        $clearance = DB::table('clearances')->latest()->first();
        $id = $clearance->id;
        $response = ClearanceRepo::reviewClearance($data, $id);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
    }

    public function testNotifyUser()
    {
        $clearance = DB::table('clearances')->latest()->first();
        $response = ClearanceRepo::notifyStatus($clearance->id);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
    }
    
    public function testDownloadZip()
    {
        $cf = DB::table('clearances_files')->latest()->first();
        $response = ClearanceRepo::downloadFile($cf->id);
        $this->assertEquals($response->getStatusCode(), 200);
  
    }

    public function testEditClearanceBeforeReview()
    {
//        $cl = DB::table('files')->latest()->first();
        $cl = File::latest()->first();
        $response = ClearanceRepo::deleteFile($cl->id);
        $this->assertArrayHasKey('status', $response);
        $this->assertTrue($response['status']);
    }

    public function testDeleteSection()
    {
        $section = DB::table('clearances_sections')->latest()->first();
        $response = ClearanceRepo::deleteSection($section->id);
        $this->assertArrayHasKey('status', $response);
        $this->assertTrue($response['status']);
    }
    
    public function testDeleteSession()
    {
        $session = DB::table('sessions')->latest()->first();
        $response = ClearanceRepo::deleteSession($session->id);
        $this->assertArrayHasKey('status', $response);
        $this->assertTrue($response['status']);
    }

}
