<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecommandResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecommandResourcesController extends Controller
{
    private function errorResponse($message, $errors = null, $statusCode = 422)
    {
        return response()->json([
            'status' => $statusCode,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }


    public function index()
    {
        $recommandresouce = RecommandResource::all();
        return response()->json([
            'data'=> $recommandresouce,
            'status' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skill'=>"required",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed while creating resources', $validator->errors());
        }

        try {
            $recommandresource = new RecommandResource();
            $recommandresource->skill = $request->skill;

            $recommandresource->save();
               
            return response()->json([
                'status' => 200,
                'message' => 'Resources created successfully'
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while creating resources', ['exception' => [$e->getMessage()]], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'skill'=>"required",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed while creating resources', $validator->errors());
        }

        try {
            $recommandresource = RecommandResource::findOrFail($id);
            $recommandresource->skill = $request->skill;

            $recommandresource->save();
               
            return response()->json([
                'status' => 200,
                'message' => 'Resources updated successfully'
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while updated Resources', ['exception' => [$e->getMessage()]], 500);
        }
    }

    public function destroy(string $id)
    {
        $recommandresource = RecommandResource::findOrFail($id);

        try {
            if (!$recommandresource) {
                return $this->errorResponse('Resources not found', null, 404);
            }

            $recommandresource->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Resource deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting resource', ['exception' => [$e->getMessage()]], 500);
        }
    }
}
