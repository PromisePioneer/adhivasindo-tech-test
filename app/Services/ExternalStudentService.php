<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ExternalStudentService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
    }

    public function search(string $field, string $value): array
    {
        return array_values(
            array_filter($this->fetchAll(), fn ($s) => $this->matches($s, $field, $value))
        );
    }

    private function fetchAll(): array
    {
        try {
            $response = $this->client->get(config('external_api.student_api_url'));
            $data = json_decode($response->getBody()->getContents(), true);

            return ($data['RC'] ?? null) === 200
                ? $this->parse($data['DATA'] ?? '')
                : [];
        } catch (GuzzleException $e) {
            Log::error('External API failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    private function parse(string $data): array
    {
        $students = [];

        foreach (explode("\n", trim($data)) as $line) {
            $parts = array_map('trim', explode('|', trim($line)));

            if (count($parts) >= 3 && $parts[0] !== 'NAMA') {
                $students[] = [
                    'nama' => $parts[0],
                    'nim' => $parts[1],
                    'ymd' => $parts[2],
                ];
            }
        }

        return $students;
    }

    private function matches(array $student, string $field, string $value): bool
    {
        return match ($field) {
            'nama' => str_contains(strtolower($student['nama']), strtolower($value)),
            'nim', 'ymd' => $student[$field] === $value,
        };
    }
}
