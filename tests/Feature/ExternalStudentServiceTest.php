<?php

namespace Tests\Feature;

use App\Services\ExternalStudentService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

function createService(string $responseBody): ExternalStudentService
{
    $mock = new MockHandler([new Response(200, [], $responseBody)]);
    $handler = HandlerStack::create($mock);
    $client = new Client(['handler' => $handler]);

    return new ExternalStudentService($client);
}

test('it parses data with nama|nim|ymd order', function () {
    $data = "NAMA|NIM|YMD\nAbigail Williams|0178453629|20220803";
    $service = createService(json_encode(['RC' => 200, 'DATA' => $data]));

    $results = $service->search('ymd', '20220803');

    expect($results)->toHaveCount(1)
        ->and($results[0]['nama'])->toBe('Abigail Williams')
        ->and($results[0]['nim'])->toBe('0178453629')
        ->and($results[0]['ymd'])->toBe('20220803');
});

test('it parses data with ymd|nim|nama order', function () {
    $data = "YMD|NIM|NAMA\n20220803|0178453629|Abigail Williams";
    $service = createService(json_encode(['RC' => 200, 'DATA' => $data]));

    $results = $service->search('nama', 'Abigail');

    expect($results)->toHaveCount(1)
        ->and($results[0]['nama'])->toBe('Abigail Williams')
        ->and($results[0]['nim'])->toBe('0178453629')
        ->and($results[0]['ymd'])->toBe('20220803');
});

test('it searches by nama case insensitive', function () {
    $data = "NAMA|NIM|YMD\nTurner Mia|1234567890|20230405";
    $service = createService(json_encode(['RC' => 200, 'DATA' => $data]));

    $results = $service->search('nama', 'TURNER');

    expect($results)->toHaveCount(1)
        ->and($results[0]['nama'])->toBe('Turner Mia');
});

test('it searches by nim exact match', function () {
    $data = "NAMA|NIM|YMD\nTurner Mia|9352078461|20230405";
    $service = createService(json_encode(['RC' => 200, 'DATA' => $data]));

    $results = $service->search('nim', '9352078461');

    expect($results)->toHaveCount(1)
        ->and($results[0]['nim'])->toBe('9352078461');
});

test('it searches by ymd exact match', function () {
    $data = "NAMA|NIM|YMD\nTurner Mia|9352078461|20230405";
    $service = createService(json_encode(['RC' => 200, 'DATA' => $data]));

    $results = $service->search('ymd', '20230405');

    expect($results)->toHaveCount(1)
        ->and($results[0]['ymd'])->toBe('20230405');
});

test('it returns empty when no match', function () {
    $data = "NAMA|NIM|YMD\nTurner Mia|9352078461|20230405";
    $service = createService(json_encode(['RC' => 200, 'DATA' => $data]));

    $results = $service->search('nama', 'NonExistent');

    expect($results)->toHaveCount(0);
});

test('it returns empty when api returns error', function () {
    $service = createService(json_encode(['RC' => 500, 'DATA' => '']));

    $results = $service->search('nama', 'test');

    expect($results)->toHaveCount(0);
});
