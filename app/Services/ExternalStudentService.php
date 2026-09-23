<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ExternalStudentService
{
    private Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client(['timeout' => 30]);
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
        $lines = explode("\n", trim($data));

        if (empty($lines)) {
            return [];
        }

        // Parse header to determine field order
        $header = array_map('strtolower', array_map('trim', explode('|', trim($lines[0]))));
        $fieldMap = array_flip($header);

        foreach (array_slice($lines, 1) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 3) {
                continue;
            }

            $students[] = [
                'nama' => $parts[$fieldMap['nama'] ?? 0] ?? '',
                'nim' => $parts[$fieldMap['nim'] ?? 1] ?? '',
                'ymd' => $parts[$fieldMap['ymd'] ?? 2] ?? '',
            ];
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
