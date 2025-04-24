<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resourcelink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResourcelinksController extends Controller
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
        $resoucelink = Resourcelink::all();
        return response()->json([
            'data'=> $resoucelink,
            'status' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recommand_resource_id'=>"required|exists:recommand_resources,id",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed while creating resources', $validator->errors());
        }

        try {
            $resoucelink = new Resourcelink();
            $resoucelink->recommand_resource_id = $request->recommand_resource_id;

            $resoucelink->save();
               
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
            'recommand_resource_id'=>"required|exists:recommand_resources,id",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed while creating resources', $validator->errors());
        }

        try {
            $resoucelink = Resourcelink::findOrFail($id);
            $resoucelink->recommand_resource_id = $request->recommand_resource_id;

            $resoucelink->save();
               
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
        $resoucelink = Resourcelink::findOrFail($id);

        try {
            if (!$resoucelink) {
                return $this->errorResponse('Resources not found', null, 404);
            }

            $resoucelink->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Resource deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting resource', ['exception' => [$e->getMessage()]], 500);
        }
    }
}
