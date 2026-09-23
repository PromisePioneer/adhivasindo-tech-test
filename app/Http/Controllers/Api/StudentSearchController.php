<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\ExternalStudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentSearchController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ExternalStudentService $service
    ) {}

    public function search(Request $request, string $field): JsonResponse
    {
        $query = $request->query('q');

        if (empty($query)) {
            return $this->error('Query parameter "q" is required', 400);
        }

        $results = $this->service->search($field, $query);

        return $this->searchResult($results, $query, $field);
    }
}
