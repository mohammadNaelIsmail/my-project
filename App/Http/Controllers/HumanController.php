<?php

namespace App\Http\Controllers;

use App\Models\Human;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Humans",
 *     description="API Endpoints for Humans"
 * )
 */
class HumanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/humans",
     *     tags={"Humans"},
     *     summary="Get list of humans with pagination",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Human")
     *             ),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Human::paginate(10));
    }

    /**
     * @OA\Post(
     *     path="/api/humans",
     *     tags={"Humans"},
     *     summary="Create a new human",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Human")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Human created",
     *         @OA\JsonContent(ref="#/components/schemas/Human")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'age' => 'required|integer',
            'name' => 'required|string',
            'password' => 'required|string|min:6',
            'email' => 'required|email|unique:human,email',
            'location' => 'nullable|string',
            'creditcard' => 'nullable|string',
        ]);

        $human = Human::create($validated);

        return response()->json($human, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/humans/{id}",
     *     tags={"Humans"},
     *     summary="Get a human by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Human ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Human found",
     *         @OA\JsonContent(ref="#/components/schemas/Human")
     *     ),
     *     @OA\Response(response=404, description="Human not found")
     * )
     */
    public function show($id)
    {
        $human = Human::find($id);
        if (!$human) {
            return response()->json(['message' => 'Human not found'], 404);
        }
        return response()->json($human);
    }

    /**
     * @OA\Put(
     *     path="/api/humans/{id}",
     *     tags={"Humans"},
     *     summary="Update a human",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Human ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Human")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Human updated",
     *         @OA\JsonContent(ref="#/components/schemas/Human")
     *     ),
     *     @OA\Response(response=404, description="Human not found"),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $human = Human::find($id);
        if (!$human) {
            return response()->json(['message' => 'Human not found'], 404);
        }

        $validated = $request->validate([
            'age' => 'sometimes|required|integer',
            'name' => 'sometimes|required|string',
            'password' => 'sometimes|required|string|min:6',
            'email' => 'sometimes|required|email|unique:human,email,' . $id . ',human_id',
            'location' => 'nullable|string',
            'creditcard' => 'nullable|string',
        ]);

        if (isset($validated['password'])) {
            $human->password = bcrypt($validated['password']);
            unset($validated['password']);
        }

        $human->update($validated);

        return response()->json($human);
    }

    /**
     * @OA\Delete(
     *     path="/api/humans/{id}",
     *     tags={"Humans"},
     *     summary="Delete a human",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Human ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Human deleted"),
     *     @OA\Response(response=404, description="Human not found")
     * )
     */
    public function destroy($id)
    {
        $human = Human::find($id);
        if (!$human) {
            return response()->json(['message' => 'Human not found'], 404);
        }

        $human->delete();

        return response()->json(null, 204);
    }
}
